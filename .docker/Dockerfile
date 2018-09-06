FROM php:7.1.8-apache

COPY . /var/www
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www && a2enmod rewrite
RUN apt-get install vim
RUN php composer.phar config "platform.ext-mongo" "1.6.16" && php composer.phar require alcaeus/mongo-php-adapter