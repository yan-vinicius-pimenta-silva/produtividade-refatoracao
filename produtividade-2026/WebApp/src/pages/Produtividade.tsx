import { useEffect, useMemo, useState } from 'react';
import {
  Alert,
  Box,
  Button,
  Checkbox,
  Chip,
  Divider,
  Dialog,
  DialogActions,
  DialogContent,
  DialogTitle,
  IconButton,
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
  Close,
  TaskAlt,
} from '@mui/icons-material';
import { useLocation } from 'react-router-dom';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { ptBR } from 'date-fns/locale';
import { useAuth } from '../hooks';

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

const monthYearFormatter = new Intl.DateTimeFormat('pt-BR', {
  month: '2-digit',
  year: 'numeric',
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
  const { authUser } = useAuth();
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
  const [isReportModalOpen, setIsReportModalOpen] = useState(false);
  const [isProductivityReportOpen, setIsProductivityReportOpen] = useState(false);
  const [isScoreReportOpen, setIsScoreReportOpen] = useState(false);
  const [reportMonth, setReportMonth] = useState<Date | null>(new Date());
  const [reportUserInput, setReportUserInput] = useState('');

  const reportUserName =
    authUser?.fullName || authUser?.username || 'Usuário atual';

  useEffect(() => {
    setReportUserInput(reportUserName);
  }, [reportUserName]);

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

  const allActivities = [...pendingActivities, ...validatedActivities];

  const buildProductivityReportHtml = () => {
    const targetName = reportUserInput.trim();
    const reportRows = allActivities.filter(
      (row) => row.fiscal.toLowerCase() === targetName.toLowerCase()
    );

    const rowsHtml = reportRows.length
      ? reportRows
          .map(
            (row) => `
              <tr>
                <td>${row.type}</td>
                <td style="text-align:right;">${row.points}</td>
                <td style="text-align:right;">${row.quantity}</td>
                <td style="text-align:right;">${currencyFormatter.format(
                  row.value
                )}</td>
              </tr>
            `
          )
          .join('')
      : `
        <tr>
          <td colspan="4" style="text-align:center;">Nenhuma atividade encontrada para o fiscal informado.</td>
        </tr>
      `;

    const totals = reportRows.reduce(
      (acc, row) => ({
        points: acc.points + row.points,
        quantity: acc.quantity + row.quantity,
        value: acc.value + row.value,
      }),
      { points: 0, quantity: 0, value: 0 }
    );

    return `
      <html>
        <head>
          <meta charset="utf-8" />
          <title>Relatório Mensal Individual de Produtividade</title>
          <style>
            body { font-family: Arial, Helvetica, sans-serif; padding: 32px; color: #111; }
            .header { text-align: center; margin-bottom: 24px; }
            .header h1 { font-size: 18px; margin: 8px 0; text-transform: uppercase; }
            .header p { margin: 2px 0; font-size: 12px; }
            .divider { margin: 16px 0; border-top: 2px solid #111; }
            .meta { font-size: 14px; margin-bottom: 16px; }
            .meta strong { display: inline-block; min-width: 120px; }
            table { width: 100%; border-collapse: collapse; font-size: 12px; }
            th, td { border: 1px solid #111; padding: 8px; }
            th { background: #f3f3f3; text-transform: uppercase; }
            tfoot td { font-weight: bold; }
          </style>
        </head>
        <body>
          <div class="header">
            <p>PREFEITURA DE ARARAS</p>
            <p>SECRETARIA MUNICIPAL DE DESENVOLVIMENTO URBANO E OBRAS PÚBLICAS</p>
            <p>COORDENADORIA DE FISCALIZAÇÃO URBANA</p>
            <p>fiscalizacaourbana@araras.sp.gov.br | (19) 3547-3003</p>
            <div class="divider"></div>
            <h1>Relatório Mensal Individual de Produtividade</h1>
          </div>

          <div class="meta">
            <p><strong>Fiscal:</strong> ${targetName}</p>
            <p><strong>Vigência:</strong> ${formatMonthYear(reportMonth)}</p>
          </div>

          <table>
            <thead>
              <tr>
                <th>Atividade</th>
                <th>Pontos</th>
                <th>Quantidade</th>
                <th>Valor</th>
              </tr>
            </thead>
            <tbody>
              ${rowsHtml}
            </tbody>
            <tfoot>
              <tr>
                <td>Total</td>
                <td style="text-align:right;">${totals.points.toFixed(1)}</td>
                <td style="text-align:right;">${totals.quantity.toFixed(1)}</td>
                <td style="text-align:right;">${currencyFormatter.format(
                  totals.value
                )}</td>
              </tr>
            </tfoot>
          </table>
        </body>
      </html>
    `;
  };

  const buildScoreReportHtml = () => {
    const grouped = allActivities.reduce<Record<string, typeof allActivities>>(
      (acc, row) => {
        const key = row.fiscal;
        acc[key] = acc[key] ? [...acc[key], row] : [row];
        return acc;
      },
      {}
    );

    const scoreRows = Object.entries(grouped).map(([fiscal, rows]) => {
      const totals = rows.reduce(
        (acc, row) => ({
          points: acc.points + row.points,
          quantity: acc.quantity + row.quantity,
          value: acc.value + row.value,
        }),
        { points: 0, quantity: 0, value: 0 }
      );
      return { fiscal, ...totals };
    });

    const maxPoints = Math.max(...scoreRows.map((row) => row.points), 0);

    const rowsHtml = scoreRows.length
      ? scoreRows
          .map(
            (row) => `
              <tr>
                <td>${row.fiscal}</td>
                <td style="text-align:right;">${row.points.toFixed(1)}</td>
                <td style="text-align:right;">${row.quantity.toFixed(1)}</td>
                <td style="text-align:right;">${currencyFormatter.format(row.value)}</td>
                <td style="text-align:right;">${
                  maxPoints ? Math.round((row.points / maxPoints) * 100) : 0
                }%</td>
              </tr>
            `
          )
          .join('')
      : `
        <tr>
          <td colspan="5" style="text-align:center;">Nenhum usuário disponível.</td>
        </tr>
      `;

    return `
      <html>
        <head>
          <meta charset="utf-8" />
          <title>Relatório de Pontuação</title>
          <style>
            body { font-family: Arial, Helvetica, sans-serif; padding: 32px; color: #111; }
            .header { text-align: center; margin-bottom: 24px; }
            .header h1 { font-size: 18px; margin: 8px 0; text-transform: uppercase; }
            .header p { margin: 2px 0; font-size: 12px; }
            .divider { margin: 16px 0; border-top: 2px solid #111; }
            .meta { font-size: 14px; margin-bottom: 16px; }
            table { width: 100%; border-collapse: collapse; font-size: 12px; }
            th, td { border: 1px solid #111; padding: 8px; }
            th { background: #f3f3f3; text-transform: uppercase; }
          </style>
        </head>
        <body>
          <div class="header">
            <p>PREFEITURA DE ARARAS</p>
            <p>SECRETARIA MUNICIPAL DE DESENVOLVIMENTO URBANO E OBRAS PÚBLICAS</p>
            <p>COORDENADORIA DE FISCALIZAÇÃO URBANA</p>
            <p>fiscalizacaourbana@araras.sp.gov.br | (19) 3547-3003</p>
            <div class="divider"></div>
            <h1>Relatório de Pontuação</h1>
          </div>

          <div class="meta">
            <p><strong>Vigência:</strong> ${formatMonthYear(reportMonth)}</p>
          </div>

          <table>
            <thead>
              <tr>
                <th>Fiscal</th>
                <th>Pontos</th>
                <th>Quantidade</th>
                <th>Valor Total</th>
                <th>Percentual</th>
              </tr>
            </thead>
            <tbody>
              ${rowsHtml}
            </tbody>
          </table>
        </body>
      </html>
    `;
  };

  const handleOpenDescriptiveReport = () => {
    setIsReportModalOpen(true);
  };

  const formatMonthYear = (value: Date | null) => {
    if (!value) return '--/----';
    return monthYearFormatter.format(value);
  };

  const buildDescriptiveReportHtml = () => {
    const rows = filteredActivities.length
      ? filteredActivities
          .map(
            (row) => `
            <tr>
              <td>${row.type}</td>
              <td>${row.protocol}</td>
              <td>${row.date}</td>
              <td>${row.document}</td>
              <td style="text-align:right;">${currencyFormatter.format(
                row.value
              )}</td>
            </tr>
          `
          )
          .join('')
      : `
        <tr>
          <td colspan="5" style="text-align:center;">Nenhuma atividade encontrada para o período.</td>
        </tr>
      `;

    return `
      <html>
        <head>
          <meta charset="utf-8" />
          <title>Relatório Individual Descritivo</title>
          <style>
            body { font-family: Arial, Helvetica, sans-serif; padding: 32px; color: #111; }
            .header { text-align: center; margin-bottom: 24px; }
            .header h1 { font-size: 18px; margin: 8px 0; text-transform: uppercase; }
            .header p { margin: 2px 0; font-size: 12px; }
            .divider { margin: 16px 0; border-top: 2px solid #111; }
            .meta { font-size: 14px; margin-bottom: 16px; }
            .meta strong { display: inline-block; min-width: 100px; }
            table { width: 100%; border-collapse: collapse; font-size: 12px; }
            th, td { border: 1px solid #111; padding: 8px; }
            th { background: #f3f3f3; text-transform: uppercase; }
            .signature { margin-top: 40px; text-align: center; font-size: 13px; }
            .signature-line { margin: 24px auto 8px; width: 60%; border-top: 1px solid #111; }
          </style>
        </head>
        <body>
          <div class="header">
            <p>PREFEITURA DE ARARAS</p>
            <p>SECRETARIA MUNICIPAL DE DESENVOLVIMENTO URBANO E OBRAS PÚBLICAS</p>
            <p>COORDENADORIA DE FISCALIZAÇÃO URBANA</p>
            <p>fiscalizacaourbana@araras.sp.gov.br | (19) 3547-3003</p>
            <div class="divider"></div>
            <h1>Relatório Individual Descritivo de Atividades</h1>
          </div>

          <div class="meta">
            <p><strong>Fiscal:</strong> ${reportUserName}</p>
            <p><strong>Vigência:</strong> ${formatMonthYear(reportMonth)}</p>
          </div>

          <table>
            <thead>
              <tr>
                <th>Atividade</th>
                <th>Nº do Processo/Protocolo</th>
                <th>Data</th>
                <th>Nº Lançamento/Doc</th>
                <th>Valor</th>
              </tr>
            </thead>
            <tbody>
              ${rows}
            </tbody>
          </table>

          <div class="signature">
            <p>Araras, ____ de __________ de _______</p>
            <div class="signature-line"></div>
            <strong>${reportUserName}</strong>
          </div>
        </body>
      </html>
    `;
  };

  const handleGenerateDescriptivePdf = () => {
    const reportWindow = window.open('', '_blank', 'noopener,noreferrer');
    if (!reportWindow) {
      setSnackbar({
        open: true,
        message: 'Não foi possível abrir a janela do relatório. Libere pop-ups.',
        severity: 'warning',
      });
      return;
    }

    reportWindow.document.write(buildDescriptiveReportHtml());
    reportWindow.document.close();
    reportWindow.focus();
    reportWindow.print();
    reportWindow.addEventListener('afterprint', () => reportWindow.close());

    setIsReportModalOpen(false);
    setSnackbar({
      open: true,
      message: 'Relatório descritivo gerado com sucesso.',
      severity: 'success',
    });
  };

  const handleGenerateProductivityPdf = () => {
    if (!reportUserInput.trim()) {
      setSnackbar({
        open: true,
        message: 'Informe o nome do fiscal para gerar o relatório.',
        severity: 'warning',
      });
      return;
    }
    if (!reportMonth) {
      setSnackbar({
        open: true,
        message: 'Selecione a data de vigência do relatório.',
        severity: 'warning',
      });
      return;
    }

    const reportWindow = window.open('', '_blank', 'noopener,noreferrer');
    if (!reportWindow) {
      setSnackbar({
        open: true,
        message: 'Não foi possível abrir a janela do relatório. Libere pop-ups.',
        severity: 'warning',
      });
      return;
    }

    reportWindow.document.write(buildProductivityReportHtml());
    reportWindow.document.close();
    reportWindow.focus();
    reportWindow.print();
    reportWindow.addEventListener('afterprint', () => reportWindow.close());

    setIsProductivityReportOpen(false);
    setSnackbar({
      open: true,
      message: 'Relatório de produtividade gerado com sucesso.',
      severity: 'success',
    });
  };

  const handleGenerateScorePdf = () => {
    if (!reportMonth) {
      setSnackbar({
        open: true,
        message: 'Selecione a data de vigência do relatório.',
        severity: 'warning',
      });
      return;
    }

    const reportWindow = window.open('', '_blank', 'noopener,noreferrer');
    if (!reportWindow) {
      setSnackbar({
        open: true,
        message: 'Não foi possível abrir a janela do relatório. Libere pop-ups.',
        severity: 'warning',
      });
      return;
    }

    reportWindow.document.write(buildScoreReportHtml());
    reportWindow.document.close();
    reportWindow.focus();
    reportWindow.print();
    reportWindow.addEventListener('afterprint', () => reportWindow.close());

    setIsScoreReportOpen(false);
    setSnackbar({
      open: true,
      message: 'Relatório de pontuação gerado com sucesso.',
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
    <LocalizationProvider dateAdapter={AdapterDateFns} adapterLocale={ptBR}>
      <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', pb: 6 }}>
        <Box sx={{ px: { xs: 2.5, md: 4 }, mt: 3 }}>
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
                  onClick={handleOpenDescriptiveReport}
                >
                  Relatório Descritivo
                </Button>
                <Button
                  variant="contained"
                  color="success"
                  onClick={() => setIsProductivityReportOpen(true)}
                >
                  Relatório de Produtividade
                </Button>
                <Button
                  variant="contained"
                  color="primary"
                  onClick={() => setIsScoreReportOpen(true)}
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

            <TableContainer
              sx={{ mt: 3, borderRadius: 2, border: '1px solid #e0e0e0' }}
            >
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

        <Dialog
          open={isReportModalOpen}
          onClose={() => setIsReportModalOpen(false)}
          maxWidth="sm"
          fullWidth
        >
          <DialogTitle
            sx={{ display: 'flex', alignItems: 'center', pr: 6 }}
          >
            Relatório descritivo de produtividade
            <IconButton
              onClick={() => setIsReportModalOpen(false)}
              sx={{ position: 'absolute', right: 8, top: 8 }}
              aria-label="Fechar"
            >
              <Close />
            </IconButton>
          </DialogTitle>
          <DialogContent dividers>
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
              <TextField
                label="Nome do fiscal"
                value={reportUserName}
                fullWidth
                InputProps={{ readOnly: true }}
              />
              <DatePicker
                label="Escolha o mês"
                value={reportMonth}
                onChange={(value) => setReportMonth(value)}
                views={['month', 'year']}
                slotProps={{
                  textField: {
                    fullWidth: true,
                  },
                }}
              />
            </Box>
          </DialogContent>
          <DialogActions>
            <Button onClick={() => setIsReportModalOpen(false)}>Cancelar</Button>
            <Button variant="contained" onClick={handleGenerateDescriptivePdf}>
              Gerar
            </Button>
          </DialogActions>
        </Dialog>

        <Dialog
          open={isProductivityReportOpen}
          onClose={() => setIsProductivityReportOpen(false)}
          maxWidth="sm"
          fullWidth
        >
          <DialogTitle sx={{ display: 'flex', alignItems: 'center', pr: 6 }}>
            Relatório de produtividade
            <IconButton
              onClick={() => setIsProductivityReportOpen(false)}
              sx={{ position: 'absolute', right: 8, top: 8 }}
              aria-label="Fechar"
            >
              <Close />
            </IconButton>
          </DialogTitle>
          <DialogContent dividers>
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
              <TextField
                label="Nome do fiscal"
                value={reportUserInput}
                onChange={(event) => setReportUserInput(event.target.value)}
                fullWidth
              />
              <DatePicker
                label="Data vigência"
                value={reportMonth}
                onChange={(value) => setReportMonth(value)}
                views={['month', 'year']}
                slotProps={{
                  textField: {
                    fullWidth: true,
                  },
                }}
              />
            </Box>
          </DialogContent>
          <DialogActions>
            <Button onClick={() => setIsProductivityReportOpen(false)}>
              Cancelar
            </Button>
            <Button variant="contained" onClick={handleGenerateProductivityPdf}>
              Gerar
            </Button>
          </DialogActions>
        </Dialog>

        <Dialog
          open={isScoreReportOpen}
          onClose={() => setIsScoreReportOpen(false)}
          maxWidth="sm"
          fullWidth
        >
          <DialogTitle sx={{ display: 'flex', alignItems: 'center', pr: 6 }}>
            Relatório de pontuação
            <IconButton
              onClick={() => setIsScoreReportOpen(false)}
              sx={{ position: 'absolute', right: 8, top: 8 }}
              aria-label="Fechar"
            >
              <Close />
            </IconButton>
          </DialogTitle>
          <DialogContent dividers>
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
              <DatePicker
                label="Data vigência"
                value={reportMonth}
                onChange={(value) => setReportMonth(value)}
                views={['month', 'year']}
                slotProps={{
                  textField: {
                    fullWidth: true,
                  },
                }}
              />
            </Box>
          </DialogContent>
          <DialogActions>
            <Button onClick={() => setIsScoreReportOpen(false)}>Cancelar</Button>
            <Button variant="contained" onClick={handleGenerateScorePdf}>
              Gerar
            </Button>
          </DialogActions>
        </Dialog>

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
    </LocalizationProvider>
  );
}
