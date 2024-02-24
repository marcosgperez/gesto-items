# Usado para la construcción en producción.
FROM 702643951612.dkr.ecr.us-west-1.amazonaws.com/prod-gesto-items-base-image:latest as php

# Copia los archivos de configuración.
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Establece el directorio de trabajo.
WORKDIR /app

# Copia el archivo composer.json y composer.lock (si existe) en tu contenedor.
COPY composer.json composer.lock* /app/

# Instala las dependencias de Composer.
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Copia los archivos del proyecto al contenedor.
COPY --chown=www-data:www-data . .

# Después de copiar el resto de tu aplicación, asegúrate de volcar el autoload.
RUN composer dump-autoload --no-scripts --no-dev --optimize

# Crea carpetas de caché de Laravel.
RUN mkdir -p ./storage/framework
RUN mkdir -p ./storage/framework/{cache, testing, sessions, views}
RUN mkdir -p ./storage/framework/bootstrap
RUN mkdir -p ./storage/framework/bootstrap/cache

# Ajusta los permisos de usuario y grupo.
RUN usermod --uid 1000 www-data
RUN groupmod --gid 1000 www-data

# Ejecuta el archivo de punto de entrada.
ENTRYPOINT [ "docker/entrypoint.sh" ]
