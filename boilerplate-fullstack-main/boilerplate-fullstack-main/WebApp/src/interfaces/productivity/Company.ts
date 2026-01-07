export interface CompanyCreatePayload {
  name: string;
  email?: string | null;
  secretary?: string | null;
  division?: string | null;
  phone?: string | null;
  logoUrl?: string | null;
  parametersJson?: string | null;
}

export interface CompanyRead {
  id: number;
  name: string;
  email?: string | null;
  secretary?: string | null;
  division?: string | null;
  phone?: string | null;
  logoUrl?: string | null;
  parametersJson?: string | null;
  active: boolean;
  deleted: boolean;
}
