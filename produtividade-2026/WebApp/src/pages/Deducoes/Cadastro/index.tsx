import { useState } from 'react';
import {
  Box,
  Button,
  Card,
  CardContent,
  Divider,
  Grid,
  MenuItem,
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

export default function DeducaoCadastro() {
  const [deducao, setDeducao] = useState('');
  const [fiscal, setFiscal] = useState('');
  const [vigencia, setVigencia] = useState('');
  const [justificativa, setJustificativa] = useState('');

  const renderSelectValue = (value: string) => value || 'Escolha...';

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
            <Button variant="contained" color="warning">
              Cadastrar
            </Button>
            <Button variant="outlined">Voltar</Button>
          </Box>
        </CardContent>
      </Card>
    </Box>
  );
}
