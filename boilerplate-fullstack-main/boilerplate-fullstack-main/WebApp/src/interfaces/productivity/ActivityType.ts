export interface ActivityTypeCreatePayload {
  name: string;
}

export interface ActivityTypeRead {
  id: number;
  name: string;
  active: boolean;
}
