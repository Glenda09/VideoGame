# syntax=docker/dockerfile:1

FROM php:8.2-fpm-bullseye AS base

RUN apt-get update \
    && apt-get install -y \
        curl \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
        libpq-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath gd intl pdo_mysql pcntl zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

RUN set -eux; \
    mkdir -p \
        storage/app \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs \
        bootstrap/cache; \
    chown -R www-data:www-data storage bootstrap/cache

FROM base AS vendor

ARG WITH_DEV=false

COPY composer.json composer.lock ./
COPY artisan artisan
COPY app app
COPY bootstrap bootstrap
COPY config config
COPY database database
COPY public public
COPY resources resources
COPY routes routes

RUN if [ "$WITH_DEV" = "true" ]; then \
        composer install --no-ansi --no-interaction --prefer-dist; \
    else \
        composer install --no-dev --no-ansi --no-interaction --prefer-dist --optimize-autoloader; \
    fi

FROM base AS app

COPY --from=vendor /var/www/html /var/www/html

CMD ["php-fpm"]
