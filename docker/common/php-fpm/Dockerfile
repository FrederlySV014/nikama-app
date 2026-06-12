# ============================================================================= 
# Nikama — PHP-FPM Dockerfile (multi-stage) # Base: PHP 8.4 FPM Alpine 
# Targets: base → development | base → production 
# =============================================================================

ARG PHP_VERSION=8.4

# ==========================================================
# Stage 0: Node Builder (Vite Asset Compilation)
# ==========================================================
FROM node:24-bookworm-slim AS node-builder

WORKDIR /var/www

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# ==========================================================
# Stage 1: Builder
# ==========================================================
FROM php:${PHP_VERSION}-fpm AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        bcmath \
        intl \
        mbstring \
        zip \
        gd \
        pcntl \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Copiar archivos de dependencias e instalar primero para usar la caché de capas de Docker
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear directorios temporales necesarios para scripts de Composer
RUN mkdir -p bootstrap/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs

# Instalar dependencias sin ejecutar scripts ni generar cargador automático (requieren el resto del código)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# Copiar el código del proyecto
COPY . .

# Copiar activos de Vite compilados en la etapa de node-builder
COPY --from=node-builder /var/www/public/build ./public/build

# Preservar estructura inicial de storage para inicializar volúmenes vacíos en producción
RUN cp -R storage storage-init

# Ejecutar optimización final y scripts post-instalación de Laravel
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

# ==========================================================
# Stage 2: Production
# ==========================================================
FROM php:${PHP_VERSION}-fpm AS production

# Paquetes runtime — se usan variantes -dev para evitar depender de nombres
# versionados que cambian entre releases de Debian (libicu72 en Bookworm,
# libicu76 en Trixie, etc.). Los paquetes -dev traen la shared lib correcta.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpq5 \
        libicu-dev \
        libzip-dev \
        libjpeg62-turbo \
        libpng-dev \
        libfreetype6-dev \
        libfcgi-bin \
        procps \
        supervisor \
    && mkdir -p /var/log/supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -o /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

COPY ./docker/production/php-fpm/entrypoint.sh \
    /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

COPY --from=builder /usr/local/lib/php/extensions/ \
    /usr/local/lib/php/extensions/

COPY --from=builder /usr/local/etc/php/conf.d/ \
    /usr/local/etc/php/conf.d/

COPY --from=builder /var/www \
    /var/www

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY ./docker/production/php-fpm/local.ini /usr/local/etc/php/conf.d/99-local.ini

COPY ./docker/common/php-fpm/conf.d/*.conf \
    /usr/local/etc/php-fpm.d/

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www \
    /var/www/storage-init \
    /var/www/storage \
    /var/www/bootstrap/cache

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000

CMD ["php-fpm"]

# ==========================================================
# Stage 3: Development
# ==========================================================
FROM production AS development

USER root

# Declarar argumentos de desarrollo para mapeo de UID/GID con el host
ARG UID=1000
ARG GID=1000

# Sincronizar UID/GID de www-data con el Host en desarrollo
RUN groupmod -g ${GID} www-data || true \
    && usermod -u ${UID} -g www-data www-data || true

ARG XDEBUG_ENABLED=true
ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=DOCKER

RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug ; \
fi

COPY ./docker/development/php-fpm/entrypoint.sh \
    /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

COPY ./docker/development/php-fpm/local.ini /usr/local/etc/php/conf.d/99-local.ini

USER www-data

# ==========================================================
# Stage 4: Default Production Target (for Render and platforms without --target)
# ==========================================================
FROM production AS default-production