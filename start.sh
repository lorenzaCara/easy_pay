#!/bin/bash

# Usa la porta passata da Render, altrimenti 80
PORT=${PORT:-80}

# Modifica configurazioni Apache per la porta dinamica
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

# Avvia Apache in foreground
apache2-foreground
