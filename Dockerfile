FROM php:8.2-apache

# Installa estensioni necessarie
RUN apt-get update && apt-get install -y \
    unzip libpng-dev libonig-dev libxml2-dev zip curl git \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installa Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia i file del progetto
COPY . /var/www/html

# Imposta working dir
WORKDIR /var/www/html

# Installa dipendenze Laravel
RUN composer install --no-dev --optimize-autoloader

# Setta i permessi per storage e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Porta esposta
EXPOSE 80

# Comando di avvio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
