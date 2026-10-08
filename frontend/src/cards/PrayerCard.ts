/**
 * Iran-Daily — Prayer Times Card Renderer
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import { createCard, createRow } from '../components/Card';
import { relativeTime, toPersianTime } from '../jalali';
import type { PrayerData } from '../api';

const PRAYER_LABELS: Record<string, string> = {
  Fajr: '🌅 اذان صبح',
  Dhuhr: '🌞 اذان ظهر',
  Asr: '🌇 اذان عصر',
  Maghrib: '🌆 اذان مغرب',
  Isha: '🌙 اذان عشا',
};

export function renderPrayerCard(data: PrayerData | null): HTMLElement {
  const { root, body, footerEl } = createCard({
    title: `اذان — ${data?.city ?? 'تهران'}`,
    icon: '🕌',
    badge: data ? 'زنده' : 'خطا',
    badgeType: data ? 'live' : 'offline',
  });

  if (!data) {
    body.innerHTML = '<p class="card__empty">داده‌ای موجود نیست</p>';
    return root;
  }

  const now = new Date();
  const currentMinutes = now.getHours() * 60 + now.getMinutes();

  for (const [key, label] of Object.entries(PRAYER_LABELS)) {
    const time = data.times[key] ?? null;
    const row = createRow(label, toPersianTime(time));

    // Highlight the next prayer
    if (time) {
      const [h, m] = time.split(':').map(Number);
      const prayerMinutes = (h ?? 0) * 60 + (m ?? 0);
      if (prayerMinutes > currentMinutes) {
        row.classList.add('card__row--next');
      }
    }

    body.appendChild(row);
  }

  footerEl.textContent = `به‌روزرسانی: ${relativeTime(data.updated_at)} · ${data.source}`;
  return root;
}
