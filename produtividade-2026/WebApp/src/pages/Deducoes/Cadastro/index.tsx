import { useState } from 'react';
import {
  Alert,
  Box,
  Button,
  Card,
  CardContent,
  Divider,
  Grid,
  MenuItem,
  Snackbar,
  TextField,
  Typography,
} from '@mui/material';

const deductions = [
  'Dedução por treinamento',
  'Dedução por afastamento',
  'Dedução por licença médica',
];

const fiscals = [
  'Pedro de Melo',
  'Sisuley Zaniboni Gouveia',
  'Fernando Pagioro',
];

const STORAGE_KEY = 'deducoesMockRows';

type DeducaoRow = {
  id: number;
  tipo: string;
  data: string;
  protocolo: string;
  documento: string;
  rc: string;
  cpfCnpj: string;
  pontos: number;
  quantidade: number;
  valor: number;
  fiscal: string;
  documentoAnexo: string;
  validacao: string;
  observacao: string;
  usuarioDeducao: string;
  justificativa: string;
};

const parseStoredRows = () => {
  const raw = localStorage.getItem(STORAGE_KEY);
  if (!raw) {
    return [];
  }

  try {
    const parsed = JSON.parse(raw);
    return Array.isArray(parsed) ? (parsed as DeducaoRow[]) : [];
  } catch {
    return [];
  }
};

export default function DeducaoCadastro() {
  const [deducao, setDeducao] = useState('');
  const [fiscal, setFiscal] = useState('');
  const [vigencia, setVigencia] = useState('');
  const [justificativa, setJustificativa] = useState('');
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'info' as 'success' | 'info' | 'warning' | 'error',
  });

  const renderSelectValue = (value: string) => value || 'Escolha...';

  const handleSubmit = () => {
    if (!deducao || !fiscal || !vigencia) {
      setSnackbar({
        open: true,
        message: 'Preencha todos os campos obrigatórios antes de cadastrar.',
        severity: 'warning',
      });
      return;
    }

    const newRow: DeducaoRow = {
      id: Date.now(),
      tipo: deducao,
      data: new Date(vigencia).toLocaleDateString('pt-BR'),
      protocolo: 'Novo cadastro',
      documento: '--',
      rc: '--',
      cpfCnpj: '--',
      pontos: 0,
      quantidade: 1,
      valor: 0,
      fiscal,
      documentoAnexo: 'sem-anexo.pdf',
      validacao: 'Pendente',
      observacao: 'Cadastro via formulário.',
      usuarioDeducao: 'Usuário atual',
      justificativa: justificativa || 'Sem justificativa informada.',
    };

    const storedRows = parseStoredRows();
    localStorage.setItem(STORAGE_KEY, JSON.stringify([newRow, ...storedRows]));

    setSnackbar({
      open: true,
      message: `Dedução "${deducao}" cadastrada para ${fiscal}.`,
      severity: 'success',
    });
    setDeducao('');
    setFiscal('');
    setVigencia('');
    setJustificativa('');
  };

  const handleReset = () => {
    setDeducao('');
    setFiscal('');
    setVigencia('');
    setJustificativa('');
    setSnackbar({
      open: true,
      message: 'Formulário limpo com sucesso.',
      severity: 'info',
    });
  };

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
      <Card sx={{ borderRadius: 3, boxShadow: 3 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 1 }}>
            Cadastrar Dedução
          </Typography>
          <Divider sx={{ mb: 3 }} />

          <Grid container spacing={3}>
            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="deducao"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Dedução:{' '}
                <Box component="span" sx={{ color: 'error.main' }}>
                  *
                </Box>
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="deducao"
                select
                fullWidth
                required
                value={deducao}
                onChange={(event) => setDeducao(event.target.value)}
                variant="standard"
                SelectProps={{
                  displayEmpty: true,
                  renderValue: (selected) => renderSelectValue(selected as string),
                }}
              >
                <MenuItem value="">
                  Escolha...
                </MenuItem>
                {deductions.map((item) => (
                  <MenuItem key={item} value={item}>
                    {item}
                  </MenuItem>
                ))}
              </TextField>
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="fiscal"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Fiscal:{' '}
                <Box component="span" sx={{ color: 'error.main' }}>
                  *
                </Box>
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="fiscal"
                select
                fullWidth
                required
                value={fiscal}
                onChange={(event) => setFiscal(event.target.value)}
                variant="standard"
                SelectProps={{
                  displayEmpty: true,
                  renderValue: (selected) => renderSelectValue(selected as string),
                }}
              >
                <MenuItem value="">
                  Escolha...
                </MenuItem>
                {fiscals.map((item) => (
                  <MenuItem key={item} value={item}>
                    {item}
                  </MenuItem>
                ))}
              </TextField>
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="vigencia"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Data de Vigência:{' '}
                <Box component="span" sx={{ color: 'error.main' }}>
                  *
                </Box>
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="vigencia"
                fullWidth
                required
                type="date"
                value={vigencia}
                onChange={(event) => setVigencia(event.target.value)}
                InputLabelProps={{ shrink: true }}
                inputProps={{ placeholder: 'dd/mm/aaaa' }}
                variant="standard"
              />
            </Grid>

            <Grid item xs={12} md={3}>
              <Typography
                component="label"
                htmlFor="justificativa"
                sx={{ fontWeight: 600, color: 'text.secondary' }}
              >
                Justificativa:
              </Typography>
            </Grid>
            <Grid item xs={12} md={9}>
              <TextField
                id="justificativa"
                fullWidth
                value={justificativa}
                onChange={(event) => setJustificativa(event.target.value)}
                multiline
                minRows={2}
                variant="standard"
              />
            </Grid>
          </Grid>

          <Box sx={{ display: 'flex', gap: 2, mt: 4 }}>
            <Button variant="contained" color="warning" onClick={handleSubmit}>
              Cadastrar
            </Button>
            <Button variant="outlined" onClick={handleReset}>
              Voltar
            </Button>
          </Box>
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
