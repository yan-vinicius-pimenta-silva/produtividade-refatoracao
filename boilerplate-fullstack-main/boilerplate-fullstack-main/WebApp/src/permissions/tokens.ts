export const PERMISSIONS = {
  ROOT: 'root',
  USERS: 'users',
  RESOURCES: 'resources',
  REPORTS: 'reports',
  COMPANIES: 'companies',
  ACTIVITY_TYPES: 'activity-types',
  ACTIVITIES: 'activities',
  UFESP_RATES: 'ufesp-rates',
  FISCAL_ACTIVITIES: 'fiscal-activities',
  SERVICE_ORDERS: 'service-orders',
} as const;

export type ValidPermission = (typeof PERMISSIONS)[keyof typeof PERMISSIONS];
