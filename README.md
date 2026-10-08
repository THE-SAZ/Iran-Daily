
<div align="center">

# 🇮🇷 Iran-Daily

**داشبورد زنده کاربران حرفه‌ای ایرانی**

یک داشبورد سبک، سریع، و کاملاً خودمیزبان برای نمایش اطلاعات روزمره:
ارز، طلا، کریپتو، هوا، اذان، و IP — همه در یک صفحه.

<br>

[![CI](https://github.com/THE-SAZ/Iran-Daily/actions/workflows/ci.yml/badge.svg)](https://github.com/THE-SAZ/Iran-Daily/actions/workflows/ci.yml)
[![Deploy](https://github.com/THE-SAZ/Iran-Daily/actions/workflows/deploy.yml/badge.svg)](https://github.com/THE-SAZ/Iran-Daily/actions/workflows/deploy.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.5-3178C6?logo=typescript&logoColor=white)](https://typescriptlang.org)
[![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker&logoColor=white)](Dockerfile)

<br>

ساخته شده با ❤️ توسط **[THE SAZ](https://github.com/THE-SAZ)**

</div>

---

## ✨ ویژگی‌ها

| ویژگی | توضیح |
|--------|-------|
| 💱 **ارز** | دلار، یورو، پوند، درهم، لیر، یوان |
| 🥇 **طلا و فلزات** | انس و گرم طلا، نقره |
| 🪙 **کریپتو** | بیت‌کوین، اتریوم، تتر، بایننس با تغییرات ۲۴ ساعته |
| 🌤️ **هوا** | دمای تهران، رطوبت، باد، وضعیت آب‌وهوا |
| 🕌 **اذان** | اوقات شرعی به روش دانشگاه تهران |
| 🌐 **اتصال** | IP، موقعیت جغرافیایی، ISP |
| 📅 **تقویم جلالی** | نمایش تاریخ و ساعت شمسی |
| ⚡ **Live Status** | SSE + Polling خودکار با نمایش تأخیر |
| 🎨 **دارک تم** | طراحی مدرن و چشم‌نواز با فونت وزیرمتن |
| 🐳 **Docker** | آماده اجرا با یک دستور |
| 🔄 **CI/CD** | GitHub Actions بهینه‌شده |

## 🚀 شروع سریع

### با Docker (پیشنهادی)

```bash
git clone https://github.com/THE-SAZ/Iran-Daily.git
cd Iran-Daily
docker compose up -d
```

سپس مرورگر را باز کنید: [http://localhost:8080](http://localhost:8080)

### اجرای دستی

```bash
# Backend
cd backend
composer install

# Frontend
cd ../frontend
npm install
npm run dev
```

## 📡 API

| Endpoint | توضیح |
|----------|-------|
| `GET /api/health` | سلامت سیستم |
| `GET /api/all` | همه داده‌ها |
| `GET /api/currency` | ارز |
| `GET /api/gold` | طلا |
| `GET /api/crypto` | کریپتو |
| `GET /api/weather` | هوا |
| `GET /api/prayer` | اذان |
| `GET /api/ip` | IP و موقعیت |
| `GET /stream` | SSE استریم زنده |

## 🔧 متغیرهای محیطی

| متغیر | پیش‌فرض | توضیح |
|-------|---------|-------|
| `APP_PORT` | `8080` | پورت اپلیکیشن |
| `VITE_API_BASE` | `/api` | آدرس API |
| `VITE_SSE_ENABLED` | `false` | فعال‌سازی SSE |

## 🏗️ معماری

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Frontend    │────▶│  PHP Gateway │────▶│  Providers   │
│  (TypeScript)│◀────│  (Router +   │◀────│  (External   │
│  Vite + SSE  │     │   Cache +    │     │   APIs)      │
│              │     │   Rate Limit)│     │              │
└──────────────┘     └──────────────┘     └──────────────┘
```

## 📄 مجوز

MIT © [THE SAZ](https://github.com/THE-SAZ)

---

<div align="center">

**Iran-Daily** — ساخته شده با ❤️ توسط **[THE SAZ](https://github.com/THE-SAZ)**

</div>
```

### `backend/storage/cache/.gitkeep` و `backend/storage/data/.gitkeep`

```bash
# Placeholder — keeps the directory in git.
# Author: THE SAZ (https://github.com/THE-SAZ)
```

---

## خلاصه هوشمندسازی‌ها و بهینه‌سازی‌ها

| ویژگی | محل | توضیح |
|--------|------|-------|
| **SSE + Polling خودکار** | `live.ts` | تشخیص خودکار SSE، بازگشت به Polling در صورت خطا |
| **Exponential Backoff** | `live.ts` | تأخیر تدریجی در reconnect برای جلوگیری از سرباری |
| **Visibility-Aware** | `live.ts` | توقف Polling هنگام مخفی بودن تب |
| **Rate Limiting** | `RateLimiter.php` | Sliding Window بدون Redis |
| **Atomic Cache** | `Cache.php` | نوشتن اتمیک با `rename()` برای جلوگیری از Race Condition |
| **Retry + Fallback** | `Http.php` | تلاش مجدد + URL جایگزین برای هر Provider |
| **Jalali Engine** | `jalali.ts` + `Aggregator.php` | تبدیل تاریخ بدون وابستگی خارجی |
| **Reactive Store** | `store.ts` | مدیریت state سبک و تایپ‌سیف |
| **Toast System** | `toast.ts` | اطلاع‌رسانی تغییر وضعیت به کاربر |
| **Health Check** | `health.php` + Docker | بررسی سلامت سیستم |
| **CI/CD بهینه** | `ci.yml` + `deploy.yml` | Matrix strategy، Path filters، Concurrency Cancel، Cache |
| **Skeleton Loading** | `main.css` | نمایش اسکلتون هنگام بارگذاری اولیه |
| **Card Hover Effects** | `cards.css` | گرادیان بالای کارت + انیمیشن |
| **Next Prayer Highlight** | `PrayerCard.ts` | هایلایت خودکار اذان بعدی |

---
