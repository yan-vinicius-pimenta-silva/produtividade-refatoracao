export type ProdutividadeFiscalActivitySummary = {
  id: number;
  activityId: number;
  activityName: string;
  calculationType: number;
  completedAt?: string | null;
  protocol?: string | null;
  document?: string | null;
  rc?: string | null;
  cpfCnpj?: string | null;
  totalPoints?: number | null;
  quantity?: number | null;
  value?: number | null;
  fiscalName: string;
  notes?: string | null;
  validatedAt?: string | null;
};
