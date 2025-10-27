FROM php:8.2-apache

# Instalar PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar todo el proyecto a /var/www/html
COPY . /var/www/html/

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Habilitar mod_rewrite (opcional, útil para frameworks)
RUN a2enmod rewrite
