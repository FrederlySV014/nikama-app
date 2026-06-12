# =============================================================================
# Nikama — Nginx Production Image
# =============================================================================

FROM node:24-alpine AS builder

WORKDIR /var/www

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build

# -----------------------------------------------------------------------------

FROM nginx:1.30-alpine

COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

COPY --from=builder /var/www/public /var/www/public

# Asegurar que el symlink de storage no exista antes de crearlo y enlazarlo al volumen compartido
RUN rm -rf /var/www/public/storage && ln -s /var/www/storage/app/public /var/www/public/storage

WORKDIR /var/www/public

EXPOSE 80
EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]