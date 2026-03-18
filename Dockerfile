# 1. Use the official PHP image with Apache
FROM php:8.2-apache

# 2. Install PostgreSQL drivers so PHP can talk to Neon
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# 3. Enable Apache Rewrite (Important for clean URLs)
RUN a2enmod rewrite

# 4. Copy ALL your files into the server
COPY . /var/www/html/

# 5. Fix the 403 Forbidden error by setting permissions
RUN chmod -R 755 /var/www/html/ && chown -R www-data:www-data /var/www/html/

# 6. Expose the port
EXPOSE 80