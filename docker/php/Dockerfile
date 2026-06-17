FROM php:8.2-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        cron \
        default-mysql-client \
        git \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libzip-dev \
        procps \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-popcorn-server.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-popcorn-server.conf
COPY docker/scripts/docker-entrypoint.sh /usr/local/bin/popcorn-server-entrypoint
COPY docker/scripts/cron-entrypoint.sh /usr/local/bin/popcorn-server-cron-entrypoint
COPY docker/cron/popcorn-server /etc/cron.d/popcorn-server

RUN chmod +x /usr/local/bin/popcorn-server-entrypoint /usr/local/bin/popcorn-server-cron-entrypoint \
    && chmod 0644 /etc/cron.d/popcorn-server \
    && crontab /etc/cron.d/popcorn-server

WORKDIR /app

ENTRYPOINT ["/usr/local/bin/popcorn-server-entrypoint"]
CMD ["php-fpm"]
