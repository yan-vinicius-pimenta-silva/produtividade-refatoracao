import { useState } from 'react';
import {
  Box,
  Button,
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
  FilterList,
  Refresh,
  TaskAlt,
} from '@mui/icons-material';

export default function Produtividade() {
  const [activeTab, setActiveTab] = useState(0);

  const rows = [
    {
      id: 2868,
      tipo: 'Autos de infração e imposição de multa',
      data: '06/01/2026',
      protocolo: '0955.560.0000115/2026',
      documento: '05269966',
      rc: '11.6.06.30.043.000',
      cpf: '12.625.069/0001-90',
      pontos: '173.4',
      quantidade: '115.6',
      valor: '44.424,00',
      fiscal: 'Pedro de Melo',
      obs: '--',
    },
    {
      id: 2869,
      tipo: 'Taxa de licença para publicidade',
      data: '07/01/2026',
      protocolo: '16138/2025',
      documento: '05270031',
      rc: '--',
      cpf: '16.670.085/1399-00',
      pontos: '6',
      quantidade: '3',
      valor: '1.156,42',
      fiscal: 'Sisuley Zaniboni Gouveia',
      obs: 'Localiza Araras',
    },
  ];

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
            <Button variant="contained" color="success" startIcon={<TaskAlt />}>
              Confirmar
            </Button>
            <Button variant="contained" color="success" startIcon={<Refresh />}>
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
                </TableRow>
              </TableHead>
              <TableBody>
                {rows.map((row) => (
                  <TableRow key={row.id}>
                    <TableCell>{row.id}</TableCell>
                    <TableCell>{row.tipo}</TableCell>
                    <TableCell>{row.data}</TableCell>
                    <TableCell>{row.protocolo}</TableCell>
                    <TableCell>{row.documento}</TableCell>
                    <TableCell>{row.rc}</TableCell>
                    <TableCell>{row.cpf}</TableCell>
                    <TableCell>{row.pontos}</TableCell>
                    <TableCell>{row.quantidade}</TableCell>
                    <TableCell>{row.valor}</TableCell>
                    <TableCell>{row.fiscal}</TableCell>
                    <TableCell>{row.obs}</TableCell>
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
            <TextField label="Dedução *" placeholder="Escolha..." />
            <TextField label="Fiscal *" placeholder="Escolha..." />
            <TextField label="Data de Vigência *" placeholder="dd/mm/aaaa" />
            <TextField label="Justificativa" />
          </Box>
          <Box sx={{ mt: 3, display: 'flex', gap: 2 }}>
            <Button variant="contained" color="warning">
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
            <TextField label="Nome *" placeholder="Nome da atividade" />
            <TextField label="Descrição *" placeholder="Descrição" />
            <TextField label="Pontos *" type="number" placeholder="1.0" />
            <TextField label="Tipo de Contabilização *" placeholder="Escolha..." />
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Typography variant="body2">Ativo:</Typography>
              <Box
                component="span"
                sx={{ width: 18, height: 18, border: '1px solid #777' }}
              />
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Typography variant="body2">Aceita multiplicador:</Typography>
              <Box
                component="span"
                sx={{ width: 18, height: 18, border: '1px solid #777' }}
              />
            </Box>
          </Box>
          <Box sx={{ mt: 3, display: 'flex', gap: 2 }}>
            <Button variant="contained" color="warning">
              Cadastrar
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
                <TableRow>
                  <TableCell>159</TableCell>
                  <TableCell>
                    Taxa decorrente do poder de polícia administrativa
                  </TableCell>
                  <TableCell>UFESP</TableCell>
                  <TableCell>2</TableCell>
                  <TableCell>✔</TableCell>
                  <TableCell>✎</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </Paper>
        </Paper>
      </Box>
    </Box>
  );
}
