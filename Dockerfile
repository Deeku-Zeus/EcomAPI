# Use PHP 8.3 with Alpine Linux
FROM php:8.3-alpine

# Install necessary packages and PHP extensions
RUN apk update && apk add --no-cache \
    libpng libpng-dev \
    libjpeg-turbo libjpeg-turbo-dev \
    libwebp libwebp-dev \
    zlib zlib-dev \
    git \
    unzip \
    bash \
    && docker-php-ext-install pdo pdo_mysql gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy project files into the container
COPY . .

# Install PHP dependencies
RUN composer install

# Expose port 80
EXPOSE 80

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
