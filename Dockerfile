FROM php:7.4-apache

# Install system dependencies and PHP extensions required by SuiteCRM
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip \
        curl \
        json \
        mbstring \
        mysqli \
        pdo_mysql \
        xml \
        intl \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite
RUN a2enmod headers

# Configure PHP settings for SuiteCRM
RUN echo "upload_max_filesize = 8M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 8M" >> /usr/local/etc/php/php.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini \
    && echo "max_execution_time = 120" >> /usr/local/etc/php/php.ini \
    && echo "max_input_vars = 3000" >> /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Set proper permissions on startup
CMD ["apache2-foreground"]