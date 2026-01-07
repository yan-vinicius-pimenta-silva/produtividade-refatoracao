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
import type { ActivityTypeCreatePayload, ActivityTypeRead } from '../../interfaces';
import { createActivityType, listActivityTypes } from '../../services';
import { getErrorMessage } from '../../helpers';

export default function ActivityTypes() {
  const [types, setTypes] = useState<ActivityTypeRead[]>([]);
  const [name, setName] = useState('');
  const { showNotification } = useNotification();
  const { permissionsMap } = usePermissions();

  async function loadTypes() {
    const data = await listActivityTypes();
    setTypes(data);
  }

  async function handleSubmit() {
    try {
      await createActivityType({ name });
      setName('');
      await loadTypes();
      showNotification('Tipo de atividade cadastrado!', 'success');
    } catch (err) {
      showNotification(getErrorMessage(err), 'error');
    }
  }

  useEffect(() => {
    loadTypes();
  }, []);

  return (
    <Container sx={{ mt: 4 }}>
      <PageTitle icon={permissionsMap.ACTIVITY_TYPES} title="Tipos de Atividade" />

      <Paper sx={{ p: 3, mb: 3 }}>
        <Box display="flex" gap={2} alignItems="center">
          <TextField
            fullWidth
            label="Nome"
            value={name}
            onChange={(event) => setName(event.target.value)}
          />
          <Button variant="contained" onClick={handleSubmit}>
            Cadastrar
          </Button>
        </Box>
      </Paper>

      <Paper>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Nome</TableCell>
              <TableCell>Ativo</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {types.map((type) => (
              <TableRow key={type.id}>
                <TableCell>{type.id}</TableCell>
                <TableCell>{type.name}</TableCell>
                <TableCell>{type.active ? 'Sim' : 'Não'}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </Container>
  );
}
