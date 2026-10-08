/**
 * Iran-Daily — Card Component
 *
 * A composable card builder with typed slots.
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

export interface CardOptions {
  title: string;
  icon?: string;
  badge?: string;
  badgeType?: 'live' | 'stale' | 'offline';
  footer?: string;
}

export function createCard(opts: CardOptions): {
  root: HTMLElement;
  body: HTMLElement;
  footerEl: HTMLElement;
} {
  const root = document.createElement('article');
  root.className = 'card';
  root.setAttribute('role', 'region');
  root.setAttribute('aria-label', opts.title);

  // Header
  const header = document.createElement('header');
  header.className = 'card__header';

  const titleWrap = document.createElement('div');
  titleWrap.className = 'card__title-wrap';

  if (opts.icon) {
    const icon = document.createElement('span');
    icon.className = 'card__icon';
    icon.textContent = opts.icon;
    icon.setAttribute('aria-hidden', 'true');
    titleWrap.appendChild(icon);
  }

  const title = document.createElement('h2');
  title.className = 'card__title';
  title.textContent = opts.title;
  titleWrap.appendChild(title);

  header.appendChild(titleWrap);

  if (opts.badge) {
    const badge = document.createElement('span');
    badge.className = `card__badge card__badge--${opts.badgeType ?? 'live'}`;
    badge.textContent = opts.badge;
    header.appendChild(badge);
  }

  // Body
  const body = document.createElement('div');
  body.className = 'card__body';

  // Footer
  const footerEl = document.createElement('footer');
  footerEl.className = 'card__footer';
  if (opts.footer) {
    footerEl.textContent = opts.footer;
  }

  root.append(header, body, footerEl);

  return { root, body, footerEl };
}

/**
 * Create a row of label + value inside a card.
 */
export function createRow(
  label: string,
  value: string,
  trend?: 'up' | 'down' | 'flat',
): HTMLElement {
  const row = document.createElement('div');
  row.className = 'card__row';

  const labelEl = document.createElement('span');
  labelEl.className = 'card__label';
  labelEl.textContent = label;

  const valueEl = document.createElement('span');
  valueEl.className = 'card__value';
  valueEl.textContent = value;

  if (trend) {
    valueEl.classList.add(`card__value--${trend}`);
    const arrow = document.createElement('span');
    arrow.className = 'card__trend';
    arrow.textContent = trend === 'up' ? '▲' : trend === 'down' ? '▼' : '—';
    valueEl.prepend(arrow);
  }

  row.append(labelEl, valueEl);
  return row;
}
