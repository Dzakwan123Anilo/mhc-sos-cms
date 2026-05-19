FROM php:8.4-fpm-alpine3.21

RUN apk update

RUN apk --no-cache add \
        freetype freetype-dev zlib zlib-dev libpng libpng-dev libjpeg-turbo libjpeg-turbo-dev libzip libzip-dev oniguruma-dev libxml2-dev \
        wget \
        curl \
        git \
        supervisor \
        bash \
        nano

RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) gd fileinfo dom mbstring zip pdo_mysql mysqli \
    && docker-php-source delete

RUN apk add --no-cache nginx \
    && mkdir -p /var/lib/nginx/tmp/client_body \
    && chown -R www-data:www-data /var/lib/nginx/tmp \
    && chmod -R 700 /var/lib/nginx/tmp

# Install Composer from official source for latest version
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY fpm/zz-docker.conf.example /usr/local/etc/php-fpm.d/zz-docker.conf

COPY nginx/nginx.conf.example /etc/nginx/nginx.conf

COPY nginx/default.conf.example /etc/nginx/http.d/default.conf

COPY supervisor/supervisord.conf.example /etc/supervisord.conf

RUN mkdir -p /home/app/sessions && chmod 777 /home/app/sessions

WORKDIR /home

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]