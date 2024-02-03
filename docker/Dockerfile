FROM php:8.2.0-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf bash

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown www-data:www-data /var/www/html
COPY --chown=www-data:www-data ./ /var/www/html
WORKDIR /var/www/html/app
