FROM php:8.4-fpm-alpine3.21

RUN apk update

RUN apk --no-cache add \
        freetype-dev zlib-dev libpng-dev libjpeg-turbo-dev libzip-dev libxml2-dev oniguruma-dev \
        nginx \
        supervisor \
        bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd fileinfo dom mbstring zip pdo_mysql mysqli \
    && docker-php-source delete

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

COPY fpm/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY php/docker-custom-php.ini /usr/local/etc/php/conf.d/docker-custom-php.ini

COPY nginx/nginx.conf /etc/nginx/nginx.conf

COPY nginx/default.conf /etc/nginx/http.d/default.conf

COPY supervisor/supervisord.conf /etc/supervisord.conf

RUN mkdir -p /home/app/sessions && chmod 777 /home/app/sessions

WORKDIR /home

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
