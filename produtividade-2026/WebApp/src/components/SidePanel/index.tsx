import {
  Box,
  Collapse,
  Drawer,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Tooltip,
} from '@mui/material';
import { useLocation } from 'react-router-dom';
import { useState } from 'react';
import AuthUserDisplay from '../AuthUserDisplay';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useAuth, usePermissions } from '../../hooks';
import type { MenuItem } from '../../interfaces';

interface SidePanelProps {
  open: boolean;
  onNavigate: (route: string) => void;
}

const DRAWER_OPEN = 260;
const DRAWER_CLOSED = 52;

export default function SidePanel({ open, onNavigate }: SidePanelProps) {
  const { authUser, handleLogout } = useAuth();
  const { getMenuItemsForUser } = usePermissions();
  const location = useLocation();
  const [expandedItems, setExpandedItems] = useState<Record<string, boolean>>(
    {}
  );

  const drawerWidth = open ? DRAWER_OPEN : DRAWER_CLOSED;

  const filteredMenu = getMenuItemsForUser(authUser);

  return (
    <Drawer
      variant="permanent"
      open={open}
      sx={{
        width: drawerWidth,
        flexShrink: 0,
        whiteSpace: 'nowrap',
        transition: (theme) =>
          theme.transitions.create('width', {
            easing: theme.transitions.easing.sharp,
            duration: theme.transitions.duration.enteringScreen,
          }),
        '& .MuiDrawer-paper': {
          width: drawerWidth,
          transition: (theme) =>
            theme.transitions.create('width', {
              easing: theme.transitions.easing.sharp,
              duration: theme.transitions.duration.enteringScreen,
            }),
          overflowX: 'hidden',
        },
      }}
    >
      <Box sx={{ overflow: 'hidden' }}>
        <AuthUserDisplay collapsed={!open} />

        <List>
          {filteredMenu.map((item) => {
            const renderMenuItem = (menuItem: MenuItem, depth = 0) => {
              const hasChildren = Boolean(menuItem.children?.length);
              const isItemRouteActive = (currentItem: MenuItem): boolean =>
                Boolean(
                  currentItem.route &&
                    location.pathname === currentItem.route
                );
              const hasActiveDescendant = (
                currentItem: MenuItem
              ): boolean =>
                currentItem.children?.some(
                  (child) =>
                    isItemRouteActive(child) || hasActiveDescendant(child)
                ) ?? false;
              const active = isItemRouteActive(menuItem);
              const hasActiveChild = hasActiveDescendant(menuItem);
              const expanded =
                expandedItems[menuItem.label] ?? (open && hasActiveChild);

              const handleClick = () => {
                if (hasChildren) {
                  if (!open) return;
                  setExpandedItems((prev) => ({
                    ...prev,
                    [menuItem.label]: !expanded,
                  }));
                  return;
                }

                if (!menuItem.route) return;
                if (menuItem.route === '/logout') {
                  handleLogout();
                  onNavigate('/login');
                  return;
                }

                onNavigate(menuItem.route);
              };

              return (
                <Box key={`${menuItem.label}-${depth}`}>
                  <ListItem disablePadding>
                    <Tooltip
                      title={menuItem.label}
                      placement="right"
                      arrow
                      disableHoverListener={open}
                    >
                      <ListItemButton
                        onClick={handleClick}
                        sx={{
                          pl: open ? 2 + depth * 2 : 2,
                          backgroundColor: active
                            ? (theme) => theme.palette.primary.main
                            : 'transparent',
                          color: active
                            ? (theme) => theme.palette.primary.contrastText
                            : (theme) => theme.palette.text.primary,
                          '& .MuiListItemIcon-root': {
                            color: active
                              ? (theme) => theme.palette.primary.contrastText
                              : (theme) => theme.palette.text.secondary,
                            minWidth: open ? 40 : 32,
                          },
                          '&:hover': {
                            backgroundColor: active
                              ? (theme) => theme.palette.primary.dark
                              : (theme) => theme.palette.action.hover,
                            color: active
                              ? (theme) => theme.palette.primary.contrastText
                              : (theme) => theme.palette.text.primary,
                            '& .MuiListItemIcon-root': {
                              color: active
                                ? (theme) => theme.palette.primary.contrastText
                                : (theme) => theme.palette.text.primary,
                            },
                          },
                        }}
                      >
                        <ListItemIcon>
                          <FontAwesomeIcon icon={menuItem.icon} />
                        </ListItemIcon>
                        <ListItemText
                          primary={menuItem.label}
                          sx={{
                            opacity: open ? 1 : 0,
                            transition: 'opacity 0.3s',
                            whiteSpace: 'nowrap',
                          }}
                        />
                      </ListItemButton>
                    </Tooltip>
                  </ListItem>
                  {hasChildren && (
                    <Collapse in={open && expanded} timeout="auto" unmountOnExit>
                      <List disablePadding>
                        {menuItem.children?.map((child) =>
                          renderMenuItem(child, depth + 1)
                        )}
                      </List>
                    </Collapse>
                  )}
                </Box>
              );
            };

            return renderMenuItem(item);
          })}
        </List>
      </Box>
    </Drawer>
  );
}
