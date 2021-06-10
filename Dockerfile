FROM alpine:3.13

LABEL Maintainer="Yoga Hanggara <yohang88@gmail.com>" \
    Description="Lightweight Laravel app container with Nginx 1.16 & PHP-FPM 7.4 based on Alpine Linux (forked from trafex/alpine-nginx-php7)."

# Install packages
RUN apk --no-cache add \
    php7 \
    php7-fpm \
    php7-opcache \
    php7-json \
    php7-openssl \
    php7-curl \
    php7-phar \
    php7-session \
    php7-pdo \
    php7-pdo_mysql \
    php7-pdo_sqlite \
    php7-mbstring \
    php7-dom \
    php7-gd \
    php7-iconv \
    php7-zip \
    php7-zlib \
    php7-xml \
    php7-intl \
    php7-dom \
    php7-simplexml \
    php7-xmlwriter \
    php7-xmlreader \
    php7-ctype \
    php7-fileinfo \
    php7-tokenizer \
    nginx \
    supervisor

# Remove default.conf nginx
RUN rm /etc/nginx/conf.d/default.conf

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY docker/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY docker/php.ini /etc/php7/conf.d/custom.ini

# Configure supervisord
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup document root
RUN mkdir -p /var/www/html

# Setup entrypoint
COPY docker/docker-entrypoint.sh docker-entrypoint.sh
RUN chmod +x docker-entrypoint.sh

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /var/www/html && \
    chown -R nobody.nobody /run && \
    chown -R nobody.nobody /var/lib/nginx && \
    chown -R nobody.nobody /var/log/nginx

# Switch to use a non-root user from here on
USER nobody

# Add application
WORKDIR /var/www/html
COPY --chown=nobody . /var/www/html

# Install composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Run composer install to install the dependencies
RUN composer install --no-cache --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress && \
    composer dump-autoload --optimize

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
ENTRYPOINT ["/bin/sh", "/docker-entrypoint.sh"]
