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
import { Check, Close, Delete, Edit } from '@mui/icons-material';

const pageSizes = [10, 25, 50];
const columns = ['Ano', 'Descrição', 'Valor', 'Ativo', 'Opções'];
const MAX_PDF_SIZE_MB = 2;
const MAX_PDF_SIZE_BYTES = MAX_PDF_SIZE_MB * 1024 * 1024;
const initialUfespRows = [
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
  const [searchTerm, setSearchTerm] = useState('');
  const [editingAno, setEditingAno] = useState<string | null>(null);
  const [rows, setRows] = useState(initialUfespRows);
  const [pdfFile, setPdfFile] = useState<File | null>(null);
  const [pdfError, setPdfError] = useState('');
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'info' as 'success' | 'info' | 'warning' | 'error',
  });

  const filteredRows = useMemo(() => {
    const normalizedSearch = searchTerm.trim().toLowerCase();
    const filtered = normalizedSearch
      ? rows.filter((row) =>
          [row.ano, row.descricao, row.valor]
            .join(' ')
            .toLowerCase()
            .includes(normalizedSearch)
        )
      : rows;

    return filtered.slice(0, pageSize);
  }, [pageSize, rows, searchTerm]);

  const resetForm = () => {
    setAno('');
    setDescricao('UFESP YYYY');
    setValor('');
    setAtivo(false);
    setEditingAno(null);
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
    if (!ano || !descricao || !valor) {
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

    if (editingAno) {
      setRows((current) =>
        current.map((row) =>
          row.ano === editingAno ? { ano, descricao, valor, ativo } : row
        )
      );
      setSnackbar({
        open: true,
        message: 'UFESP atualizada com sucesso.',
        severity: 'success',
      });
    } else {
      setRows((current) => [{ ano, descricao, valor, ativo }, ...current]);
      setSnackbar({
        open: true,
        message: 'UFESP cadastrada com sucesso.',
        severity: 'success',
      });
    }

    resetForm();
  };

  const handleEdit = (targetAno: string) => {
    const row = rows.find((item) => item.ano === targetAno);
    if (!row) {
      return;
    }

    setAno(row.ano);
    setDescricao(row.descricao);
    setValor(row.valor);
    setAtivo(row.ativo);
    setEditingAno(targetAno);
    setSnackbar({
      open: true,
      message: `UFESP ${targetAno} carregada para edição.`,
      severity: 'info',
    });
  };

  const handleDelete = (targetAno: string) => {
    setRows((current) => current.filter((row) => row.ano !== targetAno));
    setSnackbar({
      open: true,
      message: `UFESP ${targetAno} removida com sucesso.`,
      severity: 'success',
    });
  };

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
              justifyContent: 'flex-start',
              gap: 2,
              mt: 4,
            }}
          >
            <Button variant="contained" color="warning" onClick={handleSubmit}>
              {editingAno ? 'Salvar' : 'Cadastrar'}
            </Button>
            <Button variant="outlined" onClick={resetForm}>
              Voltar
            </Button>
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
                {filteredRows.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={columns.length}>
                      Nenhum registro encontrado
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredRows.map((row) => (
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
                            onClick={() => handleEdit(row.ano)}
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
                            onClick={() => handleDelete(row.ano)}
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

          <Typography variant="body2" color="text.secondary" sx={{ mt: 2 }}>
            Mostrando {Math.min(filteredRows.length, pageSize)} de {rows.length} registros
          </Typography>
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
