# PHP 5.6 is used instead of a nonexistent "5.7" — it's the last PHP version
# that still ships the legacy ext/mysql driver this CodeIgniter app relies on
# (system/application/config/database.php sets dbdriver = "mysql").
FROM php:5.6-apache

# Debian Stretch (this image's base) is EOL: only the plain "main" archive
# mirror still has packages (updates/security pockets 404 on archive.debian.org),
# and its signing key has expired, so signature checks must be relaxed too.
RUN echo 'deb http://archive.debian.org/debian stretch main' > /etc/apt/sources.list \
    && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid \
    && echo 'Acquire::AllowInsecureRepositories "true";' > /etc/apt/apt.conf.d/99allow-insecure

RUN apt-get update && apt-get install -y --no-install-recommends --allow-unauthenticated \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libmcrypt-dev \
        libzip-dev \
        zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr --with-jpeg-dir=/usr \
    && docker-php-ext-install -j"$(nproc)" gd mysql mysqli mcrypt zip mbstring \
    && a2enmod rewrite \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
