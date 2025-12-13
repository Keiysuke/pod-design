FROM php:8.4-fpm-bullseye

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libxml2-dev zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html