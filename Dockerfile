FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html

RUN echo "ServerName localhost" >> /etc/apache2/apache2.confFROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf