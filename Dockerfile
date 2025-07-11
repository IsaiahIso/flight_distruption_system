# Use PHP with Apache
FROM php:8.1-apache

# Set the Apache DocumentRoot to /var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Update Apache's default site config to use the new DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Copy your entire project into the container
COPY ./ /var/www/html/

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Ensure the home page is loaded from home/index.html
RUN echo "DirectoryIndex home/index.html" >> /etc/apache2/apache2.conf

# Fix permissions
RUN chmod -R 755 /var/www/html/auth /var/www/html/home /var/www/html/styles

# Expose port 80
EXPOSE 80
 