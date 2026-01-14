import { useEffect, useMemo, useState } from 'react';
import {
  Alert,
  Box,
  Button,
  Checkbox,
  Chip,
  Divider,
  Paper,
  Snackbar,
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
  Description,
  FilterList,
  Refresh,
  TaskAlt,
} from '@mui/icons-material';
import { useLocation } from 'react-router-dom';

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

type SnackbarState = {
  open: boolean;
  message: string;
  severity: 'success' | 'info' | 'warning' | 'error';
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
  const location = useLocation();
  const isHistorico = location.pathname === '/produtividade/historico';
  const [homeTab, setHomeTab] = useState(() => (isHistorico ? 1 : 0));
  const [pendingActivities, setPendingActivities] =
    useState<ActivityRow[]>(initialPendingActivities);
  const [validatedActivities, setValidatedActivities] = useState<ActivityRow[]>(
    initialValidatedActivities
  );
  const [selectedIds, setSelectedIds] = useState<number[]>([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [onlyHighValue, setOnlyHighValue] = useState(false);
  const [snackbar, setSnackbar] = useState<SnackbarState>({
    open: false,
    message: '',
    severity: 'info',
  });

  useEffect(() => {
    setHomeTab(isHistorico ? 1 : 0);
  }, [isHistorico]);

  const activities = homeTab === 0 ? pendingActivities : validatedActivities;

  const filteredActivities = useMemo(() => {
    const normalizedSearch = searchTerm.trim().toLowerCase();
    return activities.filter((row) => {
      const matchesSearch = normalizedSearch
        ? [
            row.type,
            row.protocol,
            row.document,
            row.rc,
            row.cpfCnpj,
            row.fiscal,
            row.attachment,
            row.notes,
          ]
            .join(' ')
            .toLowerCase()
            .includes(normalizedSearch)
        : true;

      const matchesHighValue = onlyHighValue ? row.value >= 5000 : true;

      return matchesSearch && matchesHighValue;
    });
  }, [activities, onlyHighValue, searchTerm]);

  const handleToggleSelection = (id: number) => {
    setSelectedIds((current) =>
      current.includes(id) ? current.filter((item) => item !== id) : [...current, id]
    );
  };

  const handleConfirm = () => {
    if (selectedIds.length === 0) {
      setSnackbar({
        open: true,
        message: 'Selecione ao menos uma atividade para validar.',
        severity: 'warning',
      });
      return;
    }

    const approvedActivities = pendingActivities.filter((row) =>
      selectedIds.includes(row.id)
    );

    setPendingActivities((current) =>
      current.filter((row) => !selectedIds.includes(row.id))
    );
    setValidatedActivities((current) => [...approvedActivities, ...current]);
    setSelectedIds([]);
    setSnackbar({
      open: true,
      message: `${approvedActivities.length} atividade(s) confirmada(s) com sucesso.`,
      severity: 'success',
    });
  };

  const handleRefresh = () => {
    setPendingActivities(initialPendingActivities);
    setValidatedActivities(initialValidatedActivities);
    setSelectedIds([]);
    setSearchTerm('');
    setOnlyHighValue(false);
    setSnackbar({
      open: true,
      message: 'Dados fictícios recarregados.',
      severity: 'info',
    });
  };

  const handleGenerateReport = (label: string) => {
    const rows = activities.map((row) =>
      [
        row.id,
        row.type,
        row.date,
        row.protocol,
        row.document,
        row.rc,
        row.cpfCnpj,
        row.points,
        row.quantity,
        row.value,
        row.fiscal,
        row.attachment,
        row.notes,
      ].join(';')
    );
    const content = [
      `Relatório ${label}`,
      'ID;Tipo;Data;Protocolo;Documento;RC;CPF/CNPJ;Pontos;Quantidade;Valor;Fiscal;Anexo;Observações',
      ...rows,
    ].join('\n');

    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `relatorio-${label.replace(/\s+/g, '-').toLowerCase()}.csv`;
    link.click();
    URL.revokeObjectURL(url);

    setSnackbar({
      open: true,
      message: `Relatório ${label.toLowerCase()} gerado com sucesso.`,
      severity: 'success',
    });
  };

  const handleToggleFilter = () => {
    setOnlyHighValue((current) => !current);
    setSnackbar({
      open: true,
      message: onlyHighValue
        ? 'Filtro de alto valor removido.'
        : 'Filtro aplicado: valores acima de R$ 5.000.',
      severity: 'info',
    });
  };

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', pb: 6 }}>
      <Box
        sx={{
          px: { xs: 2.5, md: 4 },
          py: 4,
          bgcolor: 'background.paper',
          color: 'text.primary',
          borderRadius: { xs: 0, md: 3 },
          boxShadow: { xs: 'none', md: 1 },
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
              <Button
                variant="contained"
                color="warning"
                onClick={() => handleGenerateReport('Descritivo')}
              >
                Relatório Descritivo
              </Button>
              <Button
                variant="contained"
                color="success"
                onClick={() => handleGenerateReport('Produtividade')}
              >
                Relatório de Produtividade
              </Button>
              <Button
                variant="contained"
                color="primary"
                onClick={() => handleGenerateReport('Pontuacao')}
              >
                Relatório de Pontuação
              </Button>
            </Box>
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 1.5 }}>
              <Button
                variant="contained"
                color="success"
                startIcon={<TaskAlt />}
                onClick={handleConfirm}
              >
                Confirmar
              </Button>
              <Button
                variant="contained"
                color="success"
                startIcon={<Refresh />}
                onClick={handleRefresh}
              >
                Atualizar
              </Button>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Button
                variant="text"
                startIcon={<FilterList />}
                onClick={handleToggleFilter}
              >
                {onlyHighValue ? 'Remover filtro' : 'Filtrar alto valor'}
              </Button>
              <TextField
                label="Pesquisar"
                variant="standard"
                value={searchTerm}
                onChange={(event) => setSearchTerm(event.target.value)}
                sx={{ minWidth: 200 }}
              />
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
                </TableRow>
              </TableHead>
              <TableBody>
                {filteredActivities.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={13}>
                      Nenhuma atividade encontrada com os filtros aplicados.
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredActivities.map((row) => (
                    <TableRow key={row.id}>
                      <TableCell>
                        {homeTab === 0 ? (
                          <Checkbox
                            checked={selectedIds.includes(row.id)}
                            onChange={() => handleToggleSelection(row.id)}
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
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </TableContainer>
        </Paper>
      </Box>

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
