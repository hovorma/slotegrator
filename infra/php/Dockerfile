FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y libpq-dev libzip-dev libcurl4-openssl-dev libpng-dev libfreetype6-dev libjpeg-dev libwebp-dev
RUN docker-php-ext-install pdo pdo_pgsql pgsql curl zip gd

RUN apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

ENTRYPOINT ["docker-php-entrypoint"]
WORKDIR /var/www/html
EXPOSE 9000
CMD ["php-fpm"]