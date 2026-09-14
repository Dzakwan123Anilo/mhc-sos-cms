FROM php:8.4-fpm-alpine3.21

RUN apk add --no-cache \
        bash \
        ca-certificates \
        freetype \
        libjpeg-turbo \
        libpng \
        libxml2 \
        libzip \
        nginx \
        oniguruma \
        supervisor \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        oniguruma-dev \
        zlib-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" gd dom mbstring zip pdo_mysql mysqli \
    && apk del .build-deps

COPY fpm/zz-docker.conf.example /usr/local/etc/php-fpm.d/zz-docker.conf
COPY fpm/security.ini /usr/local/etc/php/conf.d/zz-security.ini
COPY nginx/nginx.conf.example /etc/nginx/nginx.conf
COPY nginx/default.conf.example /etc/nginx/http.d/default.conf
COPY supervisor/supervisord.conf.example /etc/supervisord.conf

RUN mkdir -p /home/app/sessions /run/nginx \
    && chown -R www-data:www-data /home/app/sessions \
    && chmod 0700 /home/app/sessions

WORKDIR /home/app
EXPOSE 1880

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
