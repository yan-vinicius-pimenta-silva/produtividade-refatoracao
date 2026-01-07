export interface ServiceOrderCreatePayload {
  companyId: number;
  activityId?: number | null;
  fiscalUserId?: number | null;
  chiefUserId?: number | null;
  description: string;
  observation?: string | null;
  rc?: string | null;
  documentNumber?: string | null;
  protocolNumber?: string | null;
  dueDate?: string | null;
  completionDate?: string | null;
}

export interface ServiceOrderRead {
  id: number;
  companyId: number;
  activityId?: number | null;
  fiscalUserId?: number | null;
  chiefUserId?: number | null;
  description: string;
  observation?: string | null;
  rc?: string | null;
  documentNumber?: string | null;
  protocolNumber?: string | null;
  isResponded: boolean;
  validated: boolean;
  excluded: boolean;
  dueDate?: string | null;
  completionDate?: string | null;
  createdAt: string;
}
