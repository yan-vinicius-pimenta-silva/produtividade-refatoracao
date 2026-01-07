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
  CompanyRead,
  ServiceOrderCreatePayload,
  ServiceOrderRead,
} from '../../interfaces';
import { createServiceOrder, listCompanies, listServiceOrders } from '../../services';
import { getErrorMessage } from '../../helpers';

const initialForm: ServiceOrderCreatePayload = {
  companyId: 0,
  description: '',
  observation: '',
  rc: '',
  documentNumber: '',
  protocolNumber: '',
  dueDate: undefined,
  completionDate: undefined,
};

export default function ServiceOrders() {
  const [serviceOrders, setServiceOrders] = useState<ServiceOrderRead[]>([]);
  const [companies, setCompanies] = useState<CompanyRead[]>([]);
  const [form, setForm] = useState<ServiceOrderCreatePayload>(initialForm);
  const { showNotification } = useNotification();
  const { permissionsMap } = usePermissions();

  async function loadData() {
    const [companiesData, ordersData] = await Promise.all([
      listCompanies(),
      listServiceOrders(),
    ]);
    setCompanies(companiesData);
    setServiceOrders(ordersData);
  }

  async function handleSubmit() {
    try {
      await createServiceOrder(form);
      setForm(initialForm);
      await loadData();
      showNotification('Ordem de serviço criada!', 'success');
    } catch (err) {
      showNotification(getErrorMessage(err), 'error');
    }
  }

  useEffect(() => {
    loadData();
  }, []);

  return (
    <Container sx={{ mt: 4 }}>
      <PageTitle icon={permissionsMap.SERVICE_ORDERS} title="Ordens de Serviço" />

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
            label="Descrição"
            value={form.description}
            onChange={(event) =>
              setForm({ ...form, description: event.target.value })
            }
          />
          <TextField
            label="RC"
            value={form.rc ?? ''}
            onChange={(event) => setForm({ ...form, rc: event.target.value })}
          />
          <TextField
            type="date"
            label="Prazo"
            value={form.dueDate ?? ''}
            onChange={(event) =>
              setForm({ ...form, dueDate: event.target.value })
            }
            InputLabelProps={{ shrink: true }}
          />
        </Box>
        <Box mt={2} textAlign="right">
          <Button variant="contained" onClick={handleSubmit}>
            Criar ordem
          </Button>
        </Box>
      </Paper>

      <Paper>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Empresa</TableCell>
              <TableCell>Descrição</TableCell>
              <TableCell>Prazo</TableCell>
              <TableCell>Respondido</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {serviceOrders.map((order) => (
              <TableRow key={order.id}>
                <TableCell>{order.id}</TableCell>
                <TableCell>
                  {companies.find((c) => c.id === order.companyId)?.name ?? '-'}
                </TableCell>
                <TableCell>{order.description}</TableCell>
                <TableCell>{order.dueDate?.slice(0, 10) ?? '-'}</TableCell>
                <TableCell>{order.isResponded ? 'Sim' : 'Não'}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </Container>
  );
}
