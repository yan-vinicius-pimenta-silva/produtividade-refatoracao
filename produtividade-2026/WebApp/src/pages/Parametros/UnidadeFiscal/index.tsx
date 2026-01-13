import { useState } from 'react';
import {
  Box,
  Button,
  Card,
  CardContent,
  Checkbox,
  Divider,
  FormControlLabel,
  Grid,
  IconButton,
  MenuItem,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TableSortLabel,
  TextField,
  Typography,
} from '@mui/material';
import { Check, Close, Delete, Edit } from '@mui/icons-material';

const pageSizes = [10, 25, 50];
const columns = ['Ano', 'Descrição', 'Valor', 'Ativo', 'Opções'];
const ufespRows = [
  {
    ano: '2024',
    descricao: 'UFESP 2024',
    valor: '35,36',
    ativo: false,
  },
  {
    ano: '2025',
    descricao: 'UFESP 2025',
    valor: '37,02',
    ativo: false,
  },
  {
    ano: '2026',
    descricao: 'UFESP 2026',
    valor: '38,42',
    ativo: true,
  },
];

export default function ParametrosUnidadeFiscal() {
  const [ano, setAno] = useState('2026');
  const [descricao, setDescricao] = useState('UFESP YYYY');
  const [valor, setValor] = useState('');
  const [ativo, setAtivo] = useState(false);
  const [pageSize, setPageSize] = useState(10);

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
      <Card sx={{ borderRadius: 2, boxShadow: 3, mb: 4 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 2, textTransform: 'uppercase' }}>
            Cadastro de UFESP
          </Typography>
          <Divider sx={{ mb: 3 }} />

          <Box sx={{ maxWidth: 900, mx: 'auto' }}>
            <Grid container spacing={3} alignItems="center" justifyContent="center">
              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                <Typography
                  component="label"
                  htmlFor="ufesp-ano"
                  sx={{ fontWeight: 600, color: 'text.secondary' }}
                >
                  Ano:{' '}
                  <Box component="span" sx={{ color: 'error.main' }}>
                    *
                  </Box>
                </Typography>
              </Grid>
              <Grid item xs={12} md={7}>
                <TextField
                  id="ufesp-ano"
                  fullWidth
                  required
                  value={ano}
                  onChange={(event) => setAno(event.target.value)}
                  variant="standard"
                />
              </Grid>

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                <Typography
                  component="label"
                  htmlFor="ufesp-descricao"
                  sx={{ fontWeight: 600, color: 'text.secondary' }}
                >
                  Descrição:{' '}
                  <Box component="span" sx={{ color: 'error.main' }}>
                    *
                  </Box>
                </Typography>
              </Grid>
              <Grid item xs={12} md={7}>
                <TextField
                  id="ufesp-descricao"
                  fullWidth
                  required
                  value={descricao}
                  onChange={(event) => setDescricao(event.target.value)}
                  variant="standard"
                />
              </Grid>

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                <Typography
                  component="label"
                  htmlFor="ufesp-valor"
                  sx={{ fontWeight: 600, color: 'text.secondary' }}
                >
                  Valor:{' '}
                  <Box component="span" sx={{ color: 'error.main' }}>
                    *
                  </Box>
                </Typography>
              </Grid>
              <Grid item xs={12} md={7}>
                <TextField
                  id="ufesp-valor"
                  fullWidth
                  required
                  value={valor}
                  onChange={(event) => setValor(event.target.value)}
                  variant="standard"
                  placeholder="Valor da ufesp"
                />
              </Grid>

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                <Typography sx={{ fontWeight: 600, color: 'text.secondary' }}>Ativo:</Typography>
              </Grid>
              <Grid item xs={12} md={7}>
                <FormControlLabel
                  control={
                    <Checkbox checked={ativo} onChange={(event) => setAtivo(event.target.checked)} />
                  }
                  label=""
                />
              </Grid>
            </Grid>
          </Box>

          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'flex-start',
              gap: 2,
              mt: 4,
            }}
          >
            <Button variant="contained" color="warning">
              Cadastrar
            </Button>
            <Button variant="outlined">Voltar</Button>
          </Box>
        </CardContent>
      </Card>

      <Card sx={{ borderRadius: 2, boxShadow: 3 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 2, textTransform: 'uppercase' }}>
            UFESP cadastradas
          </Typography>
          <Divider sx={{ mb: 3 }} />

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
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <TextField
                select
                variant="standard"
                value={pageSize}
                onChange={(event) => setPageSize(Number(event.target.value))}
                sx={{ minWidth: 80 }}
              >
                {pageSizes.map((size) => (
                  <MenuItem key={size} value={size}>
                    {size}
                  </MenuItem>
                ))}
              </TextField>
              <Typography variant="body2" color="text.secondary">
                resultados por página
              </Typography>
            </Box>

            <TextField label="Pesquisar" variant="standard" sx={{ minWidth: 200 }} />
          </Box>

          <TableContainer sx={{ mt: 3, borderRadius: 2, border: '1px solid #e0e0e0' }}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  {columns.map((column) => (
                    <TableCell key={column} sx={{ fontWeight: 600 }}>
                      <TableSortLabel hideSortIcon={false} active={false}>
                        {column}
                      </TableSortLabel>
                    </TableCell>
                  ))}
                </TableRow>
              </TableHead>
              <TableBody>
                {ufespRows.map((row) => (
                  <TableRow key={row.ano}>
                    <TableCell>{row.ano}</TableCell>
                    <TableCell>{row.descricao}</TableCell>
                    <TableCell>{row.valor}</TableCell>
                    <TableCell>
                      <Box
                        sx={{
                          width: 32,
                          height: 32,
                          borderRadius: 1,
                          display: 'inline-flex',
                          alignItems: 'center',
                          justifyContent: 'center',
                          bgcolor: row.ativo ? 'success.main' : 'grey.400',
                          color: 'common.white',
                        }}
                      >
                        {row.ativo ? <Check fontSize="small" /> : <Close fontSize="small" />}
                      </Box>
                    </TableCell>
                    <TableCell>
                      <Box sx={{ display: 'flex', gap: 1 }}>
                        <IconButton
                          size="small"
                          sx={{
                            bgcolor: 'primary.main',
                            color: 'common.white',
                            '&:hover': { bgcolor: 'primary.dark' },
                          }}
                        >
                          <Edit fontSize="small" />
                        </IconButton>
                        <IconButton
                          size="small"
                          sx={{
                            bgcolor: 'error.main',
                            color: 'common.white',
                            '&:hover': { bgcolor: 'error.dark' },
                          }}
                        >
                          <Delete fontSize="small" />
                        </IconButton>
                      </Box>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>

          <Typography variant="body2" color="text.secondary" sx={{ mt: 2 }}>
            Mostrando de 1 até 3 de 3 registros
          </Typography>
        </CardContent>
      </Card>
    </Box>
  );
}
