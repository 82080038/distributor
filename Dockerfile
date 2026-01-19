FROM php:7.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo_mysql \
    zip \
    mbstring \
    json \
    opcache

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure PHP
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.ini && \
    echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.ini

# Set Apache configuration
RUN echo "<Directory /var/www/html>" > /etc/apache2/sites-available/000-default.conf && \
    echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Create logs directory
RUN mkdir -p /var/log/apache2 && \
    chown www-data:www-data /var/log/apache2

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
