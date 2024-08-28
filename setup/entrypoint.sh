#!/bin/sh

# Navigate to the working directory
# shellcheck disable=SC2164
cd /var/www/html

# Install Composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Update Composer dependencies (if needed)
composer update --no-interaction --prefer-dist --optimize-autoloader

mysql -h ecom-db -u root -prootpasswd -e "CREATE DATABASE ecom_media_backend;"
mysql -h ecom-db -u root -prootpasswd -e "CREATE DATABASE ecom_media;"

# Run database migrations
php artisan migrate --force

php artisan migrate --force --path=database/migrations/EcomBackend

php artisan migrate --force --path=database/migrations/EcomApi

php artisan db:seed

# Start the PHP built-in server in the foreground
php -S 0.0.0.0:80 -t public

