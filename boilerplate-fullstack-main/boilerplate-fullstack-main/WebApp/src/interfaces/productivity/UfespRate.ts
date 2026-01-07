export interface UfespRateCreatePayload {
  year: number;
  value: number;
  active?: boolean;
}

export interface UfespRateRead {
  id: number;
  year: number;
  value: number;
  active: boolean;
}
