# Étape 1 : Builder les assets front (Vite)
FROM node:18 AS nodebuilder

WORKDIR /app
COPY package.json pnpm-lock.yaml ./
RUN npm install -g pnpm
RUN pnpm install

COPY . .
RUN pnpm run build     # Compile les assets dans public/build

# Étape 2 : Builder l’application Laravel
FROM php:8.2-fpm AS phpbuilder

RUN apt-get update && apt-get install -y \
    zip unzip curl git libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=nodebuilder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader
RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
