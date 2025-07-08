FROM php:8.2-apache

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    npm \
    nodejs \
    && apt-get clean

# Instala extensiones PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql intl mbstring zip

# Instala Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia todo el proyecto Laravel
COPY . .

# Instala dependencias PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instala y compila assets (si usas frontend aquí)
RUN npm install && npm run build

# Permisos a carpetas necesarias
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configura Apache para apuntar a /public y activar mod_rewrite
RUN sed -i 's!/var/www/html!/var/www/html/public!' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

EXPOSE 80

# Comando de arranque
CMD php artisan migrate --force && \
    php artisan config:cache && \
    apache2-foreground
