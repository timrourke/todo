FROM php:7.2.9-fpm-alpine

RUN apk update && apk add \
    bash \
    mysql

RUN docker-php-ext-install \
    pdo_mysql

RUN docker-php-ext-enable \
    pdo_mysql

ADD symfony.ini /usr/local/etc/php/conf.d/symfony.ini
ADD symfony.ini /usr/local/etc/php-fpm.d/symfony.ini
ADD symfony.pool.conf /usr/local/etc/php-fpm.d/symfony.pool.conf
