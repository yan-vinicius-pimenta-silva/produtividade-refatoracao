// router.tsx (ou onde você monta o router)
import { createBrowserRouter } from 'react-router-dom';
import ProtectedRoute from './ProtectedRoute';
import {
  DashBoard,
  Login,
  NotFound,
  PasswordReset,
  Produtividade,
  Reports,
  Resources,
  UnauthorizedAccess,
  Users,
} from '../pages';
import { CleanLayout, DefaultLayout } from '../layouts';
import { UsersProvider } from '../contexts';
import { PERMISSIONS } from '../permissions';
import DeducaoCadastro from '../pages/Deducoes/Cadastro';
import DeducaoConsulta from '../pages/Deducoes/Consulta';
import ParametrosAtividades from '../pages/Parametros/Atividades';
import ParametrosUnidadeFiscal from '../pages/Parametros/UnidadeFiscal';

const publicRoutes = [
  { path: '/login', element: <Login /> },
  { path: '/password-reset', element: <PasswordReset /> },
];

const privateRoutes = [
  { path: '/dashboard', element: <DashBoard /> },
  { path: '/produtividade', element: <Produtividade /> },
  { path: '/deducoes/cadastro', element: <DeducaoCadastro /> },
  { path: '/deducoes/consulta', element: <DeducaoConsulta /> },

  // LIBERADOS (sem requiredPermission)
  { path: '/parametros/atividades', element: <ParametrosAtividades /> },
  { path: '/parametros/unidadefiscal', element: <ParametrosUnidadeFiscal /> },

  // COM PERMISSÃO
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
];


const protectedRoutes = privateRoutes.map((route) => ({
  path: route.path,
  element: route.requiredPermission ? (
    <ProtectedRoute requiredPermission={route.requiredPermission}>
      {route.element}
    </ProtectedRoute>
  ) : (
    route.element
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
