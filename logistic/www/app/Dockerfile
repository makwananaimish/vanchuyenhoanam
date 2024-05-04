FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libzip-dev

RUN docker-php-ext-install pdo_mysql mysqli zip

RUN apt-get update && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev jpegoptim webp && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd

RUN apt-get install -y libgmp-dev re2c libmhash-dev libmcrypt-dev file
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/
RUN docker-php-ext-configure gmp 
RUN docker-php-ext-install gmp

# Install dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libcurl4-nss-dev \
    libc-client-dev \
    libkrb5-dev \
    firebird-dev \
    libicu-dev \
    libxml2-dev \
    libxslt1-dev \
    autoconf \
    zip \
    cron \
    git \
    libssh2-1-dev
    
RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl && docker-php-ext-install imap

RUN docker-php-ext-install bcmath

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install nodejs
RUN cd ~ && curl -sL https://deb.nodesource.com/setup_12.x -o nodesource_setup.sh && bash nodesource_setup.sh

# Install pm2
RUN apt install nodejs -y && apt install build-essential && npm install pm2 -g

RUN echo 'max_input_vars = 10000' >> /usr/local/etc/php/conf.d/docker-php-max_input_vars.ini;

RUN echo 'memory_limit = 2048M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;