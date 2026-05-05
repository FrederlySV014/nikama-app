FROM php:8.4-fpm-alpine

# Dependencias
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libpq-dev \
    postgresql-client \
    zip \
    unzip

# Extensiones
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Permisos correctos
RUN chown -R www-data:www-data /var/www

CMD ["php-fpm"]