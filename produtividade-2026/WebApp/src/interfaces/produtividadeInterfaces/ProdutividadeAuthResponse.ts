export type ProdutividadeAuthResponse = {
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
