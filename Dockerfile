FROM php:8.2-apache

# Installa dipendenze di sistema e PHP estensioni
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Abilita mod_rewrite per Laravel
RUN a2enmod rewrite

# Installa Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installa Node.js (per buildare Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Imposta la working directory
WORKDIR /var/www/html

# Copia i file del progetto
COPY . .

# Installa dipendenze PHP
RUN composer install --no-dev --optimize-autoloader

# Installa dipendenze JS e builda gli asset con Vite
RUN npm ci && npm run build

# Permessi storage e cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Copia lo script start e rendilo eseguibile
COPY start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

# Configura Apache per puntare alla cartella public
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Espone la porta
EXPOSE 80

# Avvio
CMD ["/var/www/html/start.sh"]
