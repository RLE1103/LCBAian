FROM php:8.2-fpm
RUN apt-get update && apt-get install -y nginx supervisor ca-certificates git curl unzip libonig-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction
RUN mkdir -p storage bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
EXPOSE 10000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
