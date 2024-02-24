FROM php:8.2-fpm

# Instala dependencias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    libmagickwand-dev \
    nginx \
    libonig-dev \
    && docker-php-ext-install gd pdo pdo_mysql bcmath curl opcache mbstring

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos de configuración
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

WORKDIR /app

# Instala dependencias de Composer
COPY composer.json composer.lock ./
RUN composer install --no-autoloader --no-scripts --no-dev

COPY . .
RUN composer dump-autoload --optimize && composer run-script post-autoload-dump

# Crea directorios necesarios para Laravel
RUN mkdir -p ./storage/framework/{sessions,views,cache}
RUN mkdir -p ./storage/logs
RUN chown -R www-data:www-data ./storage
RUN chown -R www-data:www-data ./bootstrap/cache

# Ajusta permisos de usuario
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

ENTRYPOINT [ "docker/entrypoint.sh" ]
