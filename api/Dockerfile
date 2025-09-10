FROM php:8.4-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libonig-dev libicu-dev libxml2-dev \
    && docker-php-ext-install zip intl pdo pdo_mysql pdo_pgsql bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create Laravel app if not present (idempotent for bind mounts)
RUN if [ ! -f composer.json ]; then \
      composer create-project laravel/laravel:^12.0 . --no-interaction --prefer-dist; \
    fi

# PHP ini dev defaults
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/php-custom.ini

# Install required PHP packages via Composer
RUN composer require --no-interaction \
      laravel/sanctum:^4.0 \
      spatie/laravel-permission:^6.10 \
      nwidart/laravel-modules:^11.0 \
      darkaonline/l5-swagger:^9.0 \
      laravel/horizon:^6.0 \
    && composer require --dev --no-interaction \
      pestphp/pest:^3.0 pestphp/pest-plugin-laravel:^3.0 \
      phpstan/phpstan:^1.11 nunomaduro/larastan:^2.9 \
    || true

# App bootstrap (publish assets/configs)
RUN php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force || true \
 && php artisan vendor:publish --provider="DarkaOnLine\L5Swagger\L5SwaggerServiceProvider" --force || true \
 && php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider" --force || true \
 && php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider" --force || true \
 || true

# Entry
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]


