export type AuthResponse = {
  token: string;
  user: {
    id: number;
    login: string;
    name: string;
    role: number;
    companyId: number;
    companyName: string;
  };
};

export type PointsSummary = {
  pointsPontuacao: number;
  pointsDeducao: number;
  pointsUfesp: number;
  pointsTotal: number;
  totalCollected: number;
  remainingBalance: number;
};

const API_BASE = "/api";

export async function login(token: string, loginOverride?: string): Promise<AuthResponse> {
  const response = await fetch(`${API_BASE}/auth/login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ token, login: loginOverride })
  });

  if (!response.ok) {
    throw new Error("Falha no login.");
  }

  return response.json();
}

export async function fetchPoints(fiscalId: number, period: string): Promise<PointsSummary> {
  const response = await fetch(`${API_BASE}/points?fiscalId=${fiscalId}&period=${period}`);
  if (!response.ok) {
    throw new Error("Falha ao buscar pontuação.");
  }
  return response.json();
}

export async function createFiscalActivity(payload: Record<string, unknown>): Promise<void> {
  const response = await fetch(`${API_BASE}/fiscal-activities`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(payload)
  });

  if (!response.ok) {
    throw new Error("Falha ao lançar atividade.");
  }
}
