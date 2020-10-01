FROM php:7.4-cli

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

WORKDIR /app

COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Require the library and will also update dependencies
RUN composer require stuartapp/stuart-client-php

ENTRYPOINT ["php", "index.php"]