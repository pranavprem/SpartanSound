FROM php:8.5-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite \
    && printf '%s\n' \
        'display_errors=Off' \
        'log_errors=On' \
        > /usr/local/etc/php/conf.d/spartansound.ini \
    && printf '%s\n' \
        'ServerName localhost' \
        '<Directory /var/www/html>' \
        'AllowOverride All' \
        'Require all granted' \
        '</Directory>' \
        > /etc/apache2/conf-available/spartansound.conf \
    && a2enconf spartansound
