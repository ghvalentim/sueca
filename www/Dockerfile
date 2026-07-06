FROM php:8.3-apache

# Instalar extensão PDO MySQL exigida pelo Portal
RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libonig-dev


# Ativar o mod_rewrite do Apache para as rotas MVC funcionarem
RUN a2enmod rewrite

RUN printf '<Directory "/var/www/html/public">\n\
AllowOverride All\n\
Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/portal.conf \
&& a2enconf portal

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Alterar o DocumentRoot para apontar para a pasta public do Portal
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf