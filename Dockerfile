FROM ghcr.io/roadrunner-server/roadrunner:2.12.1 AS roadrunner
FROM php:8.1.1-fpm as development

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# RUN usermod -u 1000 www-data

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN pecl install -o -f redis \
    && pecl install xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis xdebug

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# USER www-data

EXPOSE 9000
