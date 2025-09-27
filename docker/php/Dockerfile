# Image PHP officielle avec FPM
FROM php:8.3-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation de Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get install symfony-cli

# Configuration du répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers de dépendances
COPY composer.json composer.lock ./

# Installation des dépendances PHP
RUN composer install --no-scripts --no-autoloader

# Copie du code source
COPY . .

# Génération de l'autoloader optimisé
RUN composer dump-autoload --optimize

# Permissions pour le cache et les logs
RUN chown -R www-data:www-data /var/www/html/var

# Exposition du port 8000
EXPOSE 8000

# Commande par défaut (surchargée par docker-compose)
CMD ["symfony", "server:start", "--host=0.0.0.0", "--port=8000", "--no-tls"]