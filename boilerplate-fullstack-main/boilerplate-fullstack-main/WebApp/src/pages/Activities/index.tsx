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
  ActivityCreatePayload,
  ActivityRead,
  ActivityTypeRead,
  CompanyRead,
} from '../../interfaces';
import {
  createActivity,
  listActivities,
  listActivityTypes,
  listCompanies,
} from '../../services';
import { getErrorMessage } from '../../helpers';

const initialForm: ActivityCreatePayload = {
  companyId: 0,
  activityTypeId: 0,
  name: '',
  pointsBase: 0,
};

export default function Activities() {
  const [activities, setActivities] = useState<ActivityRead[]>([]);
  const [companies, setCompanies] = useState<CompanyRead[]>([]);
  const [types, setTypes] = useState<ActivityTypeRead[]>([]);
  const [form, setForm] = useState<ActivityCreatePayload>(initialForm);
  const { showNotification } = useNotification();
  const { permissionsMap } = usePermissions();

  async function loadData() {
    const [companiesData, typesData, activitiesData] = await Promise.all([
      listCompanies(),
      listActivityTypes(),
      listActivities(),
    ]);
    setCompanies(companiesData);
    setTypes(typesData);
    setActivities(activitiesData);
  }

  async function handleSubmit() {
    try {
      await createActivity(form);
      setForm(initialForm);
      await loadData();
      showNotification('Atividade cadastrada com sucesso!', 'success');
    } catch (err) {
      showNotification(getErrorMessage(err), 'error');
    }
  }

  useEffect(() => {
    loadData();
  }, []);

  return (
    <Container sx={{ mt: 4 }}>
      <PageTitle icon={permissionsMap.ACTIVITIES} title="Atividades" />

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
            label="Tipo"
            value={form.activityTypeId || ''}
            onChange={(event) =>
              setForm({ ...form, activityTypeId: Number(event.target.value) })
            }
          >
            {types.map((type) => (
              <MenuItem key={type.id} value={type.id}>
                {type.name}
              </MenuItem>
            ))}
          </TextField>
          <TextField
            label="Nome"
            value={form.name}
            onChange={(event) => setForm({ ...form, name: event.target.value })}
          />
          <TextField
            type="number"
            label="Pontos Base"
            value={form.pointsBase || ''}
            onChange={(event) =>
              setForm({ ...form, pointsBase: Number(event.target.value) })
            }
          />
        </Box>
        <Box mt={2} textAlign="right">
          <Button variant="contained" onClick={handleSubmit}>
            Cadastrar atividade
          </Button>
        </Box>
      </Paper>

      <Paper>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Nome</TableCell>
              <TableCell>Empresa</TableCell>
              <TableCell>Tipo</TableCell>
              <TableCell>Pontos</TableCell>
              <TableCell>Ativa</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {activities.map((activity) => (
              <TableRow key={activity.id}>
                <TableCell>{activity.id}</TableCell>
                <TableCell>{activity.name}</TableCell>
                <TableCell>
                  {companies.find((c) => c.id === activity.companyId)?.name ?? '-'}
                </TableCell>
                <TableCell>
                  {types.find((t) => t.id === activity.activityTypeId)?.name ?? '-'}
                </TableCell>
                <TableCell>{activity.pointsBase}</TableCell>
                <TableCell>{activity.active ? 'Sim' : 'Não'}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </Container>
  );
}
