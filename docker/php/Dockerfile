ARG PHP_SAPI
ARG PHP_VERSION

FROM php:${PHP_VERSION}-${PHP_SAPI}-alpine

ENV PHPIZE_DEPS $PHPIZE_DEPS libzip-dev zlib-dev openssl-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apk add --no-cache libzip && \
    apk add --no-cache --virtual build-dependencies  libzip-dev g++ make autoconf && \
    docker-php-ext-configure zip --with-libzip && docker-php-ext-install zip phar sockets bcmath && \
    yes | pecl install xdebug && docker-php-ext-enable xdebug && \
    echo "xdebug.idekey = PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.default_enable = 1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_autostart = 0" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_connect_back = 0" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.profiler_enable = 0" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_host = 192.168.65.2" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    docker-php-source delete && apk del build-dependencies

WORKDIR /var/www/html
