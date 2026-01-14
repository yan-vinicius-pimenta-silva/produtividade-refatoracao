import { useMemo, useState } from 'react';
import {
  Box,
  Button,
  Card,
  CardContent,
  Divider,
  MenuItem,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TextField,
  Typography,
} from '@mui/material';
import { FilterList } from '@mui/icons-material';

const columns = [
  'ID',
  'Tipo',
  'Data',
  'Nº protocolo',
  'Nº documento',
  'RC',
  'CPF/CNPJ',
  'Pontos',
  'Quantidade',
  'Valor',
  'Fiscal',
  'Documento',
  'Validação',
  'Observação',
  'Usuário Dedução',
  'Justificativa Dedução',
  'Opções',
];

const pageSizes = [10, 25, 50];
const mockRows = [
  {
    id: 4901,
    tipo: 'Dedução por treinamento',
    data: '05/01/2026',
    protocolo: '0912.001.000034/2026',
    documento: '05270012',
    rc: '11.6.01.10.001.000',
    cpfCnpj: '12.625.069/0001-90',
    pontos: 2.4,
    quantidade: 1,
    valor: 320,
    fiscal: 'Pedro de Melo',
    documentoAnexo: 'TR-021.pdf',
    validacao: 'Pendente',
    observacao: 'Treinamento anual obrigatório.',
    usuarioDeducao: 'Livia Prado',
    justificativa: 'Participação em workshop regional.',
  },
  {
    id: 4902,
    tipo: 'Dedução por afastamento',
    data: '08/01/2026',
    protocolo: '0912.001.000057/2026',
    documento: '05270045',
    rc: '--',
    cpfCnpj: '269.891.668-00',
    pontos: 3.1,
    quantidade: 2,
    valor: 880,
    fiscal: 'Sisuley Zaniboni Gouveia',
    documentoAnexo: 'AF-143.pdf',
    validacao: 'Aprovada',
    observacao: 'Afastamento médico homologado.',
    usuarioDeducao: 'Helena Costa',
    justificativa: 'Atestado médico anexado.',
  },
  {
    id: 4903,
    tipo: 'Dedução por licença médica',
    data: '12/01/2026',
    protocolo: '0912.001.000098/2026',
    documento: '05270089',
    rc: '11.6.09.21.055.000',
    cpfCnpj: '16.670.085/1399-00',
    pontos: 1.5,
    quantidade: 1,
    valor: 520,
    fiscal: 'Fernando Pagioro',
    documentoAnexo: 'LM-067.pdf',
    validacao: 'Pendente',
    observacao: 'Licença curta para tratamento.',
    usuarioDeducao: 'Rafael Azevedo',
    justificativa: 'Período inferior a 10 dias.',
  },
];

export default function DeducaoConsulta() {
  const [pageSize, setPageSize] = useState(10);
  const [searchTerm, setSearchTerm] = useState('');
  const [page, setPage] = useState(0);

  const filteredRows = useMemo(() => {
    const normalized = searchTerm.trim().toLowerCase();
    if (!normalized) {
      return mockRows;
    }

    return mockRows.filter((row) =>
      [
        row.tipo,
        row.protocolo,
        row.documento,
        row.rc,
        row.cpfCnpj,
        row.fiscal,
        row.documentoAnexo,
        row.validacao,
        row.observacao,
        row.usuarioDeducao,
        row.justificativa,
      ]
        .join(' ')
        .toLowerCase()
        .includes(normalized)
    );
  }, [searchTerm]);

  const pagedRows = useMemo(() => {
    const start = page * pageSize;
    return filteredRows.slice(start, start + pageSize);
  }, [filteredRows, page, pageSize]);

  const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));

  const handlePageSizeChange = (value: number) => {
    setPageSize(value);
    setPage(0);
  };

  const handlePrevious = () => {
    setPage((current) => Math.max(0, current - 1));
  };

  const handleNext = () => {
    setPage((current) => Math.min(totalPages - 1, current + 1));
  };

  return (
    <Box sx={{ bgcolor: '#f6f7fb', minHeight: '100vh', py: 4, px: { xs: 2, md: 4 } }}>
      <Card sx={{ borderRadius: 3, boxShadow: 3 }}>
        <CardContent sx={{ p: { xs: 3, md: 4 } }}>
          <Typography variant="h6" sx={{ fontWeight: 700, mb: 1, textTransform: 'uppercase' }}>
            Consulta de deduções
          </Typography>
          <Divider sx={{ mb: 3 }} />

          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'space-between',
              gap: 2,
              mb: 2,
            }}
          >
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <TextField
                select
                variant="standard"
                value={pageSize}
                onChange={(event) => handlePageSizeChange(Number(event.target.value))}
                sx={{ minWidth: 80 }}
              >
                {pageSizes.map((size) => (
                  <MenuItem key={size} value={size}>
                    {size}
                  </MenuItem>
                ))}
              </TextField>
              <Typography variant="body2" color="text.secondary">
                resultados por página
              </Typography>
            </Box>

            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <FilterList fontSize="small" color="action" />
              <TextField
                label="Pesquisar"
                variant="standard"
                sx={{ minWidth: 200 }}
                value={searchTerm}
                onChange={(event) => {
                  setSearchTerm(event.target.value);
                  setPage(0);
                }}
              />
            </Box>
          </Box>

          <TableContainer sx={{ mt: 3, borderRadius: 2, border: '1px solid #e0e0e0' }}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  {columns.map((column) => (
                    <TableCell key={column} sx={{ fontWeight: 600 }}>
                      {column}
                    </TableCell>
                  ))}
                </TableRow>
              </TableHead>
              <TableBody>
                {pagedRows.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={columns.length}>
                      Nenhum registro encontrado
                    </TableCell>
                  </TableRow>
                ) : (
                  pagedRows.map((row) => (
                    <TableRow key={row.id}>
                      <TableCell>{row.id}</TableCell>
                      <TableCell>{row.tipo}</TableCell>
                      <TableCell>{row.data}</TableCell>
                      <TableCell>{row.protocolo}</TableCell>
                      <TableCell>{row.documento}</TableCell>
                      <TableCell>{row.rc}</TableCell>
                      <TableCell>{row.cpfCnpj}</TableCell>
                      <TableCell>{row.pontos}</TableCell>
                      <TableCell>{row.quantidade}</TableCell>
                      <TableCell>{row.valor}</TableCell>
                      <TableCell>{row.fiscal}</TableCell>
                      <TableCell>{row.documentoAnexo}</TableCell>
                      <TableCell>{row.validacao}</TableCell>
                      <TableCell>{row.observacao}</TableCell>
                      <TableCell>{row.usuarioDeducao}</TableCell>
                      <TableCell>{row.justificativa}</TableCell>
                      <TableCell>-</TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </TableContainer>

          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'space-between',
              gap: 2,
              mt: 3,
            }}
          >
            <Typography variant="body2" color="text.secondary">
              Mostrando {pagedRows.length === 0 ? 0 : page * pageSize + 1} até{' '}
              {Math.min((page + 1) * pageSize, filteredRows.length)} de{' '}
              {filteredRows.length} registros
            </Typography>
            <Box sx={{ display: 'flex', gap: 1 }}>
              <Button variant="text" disabled={page === 0} onClick={handlePrevious}>
                Anterior
              </Button>
              <Button
                variant="text"
                disabled={page >= totalPages - 1}
                onClick={handleNext}
              >
                Próximo
              </Button>
            </Box>
          </Box>
        </CardContent>
      </Card>
    </Box>
  );
}
