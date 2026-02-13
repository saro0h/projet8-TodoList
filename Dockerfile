# Dockerfile
FROM php:7.2-fpm

# Installation des dépendances système
RUN sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list \
    && sed -i '/security.debian.org/d' /etc/apt/sources.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       zip unzip \
       libicu-dev \
       libxml2-dev \
       libzip-dev \
       libonig-dev \
       curl \
    && docker-php-ext-install \
       intl \
       pdo \
       pdo_mysql \
       mbstring \
       xml \
       zip \
       opcache \
    # Installer Xdebug pour PHP 7.2
    && pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug \
    # Installer APCu
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && rm -rf /var/lib/apt/lists/*

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier une config xdebug (tu peux l’éditer depuis docker-compose si besoin)
COPY ./docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Définir le répertoire de travail
WORKDIR /var/www/html

# Exposer le port PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
