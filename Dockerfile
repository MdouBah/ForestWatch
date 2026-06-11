FROM php:8.3-apache

# ── Dépendances système ────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libpq-dev libxml2-dev libzip-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# ── Extensions PHP ─────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
      pdo pdo_pgsql pgsql \
      mbstring bcmath gd \
      exif pcntl zip \
      dom xml opcache

# ── Configuration opcache (production) ────────────────────────
RUN echo "opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=10000\nopcache.revalidate_freq=0" \
    >> /usr/local/etc/php/conf.d/opcache.ini

# ── Composer ───────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Apache : pointer sur /public ──────────────────────────────
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf

# Autoriser .htaccess dans public/
RUN printf '<Directory ${APACHE_DOCUMENT_ROOT}>\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' \
    > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel

WORKDIR /var/www/html

# ── Dépendances PHP (couche cachée séparément) ─────────────────
COPY composer.json composer.lock ./
RUN composer install \
      --no-dev \
      --no-scripts \
      --no-interaction \
      --optimize-autoloader

# ── Code source ────────────────────────────────────────────────
COPY . .

# ── Lien stockage + permissions ────────────────────────────────
RUN mkdir -p \
      storage/app/public/avatars \
      storage/app/public/rapports \
      storage/framework/cache/data \
      storage/framework/sessions \
      storage/framework/views \
      storage/logs \
      bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# ── Script de démarrage ────────────────────────────────────────
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
