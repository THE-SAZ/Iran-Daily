/**
 * Iran-Daily — Application Bootstrap
 *
 * Live Iranian Power-User Dashboard
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 * @version   1.0.0
 */

import './styles/theme.css';
import './styles/main.css';
import './styles/cards.css';

import { LiveEngine } from './live';
import { formatJalali } from './jalali';
import { createStatusDot } from './components/StatusDot';
import { toast } from './toast';

import { renderCurrencyCard } from './cards/CurrencyCard';
import { renderGoldCard } from './cards/GoldCard';
import { renderCryptoCard } from './cards/CryptoCard';
import { renderWeatherCard } from './cards/WeatherCard';
import { renderPrayerCard } from './cards/PrayerCard';
import { renderIpCard } from './cards/IpCard';

// ── DOM Setup ────────────────────────────────────────

const app = document.querySelector<HTMLDivElement>('#app')!;

app.innerHTML = `
  <header class="topbar">
    <div class="topbar__brand">
      <span class="topbar__logo" aria-hidden="true">🇮🇷</span>
      <div>
        <h1 class="topbar__title">Iran-Daily</h1>
        <p class="topbar__subtitle">
          ساخته شده توسط
          <a href="https://github.com/THE-SAZ" target="_blank" rel="noopener noreferrer">THE SAZ</a>
        </p>
      </div>
    </div>
    <div class="topbar__status" id="status-slot"></div>
    <div class="topbar__clock" id="clock"></div>
  </header>
  <main class="grid" id="grid">
    <div class="skeleton-grid">
      ${Array(6).fill('<div class="skeleton-card"></div>').join('')}
    </div>
  </main>
  <footer class="footer">
    <p>
      Iran-Daily v1.0.0 — ساخته شده با ❤️ توسط
      <a href="https://github.com/THE-SAZ" target="_blank" rel="noopener noreferrer">THE SAZ</a>
    </p>
  </footer>
`;

// ── Status Dot ───────────────────────────────────────

const statusDot = createStatusDot();
document.getElementById('status-slot')!.appendChild(statusDot.root);

// ── Clock ────────────────────────────────────────────

const clockEl = document.getElementById('clock')!;

function tickClock(): void {
  clockEl.textContent = formatJalali(new Date());
}

tickClock();
setInterval(tickClock, 1_000);

// ── Live Engine ──────────────────────────────────────

const engine = new LiveEngine();
const grid = document.getElementById('grid')!;
let firstLoad = true;

engine.subscribe(({ payload, status, latencyMs, source, consecutiveErrors }) => {
  // Update status indicator
  statusDot.update(status, latencyMs, consecutiveErrors);

  // Toast on status transitions
  if (status === 'offline' && consecutiveErrors === 3) {
    toast('ارتباط با سرور قطع شد. تلاش مجدد...', 'error');
  } else if (status === 'live' && !firstLoad && consecutiveErrors === 0) {
    toast('اتصال مجدد برقرار شد', 'success');
  }

  if (!payload) return;

  // Render all cards
  grid.innerHTML = '';

  const cards = [
    renderCurrencyCard(payload.data.currency),
    renderGoldCard(payload.data.gold),
    renderCryptoCard(payload.data.crypto),
    renderWeatherCard(payload.data.weather),
    renderPrayerCard(payload.data.prayer),
    renderIpCard(payload.data.ip),
  ];

  for (const card of cards) {
    grid.appendChild(card);
  }

  // Show errors as toast
  if (payload.meta.errors) {
    for (const [provider, msg] of Object.entries(payload.meta.errors)) {
      toast(`${provider}: ${msg}`, 'warning');
    }
  }

  if (firstLoad) {
    firstLoad = false;
    if (source === 'sse') toast('اتصال زنده برقرار شد (SSE)', 'success');
  }
});

// ── Start ────────────────────────────────────────────

engine.start();
