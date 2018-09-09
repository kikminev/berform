FROM php:7.1.8-apache

COPY . /var/www
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www && a2enmod rewrite

RUN apt-get update
#RUN pecl install mongodb
# alcaeus/mongo-php-adapter depends on this
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev
RUN pecl install mongodb
RUN echo extension=mongodb.so >> /usr/local/etc/php/conf.d/php.ini
RUN apachectl restart
RUN apt-get install vim -y
