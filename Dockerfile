# Use PHP 8.2 Apache
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql pgsql zip bcmath

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Setup Working Directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Clear Laravel caches to ensure clean build
RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Setup Entrypoint Script
COPY docker-run.sh /usr/local/bin/docker-run.sh
RUN chmod +x /usr/local/bin/docker-run.sh

# Expose port (Documentation only, Render ignores this but good practice)
EXPOSE 80

# Start command
CMD ["/usr/local/bin/docker-run.sh"]
