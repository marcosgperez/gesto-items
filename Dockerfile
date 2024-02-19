# Utilizar la imagen base de PHP 8.2.8 con Apache
# FROM php:8.2.8-apache
FROM php:8.2.8-fpm

# Instalar las dependencias requeridas
RUN apt-get update \
    && apt-get install -y libpng-dev libzip-dev zip libmagickwand-dev \
    && docker-php-ext-install gd pdo pdo_mysql \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Me paro en la ruta del proyecto 
WORKDIR /var/www/html/

# Instalo composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Exposed Port
EXPOSE 9000