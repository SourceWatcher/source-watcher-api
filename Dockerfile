# API server: PHP 8.4 Apache (aligned with board and dev PHP 8.4).
# Rebuild after changes: docker compose build api
FROM php:8.4-apache

RUN apt-get update -y && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends libzip-dev libpq-dev libonig-dev \
    && docker-php-ext-install pdo_mysql mysqli mbstring zip pdo_pgsql pgsql \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/
# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
