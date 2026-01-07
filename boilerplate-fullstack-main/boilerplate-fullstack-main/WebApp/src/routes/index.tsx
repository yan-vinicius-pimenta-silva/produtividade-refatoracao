import { createBrowserRouter } from 'react-router-dom';
import ProtectedRoute from './ProtectedRoute';
import {
  DashBoard,
  Login,
  NotFound,
  PasswordReset,
  Reports,
  Resources,
  UnauthorizedAccess,
  Users,
  Companies,
  ActivityTypes,
  Activities,
  UfespRates,
  FiscalActivities,
  ServiceOrders,
} from '../pages';
import { CleanLayout, DefaultLayout } from '../layouts';
import { UsersProvider } from '../contexts';
import { PERMISSIONS } from '../permissions';

const publicRoutes = [
  { path: '/login', element: <Login /> },
  { path: '/password-reset', element: <PasswordReset /> },
];

const privateRoutes = [
  {
    path: '/dashboard',
    element: <DashBoard />,
  },
  {
    path: '/users',
    element: (
      <UsersProvider>
        <Users />
      </UsersProvider>
    ),
    requiredPermission: PERMISSIONS.USERS,
  },
  {
    path: '/resources',
    element: <Resources />,
    requiredPermission: PERMISSIONS.RESOURCES,
  },
  {
    path: '/reports',
    element: (
      <UsersProvider>
        <Reports />
      </UsersProvider>
    ),
    requiredPermission: PERMISSIONS.REPORTS,
  },
  {
    path: '/companies',
    element: <Companies />,
    requiredPermission: PERMISSIONS.COMPANIES,
  },
  {
    path: '/activity-types',
    element: <ActivityTypes />,
    requiredPermission: PERMISSIONS.ACTIVITY_TYPES,
  },
  {
    path: '/activities',
    element: <Activities />,
    requiredPermission: PERMISSIONS.ACTIVITIES,
  },
  {
    path: '/ufesp-rates',
    element: <UfespRates />,
    requiredPermission: PERMISSIONS.UFESP_RATES,
  },
  {
    path: '/fiscal-activities',
    element: <FiscalActivities />,
    requiredPermission: PERMISSIONS.FISCAL_ACTIVITIES,
  },
  {
    path: '/service-orders',
    element: <ServiceOrders />,
    requiredPermission: PERMISSIONS.SERVICE_ORDERS,
  },
];

const protectedRoutes = privateRoutes.map((route) => ({
  path: route.path,
  element: (
    <ProtectedRoute requiredPermission={route.requiredPermission}>
      {route.element}
    </ProtectedRoute>
  ),
}));

const router = createBrowserRouter([
  {
    element: <CleanLayout />,
    children: [
      { path: '/', element: <Login /> },
      ...publicRoutes,
      { path: '/unauthorized', element: <UnauthorizedAccess /> },
      { path: '*', element: <NotFound /> },
    ],
  },

  {
    element: <DefaultLayout />,
    children: protectedRoutes,
  },
]);

export default router;
