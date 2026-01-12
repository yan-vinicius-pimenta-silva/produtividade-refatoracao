import api from '../../api';
import type {
  SystemResourcesPagination,
  SystemResource,
} from '../../interfaces';

type SystemResourcesResponse =
  | SystemResourcesPagination
  | {
      totalItems?: number;
      TotalItems?: number;
      page?: number;
      Page?: number;
      pageSize?: number;
      PageSize?: number;
      totalPages?: number;
      TotalPages?: number;
      data?: SystemResource[];
      Data?: SystemResource[];
    };

type SystemResourcesSelectResponse =
  | SystemResource[]
  | {
      data?: SystemResource[];
      Data?: SystemResource[];
    };

const normalizeSystemResourcesPagination = (
  response: SystemResourcesResponse,
  fallbackPage: number,
  fallbackPageSize: number
): SystemResourcesPagination => {
  const payload = response ?? {};
  const data = Array.isArray(payload.data)
    ? payload.data
    : Array.isArray(payload.Data)
      ? payload.Data
      : [];

  return {
    totalItems:
      payload.totalItems ?? payload.TotalItems ?? data.length ?? 0,
    page: payload.page ?? payload.Page ?? fallbackPage,
    pageSize: payload.pageSize ?? payload.PageSize ?? fallbackPageSize,
    totalPages: payload.totalPages ?? payload.TotalPages ?? 0,
    data,
  };
};

const normalizeSystemResourcesSelect = (
  response: SystemResourcesSelectResponse
): SystemResource[] => {
  if (Array.isArray(response)) {
    return response;
  }

  if (response && typeof response === 'object') {
    if (Array.isArray(response.data)) {
      return response.data;
    }

    if (Array.isArray(response.Data)) {
      return response.Data;
    }
  }

  return [];
};

export async function listSystemResources(
  pageNumber = 1,
  pageSize = 10,
  searchKey: string = ''
) {
  const defaultParams = `page=${pageNumber}&pageSize=${pageSize}`;

  const endpoint = searchKey
    ? `/resources/search?key=${encodeURIComponent(searchKey)}&${defaultParams}`
    : `/resources?${defaultParams}`;

  const { data } = await api.get<SystemResourcesResponse>(endpoint);
  return normalizeSystemResourcesPagination(data, pageNumber, pageSize);
}

export async function listSystemResourcesForSelect() {
  const { data } = await api.get<SystemResourcesSelectResponse>(
    '/resources/options'
  );
  return normalizeSystemResourcesSelect(data);
}

export async function listSystemResourceById(id: number) {
  const { data } = await api.get<SystemResource>(`/resources/${id}`);
  return data;
}
