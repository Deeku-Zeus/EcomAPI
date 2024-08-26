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
    mysql-client \
    && docker-php-ext-install pdo pdo_mysql gd

# Enable Apache mod_rewrite
RUN sed -i 's/#LoadModule rewrite_module/LoadModule rewrite_module/' /etc/apache2/httpd.conf \
    && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/httpd.conf

RUN echo "upload_max_filesize = 200M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 800M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 1G" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Remove default php.ini files
RUN rm -f /usr/local/etc/php/php.ini-development \
    && rm -f /usr/local/etc/php/php.ini-production

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy custom php.ini configuration
COPY setup/php.ini /usr/local/etc/php/conf.d/
COPY setup/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the working directory
WORKDIR /var/www/html

# Copy project files into the container
COPY . .

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache server
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
