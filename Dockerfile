# PHP 5.2 still ships the legacy ext/mysql driver this CodeIgniter app relies
# on (system/application/config/database.php sets dbdriver = "mysql"). This
# image bundles PHP 5.2 with gd/mysql/mysqli/mcrypt/zip/mbstring/rewrite
# already built in, so no extension compilation is needed.
FROM lamarques/php:5.2-apache

# The default vhost serves /var/www with AllowOverride disabled; point it at
# the app's docroot (matches the docker-compose bind mount) and allow the
# CodeIgniter .htaccess to take effect.
RUN sed -i \
    -e 's#DocumentRoot /var/www#DocumentRoot /var/www/html#' \
    -e 's#<Directory /var/www/>#<Directory /var/www/html/>#' \
    /etc/apache2/sites-enabled/000-default \
    && sed -i '/<Directory \/var\/www\/html\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/sites-enabled/000-default

WORKDIR /var/www/html
