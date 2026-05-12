# Etapa 1: Frontend (Compilar Vite)
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Backend (PHP-FPM + Nginx)
FROM php:8.4-fpm-alpine

# 1. Dependencias del sistema (incluye Nginx y Supervisor)
RUN apk add --no-cache \
    bash git curl unzip zip \
    libpng-dev libxml2-dev libpq-dev postgresql-client \
    nginx supervisor

# 2. Extensiones PHP (Listas para Supabase)
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd

# 3. Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Copiar configuración de Nginx y Supervisor
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
RUN echo -e "[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=php-fpm -F\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0\n\n[program:nginx]\ncommand=nginx -g 'daemon off;'\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0" > /etc/supervisord.conf

# 5. Instalar dependencias de Laravel
COPY composer.json composer.lock ./
COPY artisan ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes

# Crear carpetas necesarias antes de instalar dependencias
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 6. Copiar código fuente y assets compilados
COPY . .
COPY --from=frontend /app/public/build ./public/build

# 7. Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# 8. Optimización Laravel
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 80

# Iniciar Supervisor (que arranca Nginx y PHP)
CMD ["supervisord", "-c", "/etc/supervisord.conf"]