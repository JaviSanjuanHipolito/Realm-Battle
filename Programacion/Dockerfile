FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/

RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf