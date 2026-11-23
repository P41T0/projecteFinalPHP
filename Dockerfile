FROM php:8.6-apache AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends libjpeg62-turbo-dev libonig-dev libpng-dev libwebp-dev libxml2-dev libzip-dev unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install bcmath gd mbstring pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

FROM php-base AS app-build

COPY --from=composer:2.11 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .
RUN rm -f bootstrap/cache/*.php \
    && composer dump-autoload --no-dev --optimize --classmap-authoritative --no-scripts \
    && php artisan package:discover --ansi

FROM node:26-bookworm-slim AS frontend-build

WORKDIR /app

RUN npm install --global pnpm@12.21.0
COPY package.json pnpm-lock.yaml pnpm-workspace.yaml ./
RUN pnpm install --frozen-lockfile

COPY resources ./resources
COPY vite.config.js ./
COPY public ./public
COPY --from=app-build /var/www/html/vendor ./vendor

RUN pnpm run build

FROM php-base AS app

COPY --from=app-build --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=frontend-build --chown=www-data:www-data /app/public/build /var/www/html/public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
    storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

FROM php-base AS app-test

COPY --from=composer:2.11 /usr/bin/composer /usr/bin/composer
COPY --from=frontend-build /usr/local/ /usr/local/

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .
COPY --from=frontend-build /app/node_modules /var/www/html/node_modules
RUN rm -f bootstrap/cache/*.php \
    && cp .env.example .env \
    && composer dump-autoload --optimize --no-scripts \
    && php artisan package:discover --ansi

COPY --from=frontend-build --chown=www-data:www-data /app/public/build /var/www/html/public/build

CMD ["php", "artisan", "test"]
