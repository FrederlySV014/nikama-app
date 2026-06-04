# =============================================================================
# Nikama — Workspace (DEVELOPMENT ONLY)
# PHP 8.4 + Node.js 24 LTS + Composer 2
# Usado para: Artisan, Composer, NPM, Vite HMR, Queue Worker, Scheduler
# =============================================================================

# Node 24 LTS — stage temporal solo para extraer los binarios
FROM node:24-alpine AS node-runtime

FROM php:8.4-cli-alpine

ARG UID=1000
ARG GID=1000
ARG XDEBUG_ENABLED=true
ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=DOCKER

RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    icu-dev \
    shadow \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        gd \
        intl \
        mbstring \
        pdo \
        pdo_pgsql \
        pgsql \
        pcntl \
        zip

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
      apk add --no-cache --virtual .xdebug-build-deps $PHPIZE_DEPS linux-headers \
      && pecl install xdebug \
      && docker-php-ext-enable xdebug \
      && apk del .xdebug-build-deps ; \
    fi

# Node.js 24 LTS — copiado desde el stage oficial para garantizar la versión exacta
# (apk add nodejs instala la versión que Alpine tenga empaquetada, que puede no ser 24)
COPY --from=node-runtime /usr/local/bin/node    /usr/local/bin/node
COPY --from=node-runtime /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && ln -s ../lib/node_modules/corepack/dist/corepack.js /usr/local/bin/corepack

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN groupmod -g ${GID} www-data \
    && usermod -u ${UID} -g www-data www-data

WORKDIR /var/www

USER www-data

ENTRYPOINT []
CMD ["sh"]

EXPOSE 5173