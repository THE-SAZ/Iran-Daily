/**
 * Iran-Daily — IP & Location Card Renderer
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import { createCard, createRow } from '../components/Card';
import { relativeTime } from '../jalali';
import type { IpData } from '../api';

export function renderIpCard(data: IpData | null): HTMLElement {
  const { root, body, footerEl } = createCard({
    title: 'اتصال',
    icon: '🌐',
    badge: data ? 'زنده' : 'خطا',
    badgeType: data ? 'live' : 'offline',
  });

  if (!data) {
    body.innerHTML = '<p class="card__empty">داده‌ای موجود نیست</p>';
    return root;
  }

  body.appendChild(createRow('IP', data.ip ?? '—'));

  if (data.geo.country) {
    body.appendChild(createRow('کشور', data.geo.country));
  }
  if (data.geo.city) {
    body.appendChild(createRow('شهر', data.geo.city));
  }
  if (data.geo.isp) {
    body.appendChild(createRow('ISP', data.geo.isp));
  }
  if (data.geo.timezone) {
    body.appendChild(createRow('منطقه زمانی', data.geo.timezone));
  }

  footerEl.textContent = `به‌روزرسانی: ${relativeTime(data.updated_at)} · ${data.source}`;
  return root;
}
