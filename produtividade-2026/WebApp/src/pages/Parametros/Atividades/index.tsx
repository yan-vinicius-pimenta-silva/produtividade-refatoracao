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

const contabilizacaoOptions = ['Mensal', 'Por ocorrência', 'Por unidade'];
const pageSizes = [10, 25, 50];
const columns = ['ID', 'Tipo', 'Tipo de Cálculo', 'Pontos', 'Ativo', 'Opções'];

export default function ParametrosAtividades() {
  const [nome, setNome] = useState('');
  const [descricao, setDescricao] = useState('');
  const [pontos, setPontos] = useState('1.0');
  const [tipoContabilizacao, setTipoContabilizacao] = useState('');
  const [ativo, setAtivo] = useState(false);
  const [aceitaMultiplicador, setAceitaMultiplicador] = useState(false);
  const [pageSize, setPageSize] = useState(10);

  const renderSelectValue = (value: string) => value || 'Escolha...';

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
      <Card sx={{ borderRadius: 3, boxShadow: 3, mb: 4 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 1, textTransform: 'uppercase' }}>
            Cadastro de atividades
          </Typography>
          <Divider sx={{ mb: 3 }} />

          <Grid container spacing={3}>
            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="atividade-nome"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Nome:{' '}
                <Box component="span" sx={{ color: 'error.main' }}>
                  *
                </Box>
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="atividade-nome"
                fullWidth
                required
                value={nome}
                onChange={(event) => setNome(event.target.value)}
                variant="standard"
                placeholder="Nome da atividade"
              />
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="atividade-descricao"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Descrição:{' '}
                <Box component="span" sx={{ color: 'error.main' }}>
                  *
                </Box>
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="atividade-descricao"
                fullWidth
                required
                value={descricao}
                onChange={(event) => setDescricao(event.target.value)}
                variant="standard"
                placeholder="Descrição"
              />
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="atividade-pontos"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Pontos:{' '}
                <Box component="span" sx={{ color: 'error.main' }}>
                  *
                </Box>
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="atividade-pontos"
                fullWidth
                required
                type="number"
                inputProps={{ step: '0.1', min: 0 }}
                value={pontos}
                onChange={(event) => setPontos(event.target.value)}
                variant="standard"
                sx={{ maxWidth: 140 }}
              />
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="atividade-contabilizacao"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Tipo de Contabilização:{' '}
                <Box component="span" sx={{ color: 'error.main' }}>
                  *
                </Box>
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="atividade-contabilizacao"
                select
                fullWidth
                required
                value={tipoContabilizacao}
                onChange={(event) => setTipoContabilizacao(event.target.value)}
                variant="standard"
                SelectProps={{
                  displayEmpty: true,
                  renderValue: (selected) => renderSelectValue(selected as string),
                }}
                sx={{ maxWidth: 280 }}
              >
                <MenuItem value="">Escolha...</MenuItem>
                {contabilizacaoOptions.map((option) => (
                  <MenuItem key={option} value={option}>
                    {option}
                  </MenuItem>
                ))}
              </TextField>
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography sx={{ fontWeight: 600, color: 'text.secondary' }}>Ativo:</Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <FormControlLabel
                control={
                  <Checkbox
                    checked={ativo}
                    onChange={(event) => setAtivo(event.target.checked)}
                  />
                }
                label=""
              />
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography sx={{ fontWeight: 600, color: 'text.secondary' }}>
                Aceita multiplicador:
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <FormControlLabel
                control={
                  <Checkbox
                    checked={aceitaMultiplicador}
                    onChange={(event) => setAceitaMultiplicador(event.target.checked)}
                  />
                }
                label=""
              />
            </Grid>
          </Grid>

          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'space-between',
              gap: 2,
              mt: 4,
            }}
          >
            <Box sx={{ display: 'flex', gap: 2 }}>
              <Button variant="contained" color="warning">
                Cadastrar
              </Button>
              <Button variant="outlined">Voltar</Button>
            </Box>
            <Button variant="contained">Importar atividades</Button>
          </Box>
        </CardContent>
      </Card>

      <Card sx={{ borderRadius: 3, boxShadow: 3 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 1, textTransform: 'uppercase' }}>
            Atividades cadastradas
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
        </CardContent>
      </Card>
    </Box>
  );
}
