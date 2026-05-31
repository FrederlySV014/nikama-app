# ============================================================================= 
# Nikama — PHP-FPM Dockerfile (multi-stage) # Base: PHP 8.4 FPM Alpine 
# Targets: base → development | base → production 
# =============================================================================

ARG PHP_VERSION=8.4

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

COPY . .

# Preservar estructura inicial de storage para inicializar volúmenes vacíos en producción
RUN cp -R storage storage-init

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# bootstrap/cache no se trackea en git (directorio vacío) — crearlo antes de
# composer install para que package:discover pueda escribir en él
RUN mkdir -p bootstrap/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs

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

COPY ./docker/common/php-fpm/conf.d/*.conf \
    /usr/local/etc/php-fpm.d/

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000

CMD ["php-fpm"]

# ==========================================================
# Stage 3: Development
# ==========================================================
FROM production AS development

USER root

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

USER www-data

# ==========================================================
# Stage 4: Default Production Target (for Render and platforms without --target)
# ==========================================================
FROM production AS default-production