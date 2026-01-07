import {
  faBuilding,
  faCalculator,
  faChartBar,
  faChartLine,
  faClipboardList,
  faCogs,
  faFileSignature,
  faListCheck,
  faUsers,
} from '@fortawesome/free-solid-svg-icons';
import type { MenuItem } from '../interfaces';
import { PERMISSIONS } from '../permissions/tokens';

export const menuItems: MenuItem[] = [
  {
    label: 'Dashboard',
    icon: faChartLine,
    route: '/dashboard',
  },
  {
    label: 'Usuários',
    icon: faUsers,
    route: '/users',
    permission: PERMISSIONS.USERS,
  },
  {
    label: 'Recursos',
    icon: faCogs,
    route: '/resources',
    permission: PERMISSIONS.RESOURCES,
  },
  {
    label: 'Relatórios',
    icon: faChartBar,
    route: '/reports',
    permission: PERMISSIONS.REPORTS,
  },
  {
    label: 'Empresas',
    icon: faBuilding,
    route: '/companies',
    permission: PERMISSIONS.COMPANIES,
  },
  {
    label: 'Tipos de Atividade',
    icon: faListCheck,
    route: '/activity-types',
    permission: PERMISSIONS.ACTIVITY_TYPES,
  },
  {
    label: 'Atividades',
    icon: faClipboardList,
    route: '/activities',
    permission: PERMISSIONS.ACTIVITIES,
  },
  {
    label: 'UFESP',
    icon: faCalculator,
    route: '/ufesp-rates',
    permission: PERMISSIONS.UFESP_RATES,
  },
  {
    label: 'Atividades Fiscais',
    icon: faFileSignature,
    route: '/fiscal-activities',
    permission: PERMISSIONS.FISCAL_ACTIVITIES,
  },
  {
    label: 'Ordens de Serviço',
    icon: faFileSignature,
    route: '/service-orders',
    permission: PERMISSIONS.SERVICE_ORDERS,
  },
];
