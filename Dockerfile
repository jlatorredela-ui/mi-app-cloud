# Usa la imagen oficial de PHP con Apache incorporado
FROM php:8.2-apache

# Instala las librerías necesarias para PostgreSQL y activa la extensión pdo_pgsql
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

# Copia todos los archivos de tu proyecto al servidor Apache
COPY . /var/www/html/

# Expone el puerto estándar de web
EXPOSE 80