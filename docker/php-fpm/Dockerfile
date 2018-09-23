FROM php:7.2.9-fpm-alpine

WORKDIR /var/www/app

RUN echo "PS1='\w\$ '" >> ~/.bashrc

RUN apk update && apk add \
    bash \
    mysql \
	$PHPIZE_DEPS

RUN pecl install xdebug-2.6.0

RUN docker-php-ext-install \
    pdo_mysql

RUN docker-php-ext-enable \
    pdo_mysql \
	xdebug

ADD symfony.ini /usr/local/etc/php/conf.d/symfony.ini
ADD symfony.ini /usr/local/etc/php-fpm.d/symfony.ini
ADD symfony.pool.conf /usr/local/etc/php-fpm.d/symfony.pool.conf
