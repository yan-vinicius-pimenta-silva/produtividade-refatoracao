import type {
  ProdutividadeActivity,
  ProdutividadeActivityType,
  ProdutividadeActivityPayload,
  ProdutividadeAuthResponse,
  ProdutividadeFiscalActivitySummary,
  ProdutividadeLoginPayload,
  ProdutividadePointsSummary,
  ProdutividadeUserSummary,
} from '../interfaces';

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://localhost:5209/api';

async function handleResponse<T>(response: Response, errorMessage: string): Promise<T> {
  if (!response.ok) {
    throw new Error(errorMessage);
  }

  return (await response.json()) as T;
}

export async function produtividadeLogin(
  payload: ProdutividadeLoginPayload
): Promise<ProdutividadeAuthResponse> {
  const response = await fetch(`${API_BASE}/produtividade/auth/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  });

  return handleResponse<ProdutividadeAuthResponse>(response, 'Falha no login.');
}

export async function fetchProdutividadePoints(
  fiscalId: number,
  period: string,
  token: string
): Promise<ProdutividadePointsSummary> {
  const response = await fetch(
    `${API_BASE}/produtividade/points?fiscalId=${fiscalId}&period=${period}`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    }
  );

  return handleResponse<ProdutividadePointsSummary>(
    response,
    'Falha ao buscar pontuação.'
  );
}

export async function createFiscalActivity(
  payload: ProdutividadeActivityPayload,
  token: string
): Promise<void> {
  const response = await fetch(`${API_BASE}/produtividade/fiscal-activities`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(payload),
  });

  if (!response.ok) {
    throw new Error('Falha ao lançar atividade.');
  }
}

export async function fetchFiscalActivities(
  token: string,
  options?: { companyId?: number; validated?: boolean }
): Promise<ProdutividadeFiscalActivitySummary[]> {
  const params = new URLSearchParams();
  if (options?.companyId) {
    params.set('companyId', String(options.companyId));
  }
  if (typeof options?.validated === 'boolean') {
    params.set('validated', String(options.validated));
  }

  const response = await fetch(
    `${API_BASE}/produtividade/fiscal-activities?${params.toString()}`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    }
  );

  return handleResponse<ProdutividadeFiscalActivitySummary[]>(
    response,
    'Falha ao buscar atividades.'
  );
}

export async function confirmFiscalActivities(
  token: string,
  activityIds: number[],
  validatedBy?: string
): Promise<void> {
  const response = await fetch(`${API_BASE}/produtividade/fiscal-activities/confirm`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({ activityIds, validatedBy }),
  });

  if (!response.ok) {
    throw new Error('Falha ao confirmar atividades.');
  }
}

export async function deleteFiscalActivity(
  token: string,
  activityId: number
): Promise<void> {
  const response = await fetch(
    `${API_BASE}/produtividade/fiscal-activities/${activityId}`,
    {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({}),
    }
  );

  if (!response.ok) {
    throw new Error('Falha ao excluir atividade.');
  }
}

export async function fetchProdutividadeActivities(
  token: string,
  options?: { companyId?: number }
): Promise<ProdutividadeActivity[]> {
  const params = new URLSearchParams();
  if (options?.companyId) {
    params.set('companyId', String(options.companyId));
  }

  const response = await fetch(
    `${API_BASE}/produtividade/activities?${params.toString()}`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    }
  );

  return handleResponse<ProdutividadeActivity[]>(
    response,
    'Falha ao buscar atividades cadastradas.'
  );
}

export async function createProdutividadeActivity(
  token: string,
  payload: {
    description: string;
    points: number;
    isActive: boolean;
    hasMultiplicator: boolean;
    isOsActivity: boolean;
    activityTypeId: number;
    companyId: number;
  }
): Promise<ProdutividadeActivity> {
  const response = await fetch(`${API_BASE}/produtividade/activities`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(payload),
  });

  return handleResponse<ProdutividadeActivity>(
    response,
    'Falha ao cadastrar atividade.'
  );
}

export async function updateProdutividadeActivity(
  token: string,
  activityId: number,
  payload: {
    description: string;
    points: number;
    isActive: boolean;
    hasMultiplicator: boolean;
    isOsActivity: boolean;
    activityTypeId: number;
    companyId: number;
  }
): Promise<ProdutividadeActivity> {
  const response = await fetch(
    `${API_BASE}/produtividade/activities/${activityId}`,
    {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(payload),
    }
  );

  return handleResponse<ProdutividadeActivity>(
    response,
    'Falha ao atualizar atividade.'
  );
}

export async function deleteProdutividadeActivity(
  token: string,
  activityId: number
): Promise<void> {
  const response = await fetch(
    `${API_BASE}/produtividade/activities/${activityId}`,
    {
      method: 'DELETE',
      headers: {
        Authorization: `Bearer ${token}`,
      },
    }
  );

  if (!response.ok) {
    throw new Error('Falha ao remover atividade.');
  }
}

export async function fetchProdutividadeActivityTypes(
  token: string
): Promise<ProdutividadeActivityType[]> {
  const response = await fetch(`${API_BASE}/produtividade/activity-types`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  return handleResponse<ProdutividadeActivityType[]>(
    response,
    'Falha ao buscar tipos de atividade.'
  );
}

export async function fetchProdutividadeUsers(
  token: string,
  role?: number
): Promise<ProdutividadeUserSummary[]> {
  const params = new URLSearchParams();
  if (role) {
    params.set('role', String(role));
  }

  const response = await fetch(
    `${API_BASE}/produtividade/users?${params.toString()}`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    }
  );

  return handleResponse<ProdutividadeUserSummary[]>(
    response,
    'Falha ao buscar usuários.'
  );
}
