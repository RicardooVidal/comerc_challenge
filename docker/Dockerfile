FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    libzip-dev

RUN set -eux \
    && apt-get update \
    && apt-get install -y libzip-dev zlib1g-dev \
    && docker-php-ext-install zip

RUN docker-php-ext-install pdo pgsql mysqli pdo_mysql

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 8085

CMD ["sh", "-c", "cp .env.dev .env && composer install --ignore-platform-reqs && php artisan key:generate && php artisan serve --host=0.0.0.0 --port=8085"]