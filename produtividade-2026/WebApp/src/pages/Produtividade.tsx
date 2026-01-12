import { useMemo, useState } from 'react';
import {
  Box,
  Button,
  Card,
  CardContent,
  Checkbox,
  Chip,
  Divider,
  FormControl,
  InputLabel,
  MenuItem,
  Paper,
  Select,
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

type DeductionRow = {
  id: number;
  type: string;
  fiscal: string;
  date: string;
  points: number;
  value: number;
  reason: string;
};

type UfespRow = {
  year: number;
  value: number;
  active: boolean;
};

type UserRow = {
  id: number;
  name: string;
  intranet: string;
  registration: string;
  role: string;
  status: string;
};

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
});

export default function Produtividade() {
  const [sectionTab, setSectionTab] = useState(0);
  const [homeTab, setHomeTab] = useState(0);

  const pendingActivities = useMemo<ActivityRow[]>(
    () => [
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
    ],
    []
  );

  const validatedActivities = useMemo<ActivityRow[]>(
    () => [
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
    ],
    []
  );

  const deductions = useMemo<DeductionRow[]>(
    () => [
      {
        id: 420,
        type: 'Advertência',
        fiscal: 'Fernando Pagioro',
        date: '05/01/2026',
        points: 2,
        value: 74.04,
        reason: 'Correção de lançamento duplicado.',
      },
      {
        id: 421,
        type: 'Devolução',
        fiscal: 'Sisuley Zaniboni Gouveia',
        date: '06/01/2026',
        points: 4,
        value: 148.08,
        reason: 'Reembolso de taxa indevida.',
      },
    ],
    []
  );

  const legacyActivities = useMemo(
    () => [
      {
        id: 980,
        regime: 'Lei 4.112/2019',
        activity: 'Auto de infração - ruído',
        date: '12/11/2022',
        fiscal: 'Luciana Dantas',
        points: 6.5,
        value: 180.12,
        status: 'Encerrado',
      },
      {
        id: 991,
        regime: 'Lei 3.880/2017',
        activity: 'Plantão fiscal noturno',
        date: '09/08/2021',
        fiscal: 'Gabriel Duarte',
        points: 5,
        value: 135.7,
        status: 'Auditável',
      },
    ],
    []
  );

  const activityParameters = useMemo(
    () => [
      {
        id: 1,
        name: 'Auto de Infração',
        description: 'Registro de infrações urbanas',
        points: 8.5,
        calculation: 'UFESP',
        active: true,
        multiplier: true,
      },
      {
        id: 2,
        name: 'Taxa de Licença',
        description: 'Emissão de taxa e cobrança',
        points: 2,
        calculation: 'UFESP',
        active: true,
        multiplier: false,
      },
      {
        id: 3,
        name: 'Plantão Fiscal',
        description: 'Atuação fora do expediente',
        points: 1.5,
        calculation: 'Valor fixo',
        active: false,
        multiplier: true,
      },
    ],
    []
  );

  const ufespValues = useMemo<UfespRow[]>(
    () => [
      { year: 2024, value: 35.36, active: false },
      { year: 2025, value: 37.02, active: false },
      { year: 2026, value: 38.42, active: true },
    ],
    []
  );

  const users = useMemo<UserRow[]>(
    () => [
      {
        id: 1,
        name: 'Pedro de Melo',
        intranet: 'pmelo',
        registration: '14523',
        role: 'Fiscal',
        status: 'Ativo',
      },
      {
        id: 2,
        name: 'Aline Castro',
        intranet: 'acastro',
        registration: '13208',
        role: 'Chefia',
        status: 'Ativo',
      },
      {
        id: 3,
        name: 'Marcos Lira',
        intranet: 'mlira',
        registration: '10945',
        role: 'Admin',
        status: 'Inativo',
      },
    ],
    []
  );

  const trashItems = useMemo(
    () => [
      {
        id: 54,
        type: 'Atividade',
        deletedAt: '07/01/2026',
        deletedBy: 'Aline Castro',
        reason: 'Duplicidade de lançamento',
      },
      {
        id: 55,
        type: 'Dedução',
        deletedAt: '08/01/2026',
        deletedBy: 'Marcos Lira',
        reason: 'Cancelamento administrativo',
      },
    ],
    []
  );

  const excludedActivities = useMemo(
    () => [
      {
        id: 610,
        date: '06/01/2026',
        reason: 'Documento incompleto',
        excludedBy: 'Aline Castro',
        value: 480.5,
        fiscal: 'Fernando Pagioro',
      },
      {
        id: 612,
        date: '07/01/2026',
        reason: 'Cancelada após recurso',
        excludedBy: 'Pedro de Melo',
        value: 1120.3,
        fiscal: 'Luciana Dantas',
      },
    ],
    []
  );

  const totalPendingValue = pendingActivities.reduce(
    (acc, item) => acc + item.value,
    0
  );
  const totalPendingPoints = pendingActivities.reduce(
    (acc, item) => acc + item.points,
    0
  );

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
        <Paper sx={{ p: 2.5, borderRadius: 3, boxShadow: 3 }}>
          <Tabs
            value={sectionTab}
            onChange={(_, value) => setSectionTab(value)}
            variant="scrollable"
            scrollButtons="auto"
            textColor="primary"
            indicatorColor="primary"
            sx={{ '& .MuiTab-root': { textTransform: 'none', fontWeight: 600 } }}
          >
            <Tab label="Home" />
            <Tab label="Deduções" />
            <Tab label="Atividades (Lei anterior)" />
            <Tab label="Parâmetros · Atividades" />
            <Tab label="Parâmetros · UFESP" />
            <Tab label="Usuários" />
            <Tab label="Lixeira" />
            <Tab label="Atividades Excluídas" />
          </Tabs>
        </Paper>

        {sectionTab === 0 && (
          <Box sx={{ mt: 3, display: 'grid', gap: 3 }}>
            <Box
              sx={{
                display: 'grid',
                gap: 2,
                gridTemplateColumns: { xs: '1fr', md: 'repeat(3, 1fr)' },
              }}
            >
              <Card sx={{ borderRadius: 3 }}>
                <CardContent>
                  <Typography variant="subtitle2" color="text.secondary">
                    Atividades pendentes
                  </Typography>
                  <Typography variant="h5" sx={{ fontWeight: 700, mt: 1 }}>
                    {pendingActivities.length}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    Lançamentos aguardando validação
                  </Typography>
                </CardContent>
              </Card>
              <Card sx={{ borderRadius: 3 }}>
                <CardContent>
                  <Typography variant="subtitle2" color="text.secondary">
                    Pontos pendentes
                  </Typography>
                  <Typography variant="h5" sx={{ fontWeight: 700, mt: 1 }}>
                    {totalPendingPoints.toFixed(1)}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    Base para cálculo financeiro
                  </Typography>
                </CardContent>
              </Card>
              <Card sx={{ borderRadius: 3 }}>
                <CardContent>
                  <Typography variant="subtitle2" color="text.secondary">
                    Valor estimado
                  </Typography>
                  <Typography variant="h5" sx={{ fontWeight: 700, mt: 1 }}>
                    {currencyFormatter.format(totalPendingValue)}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    Pré-folha de pagamento da fiscalização
                  </Typography>
                </CardContent>
              </Card>
            </Box>

            <Paper sx={{ p: 3, borderRadius: 3, boxShadow: 3 }}>
              <Typography variant="h6" sx={{ fontWeight: 700 }}>
                Central de Apuração
              </Typography>
              <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
                Valide as atividades lançadas pelos fiscais antes de liberar o
                cálculo financeiro. Atividades validadas ficam bloqueadas para edição.
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
                  <Button
                    variant="contained"
                    color="success"
                    startIcon={<TaskAlt />}
                  >
                    Confirmar
                  </Button>
                  <Button
                    variant="contained"
                    color="success"
                    startIcon={<Refresh />}
                  >
                    Atualizar
                  </Button>
                </Box>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                  <Button variant="text" startIcon={<FilterList />}>
                    Filtrar
                  </Button>
                  <TextField
                    label="Pesquisar"
                    variant="standard"
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
                        </TableRow>
                      )
                    )}
                  </TableBody>
                </Table>
              </TableContainer>
            </Paper>
          </Box>
        )}

        {sectionTab === 1 && (
          <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Cadastrar Dedução
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              Desconte pontos ou valores de um fiscal por advertência, punição ou devolução.
            </Typography>
            <Divider sx={{ my: 2 }} />

            <Box
              sx={{
                display: 'grid',
                gap: 2,
                gridTemplateColumns: { xs: '1fr', md: 'repeat(2, 1fr)' },
              }}
            >
              <FormControl fullWidth>
                <InputLabel>Tipo de dedução</InputLabel>
                <Select label="Tipo de dedução" defaultValue="Advertência">
                  <MenuItem value="Advertência">Advertência</MenuItem>
                  <MenuItem value="Punição">Punição</MenuItem>
                  <MenuItem value="Erro">Erro</MenuItem>
                  <MenuItem value="Devolução">Devolução</MenuItem>
                </Select>
              </FormControl>
              <FormControl fullWidth>
                <InputLabel>Fiscal afetado</InputLabel>
                <Select label="Fiscal afetado" defaultValue="Pedro de Melo">
                  <MenuItem value="Pedro de Melo">Pedro de Melo</MenuItem>
                  <MenuItem value="Sisuley Zaniboni Gouveia">
                    Sisuley Zaniboni Gouveia
                  </MenuItem>
                  <MenuItem value="Fernando Pagioro">Fernando Pagioro</MenuItem>
                </Select>
              </FormControl>
              <TextField label="Data de vigência" type="date" InputLabelProps={{ shrink: true }} />
              <TextField label="Justificativa" placeholder="Descreva o motivo" />
            </Box>

            <Box sx={{ mt: 2, display: 'flex', gap: 1.5 }}>
              <TextField label="Pontos" placeholder="0" type="number" />
              <TextField label="Valor (R$)" placeholder="0,00" type="number" />
            </Box>

            <Box sx={{ mt: 3, display: 'flex', gap: 2 }}>
              <Button variant="contained" color="warning">
                Registrar Dedução
              </Button>
              <Button variant="outlined">Limpar</Button>
            </Box>

            <Divider sx={{ my: 3 }} />

            <Typography variant="subtitle1" sx={{ fontWeight: 600, mb: 2 }}>
              Deduções recentes
            </Typography>
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>Tipo</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Fiscal</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Vigência</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Pontos</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Valor</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Justificativa</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {deductions.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{row.type}</TableCell>
                    <TableCell>{row.fiscal}</TableCell>
                    <TableCell>{row.date}</TableCell>
                    <TableCell>{row.points}</TableCell>
                    <TableCell>{currencyFormatter.format(row.value)}</TableCell>
                    <TableCell>{row.reason}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        )}

        {sectionTab === 2 && (
          <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Atividades (Lei anterior)
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              Histórico de atividades de regimes legais anteriores para consulta e auditoria.
            </Typography>
            <Divider sx={{ my: 2 }} />
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>Regime</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Atividade</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Data</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Fiscal</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Pontos</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Valor</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Status</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {legacyActivities.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{row.regime}</TableCell>
                    <TableCell>{row.activity}</TableCell>
                    <TableCell>{row.date}</TableCell>
                    <TableCell>{row.fiscal}</TableCell>
                    <TableCell>{row.points}</TableCell>
                    <TableCell>{currencyFormatter.format(row.value)}</TableCell>
                    <TableCell>
                      <Chip label={row.status} size="small" color="info" />
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        )}

        {sectionTab === 3 && (
          <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Parâmetros · Atividades
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              Defina o valor base de cada tipo de trabalho para o cálculo financeiro.
            </Typography>
            <Divider sx={{ my: 2 }} />

            <Box
              sx={{
                display: 'grid',
                gap: 2,
                gridTemplateColumns: { xs: '1fr', md: 'repeat(3, 1fr)' },
              }}
            >
              <TextField label="Nome da atividade" placeholder="Auto de Infração" />
              <TextField label="Descrição" placeholder="Descrição da atividade" />
              <TextField label="Pontos base" type="number" placeholder="0" />
              <FormControl>
                <InputLabel>Tipo de contabilização</InputLabel>
                <Select label="Tipo de contabilização" defaultValue="UFESP">
                  <MenuItem value="UFESP">UFESP</MenuItem>
                  <MenuItem value="Valor fixo">Valor fixo</MenuItem>
                </Select>
              </FormControl>
              <FormControl>
                <InputLabel>Status</InputLabel>
                <Select label="Status" defaultValue="Ativa">
                  <MenuItem value="Ativa">Ativa</MenuItem>
                  <MenuItem value="Inativa">Inativa</MenuItem>
                </Select>
              </FormControl>
              <FormControl>
                <InputLabel>Multiplicador</InputLabel>
                <Select label="Multiplicador" defaultValue="Sim">
                  <MenuItem value="Sim">Sim</MenuItem>
                  <MenuItem value="Não">Não</MenuItem>
                </Select>
              </FormControl>
            </Box>

            <Box sx={{ mt: 3, display: 'flex', gap: 2 }}>
              <Button variant="contained" color="warning">
                Salvar parâmetros
              </Button>
              <Button variant="outlined">Limpar</Button>
            </Box>

            <Divider sx={{ my: 3 }} />
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>Nome</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Descrição</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Pontos base</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Tipo</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Ativa</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Multiplicador</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {activityParameters.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{row.name}</TableCell>
                    <TableCell>{row.description}</TableCell>
                    <TableCell>{row.points}</TableCell>
                    <TableCell>{row.calculation}</TableCell>
                    <TableCell>{row.active ? 'Sim' : 'Não'}</TableCell>
                    <TableCell>{row.multiplier ? 'Sim' : 'Não'}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        )}

        {sectionTab === 4 && (
          <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Parâmetros · UFESP
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              Defina a unidade fiscal ativa para converter pontos em valores financeiros.
            </Typography>
            <Divider sx={{ my: 2 }} />
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>Ano</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Valor da UFESP</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Ativa</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {ufespValues.map((row) => (
                  <TableRow key={row.year}>
                    <TableCell>{row.year}</TableCell>
                    <TableCell>{currencyFormatter.format(row.value)}</TableCell>
                    <TableCell>
                      <Chip
                        label={row.active ? 'Ativa' : 'Inativa'}
                        color={row.active ? 'success' : 'default'}
                        size="small"
                      />
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        )}

        {sectionTab === 5 && (
          <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Usuários
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              Controle de acessos e responsabilidades na cadeia de validação.
            </Typography>
            <Divider sx={{ my: 2 }} />
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>Nome</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Usuário intranet</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Matrícula</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Nível</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Ativo</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {users.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{row.name}</TableCell>
                    <TableCell>{row.intranet}</TableCell>
                    <TableCell>{row.registration}</TableCell>
                    <TableCell>{row.role}</TableCell>
                    <TableCell>{row.status}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        )}

        {sectionTab === 6 && (
          <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Lixeira
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              Registros excluídos disponíveis para auditoria e rastreamento.
            </Typography>
            <Divider sx={{ my: 2 }} />
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>Tipo</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Data de exclusão</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Responsável</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Motivo</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {trashItems.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{row.type}</TableCell>
                    <TableCell>{row.deletedAt}</TableCell>
                    <TableCell>{row.deletedBy}</TableCell>
                    <TableCell>{row.reason}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        )}

        {sectionTab === 7 && (
          <Paper sx={{ mt: 3, p: 3, borderRadius: 3, boxShadow: 3 }}>
            <Typography variant="h6" sx={{ fontWeight: 700 }}>
              Atividades Excluídas
            </Typography>
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              Log antifraude de cancelamentos, exclusões e invalidações.
            </Typography>
            <Divider sx={{ my: 2 }} />
            <Table size="small">
              <TableHead sx={{ bgcolor: '#f3f4f6' }}>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600 }}>Data</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Motivo</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Quem excluiu</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Valor</TableCell>
                  <TableCell sx={{ fontWeight: 600 }}>Fiscal</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {excludedActivities.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{row.date}</TableCell>
                    <TableCell>{row.reason}</TableCell>
                    <TableCell>{row.excludedBy}</TableCell>
                    <TableCell>{currencyFormatter.format(row.value)}</TableCell>
                    <TableCell>{row.fiscal}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        )}
      </Box>
    </Box>
  );
}
