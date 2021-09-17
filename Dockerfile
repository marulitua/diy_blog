# Development image
FROM php:8-alpine AS build

RUN apk update && \
    apk add --no-cache libzip-dev git && \
    apk add --no-cache --virtual dev-deps g++ make autoconf && \
    docker-php-ext-install zip pdo_mysql pdo_sqlite && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    apk del --no-cache dev-deps

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app

COPY composer.* ./

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN composer install --no-interaction --optimize-autoloader --no-scripts --no-cache
