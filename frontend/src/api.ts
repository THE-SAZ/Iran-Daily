/**
 * Iran-Daily — API Client
 *
 * Type-safe API layer with automatic retry,
 * timeout, and error normalization.
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

// ── Types ────────────────────────────────────────────

export interface Meta {
  app: string;
  author: string;
  author_url: string;
  version: string;
  ts: number;
  jalali: string;
  elapsed: number;
  errors: Record<string, string> | null;
}

export interface CurrencyData {
  USD: number | null;
  EUR: number | null;
  GBP: number | null;
  AED: number | null;
  TRY: number | null;
  CNY: number | null;
  updated_at: number;
  source: string;
}

export interface GoldData {
  gold_oz_usd: number | null;
  silver_oz_usd: number | null;
  gold_gram_usd: number | null;
  updated_at: number;
  source: string;
}

export interface CryptoCoin {
  usd: number | null;
  change: number;
}

export interface CryptoData {
  coins: Record<string, CryptoCoin>;
  updated_at: number;
  source: string;
}

export interface WeatherData {
  temp_c: number | null;
  humidity: number | null;
  wind_kmh: number | null;
  condition: string;
  code: number;
  city: string;
  updated_at: number;
  source: string;
}

export interface PrayerData {
  times: Record<string, string | null>;
  city: string;
  updated_at: number;
  source: string;
}

export interface IpData {
  ip: string | null;
  geo: {
    country: string | null;
    city: string | null;
    isp: string | null;
    timezone: string | null;
  };
  updated_at: number;
  source: string;
}

export interface DashboardPayload {
  meta: Meta;
  data: {
    currency: CurrencyData | null;
    gold: GoldData | null;
    crypto: CryptoData | null;
    weather: WeatherData | null;
    prayer: PrayerData | null;
    ip: IpData | null;
  };
}

// ── Client ───────────────────────────────────────────

const BASE = import.meta.env.VITE_API_BASE ?? '/api';
const TIMEOUT_MS = 10_000;
const MAX_RETRIES = 1;

export class ApiError extends Error {
  constructor(
    message: string,
    public readonly status: number,
    public readonly url: string,
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

async function request<T>(path: string, retries = MAX_RETRIES): Promise<T> {
  const url = `${BASE}${path}`;
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), TIMEOUT_MS);

  try {
    const res = await fetch(url, {
      signal: controller.signal,
      cache: 'no-store',
      headers: { Accept: 'application/json' },
    });

    if (!res.ok) {
      throw new ApiError(`HTTP ${res.status}`, res.status, url);
    }

    return (await res.json()) as T;
  } catch (err) {
    if (retries > 0 && !(err instanceof ApiError && err.status < 500)) {
      return request<T>(path, retries - 1);
    }
    throw err;
  } finally {
    clearTimeout(timer);
  }
}

/**
 * Fetch the aggregated dashboard payload.
 */
export function fetchAll(): Promise<DashboardPayload> {
  return request<DashboardPayload>('/all');
}

/**
 * Fetch a single endpoint.
 */
export function fetchOne<K extends keyof DashboardPayload['data']>(
  key: K,
): Promise<DashboardPayload['data'][K]> {
  return request(`/${key}`);
}
