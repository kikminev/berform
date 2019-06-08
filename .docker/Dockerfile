FROM php:7.2.17-apache
WORKDIR /var/www

COPY . /var/www
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www && a2enmod rewrite

RUN apt-get update
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev
RUN pecl install mongodb
#alcaeus/mongo-php-adapter depends on this
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev
#RUN pecl install mongodb
RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN echo extension=mongodb.so >> /usr/local/etc/php/php.ini
RUN apachectl restart
RUN apt-get install sudo -y
RUN sudo apt-get install software-properties-common -y
RUN sudo apt-get install apt-transport-https
RUN apt-get install vim -y
RUN sudo apt install wget -y
RUN sudo apt-get install nano
RUN cp -rp .aws/ /
#FROM ubuntu:16.04
#RUN apt-get update -y && apt-get install -y apt-transport-https -y
#RUN su -
#RUN apt-get install sudo
#RUN sudo apt-get install apache2 -y
#RUN sudo apt-get install php libapache2-mod-php -y
#RUN sudo service apache2 restart
#
