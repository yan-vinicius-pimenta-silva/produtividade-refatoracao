export interface ActivityCreatePayload {
  companyId: number;
  activityTypeId: number;
  name: string;
  pointsBase: number;
}

export interface ActivityRead {
  id: number;
  companyId: number;
  activityTypeId: number;
  name: string;
  pointsBase: number;
  active: boolean;
  deleted: boolean;
}
