FROM php:8.2-apache

# Installa dipendenze
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Abilita mod_rewrite
RUN a2enmod rewrite

# Installa Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installa Node.js (per Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

# Copia composer.json e package.json prima (cache layer)
COPY composer.json composer.lock package*.json ./

# Installa dipendenze
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build

# Copia il resto del progetto
COPY . .

# Permessi
RUN chown -R www-data:www-data storage bootstrap/cache

# Script start
COPY start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

# Apache su /public
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["/var/www/html/start.sh"]
