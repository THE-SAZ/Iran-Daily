/**
 * Iran-Daily — Gold Card Renderer
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import { createCard, createRow } from '../components/Card';
import { formatNumber, relativeTime } from '../jalali';
import type { GoldData } from '../api';

export function renderGoldCard(data: GoldData | null): HTMLElement {
  const { root, body, footerEl } = createCard({
    title: 'طلا و فلزات',
    icon: '🥇',
    badge: data ? 'زنده' : 'خطا',
    badgeType: data ? 'live' : 'offline',
  });

  if (!data) {
    body.innerHTML = '<p class="card__empty">داده‌ای موجود نیست</p>';
    return root;
  }

  if (data.gold_oz_usd !== null) {
    body.appendChild(createRow('انس طلا', `$${formatNumber(data.gold_oz_usd)}`));
  }
  if (data.gold_gram_usd !== null) {
    body.appendChild(createRow('گرم طلا', `$${formatNumber(data.gold_gram_usd)}`));
  }
  if (data.silver_oz_usd !== null) {
    body.appendChild(createRow('انس نقره', `$${formatNumber(data.silver_oz_usd)}`));
  }

  footerEl.textContent = `به‌روزرسانی: ${relativeTime(data.updated_at)} · ${data.source}`;
  return root;
}
