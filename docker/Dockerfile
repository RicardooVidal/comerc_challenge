FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    libzip-dev

RUN set -eux \
    && apt-get update \
    && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libwebp-dev \
        libxpm-dev \
        libavif-dev \
        zlib1g-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
        --with-avif \
    && docker-php-ext-install zip gd pdo pgsql mysqli pdo_mysql

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 8085

CMD ["sh", "-c", "cp .env.dev .env && composer install --ignore-platform-reqs && composer dumpautoload && php artisan key:generate && php artisan serve --host=0.0.0.0 --port=8085"]