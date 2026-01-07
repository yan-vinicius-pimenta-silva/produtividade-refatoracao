export interface FiscalActivityCreatePayload {
  activityId: number;
  companyId: number;
  fiscalUserId: number;
  completionDate: string;
  documentNumber?: string | null;
  protocolNumber?: string | null;
  rc?: string | null;
  cpfCnpj?: string | null;
  value?: number | null;
  quantity?: number | null;
  observation?: string | null;
}

export interface FiscalActivityRead {
  id: number;
  activityId: number;
  companyId: number;
  fiscalUserId: number;
  documentNumber?: string | null;
  protocolNumber?: string | null;
  rc?: string | null;
  cpfCnpj?: string | null;
  ufespYear?: number | null;
  ufespValue?: number | null;
  quantity?: number | null;
  pointsTotal?: number | null;
  value?: number | null;
  observation?: string | null;
  validated: boolean;
  completionDate: string;
  createdAt: string;
}
