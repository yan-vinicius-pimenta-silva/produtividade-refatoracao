export type ProdutividadeActivityPayload = {
  activityId: number;
  fiscalId: number;
  companyId: number;
  completedAt: string;
  document?: string | null;
  protocol?: string | null;
  cpfCnpj?: string | null;
  rc?: string | null;
  value?: number | null;
  quantity?: number | null;
  notes?: string | null;
  attachments: string[];
};
