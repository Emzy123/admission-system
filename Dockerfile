FROM php:8.2-apache

# 1. Install system utilities and libraries
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# 2. Enable Apache mod_rewrite for Laravel
RUN a2enmod rewrite

# 3. Set working directory
WORKDIR /var/www/html

# 4. Copy application files
COPY . .

# 5. Install Composer and Dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 6. Set Directory Permissions (Fixes 403 Forbidden / Storage errors)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Configure Apache DocumentRoot to point to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 8. Expose Port 80 (Render maps this automatically)
EXPOSE 80
