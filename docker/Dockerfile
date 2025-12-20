FROM php:8.5-cli

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (minimal for testing)
RUN docker-php-ext-install pcntl

# Install PCOV for code coverage
RUN pecl install pcov && docker-php-ext-enable pcov

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Set composer to allow running as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy application files
COPY . /app

# Default command
CMD ["php", "-v"]
