import { useState } from "react";
import { createFiscalActivity, fetchPoints, login, PointsSummary } from "./api";

const defaultPeriod = new Date().toISOString().slice(0, 7);

export default function App() {
  const [token, setToken] = useState("");
  const [loginOverride, setLoginOverride] = useState("");
  const [auth, setAuth] = useState<{ token: string; userId: number } | null>(null);
  const [period, setPeriod] = useState(defaultPeriod);
  const [points, setPoints] = useState<PointsSummary | null>(null);
  const [message, setMessage] = useState<string | null>(null);

  const [activityForm, setActivityForm] = useState({
    activityId: "",
    fiscalId: "",
    companyId: "",
    completedAt: new Date().toISOString().slice(0, 10),
    document: "",
    protocol: "",
    cpfCnpj: "",
    rc: "",
    value: "",
    quantity: "",
    notes: ""
  });

  async function handleLogin() {
    try {
      const response = await login(token, loginOverride || undefined);
      setAuth({ token: response.token, userId: response.user.id });
      setMessage(`Bem-vindo, ${response.user.name}.`);
    } catch (error) {
      setMessage((error as Error).message);
    }
  }

  async function handleFetchPoints() {
    if (!auth) {
      setMessage("Realize o login antes de consultar a pontuação.");
      return;
    }
    try {
      const summary = await fetchPoints(auth.userId, period);
      setPoints(summary);
      setMessage(null);
    } catch (error) {
      setMessage((error as Error).message);
    }
  }

  async function handleActivitySubmit() {
    const payload = {
      activityId: Number(activityForm.activityId),
      fiscalId: Number(activityForm.fiscalId),
      companyId: Number(activityForm.companyId),
      completedAt: activityForm.completedAt,
      document: activityForm.document || null,
      protocol: activityForm.protocol || null,
      cpfCnpj: activityForm.cpfCnpj || null,
      rc: activityForm.rc || null,
      value: activityForm.value ? Number(activityForm.value) : null,
      quantity: activityForm.quantity ? Number(activityForm.quantity) : null,
      notes: activityForm.notes || null,
      attachments: [] as string[]
    };

    try {
      await createFiscalActivity(payload);
      setMessage("Atividade lançada com sucesso.");
    } catch (error) {
      setMessage((error as Error).message);
    }
  }

  return (
    <div style={{ fontFamily: "Arial, sans-serif", padding: "2rem", maxWidth: 960, margin: "0 auto" }}>
      <h1>Produtividade - Refatoração React + .NET</h1>

      <section style={{ marginBottom: "2rem" }}>
        <h2>Login</h2>
        <div style={{ display: "grid", gap: "0.5rem", maxWidth: 480 }}>
          <label>
            Token JWT (opcional)
            <input value={token} onChange={(event) => setToken(event.target.value)} />
          </label>
          <label>
            Login de desenvolvimento
            <input value={loginOverride} onChange={(event) => setLoginOverride(event.target.value)} />
          </label>
          <button onClick={handleLogin}>Entrar</button>
        </div>
      </section>

      <section style={{ marginBottom: "2rem" }}>
        <h2>Lançar atividade</h2>
        <div style={{ display: "grid", gap: "0.5rem", maxWidth: 640 }}>
          <label>
            Atividade (ID)
            <input
              value={activityForm.activityId}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, activityId: event.target.value }))
              }
            />
          </label>
          <label>
            Fiscal (ID)
            <input
              value={activityForm.fiscalId}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, fiscalId: event.target.value }))
              }
            />
          </label>
          <label>
            Empresa (ID)
            <input
              value={activityForm.companyId}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, companyId: event.target.value }))
              }
            />
          </label>
          <label>
            Data de conclusão
            <input
              type="date"
              value={activityForm.completedAt}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, completedAt: event.target.value }))
              }
            />
          </label>
          <label>
            Documento
            <input
              value={activityForm.document}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, document: event.target.value }))
              }
            />
          </label>
          <label>
            Protocolo
            <input
              value={activityForm.protocol}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, protocol: event.target.value }))
              }
            />
          </label>
          <label>
            CPF/CNPJ
            <input
              value={activityForm.cpfCnpj}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, cpfCnpj: event.target.value }))
              }
            />
          </label>
          <label>
            RC
            <input
              value={activityForm.rc}
              onChange={(event) => setActivityForm((prev) => ({ ...prev, rc: event.target.value }))}
            />
          </label>
          <label>
            Valor (UFESP)
            <input
              value={activityForm.value}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, value: event.target.value }))
              }
            />
          </label>
          <label>
            Quantidade
            <input
              value={activityForm.quantity}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, quantity: event.target.value }))
              }
            />
          </label>
          <label>
            Observação
            <input
              value={activityForm.notes}
              onChange={(event) =>
                setActivityForm((prev) => ({ ...prev, notes: event.target.value }))
              }
            />
          </label>
          <button onClick={handleActivitySubmit}>Salvar atividade</button>
        </div>
      </section>

      <section style={{ marginBottom: "2rem" }}>
        <h2>Consultar pontuação</h2>
        <div style={{ display: "flex", gap: "0.5rem", alignItems: "center" }}>
          <input type="month" value={period} onChange={(event) => setPeriod(event.target.value)} />
          <button onClick={handleFetchPoints}>Buscar</button>
        </div>
        {points && (
          <div style={{ marginTop: "1rem" }}>
            <p>Pontuação: {points.pointsPontuacao}</p>
            <p>Dedução: {points.pointsDeducao}</p>
            <p>UFESP: {points.pointsUfesp}</p>
            <p>Total: {points.pointsTotal}</p>
            <p>Arrecadado: {points.totalCollected}</p>
            <p>Saldo: {points.remainingBalance}</p>
          </div>
        )}
      </section>

      {message && <p style={{ color: "#b00020" }}>{message}</p>}
    </div>
  );
}
