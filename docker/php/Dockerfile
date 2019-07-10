ARG PHP_SAPI
ARG PHP_VERSION

FROM php:${PHP_VERSION}-${PHP_SAPI}-alpine

ENV PHPIZE_DEPS $PHPIZE_DEPS libzip-dev zlib-dev openssl-dev

RUN apk fetch -s --no-cache -X http://dl-cdn.alpinelinux.org/alpine/edge/community composer | tail -n +4 | gunzip -f | tar -C / -vx usr/bin/composer && \
    apk add --no-cache libzip && docker-php-ext-configure zip --with-libzip && docker-php-ext-install zip phar sockets bcmath

WORKDIR /var/www/html
