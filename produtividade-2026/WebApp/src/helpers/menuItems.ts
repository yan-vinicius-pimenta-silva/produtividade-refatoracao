import {
  faChartBar,
  faChartLine,
  faCogs,
  faClipboardList,
  faFileLines,
  faSignOutAlt,
  faUsers,
} from '@fortawesome/free-solid-svg-icons';
import type { MenuItem } from '../interfaces';
import { PERMISSIONS } from '../permissions/tokens';

export const menuItems: MenuItem[] = [
  {
    label: 'Produtividade',
    icon: faClipboardList,
    children: [
      {
        label: 'Dashboard',
        icon: faChartLine,
        route: '/dashboard',
      },
      {
        label: 'Home',
        icon: faChartLine,
        route: '/produtividade',
      },
      {
        label: 'Deduções',
        icon: faClipboardList,
        children: [
          {
            label: 'Cadastrar',
            icon: faClipboardList,
            route: '/deducoes/cadastro',
          },
          {
            label: 'Consultar',
            icon: faClipboardList,
            route: '/deducoes/consulta',
          },
        ],
      },
      {
        label: 'Histórico',
        icon: faChartBar,
        route: '/produtividade/historico',
      },
      {
        label: 'Parâmetros',
        icon: faCogs,
        children: [
          {
            label: 'Atividades',
            icon: faCogs,
            route: '/parametros/atividades',
          },
          {
            label: 'Unidade fiscal',
            icon: faCogs,
            route: '/parametros/unidadefiscal',
          },
        ],
      },
      {
        label: 'Lixeira',
        icon: faChartBar,
        route: '/produtividade/lixeira',
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
        label: 'Logs do sistema',
        icon: faFileLines,
        route: '/logs',
        permission: PERMISSIONS.REPORTS,
      },
      {
        label: 'Sair',
        icon: faSignOutAlt,
        route: '/logout',
      },
    ],
  },
];
