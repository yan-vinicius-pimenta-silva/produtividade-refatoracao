import type { IconDefinition } from '@fortawesome/fontawesome-svg-core';
import type { MenuItem } from '../interfaces';

export function getPageTitleIcons(menuItems: MenuItem[]) {
  const pageTitleIcons: Record<string, IconDefinition> = {};

  const registerIcons = (items: MenuItem[]) => {
    items.forEach((item) => {
      if (item.route) {
        const key = item.route.replace('/', '');
        pageTitleIcons[key] = item.icon;
      }
      if (item.children) {
        registerIcons(item.children);
      }
    });
  };

  registerIcons(menuItems);

  return pageTitleIcons;
}
