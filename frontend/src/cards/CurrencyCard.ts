/**
 * Iran-Daily — Currency Card Renderer
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import { createCard, createRow } from '../components/Card';
import { formatNumber, relativeTime, toPersianDigits } from '../jalali';
import type { CurrencyData } from '../api';

const CURRENCY_LABELS: Record<string, string> = {
  USD: '🇺🇸 دلار آمریکا',
  EUR: '🇪🇺 یورو',
  GBP: '🇬🇧 پوند',
  AED: '🇦🇪 درهم',
  TRY: '🇹🇷 لیر',
  CNY: '🇨🇳 یوان',
};

export function renderCurrencyCard(data: CurrencyData | null): HTMLElement {
  const { root, body, footerEl } = createCard({
    title: 'ارز',
    icon: '💱',
    badge: data ? 'زنده' : 'خطا',
    badgeType: data ? 'live' : 'offline',
  });

  if (!data) {
    body.innerHTML = '<p class="card__empty">داده‌ای موجود نیست</p>';
    return root;
  }

  for (const [code, label] of Object.entries(CURRENCY_LABELS)) {
    const value = data[code as keyof CurrencyData] as number | null;
    if (value === null) continue;
    body.appendChild(createRow(label, formatNumber(value, 0)));
  }

  footerEl.textContent = `به‌روزرسانی: ${relativeTime(data.updated_at)} · ${data.source}`;
  return root;
}
