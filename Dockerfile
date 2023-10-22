# Utiliza una imagen oficial de PHP con Apache como imagen base
FROM php:8.1.13-apache

# Establece el directorio de trabajo en el contenedor
WORKDIR /var/www/html

# Instala las extensiones necesarias
RUN apt-get update && apt-get install -y libpq-dev libicu-dev g++ unzip libpng-dev \
    && docker-php-ext-configure pdo_pgsql --with-pdo-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql mysqli pgsql  intl gd

# Copia los archivos de tu proyecto CodeIgniter al contenedor
COPY ./ /var/www/html

# Configura el directorio raíz de Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilita el módulo de Apache para rewrite (para URLs amigables)
RUN a2enmod rewrite

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ["composer.json", "var/www/html/"]
WORKDIR /var/www/html/
# Instala la biblioteca Firebase\JWT\JWT
RUN composer require firebase/php-jwt

# Ejecuta `composer update` en el contenedor
RUN composer update --lock

# Establece los permisos de carpetas
RUN chown -R www-data:www-data /var/www/html/writable/cache/
RUN chown -R www-data:www-data /var/www/html/writable/session/
RUN chown -R www-data:www-data /var/www/html/writable/uploads/
RUN chown -R www-data:www-data /var/www/html/writable/logs/
RUN chown -R www-data:www-data /var/www/html/recursos
RUN chown -R www-data:www-data /var/www/html/public
RUN chmod -R 775 /var/www/html/writable/cache/
RUN chmod -R 775 /var/www/html/writable/session/
RUN chmod -R 775 /var/www/html/writable/uploads/
RUN chmod -R 775 /var/www/html/writable/logs/
RUN chmod -R 775 /var/www/html/recursos
RUN chmod -R 755 /var/www/html/writable
RUN chmod -R 755 /var/www/html/public

