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
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis xdebug

# USER www-data

EXPOSE 9000
