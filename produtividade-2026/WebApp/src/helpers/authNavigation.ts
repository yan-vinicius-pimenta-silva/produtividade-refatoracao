import type { AuthUser } from '../interfaces';
import { isFiscalUser } from '../permissions/Rules';

export const getDefaultRouteForUser = (authUser: AuthUser | null): string => {
  if (!authUser) {
    return '/dashboard';
  }
  return isFiscalUser(authUser) ? '/produtividade/perfil' : '/dashboard';
};
