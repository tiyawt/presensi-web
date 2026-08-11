FROM richarvey/nginx-php-fpm:3.1.6

# Copy source code aplikasi
COPY . .

# Environment default (bisa di-override lewat Render dashboard)
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1

# Laravel specific
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Install FFI extension (dibutuhkan driver Turso/libSQL)
RUN docker-php-ext-install ffi \
    && echo "ffi.enable=true" >> /usr/local/etc/php/conf.d/ffi.ini

# Composer install (tanpa dev dependencies)
RUN composer install --no-dev --working-dir=/var/www/html --optimize-autoloader

# Permission storage & cache
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["/start.sh"]
