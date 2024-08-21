# Use PHP 8.3 with Alpine Linux
FROM php:8.3-alpine

# Install necessary packages and PHP extensions
RUN apk update && apk add --no-cache \
    apache2 \
    apache2-utils \
    libpng libpng-dev \
    libjpeg-turbo libjpeg-turbo-dev \
    libwebp libwebp-dev \
    zlib zlib-dev \
    git \
    unzip \
    bash \
    && docker-php-ext-install pdo pdo_mysql gd

# Enable Apache mod_rewrite
RUN sed -i 's/#LoadModule rewrite_module/LoadModule rewrite_module/' /etc/apache2/httpd.conf \
    && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/httpd.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy project files into the container
COPY . .

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["httpd", "-D", "FOREGROUND"]
