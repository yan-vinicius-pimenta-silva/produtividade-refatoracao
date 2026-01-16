import { ChangeEvent, useMemo, useState } from 'react';
import {
  Alert,
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
  Snackbar,
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
import { Delete, Edit } from '@mui/icons-material';

const contabilizacaoOptions = ['Mensal', 'Por ocorrência', 'Por unidade'];
const pageSizes = [10, 25, 50];
const columns = ['ID', 'Tipo', 'Tipo de Cálculo', 'Pontos', 'Ativo', 'Opções'];
const MAX_PDF_SIZE_MB = 2;
const MAX_PDF_SIZE_BYTES = MAX_PDF_SIZE_MB * 1024 * 1024;
const initialActivities = [
  {
    id: 101,
    name: 'Vistoria de Regularização',
    calculationType: 'Por ocorrência',
    points: 2.5,
    active: true,
  },
  {
    id: 102,
    name: 'Lavratura de Auto',
    calculationType: 'Por ocorrência',
    points: 8,
    active: true,
  },
  {
    id: 103,
    name: 'Atendimento em Plantão',
    calculationType: 'Mensal',
    points: 1.2,
    active: false,
  },
];

export default function ParametrosAtividades() {
  const [nome, setNome] = useState('');
  const [descricao, setDescricao] = useState('');
  const [pontos, setPontos] = useState('1.0');
  const [tipoContabilizacao, setTipoContabilizacao] = useState('');
  const [ativo, setAtivo] = useState(false);
  const [aceitaMultiplicador, setAceitaMultiplicador] = useState(false);
  const [pageSize, setPageSize] = useState(10);
  const [searchTerm, setSearchTerm] = useState('');
  const [activities, setActivities] = useState(initialActivities);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pdfError, setPdfError] = useState('');
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'info' as 'success' | 'info' | 'warning' | 'error',
  });

  const renderSelectValue = (value: string) => value || 'Escolha...';

  const filteredActivities = useMemo(() => {
    const normalizedSearch = searchTerm.trim().toLowerCase();
    const filtered = normalizedSearch
      ? activities.filter((activity) =>
          [activity.name, activity.calculationType]
            .join(' ')
            .toLowerCase()
            .includes(normalizedSearch)
        )
      : activities;

    return filtered.slice(0, pageSize);
  }, [activities, pageSize, searchTerm]);

  const resetForm = () => {
    setNome('');
    setDescricao('');
    setPontos('1.0');
    setTipoContabilizacao('');
    setAtivo(false);
    setAceitaMultiplicador(false);
    setEditingId(null);
    setPdfFile(null);
    setPdfError('');
  };

  const handlePdfChange = (event: ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (!file) {
      setPdfFile(null);
      setPdfError('');
      return;
    }

    if (file.type !== 'application/pdf') {
      setPdfFile(null);
      setPdfError('Envie um arquivo em PDF.');
      return;
    }

    if (file.size > MAX_PDF_SIZE_BYTES) {
      setPdfFile(null);
      setPdfError(`O PDF deve ter no máximo ${MAX_PDF_SIZE_MB} MB.`);
      return;
    }

    setPdfFile(file);
    setPdfError('');
  };

  const handleSubmit = () => {
    if (!nome || !descricao || !tipoContabilizacao || !pontos) {
      setSnackbar({
        open: true,
        message: 'Preencha todos os campos obrigatórios antes de cadastrar.',
        severity: 'warning',
      });
      return;
    }

    if (pdfError) {
      setSnackbar({
        open: true,
        message: 'Corrija o PDF anexado antes de cadastrar.',
        severity: 'warning',
      });
      return;
    }

    const pointsValue = Number(pontos);
    if (Number.isNaN(pointsValue) || pointsValue <= 0) {
      setSnackbar({
        open: true,
        message: 'Informe um valor de pontos válido para a atividade.',
        severity: 'warning',
      });
      return;
    }

    if (editingId) {
      setActivities((current) =>
        current.map((activity) =>
          activity.id === editingId
            ? {
                ...activity,
                name: nome,
                calculationType: tipoContabilizacao,
                points: pointsValue,
                active: ativo,
              }
            : activity
        )
      );
      setSnackbar({
        open: true,
        message: 'Atividade atualizada com sucesso.',
        severity: 'success',
      });
    } else {
      const newActivity = {
        id: Math.max(0, ...activities.map((activity) => activity.id)) + 1,
        name: nome,
        calculationType: tipoContabilizacao,
        points: pointsValue,
        active: ativo,
      };
      setActivities((current) => [newActivity, ...current]);
      setSnackbar({
        open: true,
        message: 'Atividade cadastrada com sucesso.',
        severity: 'success',
      });
    }

    resetForm();
  };

  const handleImport = () => {
    const imported = [
      {
        id: Math.max(0, ...activities.map((activity) => activity.id)) + 1,
        name: 'Monitoramento de Área Crítica',
        calculationType: 'Mensal',
        points: 3.2,
        active: true,
      },
      {
        id: Math.max(0, ...activities.map((activity) => activity.id)) + 2,
        name: 'Operação Especial Noturna',
        calculationType: 'Por unidade',
        points: 5.5,
        active: true,
      },
    ];

    setActivities((current) => [...imported, ...current]);
    setSnackbar({
      open: true,
      message: 'Atividades fictícias importadas com sucesso.',
      severity: 'success',
    });
  };

  const handleEdit = (id: number) => {
    const activity = activities.find((item) => item.id === id);
    if (!activity) {
      return;
    }
    setNome(activity.name);
    setDescricao(activity.name);
    setPontos(String(activity.points));
    setTipoContabilizacao(activity.calculationType);
    setAtivo(activity.active);
    setEditingId(id);
    setSnackbar({
      open: true,
      message: 'Atividade carregada para edição.',
      severity: 'info',
    });
  };

  const handleDelete = (id: number) => {
    setActivities((current) => current.filter((activity) => activity.id !== id));
    setSnackbar({
      open: true,
      message: 'Atividade removida com sucesso.',
      severity: 'success',
    });
  };

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
      <Card sx={{ borderRadius: 2, boxShadow: 3, mb: 4 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 2, textTransform: 'uppercase' }}>
            Cadastro de atividades
          </Typography>
          <Divider sx={{ mb: 3 }} />

          <Box sx={{ maxWidth: 900, mx: 'auto' }}>
            <Grid container spacing={3} alignItems="center" justifyContent="center">
              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
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
              <Grid item xs={12} md={7}>
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

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
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
              <Grid item xs={12} md={7}>
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

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
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
              <Grid item xs={12} md={7}>
              <TextField
                id="atividade-pontos"
                fullWidth
                required
                type="number"
                inputProps={{ step: '0.1', min: 0 }}
                value={pontos}
                onChange={(event) => setPontos(event.target.value)}
                variant="standard"
                placeholder="Somente Número"
                sx={{ maxWidth: 140 }}
              />
              </Grid>

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
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
              <Grid item xs={12} md={7}>
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

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                <Typography sx={{ fontWeight: 600, color: 'text.secondary' }}>Ativo:</Typography>
              </Grid>
              <Grid item xs={12} md={7}>
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

            <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                <Typography sx={{ fontWeight: 600, color: 'text.secondary' }}>
                  Aceita multiplicador:
                </Typography>
              </Grid>
              <Grid item xs={12} md={7}>
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

              <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                <Typography sx={{ fontWeight: 600, color: 'text.secondary' }}>
                  Documento PDF:
                </Typography>
              </Grid>
              <Grid item xs={12} md={7}>
                <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center', gap: 2 }}>
                  <Button variant="outlined" component="label">
                    Importar PDF
                    <input
                      hidden
                      type="file"
                      accept="application/pdf"
                      onChange={handlePdfChange}
                    />
                  </Button>
                  <Typography variant="body2" color="text.secondary">
                    {pdfFile
                      ? pdfFile.name
                      : `Nenhum arquivo selecionado (máx. ${MAX_PDF_SIZE_MB} MB)`}
                  </Typography>
                </Box>
                {pdfError && (
                  <Typography variant="caption" color="error.main" sx={{ mt: 1, display: 'block' }}>
                    {pdfError}
                  </Typography>
                )}
              </Grid>
            </Grid>
          </Box>

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
              <Button variant="contained" color="warning" onClick={handleSubmit}>
                {editingId ? 'Salvar' : 'Cadastrar'}
              </Button>
              <Button variant="outlined" onClick={resetForm}>
                Voltar
              </Button>
            </Box>
            <Button variant="contained" color="info" onClick={handleImport}>
              Importar atividades
            </Button>
          </Box>
        </CardContent>
      </Card>

      <Card sx={{ borderRadius: 2, boxShadow: 3 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 2, textTransform: 'uppercase' }}>
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

            <TextField
              label="Pesquisar"
              variant="standard"
              sx={{ minWidth: 200 }}
              value={searchTerm}
              onChange={(event) => setSearchTerm(event.target.value)}
            />
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
                {filteredActivities.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={columns.length}>
                      Nenhum registro encontrado
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredActivities.map((activity) => (
                    <TableRow key={activity.id}>
                      <TableCell>{activity.id}</TableCell>
                      <TableCell>{activity.name}</TableCell>
                      <TableCell>{activity.calculationType}</TableCell>
                      <TableCell>{activity.points}</TableCell>
                      <TableCell>{activity.active ? 'Sim' : 'Não'}</TableCell>
                      <TableCell>
                        <Box sx={{ display: 'flex', gap: 1 }}>
                          <IconButton
                            size="small"
                            onClick={() => handleEdit(activity.id)}
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
                            onClick={() => handleDelete(activity.id)}
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
                  ))
                )}
              </TableBody>
            </Table>
          </TableContainer>
        </CardContent>
      </Card>

      <Snackbar
        open={snackbar.open}
        autoHideDuration={4000}
        onClose={() => setSnackbar((current) => ({ ...current, open: false }))}
        anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
      >
        <Alert
          onClose={() => setSnackbar((current) => ({ ...current, open: false }))}
          severity={snackbar.severity}
          variant="filled"
          sx={{ width: '100%' }}
        >
          {snackbar.message}
        </Alert>
      </Snackbar>
    </Box>
  );
}
