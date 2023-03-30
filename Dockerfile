FROM php:8.2.4-apache as build-stage


RUN apt-get update \
    && apt-get install -y --no-install-recommends locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
#    mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql gd opcache intl zip calendar dom mbstring zip gd xsl
RUN pecl install apcu && docker-php-ext-enable apcu

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT=1

WORKDIR /var/www/

# RUN composer install --optimize-autoloader --no-interaction --no-progress;

FROM nginx:alpine as development-stage
COPY --from=build-stage /var/www/public  /var/www
COPY --from=build-stage /var/www/nginx.conf   /etc/nginx/conf.d/
COPY --from=build-stage /var/www/symfony.conf   /etc/nginx/conf.d/
RUN ls 

CMD ["nginx"]

EXPOSE 80 443