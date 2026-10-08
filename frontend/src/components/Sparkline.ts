/**
 * Iran-Daily — Sparkline Mini-Chart
 *
 * Zero-dependency SVG sparkline for showing
 * 24h price trends inline.
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

export function createSparkline(
  data: number[],
  width = 80,
  height = 24,
  color = 'var(--accent)',
): SVGElement {
  const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
  svg.setAttribute('class', 'sparkline');
  svg.setAttribute('aria-hidden', 'true');

  if (data.length < 2) return svg;

  const min = Math.min(...data);
  const max = Math.max(...data);
  const range = max - min || 1;

  const points = data
    .map((v, i) => {
      const x = (i / (data.length - 1)) * width;
      const y = height - ((v - min) / range) * (height - 2) - 1;
      return `${x},${y}`;
    })
    .join(' ');

  const polyline = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
  polyline.setAttribute('points', points);
  polyline.setAttribute('fill', 'none');
  polyline.setAttribute('stroke', color);
  polyline.setAttribute('stroke-width', '1.5');
  polyline.setAttribute('stroke-linecap', 'round');
  polyline.setAttribute('stroke-linejoin', 'round');

  svg.appendChild(polyline);
  return svg;
}
