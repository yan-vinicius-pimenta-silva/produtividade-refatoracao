export type ParametroAtividade = {
  id: number;
  name: string;
  calculationType: string;
  points: number;
  active: boolean;
};

export type ParametroUnidadeFiscal = {
  ano: string;
  descricao: string;
  valor: string;
  ativo: boolean;
};

export const initialActivities: ParametroAtividade[] = [
  {
    id: 101,
    name: 'Vistoria de Regularização',
    calculationType: 'Por ocorrência',
    points: 2.5,
    active: true,
  },
  {
    id: 102,
    name: 'Lavratura de Auto',
    calculationType: 'Por ocorrência',
    points: 8,
    active: true,
  },
  {
    id: 103,
    name: 'Atendimento em Plantão',
    calculationType: 'Mensal',
    points: 1.2,
    active: false,
  },
];

export const initialUfespRows: ParametroUnidadeFiscal[] = [
  {
    ano: '2024',
    descricao: 'UFESP 2024',
    valor: '35,36',
    ativo: false,
  },
  {
    ano: '2025',
    descricao: 'UFESP 2025',
    valor: '37,02',
    ativo: false,
  },
  {
    ano: '2026',
    descricao: 'UFESP 2026',
    valor: '38,42',
    ativo: true,
  },
];
