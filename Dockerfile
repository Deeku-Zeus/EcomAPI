# Use official PHP image as base
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy the application files
COPY . .

# Install dependencies
RUN composer install

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]