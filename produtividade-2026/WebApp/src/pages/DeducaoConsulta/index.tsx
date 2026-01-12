import { useState } from 'react';
import {
  Box,
  Button,
  Card,
  CardContent,
  MenuItem,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TextField,
  Typography,
} from '@mui/material';
import { FilterList } from '@mui/icons-material';

const columns = [
  'ID',
  'Tipo',
  'Data',
  'Nº protocolo',
  'Nº documento',
  'RC',
  'CPF/CNPJ',
  'Pontos',
  'Quantidade',
  'Valor',
  'Fiscal',
  'Documento',
  'Validação',
  'Observação',
  'Usuário Dedução',
  'Justificativa Dedução',
  'Opções',
];

const pageSizes = [10, 25, 50];

export default function DeducaoConsulta() {
  const [pageSize, setPageSize] = useState(10);

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
      <Card sx={{ borderRadius: 3, boxShadow: 3 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 3 }}>
            Consulta de Deduções
          </Typography>

          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'space-between',
              gap: 2,
              mb: 2,
            }}
          >
            <TextField
              select
              label="Resultados por página"
              variant="standard"
              value={pageSize}
              onChange={(event) => setPageSize(Number(event.target.value))}
              sx={{ minWidth: 220 }}
            >
              {pageSizes.map((size) => (
                <MenuItem key={size} value={size}>
                  {size}
                </MenuItem>
              ))}
            </TextField>

            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <FilterList fontSize="small" color="action" />
              <TextField label="Pesquisar" variant="standard" sx={{ minWidth: 200 }} />
            </Box>
          </Box>

          <TableContainer sx={{ mt: 3, borderRadius: 2, border: '1px solid #e0e0e0' }}>
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  {columns.map((column) => (
                    <TableCell key={column} sx={{ fontWeight: 600 }}>
                      {column}
                    </TableCell>
                  ))}
                </TableRow>
              </TableHead>
              <TableBody>
                <TableRow>
                  <TableCell colSpan={columns.length}>
                    Nenhum registro encontrado
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </TableContainer>

          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'space-between',
              gap: 2,
              mt: 3,
            }}
          >
            <Typography variant="body2" color="text.secondary">
              Mostrando 0 até 0 de 0 registros
            </Typography>
            <Box sx={{ display: 'flex', gap: 1 }}>
              <Button variant="text" disabled>
                Anterior
              </Button>
              <Button variant="text" disabled>
                Próximo
              </Button>
            </Box>
          </Box>
        </CardContent>
      </Card>
    </Box>
  );
}
