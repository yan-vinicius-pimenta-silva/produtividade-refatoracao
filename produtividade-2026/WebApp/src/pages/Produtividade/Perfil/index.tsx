import { useMemo, useState } from 'react';
import {
  Alert,
  Box,
  Button,
  Card,
  CardContent,
  Chip,
  Divider,
  Grid,
  MenuItem,
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
import { Description, NoteAdd, PlayArrow } from '@mui/icons-material';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { ptBR } from 'date-fns/locale';
import { useAuth } from '../../../hooks';
import {
  initialActivities,
  initialUfespRows,
} from '../../../helpers/parametrosData';

type HistoricoAtividade = {
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
  notes: string;
  deductionUser: string;
  deductionReason: string;
  validated: boolean;
  attachment: string;
  deductedPoints: number;
};

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
});

const initialHistory: HistoricoAtividade[] = [
  {
    id: 1103,
    type: 'Vistoria de Regularização',
    date: '14/01/2026',
    protocol: '0554/2026',
    document: '0233445',
    rc: '11.6.06.30.043.000',
    cpfCnpj: '12.625.069/0001-90',
    points: 2.5,
    quantity: 1,
    value: 128.4,
    notes: 'Vistoria concluída sem pendências.',
    deductionUser: '--',
    deductionReason: '--',
    validated: true,
    attachment: 'VR-1103.pdf',
    deductedPoints: 0,
  },
  {
    id: 1109,
    type: 'Lavratura de Auto',
    date: '16/01/2026',
    protocol: '0779/2026',
    document: '0233551',
    rc: '11.7.21.40.008.000',
    cpfCnpj: '269.891.668-00',
    points: 8,
    quantity: 1,
    value: 420.5,
    notes: 'Auto emitido após reincidência.',
    deductionUser: 'Coordenação',
    deductionReason: 'Documento anexado fora do prazo.',
    validated: false,
    attachment: 'LA-1109.pdf',
    deductedPoints: 1.5,
  },
];

export default function ProdutividadePerfilFiscal() {
  const { authUser } = useAuth();
  const [tab, setTab] = useState(0);
  const [historyRows, setHistoryRows] =
    useState<HistoricoAtividade[]>(initialHistory);
  const [searchTerm, setSearchTerm] = useState('');
  const [activityId, setActivityId] = useState('');
  const [documentNumber, setDocumentNumber] = useState('');
  const [protocolNumber, setProtocolNumber] = useState('');
  const [launchValue, setLaunchValue] = useState('');
  const [completionDate, setCompletionDate] = useState<Date | null>(null);
  const [rc, setRc] = useState('');
  const [cpfCnpj, setCpfCnpj] = useState('');
  const [notes, setNotes] = useState('');
  const [quantity, setQuantity] = useState('1');
  const [attachment, setAttachment] = useState<File | null>(null);
  const [snackbar, setSnackbar] = useState({
    open: false,
    message: '',
    severity: 'info' as 'success' | 'info' | 'warning' | 'error',
  });

  const activeUfesp =
    initialUfespRows.find((row) => row.ativo) ?? initialUfespRows[0];

  const selectedActivity = initialActivities.find(
    (item) => String(item.id) === activityId
  );

  const handleActivityChange = (value: string) => {
    setActivityId(value);
    if (value) {
      setLaunchValue(activeUfesp.valor);
    } else {
      setLaunchValue('');
    }
  };

  const filteredHistory = useMemo(() => {
    const normalizedSearch = searchTerm.trim().toLowerCase();
    if (!normalizedSearch) {
      return historyRows;
    }
    return historyRows.filter((row) =>
      [
        row.type,
        row.protocol,
        row.document,
        row.rc,
        row.cpfCnpj,
        row.notes,
      ]
        .join(' ')
        .toLowerCase()
        .includes(normalizedSearch)
    );
  }, [historyRows, searchTerm]);

  const totals = useMemo(() => {
    return historyRows.reduce(
      (acc, row) => ({
        points: acc.points + row.points,
        value: acc.value + row.value,
        deducted: acc.deducted + row.deductedPoints,
      }),
      { points: 0, value: 0, deducted: 0 }
    );
  }, [historyRows]);

  const remainingPoints = totals.points - totals.deducted;

  const resetForm = () => {
    setActivityId('');
    setDocumentNumber('');
    setProtocolNumber('');
    setLaunchValue('');
    setCompletionDate(null);
    setRc('');
    setCpfCnpj('');
    setNotes('');
    setQuantity('1');
    setAttachment(null);
  };

  const handleSubmit = () => {
    if (!selectedActivity) {
      setSnackbar({
        open: true,
        message: 'Selecione uma atividade para continuar.',
        severity: 'warning',
      });
      return;
    }

    if (!documentNumber || !completionDate || !cpfCnpj || !attachment) {
      setSnackbar({
        open: true,
        message: 'Preencha os campos obrigatórios antes de enviar.',
        severity: 'warning',
      });
      return;
    }

    const parsedQuantity = Number(quantity.replace(',', '.'));
    const parsedValue = Number(launchValue.replace('.', '').replace(',', '.'));
    const formattedDate = completionDate.toLocaleDateString('pt-BR');

    setHistoryRows((current) => [
      {
        id: Math.max(0, ...current.map((row) => row.id)) + 1,
        type: selectedActivity.name,
        date: formattedDate,
        protocol: protocolNumber || '--',
        document: documentNumber,
        rc: rc || '--',
        cpfCnpj,
        points: selectedActivity.points,
        quantity: Number.isNaN(parsedQuantity) ? 1 : parsedQuantity,
        value: Number.isNaN(parsedValue) ? 0 : parsedValue,
        notes: notes || '--',
        deductionUser: '--',
        deductionReason: '--',
        validated: false,
        attachment: attachment.name,
        deductedPoints: 0,
      },
      ...current,
    ]);

    resetForm();
    setSnackbar({
      open: true,
      message: 'Atividade enviada para validação com sucesso.',
      severity: 'success',
    });
    setTab(0);
  };

  const userName = authUser?.fullName || authUser?.username || 'Fiscal urbano';

  return (
    <LocalizationProvider dateAdapter={AdapterDateFns} adapterLocale={ptBR}>
      <Box sx={{ bgcolor: '#f2f2f2', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
        <Typography variant="h5" sx={{ fontWeight: 700, mb: 3 }}>
          Fiscalização Urbana - Produtividade
        </Typography>

        <Grid container spacing={2} sx={{ mb: 3 }}>
          <Grid item xs={12} md={3}>
            <Card sx={{ borderRadius: 2, bgcolor: '#ff7043', color: '#fff' }}>
              <CardContent>
                <Typography variant="subtitle2" sx={{ textTransform: 'uppercase' }}>
                  Meus pontos
                </Typography>
                <Typography variant="caption">Vigência: 01/2026</Typography>
                <Typography variant="h4" sx={{ mt: 1 }}>
                  {totals.points.toFixed(1)}
                </Typography>
              </CardContent>
            </Card>
          </Grid>
          <Grid item xs={12} md={3}>
            <Card sx={{ borderRadius: 2, bgcolor: '#2196f3', color: '#fff' }}>
              <CardContent>
                <Typography variant="subtitle2" sx={{ textTransform: 'uppercase' }}>
                  Receita aferida
                </Typography>
                <Typography variant="caption">Vigência: 01/2026</Typography>
                <Typography variant="h4" sx={{ mt: 1 }}>
                  {currencyFormatter.format(totals.value)}
                </Typography>
              </CardContent>
            </Card>
          </Grid>
          <Grid item xs={12} md={3}>
            <Card sx={{ borderRadius: 2, bgcolor: '#f44336', color: '#fff' }}>
              <CardContent>
                <Typography variant="subtitle2" sx={{ textTransform: 'uppercase' }}>
                  Pontos deduzidos
                </Typography>
                <Typography variant="caption">Vigência: 01/2026</Typography>
                <Typography variant="h4" sx={{ mt: 1 }}>
                  {totals.deducted.toFixed(1)}
                </Typography>
              </CardContent>
            </Card>
          </Grid>
          <Grid item xs={12} md={3}>
            <Card sx={{ borderRadius: 2, bgcolor: '#009688', color: '#fff' }}>
              <CardContent>
                <Typography variant="subtitle2" sx={{ textTransform: 'uppercase' }}>
                  Pontos remanescente
                </Typography>
                <Typography variant="caption">Vigência: 01/2026</Typography>
                <Typography variant="h4" sx={{ mt: 1 }}>
                  {remainingPoints.toFixed(1)}
                </Typography>
              </CardContent>
            </Card>
          </Grid>
        </Grid>

        <Paper sx={{ borderRadius: 3, boxShadow: 3, p: 3 }}>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1 }}>
            <Typography variant="subtitle1" sx={{ fontWeight: 700 }}>
              {userName}
            </Typography>
            <Chip label="Fiscal urbano" size="small" color="primary" />
          </Box>

          <Tabs
            value={tab}
            onChange={(_, value) => setTab(value)}
            textColor="primary"
            indicatorColor="primary"
            sx={{ mt: 2, '& .MuiTab-root': { textTransform: 'none' } }}
          >
            <Tab label="Histórico de atividades" />
            <Tab label="Nova atividade" />
          </Tabs>

          <Divider sx={{ my: 2 }} />

          {tab === 0 ? (
            <>
              <Box
                sx={{
                  display: 'flex',
                  flexWrap: 'wrap',
                  gap: 1.5,
                  alignItems: 'center',
                  justifyContent: 'space-between',
                  mb: 2,
                }}
              >
                <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 1.5 }}>
                  <Button variant="contained" color="warning">
                    Relatório Descritivo
                  </Button>
                  <Button variant="contained" color="success">
                    Relatório de Produtividade
                  </Button>
                </Box>
                <TextField
                  label="Pesquisar"
                  variant="standard"
                  value={searchTerm}
                  onChange={(event) => setSearchTerm(event.target.value)}
                  sx={{ minWidth: 220 }}
                />
              </Box>

              <TableContainer sx={{ borderRadius: 2, border: '1px solid #e0e0e0' }}>
                <Table size="small">
                  <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                    <TableRow>
                      <TableCell sx={{ fontWeight: 600 }}>ID</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Tipo</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Data</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Nº protocolo</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Nº documento</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>RC</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>CPF/CNPJ</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Pontuação</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Quantidade</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Valor (R$)</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Observações</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Usuário dedução</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>
                        Justificativa dedução
                      </TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Validado</TableCell>
                      <TableCell sx={{ fontWeight: 600 }}>Documento</TableCell>
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {filteredHistory.length === 0 ? (
                      <TableRow>
                        <TableCell colSpan={15}>
                          Nenhum registro encontrado.
                        </TableCell>
                      </TableRow>
                    ) : (
                      filteredHistory.map((row) => (
                        <TableRow key={row.id}>
                          <TableCell>{row.id}</TableCell>
                          <TableCell>{row.type}</TableCell>
                          <TableCell>{row.date}</TableCell>
                          <TableCell>{row.protocol}</TableCell>
                          <TableCell>{row.document}</TableCell>
                          <TableCell>{row.rc}</TableCell>
                          <TableCell>{row.cpfCnpj}</TableCell>
                          <TableCell>{row.points.toFixed(1)}</TableCell>
                          <TableCell>{row.quantity}</TableCell>
                          <TableCell>{currencyFormatter.format(row.value)}</TableCell>
                          <TableCell>{row.notes}</TableCell>
                          <TableCell>{row.deductionUser}</TableCell>
                          <TableCell>{row.deductionReason}</TableCell>
                          <TableCell>
                            <Chip
                              label={row.validated ? 'Sim' : 'Não'}
                              color={row.validated ? 'success' : 'warning'}
                              size="small"
                            />
                          </TableCell>
                          <TableCell>
                            <Chip
                              icon={<Description />}
                              label={row.attachment}
                              variant="outlined"
                              size="small"
                            />
                          </TableCell>
                        </TableRow>
                      ))
                    )}
                  </TableBody>
                </Table>
              </TableContainer>
            </>
          ) : (
            <Box sx={{ maxWidth: 980, mx: 'auto' }}>
              <Grid container spacing={3} alignItems="center">
                <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                  <Typography
                    component="label"
                    htmlFor="atividade-select"
                    sx={{ fontWeight: 600, color: 'text.secondary' }}
                  >
                    Atividade:{' '}
                    <Box component="span" sx={{ color: 'error.main' }}>
                      *
                    </Box>
                  </Typography>
                </Grid>
                <Grid item xs={12} md={9}>
                  <TextField
                    id="atividade-select"
                    select
                    fullWidth
                    value={activityId}
                    onChange={(event) => handleActivityChange(event.target.value)}
                    variant="standard"
                  >
                    <MenuItem value="">Escolha...</MenuItem>
                    {initialActivities.map((activity) => (
                      <MenuItem key={activity.id} value={String(activity.id)}>
                        {activity.name}
                      </MenuItem>
                    ))}
                  </TextField>
                </Grid>

                {selectedActivity ? (
                  <>
                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="document-number"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        Número do lançamento/documento:{' '}
                        <Box component="span" sx={{ color: 'error.main' }}>
                          *
                        </Box>
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <TextField
                        id="document-number"
                        fullWidth
                        variant="standard"
                        placeholder="Se houver, digite o número"
                        value={documentNumber}
                        onChange={(event) => setDocumentNumber(event.target.value)}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="protocol-number"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        Número do processo/protocolo:
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <TextField
                        id="protocol-number"
                        fullWidth
                        variant="standard"
                        placeholder="Se houver, digite o número"
                        value={protocolNumber}
                        onChange={(event) => setProtocolNumber(event.target.value)}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="launch-value"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        Valor do lançamento: R$
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <TextField
                        id="launch-value"
                        fullWidth
                        variant="standard"
                        value={launchValue}
                        onChange={(event) => setLaunchValue(event.target.value)}
                        helperText={`UFESP vigente: ${activeUfesp.descricao} (${activeUfesp.valor})`}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="completion-date"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        Data de conclusão:{' '}
                        <Box component="span" sx={{ color: 'error.main' }}>
                          *
                        </Box>
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <DatePicker
                        value={completionDate}
                        onChange={(value) => setCompletionDate(value)}
                        slotProps={{
                          textField: {
                            id: 'completion-date',
                            fullWidth: true,
                            variant: 'standard',
                            placeholder: 'dd / mm / aaaa',
                          },
                        }}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="rc"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        RC:
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <TextField
                        id="rc"
                        fullWidth
                        variant="standard"
                        placeholder="Se houver, digite o número"
                        value={rc}
                        onChange={(event) => setRc(event.target.value)}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="cpf-cnpj"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        CPF/CNPJ:{' '}
                        <Box component="span" sx={{ color: 'error.main' }}>
                          *
                        </Box>
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <TextField
                        id="cpf-cnpj"
                        fullWidth
                        variant="standard"
                        value={cpfCnpj}
                        onChange={(event) => setCpfCnpj(event.target.value)}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="quantity"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        Quantidade:
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <TextField
                        id="quantity"
                        fullWidth
                        variant="standard"
                        value={quantity}
                        onChange={(event) => setQuantity(event.target.value)}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="notes"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        Observações:
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <TextField
                        id="notes"
                        fullWidth
                        variant="standard"
                        placeholder="Observações não é obrigatório"
                        value={notes}
                        onChange={(event) => setNotes(event.target.value)}
                      />
                    </Grid>

                    <Grid item xs={12} md={3} sx={{ textAlign: { md: 'right' } }}>
                      <Typography
                        component="label"
                        htmlFor="attachment"
                        sx={{ fontWeight: 600, color: 'text.secondary' }}
                      >
                        Arquivos:{' '}
                        <Box component="span" sx={{ color: 'error.main' }}>
                          *
                        </Box>
                      </Typography>
                    </Grid>
                    <Grid item xs={12} md={9}>
                      <Button
                        variant="outlined"
                        component="label"
                        startIcon={<Description />}
                      >
                        {attachment ? attachment.name : 'Procurar'}
                        <input
                          id="attachment"
                          type="file"
                          hidden
                          onChange={(event) =>
                            setAttachment(event.target.files?.[0] ?? null)
                          }
                        />
                      </Button>
                    </Grid>

                    <Grid item xs={12}>
                      <Card sx={{ bgcolor: '#f8f9fb' }}>
                        <CardContent sx={{ display: 'flex', gap: 3, flexWrap: 'wrap' }}>
                          <Box>
                            <Typography variant="subtitle2" color="text.secondary">
                              Tipo de contabilização
                            </Typography>
                            <Typography variant="body1">
                              {selectedActivity.calculationType}
                            </Typography>
                          </Box>
                          <Box>
                            <Typography variant="subtitle2" color="text.secondary">
                              Pontuação estimada
                            </Typography>
                            <Typography variant="body1">
                              {selectedActivity.points} ponto(s)
                            </Typography>
                          </Box>
                          <Box>
                            <Typography variant="subtitle2" color="text.secondary">
                              UFESP vigente
                            </Typography>
                            <Typography variant="body1">
                              {activeUfesp.descricao} ({activeUfesp.valor})
                            </Typography>
                          </Box>
                        </CardContent>
                      </Card>
                    </Grid>

                    <Grid item xs={12}>
                      <Button
                        variant="contained"
                        color="success"
                        startIcon={<PlayArrow />}
                        onClick={handleSubmit}
                      >
                        Enviar
                      </Button>
                    </Grid>
                  </>
                ) : (
                  <Grid item xs={12}>
                    <Card sx={{ bgcolor: '#f8f9fb' }}>
                      <CardContent sx={{ display: 'flex', gap: 2, alignItems: 'center' }}>
                        <NoteAdd color="action" />
                        <Typography variant="body2" color="text.secondary">
                          Escolha uma atividade para visualizar os campos de cadastro.
                        </Typography>
                      </CardContent>
                    </Card>
                  </Grid>
                )}
              </Grid>
            </Box>
          )}
        </Paper>

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
