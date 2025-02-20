FROM php:8.4-fpm-bookworm

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libmagickwand-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    uuid \
  && docker-php-ext-install \
    zip \
    intl \
    opcache \
    sockets \
    pcntl

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && docker-php-ext-install gd

RUN pecl install apcu && docker-php-ext-enable apcu \
    && echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini \
    && echo "apc.enable=1" >> /usr/local/etc/php/php.ini

RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_CACHE_DIR=/tmp

COPY symfony /var/www/symfony

WORKDIR /var/www/symfony/
RUN rm -rf var/ vendor/


RUN composer install --no-progress --optimize-autoloader
