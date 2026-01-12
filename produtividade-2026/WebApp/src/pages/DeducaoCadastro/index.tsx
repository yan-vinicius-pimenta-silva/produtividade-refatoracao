import { useState } from 'react';
import {
  Box,
  Button,
  Card,
  CardContent,
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

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
      <Card sx={{ borderRadius: 3, boxShadow: 3 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 3 }}>
            Cadastrar Dedução
          </Typography>

          <Grid container spacing={3}>
            <Grid item xs={12} md={6}>
              <TextField
                select
                fullWidth
                required
                label="Dedução"
                value={deducao}
                onChange={(event) => setDeducao(event.target.value)}
                placeholder="Escolha..."
              >
                <MenuItem value="" disabled>
                  Escolha...
                </MenuItem>
                {deductions.map((item) => (
                  <MenuItem key={item} value={item}>
                    {item}
                  </MenuItem>
                ))}
              </TextField>
            </Grid>

            <Grid item xs={12} md={6}>
              <TextField
                select
                fullWidth
                required
                label="Fiscal"
                value={fiscal}
                onChange={(event) => setFiscal(event.target.value)}
                placeholder="Escolha..."
              >
                <MenuItem value="" disabled>
                  Escolha...
                </MenuItem>
                {fiscals.map((item) => (
                  <MenuItem key={item} value={item}>
                    {item}
                  </MenuItem>
                ))}
              </TextField>
            </Grid>

            <Grid item xs={12} md={6}>
              <TextField
                fullWidth
                required
                label="Data de Vigência"
                type="date"
                value={vigencia}
                onChange={(event) => setVigencia(event.target.value)}
                InputLabelProps={{ shrink: true }}
              />
            </Grid>

            <Grid item xs={12}>
              <TextField
                fullWidth
                label="Justificativa"
                value={justificativa}
                onChange={(event) => setJustificativa(event.target.value)}
                multiline
                minRows={3}
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
