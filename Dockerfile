FROM oven/bun:1 AS bun-source

FROM unit:1.34.1-php8.4

RUN apt update && apt install -y \
    curl wget unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev libpq-dev \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl opcache pdo pdo_mysql pdo_pgsql intl zip gd exif ftp bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit_buffer_size=256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=512M" > /usr/local/etc/php/conf.d/custom.ini \        
    && echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY --from=bun-source /usr/local/bin/bun /usr/local/bin/bun

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

RUN chown -R unit:unit /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY --chown=unit:unit . .

RUN composer install --prefer-dist --optimize-autoloader --no-interaction

RUN bun install && bun run build && rm -rf /var/www/html/.bun /var/www/html/node_modules

RUN chown -R unit:unit /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY unit.json /docker-entrypoint.d/unit.json
COPY supervisord.conf /etc/supervisord.conf

RUN chmod +x /var/www/html/entrypoint.sh

EXPOSE 80 8080

CMD ["/var/www/html/entrypoint.sh"]
