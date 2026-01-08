import { useState } from 'react';
import {
  Alert,
  Box,
  Button,
  Divider,
  Grid,
  Paper,
  Stack,
  TextField,
  Typography,
} from '@mui/material';
import {
  createFiscalActivity,
  fetchProdutividadePoints,
  produtividadeLogin,
} from '../services/produtividadeServices';
import type { ProdutividadePointsSummary } from '../interfaces';

const defaultPeriod = new Date().toISOString().slice(0, 7);

export default function Produtividade() {
  const [token, setToken] = useState('');
  const [loginOverride, setLoginOverride] = useState('');
  const [auth, setAuth] = useState<{ token: string; userId: number } | null>(
    null
  );
  const [period, setPeriod] = useState(defaultPeriod);
  const [points, setPoints] = useState<ProdutividadePointsSummary | null>(null);
  const [message, setMessage] = useState<string | null>(null);

  const [activityForm, setActivityForm] = useState({
    activityId: '',
    fiscalId: '',
    companyId: '',
    completedAt: new Date().toISOString().slice(0, 10),
    document: '',
    protocol: '',
    cpfCnpj: '',
    rc: '',
    value: '',
    quantity: '',
    notes: '',
  });

  async function handleLogin() {
    try {
      const response = await produtividadeLogin({
        token: token || undefined,
        login: loginOverride || undefined,
      });
      setAuth({ token: response.token, userId: response.user.id });
      setMessage(`Bem-vindo, ${response.user.name}.`);
    } catch (error) {
      setMessage((error as Error).message);
    }
  }

  async function handleFetchPoints() {
    if (!auth) {
      setMessage('Realize o login antes de consultar a pontuação.');
      return;
    }
    try {
      const summary = await fetchProdutividadePoints(
        auth.userId,
        period,
        auth.token
      );
      setPoints(summary);
      setMessage(null);
    } catch (error) {
      setMessage((error as Error).message);
    }
  }

  async function handleActivitySubmit() {
    if (!auth) {
      setMessage('Realize o login antes de lançar atividades.');
      return;
    }

    const payload = {
      activityId: Number(activityForm.activityId),
      fiscalId: Number(activityForm.fiscalId),
      companyId: Number(activityForm.companyId),
      completedAt: activityForm.completedAt,
      document: activityForm.document || null,
      protocol: activityForm.protocol || null,
      cpfCnpj: activityForm.cpfCnpj || null,
      rc: activityForm.rc || null,
      value: activityForm.value ? Number(activityForm.value) : null,
      quantity: activityForm.quantity ? Number(activityForm.quantity) : null,
      notes: activityForm.notes || null,
      attachments: [],
    };

    try {
      await createFiscalActivity(payload, auth.token);
      setMessage('Atividade lançada com sucesso.');
    } catch (error) {
      setMessage((error as Error).message);
    }
  }

  return (
    <Box sx={{ p: { xs: 2, md: 4 }, maxWidth: 1100, margin: '0 auto' }}>
      <Stack spacing={3}>
        <Typography variant="h4">Produtividade - Refatoração</Typography>

        {message && <Alert severity="info">{message}</Alert>}

        <Paper sx={{ p: 3 }}>
          <Stack spacing={2}>
            <Typography variant="h6">Login</Typography>
            <TextField
              label="Token JWT (opcional)"
              value={token}
              onChange={(event) => setToken(event.target.value)}
              size="small"
              fullWidth
            />
            <TextField
              label="Login de desenvolvimento"
              value={loginOverride}
              onChange={(event) => setLoginOverride(event.target.value)}
              size="small"
              fullWidth
            />
            <Box>
              <Button variant="contained" onClick={handleLogin}>
                Entrar
              </Button>
            </Box>
          </Stack>
        </Paper>

        <Paper sx={{ p: 3 }}>
          <Stack spacing={2}>
            <Typography variant="h6">Consulta de pontuação</Typography>
            <Stack direction={{ xs: 'column', sm: 'row' }} spacing={2}>
              <TextField
                label="Período"
                type="month"
                value={period}
                onChange={(event) => setPeriod(event.target.value)}
                size="small"
                sx={{ maxWidth: 200 }}
                InputLabelProps={{ shrink: true }}
              />
              <Button variant="outlined" onClick={handleFetchPoints}>
                Consultar
              </Button>
            </Stack>
            {points && (
              <Grid container spacing={2}>
                <Grid item xs={12} sm={6} md={4}>
                  <Paper variant="outlined" sx={{ p: 2 }}>
                    <Typography variant="subtitle2">Pontuação</Typography>
                    <Typography variant="h6">{points.pointsPontuacao}</Typography>
                  </Paper>
                </Grid>
                <Grid item xs={12} sm={6} md={4}>
                  <Paper variant="outlined" sx={{ p: 2 }}>
                    <Typography variant="subtitle2">Dedução</Typography>
                    <Typography variant="h6">{points.pointsDeducao}</Typography>
                  </Paper>
                </Grid>
                <Grid item xs={12} sm={6} md={4}>
                  <Paper variant="outlined" sx={{ p: 2 }}>
                    <Typography variant="subtitle2">UFESP</Typography>
                    <Typography variant="h6">{points.pointsUfesp}</Typography>
                  </Paper>
                </Grid>
                <Grid item xs={12} sm={6} md={4}>
                  <Paper variant="outlined" sx={{ p: 2 }}>
                    <Typography variant="subtitle2">Total</Typography>
                    <Typography variant="h6">{points.pointsTotal}</Typography>
                  </Paper>
                </Grid>
                <Grid item xs={12} sm={6} md={4}>
                  <Paper variant="outlined" sx={{ p: 2 }}>
                    <Typography variant="subtitle2">Arrecadado</Typography>
                    <Typography variant="h6">{points.totalCollected}</Typography>
                  </Paper>
                </Grid>
                <Grid item xs={12} sm={6} md={4}>
                  <Paper variant="outlined" sx={{ p: 2 }}>
                    <Typography variant="subtitle2">Saldo</Typography>
                    <Typography variant="h6">{points.remainingBalance}</Typography>
                  </Paper>
                </Grid>
              </Grid>
            )}
          </Stack>
        </Paper>

        <Paper sx={{ p: 3 }}>
          <Stack spacing={2}>
            <Typography variant="h6">Lançar atividade</Typography>
            <Divider />
            <Grid container spacing={2}>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Atividade (ID)"
                  value={activityForm.activityId}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      activityId: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Fiscal (ID)"
                  value={activityForm.fiscalId}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      fiscalId: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Empresa (ID)"
                  value={activityForm.companyId}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      companyId: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Data de conclusão"
                  type="date"
                  value={activityForm.completedAt}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      completedAt: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                  InputLabelProps={{ shrink: true }}
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Documento"
                  value={activityForm.document}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      document: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Protocolo"
                  value={activityForm.protocol}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      protocol: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="CPF/CNPJ"
                  value={activityForm.cpfCnpj}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      cpfCnpj: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="RC"
                  value={activityForm.rc}
                  onChange={(event) =>
                    setActivityForm((prev) => ({ ...prev, rc: event.target.value }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Valor"
                  value={activityForm.value}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      value: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <TextField
                  label="Quantidade"
                  value={activityForm.quantity}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      quantity: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
              <Grid item xs={12} sm={8}>
                <TextField
                  label="Observações"
                  value={activityForm.notes}
                  onChange={(event) =>
                    setActivityForm((prev) => ({
                      ...prev,
                      notes: event.target.value,
                    }))
                  }
                  size="small"
                  fullWidth
                />
              </Grid>
            </Grid>
            <Box>
              <Button variant="contained" onClick={handleActivitySubmit}>
                Lançar atividade
              </Button>
            </Box>
          </Stack>
        </Paper>
      </Stack>
    </Box>
  );
}
