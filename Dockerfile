# ===============================
# 1️⃣ Build Vite
# ===============================
FROM node:18 AS nodebuilder

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js .
COPY public ./public

RUN npm run build


# ===============================
# 2️⃣ PHP Production
# ===============================
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier uniquement le code PHP
COPY . .

# Injecter UNIQUEMENT le build Vite
COPY --from=nodebuilder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader

RUN php artisan key:generate || true
RUN php artisan config:clear
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

RUN chown -R www-data:www-data storage bootstrap/cache public/build

EXPOSE 9000
CMD ["php-fpm"]
