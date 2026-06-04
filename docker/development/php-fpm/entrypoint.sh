#!/bin/sh
set -e

# Clear configurations to avoid caching issues in development
echo "Clearing cached bootstrap files..."
rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/events.php
find storage/framework/views -type f -name "*.php" -delete 2>/dev/null || true

# Run the default command (e.g., php-fpm or bash)
exec "$@"