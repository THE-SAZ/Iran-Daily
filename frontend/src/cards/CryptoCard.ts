/**
 * Iran-Daily — Crypto Card Renderer
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import { createCard, createRow } from '../components/Card';
import { createSparkline } from '../components/Sparkline';
import { formatNumber, relativeTime } from '../jalali';
import type { CryptoData } from '../api';

const COIN_LABELS: Record<string, string> = {
  BTC: '₿ بیت‌کوین',
  ETH: 'Ξ اتریوم',
  USDT: '💵 تتر',
  BNB: '🔶 بایننس',
};

export function renderCryptoCard(data: CryptoData | null): HTMLElement {
  const { root, body, footerEl } = createCard({
    title: 'کریپتو',
    icon: '🪙',
    badge: data ? 'زنده' : 'خطا',
    badgeType: data ? 'live' : 'offline',
  });

  if (!data) {
    body.innerHTML = '<p class="card__empty">داده‌ای موجود نیست</p>';
    return root;
  }

  for (const [symbol, coin] of Object.entries(data.coins)) {
    const label = COIN_LABELS[symbol] ?? symbol;
    const trend = coin.change > 0.5 ? 'up' : coin.change < -0.5 ? 'down' : 'flat';
    const row = createRow(
      label,
      `$${formatNumber(coin.usd, coin.usd && coin.usd < 10 ? 4 : 0)}`,
      trend,
    );

    const change = document.createElement('span');
    change.className = `card__change card__change--${trend}`;
    change.textContent = `${coin.change >= 0 ? '+' : ''}${formatNumber(coin.change)}%`;
    row.appendChild(change);

    body.appendChild(row);
  }

  footerEl.textContent = `به‌روزرسانی: ${relativeTime(data.updated_at)} · ${data.source}`;
  return root;
}
