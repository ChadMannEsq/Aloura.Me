FROM wordpress:php7.4-apache

COPY --chown=www-data:www-data . /var/www/html
