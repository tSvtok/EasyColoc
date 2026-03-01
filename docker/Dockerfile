FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000

# Dépendances système
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip curl ca-certificates gnupg \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Node.js proprement via NodeSource
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest   # mettre à jour npm à la dernière version

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer utilisateur laravel
RUN groupadd -g ${GID} laravel \
    && useradd -u ${UID} -g laravel -m laravel

WORKDIR /var/www
RUN chown -R laravel:laravel /var/www
USER laravel