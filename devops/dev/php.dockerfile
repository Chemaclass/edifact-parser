FROM php:8.0-fpm
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git zip
RUN pecl install -o -f xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable xdebug
# Composer 2 from the official image — 1.10.13 (2020) cannot install from Packagist's
# current metadata, and pinning a phar by URL goes stale silently.
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN useradd -m dev
WORKDIR /srv/edifact-parser
