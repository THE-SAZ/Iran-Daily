/**
 * Iran-Daily — Weather Card Renderer
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import { createCard, createRow } from '../components/Card';
import { formatNumber, relativeTime, toPersianDigits } from '../jalali';
import type { WeatherData } from '../api';

const WEATHER_ICONS: Record<number, string> = {
  0: '☀️', 1: '🌤️', 2: '⛅', 3: '☁️',
  45: '🌫️', 48: '🌫️',
  51: '🌦️', 53: '🌧️', 55: '🌧️',
  61: '🌧️', 63: '🌧️', 65: '🌧️',
  71: '🌨️', 73: '🌨️', 75: '❄️',
  80: '🌦️', 81: '🌧️', 82: '⛈️',
  95: '⛈️', 96: '⛈️',
};

export function renderWeatherCard(data: WeatherData | null): HTMLElement {
  const icon = data ? (WEATHER_ICONS[data.code] ?? '🌡️') : '🌡️';

  const { root, body, footerEl } = createCard({
    title: `هوا — ${data?.city ?? 'تهران'}`,
    icon,
    badge: data ? 'زنده' : 'خطا',
    badgeType: data ? 'live' : 'offline',
  });

  if (!data) {
    body.innerHTML = '<p class="card__empty">داده‌ای موجود نیست</p>';
    return root;
  }

  body.appendChild(createRow('دما', `${formatNumber(data.temp_c, 0)}°C`));
  body.appendChild(createRow('رطوبت', `${toPersianDigits(data.humidity ?? 0)}%`));
  body.appendChild(createRow('باد', `${formatNumber(data.wind_kmh, 0)} km/h`));
  body.appendChild(createRow('وضعیت', data.condition));

  footerEl.textContent = `به‌روزرسانی: ${relativeTime(data.updated_at)} · ${data.source}`;
  return root;
}
