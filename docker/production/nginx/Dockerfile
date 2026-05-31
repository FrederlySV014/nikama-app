# =============================================================================
# Nikama — Nginx Production Image
# =============================================================================

FROM node:24-bookworm-slim AS builder

WORKDIR /var/www

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build

# -----------------------------------------------------------------------------

FROM nginx:1.30-alpine

COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

COPY --from=builder /var/www/public /var/www/public

WORKDIR /var/www/public

EXPOSE 80
EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]