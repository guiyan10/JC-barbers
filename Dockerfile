FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    icu-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    postgresql-dev \
    bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        tokenizer \
        xml \
        dom \
        fileinfo

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev --no-interaction

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
