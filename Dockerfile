FROM php:8.4.12-fpm

# Установим системные зависимости и расширения PHP, необходимые для Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libicu-dev \
        libxml2-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libpq-dev \
        nano \
        curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        intl \
        bcmath \
        gd \
        zip \
        opcache \
        pcntl \
    && rm -rf /var/lib/apt/lists/*

# Установим Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Создадим пользователя www-data с корректными правами
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www

# Настройки PHP (опционально можно подкрутить при необходимости)
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

# По умолчанию просто запускаем php-fpm
CMD ["php-fpm"]

