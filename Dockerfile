FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data storage bootstrap/cache

# Copia lo script e rendilo eseguibile
COPY start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

# Configura Apache per puntare alla cartella public
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["/var/www/html/start.sh"]

