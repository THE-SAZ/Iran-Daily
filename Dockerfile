# ──────────────────────────────────────────────
# Iran-Daily — Production Dockerfile
# Author: THE SAZ (https://github.com/THE-SAZ)
# ──────────────────────────────────────────────

# ── Stage 1: Frontend Build ───────────────────
FROM node:20-alpine AS frontend-builder

WORKDIR /build
COPY frontend/package.json frontend/package-lock.json* ./
RUN npm ci --prefer-offline

COPY frontend/ ./
RUN npm run build

# ── Stage 2: Backend + Runtime ────────────────
FROM php:8.3-apache

LABEL maintainer="THE SAZ <https://github.com/THE-SAZ>"
LABEL org.opencontainers.image.title="Iran-Daily"
LABEL org.opencontainers.image.author="THE SAZ"
LABEL org.opencontainers.image.url="https://github.com/THE-SAZ"

# Install PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# Apache config
RUN a2enmod rewrite headers deflate
COPY backend/.htaccess /var/www/html/.htaccess

# Copy backend
COPY backend/ /var/www/html/

# Copy built frontend into the web root
COPY --from=frontend-builder /build/dist/ /var/www/html/public/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Health check
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl -sf http://localhost/api/health || exit 1

EXPOSE 80

# Author banner
RUN echo "Iran-Daily by THE SAZ — https://github.com/THE-SAZ" > /etc/motd
