import { useEffect, useMemo, useState } from 'react';
import {
  Box,
  Button,
  Checkbox,
  FormControl,
  IconButton,
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
  DeleteOutline,
  Edit,
  FilterList,
  Refresh,
  TaskAlt,
} from '@mui/icons-material';
import {
  confirmFiscalActivities,
  createFiscalActivity,
  createProdutividadeActivity,
  deleteFiscalActivity,
  deleteProdutividadeActivity,
  fetchFiscalActivities,
  fetchProdutividadeActivities,
  fetchProdutividadeActivityTypes,
  fetchProdutividadeUsers,
  updateProdutividadeActivity,
} from '../services';
import { useAuth, useNotification } from '../hooks';
import {
  ProdutividadeActivity,
  ProdutividadeActivityType,
  ProdutividadeFiscalActivitySummary,
  ProdutividadeUserSummary,
} from '../interfaces';
import { getErrorMessage } from '../helpers';

export default function Produtividade() {
  const [activeTab, setActiveTab] = useState(0);
  const [fiscalActivities, setFiscalActivities] = useState<
    ProdutividadeFiscalActivitySummary[]
  >([]);
  const [selectedActivityIds, setSelectedActivityIds] = useState<number[]>([]);
  const [activityCatalog, setActivityCatalog] = useState<ProdutividadeActivity[]>(
    []
  );
  const [activityTypes, setActivityTypes] = useState<ProdutividadeActivityType[]>(
    []
  );
  const [fiscais, setFiscais] = useState<ProdutividadeUserSummary[]>([]);
  const [loadingActivities, setLoadingActivities] = useState(false);
  const [loadingCatalog, setLoadingCatalog] = useState(false);
  const [activityForm, setActivityForm] = useState({
    id: null as number | null,
    description: '',
    points: '',
    activityTypeId: '',
    companyId: '',
    isActive: true,
    hasMultiplicator: false,
    isOsActivity: false,
  });
  const [deducaoForm, setDeducaoForm] = useState({
    activityId: '',
    fiscalId: '',
    completedAt: '',
    notes: '',
    quantity: '',
  });

  const { token } = useAuth();
  const { showNotification } = useNotification();

  const parsedToken = useMemo(() => {
    if (!token) return null;
    const parts = token.split('.');
    if (parts.length < 2) return null;
    try {
      const decoded = JSON.parse(atob(parts[1]));
      return decoded as Record<string, string>;
    } catch {
      return null;
    }
  }, [token]);

  const defaultCompanyId = parsedToken?.companyId
    ? Number(parsedToken.companyId)
    : null;

  const deductionActivities = useMemo(
    () =>
      activityCatalog.filter((activity) => activity.calculationType === 3),
    [activityCatalog]
  );

  const calculationLabel = (value: number) => {
    if (value === 1) return 'UFESP';
    if (value === 2) return 'Pontuação';
    if (value === 3) return 'Dedução';
    return '-';
  };

  const fetchInitialData = async () => {
    if (!token) return;
    try {
      const [types, catalog, users] = await Promise.all([
        fetchProdutividadeActivityTypes(token),
        fetchProdutividadeActivities(token, {
          companyId: defaultCompanyId ?? undefined,
        }),
        fetchProdutividadeUsers(token, 2),
      ]);
      setActivityTypes(types);
      setActivityCatalog(catalog);
      setFiscais(users);
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    }
  };

  const loadFiscalActivities = async () => {
    if (!token) return;
    setLoadingActivities(true);
    try {
      const activities = await fetchFiscalActivities(token, {
        companyId: defaultCompanyId ?? undefined,
        validated: activeTab === 1,
      });
      setFiscalActivities(activities);
      setSelectedActivityIds([]);
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    } finally {
      setLoadingActivities(false);
    }
  };

  const loadCatalog = async () => {
    if (!token) return;
    setLoadingCatalog(true);
    try {
      const catalog = await fetchProdutividadeActivities(token, {
        companyId: defaultCompanyId ?? undefined,
      });
      setActivityCatalog(catalog);
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    } finally {
      setLoadingCatalog(false);
    }
  };

  useEffect(() => {
    fetchInitialData();
  }, [token]);

  useEffect(() => {
    loadFiscalActivities();
  }, [token, activeTab]);

  useEffect(() => {
    if (defaultCompanyId && !activityForm.companyId) {
      setActivityForm((prev) => ({ ...prev, companyId: String(defaultCompanyId) }));
    }
  }, [defaultCompanyId, activityForm.companyId]);

  const toggleSelectActivity = (activityId: number) => {
    setSelectedActivityIds((prev) =>
      prev.includes(activityId)
        ? prev.filter((id) => id !== activityId)
        : [...prev, activityId]
    );
  };

  const handleConfirm = async () => {
    if (!token) return;
    if (selectedActivityIds.length === 0) {
      showNotification('Selecione ao menos uma atividade para confirmar.', 'warning');
      return;
    }
    try {
      await confirmFiscalActivities(token, selectedActivityIds);
      showNotification('Atividades confirmadas com sucesso.', 'success');
      await loadFiscalActivities();
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    }
  };

  const handleDeleteFiscalActivity = async (activityId: number) => {
    if (!token) return;
    try {
      await deleteFiscalActivity(token, activityId);
      showNotification('Atividade excluída com sucesso.', 'success');
      await loadFiscalActivities();
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    }
  };

  const handleSaveCatalogActivity = async () => {
    if (!token) return;
    const parsedPoints = Number(activityForm.points);
    const parsedActivityTypeId = Number(activityForm.activityTypeId);
    const parsedCompanyId = Number(activityForm.companyId || defaultCompanyId || 0);

    if (!activityForm.description || !parsedPoints || !parsedActivityTypeId) {
      showNotification('Preencha os campos obrigatórios da atividade.', 'warning');
      return;
    }

    const payload = {
      description: activityForm.description,
      points: parsedPoints,
      isActive: activityForm.isActive,
      hasMultiplicator: activityForm.hasMultiplicator,
      isOsActivity: activityForm.isOsActivity,
      activityTypeId: parsedActivityTypeId,
      companyId: parsedCompanyId,
    };

    try {
      if (activityForm.id) {
        await updateProdutividadeActivity(token, activityForm.id, payload);
        showNotification('Atividade atualizada com sucesso.', 'success');
      } else {
        await createProdutividadeActivity(token, payload);
        showNotification('Atividade cadastrada com sucesso.', 'success');
      }
      setActivityForm({
        id: null,
        description: '',
        points: '',
        activityTypeId: '',
        companyId: parsedCompanyId ? String(parsedCompanyId) : '',
        isActive: true,
        hasMultiplicator: false,
        isOsActivity: false,
      });
      await loadCatalog();
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    }
  };

  const handleEditCatalog = (activity: ProdutividadeActivity) => {
    setActivityForm({
      id: activity.id,
      description: activity.description,
      points: String(activity.points),
      activityTypeId: String(activity.activityTypeId),
      companyId: String(activity.companyId),
      isActive: activity.isActive,
      hasMultiplicator: activity.hasMultiplicator,
      isOsActivity: activity.isOsActivity,
    });
  };

  const handleDeleteCatalog = async (activityId: number) => {
    if (!token) return;
    try {
      await deleteProdutividadeActivity(token, activityId);
      showNotification('Atividade removida com sucesso.', 'success');
      await loadCatalog();
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    }
  };

  const handleCreateDeducao = async () => {
    if (!token) return;
    const activityId = Number(deducaoForm.activityId);
    const fiscalId = Number(deducaoForm.fiscalId);
    const quantity = deducaoForm.quantity ? Number(deducaoForm.quantity) : undefined;

    if (!activityId || !fiscalId || !deducaoForm.completedAt) {
      showNotification('Preencha os campos obrigatórios da dedução.', 'warning');
      return;
    }

    try {
      await createFiscalActivity(
        {
          activityId,
          fiscalId,
          companyId: defaultCompanyId ?? 0,
          completedAt: deducaoForm.completedAt,
          document: null,
          protocol: null,
          cpfCnpj: null,
          rc: null,
          value: null,
          quantity: quantity ?? null,
          notes: deducaoForm.notes || null,
          attachments: [],
        },
        token
      );
      showNotification('Dedução cadastrada com sucesso.', 'success');
      setDeducaoForm({
        activityId: '',
        fiscalId: '',
        completedAt: '',
        notes: '',
        quantity: '',
      });
      await loadFiscalActivities();
    } catch (error) {
      showNotification(getErrorMessage(error), 'error');
    }
  };

  return (
    <Box sx={{ bgcolor: '#f2f2f2', minHeight: '100vh', pb: 6 }}>
      <Box
        sx={{
          bgcolor: '#009688',
          color: '#fff',
          px: { xs: 2, md: 4 },
          py: 2,
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
        }}
      >
        <Typography variant="subtitle1" sx={{ fontWeight: 600 }}>
          FISCALIZAÇÃO URBANA - PRODUTIVIDADE
        </Typography>
        <Button
          variant="text"
          sx={{ color: '#fff', fontWeight: 600 }}
          startIcon={<CheckCircleOutline />}
        >
          SAIR
        </Button>
      </Box>

      <Box sx={{ px: { xs: 2, md: 4 }, mt: -3 }}>
        <Paper sx={{ p: 3 }}>
          <Tabs
            value={activeTab}
            onChange={(_, value) => setActiveTab(value)}
            textColor="primary"
            indicatorColor="primary"
          >
            <Tab label="ATIVIDADES A VALIDAR" />
            <Tab label="VALIDADAS" />
          </Tabs>

          <Box
            sx={{
              mt: 2,
              display: 'flex',
              flexWrap: 'wrap',
              gap: 2,
              alignItems: 'center',
            }}
          >
            <Button variant="contained" color="warning">
              Relatório Descritivo
            </Button>
            <Button variant="contained" color="success">
              Relatório de Produtividade
            </Button>
            <Button variant="contained" color="primary">
              Relatório de Pontuação
            </Button>
            <Button
              variant="contained"
              color="success"
              startIcon={<TaskAlt />}
              onClick={handleConfirm}
              disabled={!selectedActivityIds.length}
            >
              Confirmar
            </Button>
            <Button
              variant="contained"
              color="success"
              startIcon={<Refresh />}
              onClick={loadFiscalActivities}
              disabled={loadingActivities}
            >
              Atualizar
            </Button>
            <Box sx={{ flex: 1 }} />
            <Button variant="text" startIcon={<FilterList />}>
              Filtrar
            </Button>
            <TextField
              label="Pesquisar"
              variant="standard"
              sx={{ minWidth: 220 }}
            />
          </Box>

          <TableContainer sx={{ mt: 3 }}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell padding="checkbox" />
                  <TableCell>ID</TableCell>
                  <TableCell>Tipo</TableCell>
                  <TableCell>Data</TableCell>
                  <TableCell>N° protocolo</TableCell>
                  <TableCell>N° documento</TableCell>
                  <TableCell>RC</TableCell>
                  <TableCell>CPF/CNPJ</TableCell>
                  <TableCell>Pontos</TableCell>
                  <TableCell>Quantidade</TableCell>
                  <TableCell>Valor</TableCell>
                  <TableCell>Fiscal</TableCell>
                  <TableCell>Observação</TableCell>
                  <TableCell>Opções</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {fiscalActivities.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell padding="checkbox">
                      <Checkbox
                        checked={selectedActivityIds.includes(row.id)}
                        onChange={() => toggleSelectActivity(row.id)}
                      />
                    </TableCell>
                    <TableCell>{row.id}</TableCell>
                    <TableCell>{row.activityName}</TableCell>
                    <TableCell>
                      {row.completedAt
                        ? new Date(row.completedAt).toLocaleDateString()
                        : '--'}
                    </TableCell>
                    <TableCell>{row.protocol ?? '--'}</TableCell>
                    <TableCell>{row.document ?? '--'}</TableCell>
                    <TableCell>{row.rc ?? '--'}</TableCell>
                    <TableCell>{row.cpfCnpj ?? '--'}</TableCell>
                    <TableCell>{row.totalPoints ?? 0}</TableCell>
                    <TableCell>{row.quantity ?? 0}</TableCell>
                    <TableCell>
                      {row.value ? row.value.toLocaleString('pt-BR') : '--'}
                    </TableCell>
                    <TableCell>{row.fiscalName}</TableCell>
                    <TableCell>{row.notes ?? '--'}</TableCell>
                    <TableCell>
                      <IconButton
                        color="error"
                        onClick={() => handleDeleteFiscalActivity(row.id)}
                      >
                        <DeleteOutline fontSize="small" />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        </Paper>

        <Paper sx={{ p: 3, mt: 4 }}>
          <Typography variant="h6" sx={{ mb: 2 }}>
            Cadastrar Dedução
          </Typography>
          <Box
            sx={{
              display: 'grid',
              gridTemplateColumns: { xs: '1fr', md: '1fr 1fr' },
              gap: 2,
            }}
          >
            <FormControl>
              <InputLabel id="deducao-atividade-label">Dedução *</InputLabel>
              <Select
                labelId="deducao-atividade-label"
                label="Dedução *"
                value={deducaoForm.activityId}
                onChange={(event) =>
                  setDeducaoForm((prev) => ({
                    ...prev,
                    activityId: String(event.target.value),
                  }))
                }
              >
                {deductionActivities.map((activity) => (
                  <MenuItem key={activity.id} value={activity.id}>
                    {activity.description}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
            <FormControl>
              <InputLabel id="deducao-fiscal-label">Fiscal *</InputLabel>
              <Select
                labelId="deducao-fiscal-label"
                label="Fiscal *"
                value={deducaoForm.fiscalId}
                onChange={(event) =>
                  setDeducaoForm((prev) => ({
                    ...prev,
                    fiscalId: String(event.target.value),
                  }))
                }
              >
                {fiscais.map((fiscal) => (
                  <MenuItem key={fiscal.id} value={fiscal.id}>
                    {fiscal.name}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
            <TextField
              label="Data de Vigência *"
              type="date"
              InputLabelProps={{ shrink: true }}
              value={deducaoForm.completedAt}
              onChange={(event) =>
                setDeducaoForm((prev) => ({
                  ...prev,
                  completedAt: event.target.value,
                }))
              }
            />
            <TextField
              label="Quantidade"
              type="number"
              value={deducaoForm.quantity}
              onChange={(event) =>
                setDeducaoForm((prev) => ({
                  ...prev,
                  quantity: event.target.value,
                }))
              }
            />
            <TextField
              label="Justificativa"
              value={deducaoForm.notes}
              onChange={(event) =>
                setDeducaoForm((prev) => ({
                  ...prev,
                  notes: event.target.value,
                }))
              }
            />
          </Box>
          <Box sx={{ mt: 3, display: 'flex', gap: 2 }}>
            <Button variant="contained" color="warning" onClick={handleCreateDeducao}>
              Cadastrar
            </Button>
            <Button variant="outlined">Voltar</Button>
          </Box>
        </Paper>

        <Paper sx={{ p: 3, mt: 4 }}>
          <Typography variant="h6" sx={{ mb: 2 }}>
            Cadastro de Atividades
          </Typography>
          <Box
            sx={{
              display: 'grid',
              gridTemplateColumns: { xs: '1fr', md: '1fr 1fr' },
              gap: 2,
              maxWidth: 720,
            }}
          >
            <TextField
              label="Nome *"
              placeholder="Nome da atividade"
              value={activityForm.description}
              onChange={(event) =>
                setActivityForm((prev) => ({
                  ...prev,
                  description: event.target.value,
                }))
              }
            />
            <TextField
              label="Pontos *"
              type="number"
              placeholder="1.0"
              value={activityForm.points}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, points: event.target.value }))
              }
            />
            <FormControl>
              <InputLabel id="atividade-tipo-label">
                Tipo de Contabilização *
              </InputLabel>
              <Select
                labelId="atividade-tipo-label"
                label="Tipo de Contabilização *"
                value={activityForm.activityTypeId}
                onChange={(event) =>
                  setActivityForm((prev) => ({
                    ...prev,
                    activityTypeId: String(event.target.value),
                  }))
                }
              >
                {activityTypes.map((type) => (
                  <MenuItem key={type.id} value={type.id}>
                    {type.name}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
            <TextField
              label="Empresa (ID)"
              type="number"
              value={activityForm.companyId}
              onChange={(event) =>
                setActivityForm((prev) => ({
                  ...prev,
                  companyId: event.target.value,
                }))
              }
            />
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Checkbox
                checked={activityForm.isActive}
                onChange={(event) =>
                  setActivityForm((prev) => ({
                    ...prev,
                    isActive: event.target.checked,
                  }))
                }
              />
              <Typography variant="body2">Ativo</Typography>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Checkbox
                checked={activityForm.hasMultiplicator}
                onChange={(event) =>
                  setActivityForm((prev) => ({
                    ...prev,
                    hasMultiplicator: event.target.checked,
                  }))
                }
              />
              <Typography variant="body2">Aceita multiplicador</Typography>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Checkbox
                checked={activityForm.isOsActivity}
                onChange={(event) =>
                  setActivityForm((prev) => ({
                    ...prev,
                    isOsActivity: event.target.checked,
                  }))
                }
              />
              <Typography variant="body2">Atividade OS</Typography>
            </Box>
          </Box>
          <Box sx={{ mt: 3, display: 'flex', gap: 2 }}>
            <Button variant="contained" color="warning" onClick={handleSaveCatalogActivity}>
              {activityForm.id ? 'Atualizar' : 'Cadastrar'}
            </Button>
            <Button variant="outlined">Voltar</Button>
          </Box>

          <Paper variant="outlined" sx={{ mt: 4, p: 2 }}>
            <Typography variant="subtitle1" sx={{ mb: 2 }}>
              Atividades cadastradas
            </Typography>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>ID</TableCell>
                  <TableCell>Tipo</TableCell>
                  <TableCell>Tipo de Cálculo</TableCell>
                  <TableCell>Pontos</TableCell>
                  <TableCell>Ativo</TableCell>
                  <TableCell>Opções</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {activityCatalog.map((activity) => (
                  <TableRow key={activity.id}>
                    <TableCell>{activity.id}</TableCell>
                    <TableCell>{activity.description}</TableCell>
                    <TableCell>{calculationLabel(activity.calculationType)}</TableCell>
                    <TableCell>{activity.points}</TableCell>
                    <TableCell>{activity.isActive ? '✔' : '--'}</TableCell>
                    <TableCell>
                      <IconButton
                        color="primary"
                        onClick={() => handleEditCatalog(activity)}
                      >
                        <Edit fontSize="small" />
                      </IconButton>
                      <IconButton
                        color="error"
                        onClick={() => handleDeleteCatalog(activity.id)}
                        disabled={loadingCatalog}
                      >
                        <DeleteOutline fontSize="small" />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Paper>
        </Paper>
      </Box>
    </Box>
  );
}
