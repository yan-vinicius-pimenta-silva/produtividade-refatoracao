import {
  createContext,
  useState,
  useEffect,
  type ReactNode,
  useCallback,
} from 'react';
import type {
  AuthUser,
  MenuItem,
  PermissionsContextProps,
} from '../interfaces';
import { cleanStates, getPageTitleIcons, menuItems } from '../helpers';
import { fiscalMenuItems } from '../helpers/menuItems';
import { useSystemResources } from '../hooks';
import { hasPermission, isFiscalUser, isRootUser } from '../permissions/Rules';
import type { ValidPermission } from '../permissions';

const PermissionsContext = createContext<PermissionsContextProps | undefined>(
  undefined
);
export default PermissionsContext;

export function PermissionsProvider({ children }: { children: ReactNode }) {
  const { fetchSystemResourcesForSelect } = useSystemResources();
  const [permissionsMap, setPermissionsMap] = useState<
    Record<string, ValidPermission>
  >(cleanStates.initialPermissionsMap);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadPermissions = useCallback(async () => {
    try {
      setLoading(true);
      const systemResources = await fetchSystemResourcesForSelect();

      const map = systemResources.reduce((acc, resource) => {
        acc[resource.name.toUpperCase()] = resource.name as ValidPermission;
        return acc;
      }, {} as Record<string, ValidPermission>);

      setPermissionsMap(map);
      setError(null);
    } catch (err) {
      console.error('Erro ao carregar permissions:', err);
      setError('Erro ao carregar permissões');
    } finally {
      setLoading(false);
    }
  }, [fetchSystemResourcesForSelect]);

  const refreshPermissions = async () => {
    await loadPermissions();
  };

  const pageTitleIcons = getPageTitleIcons(menuItems);

  const filterMenuItems = (
    items: MenuItem[],
    authUser: AuthUser | null
  ): MenuItem[] => {
    return items.reduce<MenuItem[]>((acc, item) => {
      if (item.permission) {
        if (!authUser) return acc;
        if (!isRootUser(authUser) && !hasPermission(authUser, item.permission)) {
          return acc;
        }
      }

      const children = item.children
        ? filterMenuItems(item.children, authUser)
        : undefined;

      acc.push({
        ...item,
        children: children && children.length > 0 ? children : undefined,
      });

      return acc;
    }, []);
  };

  const getMenuItemsForUser = (authUser: AuthUser | null): MenuItem[] => {
    if (authUser && isFiscalUser(authUser)) {
      return fiscalMenuItems;
    }
    return filterMenuItems(menuItems, authUser);
  };

  useEffect(() => {
    loadPermissions();
  }, [loadPermissions]);

  return (
    <PermissionsContext.Provider
      value={{
        permissionsMap,
        pageTitleIcons,
        menuItems,
        loading,
        error,
        refreshPermissions,
        getMenuItemsForUser,
      }}
    >
      {children}
    </PermissionsContext.Provider>
  );
}
