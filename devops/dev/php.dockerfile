FROM php:8.0-fpm
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git zip
RUN pecl install -o -f xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable xdebug
RUN curl https://getcomposer.org/download/1.10.13/composer.phar > /usr/local/bin/composer
RUN chmod 755 /usr/local/bin/composer
RUN useradd -m dev
WORKDIR /srv/edifact-parser
