const DEFAULT_API_BASE_URL = 'http://localhost:5209/api';
const PLACEHOLDER_API_BASE_URL = 'BaseUrlDaSuaApi';

export function resolveApiBaseUrl(envBaseUrl?: string): string {
  if (!envBaseUrl || envBaseUrl.trim().length === 0) {
    return DEFAULT_API_BASE_URL;
  }

  if (envBaseUrl.trim() === PLACEHOLDER_API_BASE_URL) {
    return DEFAULT_API_BASE_URL;
  }

  return envBaseUrl;
}
