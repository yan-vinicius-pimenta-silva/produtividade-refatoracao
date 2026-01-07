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
import type { CompanyCreatePayload, CompanyRead } from '../../interfaces';
import { createCompany, listCompanies } from '../../services';
import { getErrorMessage } from '../../helpers';

const initialForm: CompanyCreatePayload = {
  name: '',
  email: '',
  secretary: '',
  division: '',
  phone: '',
  logoUrl: '',
  parametersJson: '',
};

export default function Companies() {
  const [companies, setCompanies] = useState<CompanyRead[]>([]);
  const [form, setForm] = useState<CompanyCreatePayload>(initialForm);
  const { showNotification } = useNotification();
  const { permissionsMap } = usePermissions();

  async function loadCompanies() {
    const data = await listCompanies();
    setCompanies(data);
  }

  async function handleSubmit() {
    try {
      await createCompany(form);
      setForm(initialForm);
      await loadCompanies();
      showNotification('Empresa cadastrada com sucesso!', 'success');
    } catch (err) {
      showNotification(getErrorMessage(err), 'error');
    }
  }

  useEffect(() => {
    loadCompanies();
  }, []);

  return (
    <Container sx={{ mt: 4 }}>
      <PageTitle icon={permissionsMap.COMPANIES} title="Empresas" />

      <Paper sx={{ p: 3, mb: 3 }}>
        <Box display="grid" gridTemplateColumns="repeat(auto-fit, minmax(220px, 1fr))" gap={2}>
          <TextField
            label="Nome"
            value={form.name}
            onChange={(event) => setForm({ ...form, name: event.target.value })}
          />
          <TextField
            label="Email"
            value={form.email ?? ''}
            onChange={(event) => setForm({ ...form, email: event.target.value })}
          />
          <TextField
            label="Secretaria"
            value={form.secretary ?? ''}
            onChange={(event) =>
              setForm({ ...form, secretary: event.target.value })
            }
          />
          <TextField
            label="Divisão"
            value={form.division ?? ''}
            onChange={(event) =>
              setForm({ ...form, division: event.target.value })
            }
          />
          <TextField
            label="Telefone"
            value={form.phone ?? ''}
            onChange={(event) => setForm({ ...form, phone: event.target.value })}
          />
          <TextField
            label="Logo URL"
            value={form.logoUrl ?? ''}
            onChange={(event) =>
              setForm({ ...form, logoUrl: event.target.value })
            }
          />
        </Box>
        <Box mt={2} textAlign="right">
          <Button variant="contained" onClick={handleSubmit}>
            Cadastrar empresa
          </Button>
        </Box>
      </Paper>

      <Paper>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Nome</TableCell>
              <TableCell>Email</TableCell>
              <TableCell>Secretaria</TableCell>
              <TableCell>Divisão</TableCell>
              <TableCell>Ativa</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {companies.map((company) => (
              <TableRow key={company.id}>
                <TableCell>{company.id}</TableCell>
                <TableCell>{company.name}</TableCell>
                <TableCell>{company.email ?? '-'}</TableCell>
                <TableCell>{company.secretary ?? '-'}</TableCell>
                <TableCell>{company.division ?? '-'}</TableCell>
                <TableCell>{company.active ? 'Sim' : 'Não'}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </Container>
  );
}
