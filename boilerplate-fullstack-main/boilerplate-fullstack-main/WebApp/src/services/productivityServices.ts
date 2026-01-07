import api from '../api';
import type {
  ActivityCreatePayload,
  ActivityRead,
  ActivityTypeCreatePayload,
  ActivityTypeRead,
  CompanyCreatePayload,
  CompanyRead,
  FiscalActivityCreatePayload,
  FiscalActivityRead,
  ServiceOrderCreatePayload,
  ServiceOrderRead,
  UfespRateCreatePayload,
  UfespRateRead,
} from '../interfaces';

export async function listCompanies() {
  const { data } = await api.get<CompanyRead[]>('/companies');
  return data;
}

export async function createCompany(payload: CompanyCreatePayload) {
  const { data } = await api.post<CompanyRead>('/companies', payload);
  return data;
}

export async function listActivityTypes() {
  const { data } = await api.get<ActivityTypeRead[]>('/activity-types');
  return data;
}

export async function createActivityType(payload: ActivityTypeCreatePayload) {
  const { data } = await api.post<ActivityTypeRead>('/activity-types', payload);
  return data;
}

export async function listActivities(companyId?: number) {
  const endpoint = companyId ? `/activities?companyId=${companyId}` : '/activities';
  const { data } = await api.get<ActivityRead[]>(endpoint);
  return data;
}

export async function createActivity(payload: ActivityCreatePayload) {
  const { data } = await api.post<ActivityRead>('/activities', payload);
  return data;
}

export async function listUfespRates() {
  const { data } = await api.get<UfespRateRead[]>('/ufesp-rates');
  return data;
}

export async function createUfespRate(payload: UfespRateCreatePayload) {
  const { data } = await api.post<UfespRateRead>('/ufesp-rates', payload);
  return data;
}

export async function listFiscalActivities(
  companyId?: number,
  fiscalUserId?: number
) {
  const params = new URLSearchParams();
  if (companyId) params.set('companyId', companyId.toString());
  if (fiscalUserId) params.set('fiscalUserId', fiscalUserId.toString());
  const endpoint = `/fiscal-activities${params.toString() ? `?${params}` : ''}`;
  const { data } = await api.get<FiscalActivityRead[]>(endpoint);
  return data;
}

export async function createFiscalActivity(payload: FiscalActivityCreatePayload) {
  const { data } = await api.post<FiscalActivityRead>('/fiscal-activities', payload);
  return data;
}

export async function listServiceOrders(companyId?: number) {
  const endpoint = companyId
    ? `/service-orders?companyId=${companyId}`
    : '/service-orders';
  const { data } = await api.get<ServiceOrderRead[]>(endpoint);
  return data;
}

export async function createServiceOrder(payload: ServiceOrderCreatePayload) {
  const { data } = await api.post<ServiceOrderRead>('/service-orders', payload);
  return data;
}
