/**
 * Iran-Daily — Jalali (Solar Hijri) Date Utilities
 *
 * Zero-dependency, accurate Jalali calendar conversion
 * using the well-established Borkowski algorithm.
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

const MONTH_NAMES = [
  'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
  'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
];

const DAY_NAMES = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];

const GREGORIAN_DAYS_IN_MONTH = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

export interface JalaliDate {
  year: number;
  month: number;
  day: number;
  monthName: string;
  dayName: string;
}

/**
 * Convert a Gregorian Date to a Jalali date object.
 */
export function toJalali(date: Date): JalaliDate {
  const gy = date.getFullYear();
  const gm = date.getMonth() + 1;
  const gd = date.getDate();

  const gy2 = gm > 2 ? gy + 1 : gy;
  const days =
    355666 +
    365 * gy +
    Math.floor((gy2 + 3) / 4) -
    Math.floor((gy2 + 99) / 100) +
    Math.floor((gy2 + 399) / 400) +
    gd +
    GREGORIAN_DAYS_IN_MONTH[gm - 1];

  let jy = -1595 + 33 * Math.floor(days / 12053);
  let remaining = days % 12053;

  jy += 4 * Math.floor(remaining / 1461);
  remaining %= 1461;

  if (remaining > 365) {
    jy += Math.floor((remaining - 1) / 365);
    remaining = (remaining - 1) % 365;
  }

  let jm: number;
  let jd: number;

  if (remaining < 186) {
    jm = 1 + Math.floor(remaining / 31);
    jd = 1 + (remaining % 31);
  } else {
    jm = 7 + Math.floor((remaining - 186) / 30);
    jd = 1 + ((remaining - 186) % 30);
  }

  return {
    year: jy,
    month: jm,
    day: jd,
    monthName: MONTH_NAMES[jm - 1] ?? '',
    dayName: DAY_NAMES[date.getDay()] ?? '',
  };
}

/**
 * Format a Jalali date as a human-readable Persian string.
 */
export function formatJalali(date: Date): string {
  const j = toJalali(date);
  const hh = String(date.getHours()).padStart(2, '0');
  const mm = String(date.getMinutes()).padStart(2, '0');
  return `${j.dayName} ${toPersianDigits(String(j.day))} ${j.monthName} ${toPersianDigits(String(j.year))} — ${toPersianDigits(hh)}:${toPersianDigits(mm)}`;
}

/**
 * Format a Jalali date as a short numeric string (YYYY/MM/DD).
 */
export function formatJalaliShort(date: Date): string {
  const j = toJalali(date);
  return toPersianDigits(
    `${j.year}/${String(j.month).padStart(2, '0')}/${String(j.day).padStart(2, '0')}`
  );
}

/**
 * Convert a time string like "04:32" to Persian digits.
 */
export function toPersianTime(time: string | null): string {
  if (!time) return '—';
  return toPersianDigits(time);
}

/**
 * Convert a number or string to Persian digits.
 */
export function toPersianDigits(input: string | number): string {
  return String(input).replace(/\d/g, (d) => PERSIAN_DIGITS[Number(d)] ?? d);
}

/**
 * Format a number with Persian thousands separators.
 */
export function formatNumber(value: number | null, decimals = 2): string {
  if (value === null || value === undefined) return '—';
  return toPersianDigits(
    value.toLocaleString('en-US', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    })
  );
}

/**
 * Relative time in Persian (e.g., "۳ دقیقه پیش").
 */
export function relativeTime(ts: number): string {
  const diff = Math.floor((Date.now() - ts) / 1000);
  if (diff < 10) return 'همین حالا';
  if (diff < 60) return `${toPersianDigits(diff)} ثانیه پیش`;
  if (diff < 3600) return `${toPersianDigits(Math.floor(diff / 60))} دقیقه پیش`;
  if (diff < 86400) return `${toPersianDigits(Math.floor(diff / 3600))} ساعت پیش`;
  return `${toPersianDigits(Math.floor(diff / 86400))} روز پیش`;
}
