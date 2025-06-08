FROM php:8.4-cli-alpine3.20 AS build
COPY . /build/
WORKDIR /build
RUN apk add mariadb-client
RUN docker-php-ext-install pdo_mysql && curl -O https://getcomposer.org/installer && php installer && php composer.phar install
CMD ["/bin/sh", "-c", "php asatru migrate:list && php asatru serve"]
