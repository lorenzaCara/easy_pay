# Usa PHP 8.2 con Apache come base
FROM php:8.2-apache

# Installa dipendenze di sistema e librerie utili per Laravel
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Abilita mod_rewrite per Apache (necessario per le route Laravel)
RUN a2enmod rewrite

# Imposta la cartella di lavoro
WORKDIR /var/www/html

# Copia composer dal container ufficiale
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia tutto il progetto nel container
COPY . /var/www/html

# Installa le dipendenze di Laravel
RUN composer install --no-dev --optimize-autoloader

# Imposta i permessi corretti per storage e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configura Apache per puntare alla cartella public
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Espone la porta
EXPOSE 80

# Comando di avvio
CMD ["apache2-foreground"]
