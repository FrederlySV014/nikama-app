FROM php:8.4-fpm-alpine

# Dependencias del sistema
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

# Extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar archivos necesarios para composer install
COPY composer.json composer.lock ./

# Copiar archivos mínimos para que funcionen los scripts de composer
COPY artisan ./
COPY app/ app/
COPY bootstrap/ bootstrap/
COPY config/ config/
COPY routes/ routes/

# Crear directorio necesario para composer
RUN mkdir -p bootstrap/cache

# Instalar dependencias (sin dev tools para producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copiar el resto del código
COPY . .

# Crear directorios de storage necesarios
RUN mkdir -p /var/www/storage/framework/views /var/www/storage/framework/sessions /var/www/storage/framework/cache /var/www/storage/logs

# Configurar permisos
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instalar nginx
RUN apk add --no-cache nginx

# Configurar nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Puerto para HTTP
EXPOSE 80

# Iniciar nginx y php-fpm
CMD ["sh", "-c", "nginx && php-fpm"]