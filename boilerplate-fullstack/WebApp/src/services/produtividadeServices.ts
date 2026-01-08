import type {
  ProdutividadeActivityPayload,
  ProdutividadeAuthResponse,
  ProdutividadeLoginPayload,
  ProdutividadePointsSummary,
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
