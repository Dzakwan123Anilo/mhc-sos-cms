FROM php:8.3.6-fpm-alpine3.18

RUN apk update

RUN apk --no-cache add \
        freetype freetype-dev zlib zlib-dev libpng libpng-dev libjpeg-turbo libjpeg-turbo-dev libzip libzip-dev oniguruma-dev libxml2-dev \
        wget \
        git \
        supervisor \
        bash \
        nano \
        composer

RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) gd fileinfo dom mbstring zip pdo_mysql mysqli \
    && docker-php-source delete

RUN apk add openssl curl ca-certificates \
    && printf "%s%s%s\n" "http://nginx.org/packages/alpine/v" `egrep -o '^[0-9]+\.[0-9]+' /etc/alpine-release` "/main" | tee -a /etc/apk/repositories \
    && curl -o /tmp/nginx_signing.rsa.pub https://nginx.org/keys/nginx_signing.rsa.pub \
    && openssl rsa -pubin -in /tmp/nginx_signing.rsa.pub -text -noout \
    && mv /tmp/nginx_signing.rsa.pub /etc/apk/keys/ \
    && apk add nginx

COPY fpm/zz-docker.conf.example /usr/local/etc/php-fpm.d/zz-docker.conf

COPY nginx/nginx.conf.example /etc/nginx/nginx.conf

COPY nginx/default.conf.example /etc/nginx/http.d/default.conf

COPY supervisor/supervisord.conf.example /etc/supervisord.conf

RUN mkdir -p /home/app/sessions && chmod 777 /home/app/sessions

WORKDIR /home

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]