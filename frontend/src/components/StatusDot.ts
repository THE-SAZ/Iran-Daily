/**
 * Iran-Daily — Live Status Indicator
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

import type { LiveStatus } from '../live';

const STATUS_LABELS: Record<LiveStatus, string> = {
  live: 'زنده',
  stale: 'کند',
  offline: 'قطع',
  connecting: 'اتصال...',
};

export function createStatusDot(): {
  root: HTMLElement;
  update: (status: LiveStatus, latencyMs: number, errors: number) => void;
} {
  const root = document.createElement('div');
  root.className = 'status-indicator';
  root.setAttribute('role', 'status');
  root.setAttribute('aria-live', 'polite');

  const dot = document.createElement('span');
  dot.className = 'status-indicator__dot';

  const label = document.createElement('span');
  label.className = 'status-indicator__label';
  label.textContent = 'اتصال...';

  const meta = document.createElement('span');
  meta.className = 'status-indicator__meta';

  root.append(dot, label, meta);

  return {
    root,
    update(status: LiveStatus, latencyMs: number, errors: number) {
      dot.className = `status-indicator__dot status-indicator__dot--${status}`;
      label.textContent = STATUS_LABELS[status];

      if (status === 'live' && latencyMs >= 0) {
        meta.textContent = `${latencyMs}ms`;
      } else if (errors > 0) {
        meta.textContent = `${errors} خطا`;
      } else {
        meta.textContent = '';
      }
    },
  };
}
