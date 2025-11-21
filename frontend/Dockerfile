FROM php:8.2-apache

# Set locale and timezone
ENV LANG=C.UTF-8 \
    LC_ALL=C.UTF-8 \
    TZ=Asia/Ho_Chi_Minh

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    locales \
    && echo "vi_VN.UTF-8 UTF-8" >> /etc/locale.gen \
    && locale-gen \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli

# Configure PHP for UTF-8
RUN echo "default_charset = \"UTF-8\"" >> /usr/local/etc/php/conf.d/charset.ini \
    && echo "mbstring.internal_encoding = UTF-8" >> /usr/local/etc/php/conf.d/charset.ini

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache for UTF-8
RUN echo "AddDefaultCharset UTF-8" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
