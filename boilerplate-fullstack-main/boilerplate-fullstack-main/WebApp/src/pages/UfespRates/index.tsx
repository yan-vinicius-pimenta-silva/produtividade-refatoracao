import { useEffect, useState } from 'react';
import {
  Box,
  Button,
  Container,
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
import type { UfespRateCreatePayload, UfespRateRead } from '../../interfaces';
import { createUfespRate, listUfespRates } from '../../services';
import { getErrorMessage } from '../../helpers';

const initialForm: UfespRateCreatePayload = {
  year: new Date().getFullYear(),
  value: 0,
  active: true,
};

export default function UfespRates() {
  const [rates, setRates] = useState<UfespRateRead[]>([]);
  const [form, setForm] = useState<UfespRateCreatePayload>(initialForm);
  const { showNotification } = useNotification();
  const { permissionsMap } = usePermissions();

  async function loadRates() {
    const data = await listUfespRates();
    setRates(data);
  }

  async function handleSubmit() {
    try {
      await createUfespRate(form);
      setForm(initialForm);
      await loadRates();
      showNotification('UFESP cadastrada com sucesso!', 'success');
    } catch (err) {
      showNotification(getErrorMessage(err), 'error');
    }
  }

  useEffect(() => {
    loadRates();
  }, []);

  return (
    <Container sx={{ mt: 4 }}>
      <PageTitle icon={permissionsMap.UFESP_RATES} title="Tabela UFESP" />

      <Paper sx={{ p: 3, mb: 3 }}>
        <Box display="grid" gridTemplateColumns="repeat(auto-fit, minmax(200px, 1fr))" gap={2}>
          <TextField
            type="number"
            label="Ano"
            value={form.year}
            onChange={(event) =>
              setForm({ ...form, year: Number(event.target.value) })
            }
          />
          <TextField
            type="number"
            label="Valor"
            value={form.value}
            onChange={(event) =>
              setForm({ ...form, value: Number(event.target.value) })
            }
          />
        </Box>
        <Box mt={2} textAlign="right">
          <Button variant="contained" onClick={handleSubmit}>
            Cadastrar UFESP
          </Button>
        </Box>
      </Paper>

      <Paper>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Ano</TableCell>
              <TableCell>Valor</TableCell>
              <TableCell>Ativa</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {rates.map((rate) => (
              <TableRow key={rate.id}>
                <TableCell>{rate.id}</TableCell>
                <TableCell>{rate.year}</TableCell>
                <TableCell>{rate.value}</TableCell>
                <TableCell>{rate.active ? 'Sim' : 'Não'}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </Container>
  );
}
