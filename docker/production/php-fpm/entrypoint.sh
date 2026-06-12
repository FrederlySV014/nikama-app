#!/bin/sh
set -e

# Initialize storage directory if empty
# -----------------------------------------------------------
# If the storage directory is empty, copy the initial contents
# and set the correct permissions.
# -----------------------------------------------------------
if [ ! "$(ls -A /var/www/storage)" ]; then
  echo "Initializing storage directory..."
  cp -R /var/www/storage-init/. /var/www/storage
fi

# Remove storage-init directory
rm -rf /var/www/storage-init

# Crear enlace simbólico de almacenamiento público
echo "Creating storage symlink..."
php artisan storage:link --force

# Run Laravel migrations
# -----------------------------------------------------------
# Ensure the database schema is up to date.
# -----------------------------------------------------------
if [ "${RUN_MIGRATIONS}" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force
fi

# Clear and cache configurations
# -----------------------------------------------------------
# Improves performance by caching config and routes.
# -----------------------------------------------------------
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Ensure correct ownership of storage and cache directories
# -----------------------------------------------------------
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Run the default command
exec "$@"