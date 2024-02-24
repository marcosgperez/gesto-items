# Use the official PHP image with the version that includes FPM.
FROM php:8.2-fpm as php

# Set working directory
WORKDIR /app

# Install system dependencies & PHP extensions required for Laravel
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    libmagickwand-dev \
    libcurl4-gnutls-dev \
    nginx \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql gd bcmath curl opcache mbstring

# Copy PHP configuration files
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer.lock and composer.json
COPY composer.lock composer.json ./

# Set permissions for Composer to run properly
RUN chown www-data:www-data composer.lock composer.json

# Switch to www-data user to run Composer
USER www-data

# Install dependencies with Composer
# Note: Running `composer install` as www-data user for security
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Switch back to root user to perform system operations
USER root

# Copy the application code to the container
COPY --chown=www-data:www-data . .

# Create necessary Laravel directories
RUN mkdir -p ./storage/framework/{sessions,views,cache} && \
    mkdir -p ./storage/logs && \
    chown -R www-data:www-data ./storage && \
    chown -R www-data:www-data ./bootstrap/cache

# Adjust user and group IDs for www-data to match the host system
RUN usermod --uid 1000 www-data && groupmod --gid 1000 www-data

# Run the entrypoint script
COPY --chown=www-data:www-data docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
