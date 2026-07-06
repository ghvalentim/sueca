FROM php:8.3-apache

# Instalar dependências básicas para o Laravel
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libonig-dev \
    g++


ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd intl

# Ativar o mod_rewrite do Apache para as rotas do Laravel funcionarem
RUN a2enmod rewrite

RUN printf 'RewriteEngine On\n\
RewriteCond %%{HTTP:Authorization} . \n\
RewriteRule .* - [E=HTTP_AUTHORIZATION:%%{HTTP:Authorization}]\n' > /etc/apache2/conf-available/jwt_fix.conf \
&& a2enconf jwt_fix

# Instalar o Composer (gestor de pacotes do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Alterar o DocumentRoot para apontar para a pasta public do Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf