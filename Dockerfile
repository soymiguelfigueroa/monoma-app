FROM php:8.2.18-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql