import { useState } from 'react';
import {
  Box,
  Button,
  Checkbox,
  Chip,
  Divider,
  IconButton,
  Paper,
  Tab,
  Tabs,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TextField,
  Typography,
} from '@mui/material';
import {
  CheckCircleOutline,
  DeleteOutline,
  Description,
  FilterList,
  Refresh,
  TaskAlt,
} from '@mui/icons-material';

type ActivityRow = {
  id: number;
  type: string;
  date: string;
  protocol: string;
  document: string;
  rc: string;
  cpfCnpj: string;
  points: number;
  quantity: number;
  value: number;
  fiscal: string;
  attachment: string;
  notes: string;
};

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
});

const initialPendingActivities: ActivityRow[] = [
  {
    id: 2868,
    type: 'Auto de Infração - Imposição de Multa',
    date: '08/01/2026',
    protocol: '0955.560.0000115/2026',
    document: '05269966',
    rc: '11.6.06.30.043.000',
    cpfCnpj: '12.625.069/0001-90',
    points: 173.4,
    quantity: 115.6,
    value: 44424,
    fiscal: 'Pedro de Melo',
    attachment: 'AI-288.pdf',
    notes: 'Fiscalização noturna em área central.',
  },
  {
    id: 2871,
    type: 'Taxa de Licença para Publicidade',
    date: '09/01/2026',
    protocol: '16138/2025',
    document: '05270031',
    rc: '--',
    cpfCnpj: '16.670.085/1399-00',
    points: 6,
    quantity: 3,
    value: 1156.42,
    fiscal: 'Sisuley Zaniboni Gouveia',
    attachment: 'LP-044.pdf',
    notes: 'Publicidade em fachada principal.',
  },
  {
    id: 2873,
    type: 'Plantão Fiscal fora do horário (por hora)',
    date: '10/01/2026',
    protocol: '--',
    document: '--',
    rc: '--',
    cpfCnpj: '--',
    points: 7,
    quantity: 3.5,
    value: 0,
    fiscal: 'Fernando Pagioro',
    attachment: 'PL-179.pdf',
    notes: 'Apoio na feira do parque ecológico.',
  },
  {
    id: 2874,
    type: 'Auto de Infração - Imposição de Multa',
    date: '11/01/2026',
    protocol: '18182/2023',
    document: '05270090',
    rc: '11.6.12.67.031.000',
    cpfCnpj: '269.891.668-00',
    points: 8.5,
    quantity: 5.7,
    value: 2221.2,
    fiscal: 'Sisuley Zaniboni Gouveia',
    attachment: 'AI-315.pdf',
    notes: 'Não construiu a calçada conforme notificação.',
  },
];

const initialValidatedActivities: ActivityRow[] = [
  {
    id: 2810,
    type: 'Taxa de Licença para Publicidade',
    date: '02/01/2026',
    protocol: '14220/2025',
    document: '05269002',
    rc: '--',
    cpfCnpj: '93.209.765/0509-98',
    points: 2.4,
    quantity: 1.2,
    value: 496.75,
    fiscal: 'Sisuley Zaniboni Gouveia',
    attachment: 'LP-010.pdf',
    notes: 'Ponto de mídia em via arterial.',
  },
  {
    id: 2814,
    type: 'Auto de Infração - Imposição de Multa',
    date: '03/01/2026',
    protocol: '12055/2025',
    document: '05269088',
    rc: '12.5.19.23.014.000',
    cpfCnpj: '145.861.848-09',
    points: 40,
    quantity: 26.7,
    value: 10278,
    fiscal: 'Pedro de Melo',
    attachment: 'AI-241.pdf',
    notes: 'Ocorrência validada pela chefia.',
  },
];

export default function Produtividade() {
  const [homeTab, setHomeTab] = useState(0);
  const [pendingActivities, setPendingActivities] = useState<ActivityRow[]>(
    initialPendingActivities
  );
  const [validatedActivities, setValidatedActivities] = useState<ActivityRow[]>(
    initialValidatedActivities
  );

  const handleDelete = (id: number) => {
    if (homeTab === 0) {
      setPendingActivities((prev) => prev.filter((row) => row.id !== id));
      return;
    }

    setValidatedActivities((prev) => prev.filter((row) => row.id !== id));
  };

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', pb: 6 }}>
      <Box
        sx={{
          px: { xs: 2.5, md: 4 },
          py: 4,
          background: 'linear-gradient(120deg, #0f766e 0%, #059669 100%)',
          color: '#fff',
        }}
      >
        <Typography variant="overline" sx={{ letterSpacing: 1 }}>
          Fiscalização Urbana
        </Typography>
        <Typography variant="h4" sx={{ fontWeight: 700, mb: 1 }}>
          Produtividade & Validação Financeira
        </Typography>
        <Typography variant="body1" sx={{ maxWidth: 760 }}>
          Central operacional para validação de atividades, gestão de deduções e
          administração legal da produtividade. Use dados fictícios para testar
          fluxos antes de integrar com a base oficial.
        </Typography>
      </Box>

      <Box sx={{ px: { xs: 2.5, md: 4 }, mt: -4 }}>
        <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
          <Typography variant="h6" sx={{ fontWeight: 700 }}>
            Central de Apuração
          </Typography>
          <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
            Valide as atividades lançadas pelos fiscais antes de liberar o cálculo
            financeiro. Atividades validadas ficam bloqueadas para edição.
          </Typography>

          <Tabs
            value={homeTab}
            onChange={(_, value) => setHomeTab(value)}
            textColor="primary"
            indicatorColor="primary"
            sx={{ mt: 3, '& .MuiTab-root': { textTransform: 'none' } }}
          >
            <Tab label="Atividades a validar" />
            <Tab label="Validadas" />
          </Tabs>

          <Divider sx={{ my: 2 }} />

          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              gap: 1.5,
              justifyContent: 'space-between',
            }}
          >
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 1.5 }}>
              <Button variant="contained" color="warning">
                Relatório Descritivo
              </Button>
              <Button variant="contained" color="success">
                Relatório de Produtividade
              </Button>
              <Button variant="contained" color="primary">
                Relatório de Pontuação
              </Button>
            </Box>
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 1.5 }}>
              <Button variant="contained" color="success" startIcon={<TaskAlt />}>
                Confirmar
              </Button>
              <Button variant="contained" color="success" startIcon={<Refresh />}>
                Atualizar
              </Button>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Button variant="text" startIcon={<FilterList />}>
                Filtrar
              </Button>
              <TextField label="Pesquisar" variant="standard" sx={{ minWidth: 200 }} />
            </Box>
          </Box>

          <TableContainer sx={{ mt: 3, borderRadius: 2, border: '1px solid #e0e0e0' }}>
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>
                    {homeTab === 0 ? 'Validar' : 'Status'}
                  </TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Tipo</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Data</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Protocolo</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Documento</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>RC</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>CPF/CNPJ</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Pontos</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Quantidade</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Valor (R$)</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Fiscal</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Documento anexo</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Observações</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Ações</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {(homeTab === 0 ? pendingActivities : validatedActivities).map(
                  (row) => (
                    <TableRow key={row.id}>
                      <TableCell>
                        {homeTab === 0 ? (
                          <Checkbox
                            inputProps={{
                              'aria-label': `Selecionar atividade ${row.id}`,
                            }}
                          />
                        ) : (
                          <Chip
                            icon={<CheckCircleOutline />}
                            label="Validada"
                            color="success"
                            size="small"
                          />
                        )}
                      </TableCell>
                      <TableCell>{row.type}</TableCell>
                      <TableCell>{row.date}</TableCell>
                      <TableCell>{row.protocol}</TableCell>
                      <TableCell>{row.document}</TableCell>
                      <TableCell>{row.rc}</TableCell>
                      <TableCell>{row.cpfCnpj}</TableCell>
                      <TableCell>{row.points}</TableCell>
                      <TableCell>{row.quantity}</TableCell>
                      <TableCell>{currencyFormatter.format(row.value)}</TableCell>
                      <TableCell>{row.fiscal}</TableCell>
                      <TableCell>
                        <Chip
                          icon={<Description />}
                          label={row.attachment}
                          variant="outlined"
                          size="small"
                        />
                      </TableCell>
                      <TableCell>{row.notes}</TableCell>
                      <TableCell>
                        <IconButton
                          aria-label={`Excluir atividade ${row.id}`}
                          color="error"
                          onClick={() => handleDelete(row.id)}
                        >
                          <DeleteOutline fontSize="small" />
                        </IconButton>
                      </TableCell>
                    </TableRow>
                  )
                )}
              </TableBody>
            </Table>
          </TableContainer>
        </Paper>
      </Box>
    </Box>
  );
}
