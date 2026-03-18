# 1. Use an official PHP image with Apache (just like XAMPP)
FROM php:8.2-apache

# 2. Install PostgreSQL drivers so PHP can talk to Neon
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# 3. Enable Apache mod_rewrite (useful for clean URLs later)
RUN a2enmod rewrite

# 4. Copy your local files into the server's web directory
COPY . /var/www/html/

# 5. Set correct permissions for the web server
RUN chown -R www-data:www-data /var/www/html/

# 6. Tell the server to listen on the port Render provides
EXPOSE 80