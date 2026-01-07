import { useEffect, useState } from 'react';
import {
  Box,
  Button,
  Container,
  MenuItem,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  TextField,
} from '@mui/material';
import { PageTitle } from '../../components';
import { useNotification, usePermissions } from '../../hooks';
import type {
  ActivityRead,
  CompanyRead,
  FiscalActivityCreatePayload,
  FiscalActivityRead,
  UserRead,
} from '../../interfaces';
import {
  createFiscalActivity,
  listActivities,
  listCompanies,
  listFiscalActivities,
  listUsersForSelect,
} from '../../services';
import { getErrorMessage } from '../../helpers';

const initialForm: FiscalActivityCreatePayload = {
  activityId: 0,
  companyId: 0,
  fiscalUserId: 0,
  completionDate: new Date().toISOString().slice(0, 10),
  documentNumber: '',
  protocolNumber: '',
  rc: '',
  cpfCnpj: '',
  value: undefined,
  quantity: undefined,
  observation: '',
};

export default function FiscalActivities() {
  const [fiscalActivities, setFiscalActivities] = useState<FiscalActivityRead[]>([]);
  const [companies, setCompanies] = useState<CompanyRead[]>([]);
  const [activities, setActivities] = useState<ActivityRead[]>([]);
  const [users, setUsers] = useState<UserRead[]>([]);
  const [form, setForm] = useState<FiscalActivityCreatePayload>(initialForm);
  const { showNotification } = useNotification();
  const { permissionsMap } = usePermissions();

  async function loadData() {
    const [companiesData, activitiesData, usersData, fiscalData] = await Promise.all([
      listCompanies(),
      listActivities(),
      listUsersForSelect(),
      listFiscalActivities(),
    ]);
    setCompanies(companiesData);
    setActivities(activitiesData);
    setUsers(usersData);
    setFiscalActivities(fiscalData);
  }

  async function handleSubmit() {
    try {
      await createFiscalActivity(form);
      setForm(initialForm);
      await loadData();
      showNotification('Atividade fiscal cadastrada!', 'success');
    } catch (err) {
      showNotification(getErrorMessage(err), 'error');
    }
  }

  useEffect(() => {
    loadData();
  }, []);

  return (
    <Container sx={{ mt: 4 }}>
      <PageTitle icon={permissionsMap.FISCAL_ACTIVITIES} title="Atividades Fiscais" />

      <Paper sx={{ p: 3, mb: 3 }}>
        <Box display="grid" gridTemplateColumns="repeat(auto-fit, minmax(200px, 1fr))" gap={2}>
          <TextField
            select
            label="Empresa"
            value={form.companyId || ''}
            onChange={(event) =>
              setForm({ ...form, companyId: Number(event.target.value) })
            }
          >
            {companies.map((company) => (
              <MenuItem key={company.id} value={company.id}>
                {company.name}
              </MenuItem>
            ))}
          </TextField>
          <TextField
            select
            label="Atividade"
            value={form.activityId || ''}
            onChange={(event) =>
              setForm({ ...form, activityId: Number(event.target.value) })
            }
          >
            {activities.map((activity) => (
              <MenuItem key={activity.id} value={activity.id}>
                {activity.name}
              </MenuItem>
            ))}
          </TextField>
          <TextField
            select
            label="Fiscal"
            value={form.fiscalUserId || ''}
            onChange={(event) =>
              setForm({ ...form, fiscalUserId: Number(event.target.value) })
            }
          >
            {users.map((user) => (
              <MenuItem key={user.id} value={user.id}>
                {user.fullName}
              </MenuItem>
            ))}
          </TextField>
          <TextField
            label="Data de conclusão"
            type="date"
            value={form.completionDate}
            onChange={(event) =>
              setForm({ ...form, completionDate: event.target.value })
            }
            InputLabelProps={{ shrink: true }}
          />
          <TextField
            label="Valor"
            type="number"
            value={form.value ?? ''}
            onChange={(event) =>
              setForm({
                ...form,
                value: event.target.value ? Number(event.target.value) : undefined,
              })
            }
          />
          <TextField
            label="Quantidade"
            type="number"
            value={form.quantity ?? ''}
            onChange={(event) =>
              setForm({
                ...form,
                quantity: event.target.value ? Number(event.target.value) : undefined,
              })
            }
          />
        </Box>
        <Box mt={2} textAlign="right">
          <Button variant="contained" onClick={handleSubmit}>
            Cadastrar atividade fiscal
          </Button>
        </Box>
      </Paper>

      <Paper>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Empresa</TableCell>
              <TableCell>Atividade</TableCell>
              <TableCell>Fiscal</TableCell>
              <TableCell>Pontos</TableCell>
              <TableCell>Data conclusão</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {fiscalActivities.map((item) => (
              <TableRow key={item.id}>
                <TableCell>{item.id}</TableCell>
                <TableCell>
                  {companies.find((c) => c.id === item.companyId)?.name ?? '-'}
                </TableCell>
                <TableCell>
                  {activities.find((a) => a.id === item.activityId)?.name ?? '-'}
                </TableCell>
                <TableCell>
                  {users.find((u) => u.id === item.fiscalUserId)?.fullName ?? '-'}
                </TableCell>
                <TableCell>{item.pointsTotal ?? '-'}</TableCell>
                <TableCell>{item.completionDate.slice(0, 10)}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </Container>
  );
}
