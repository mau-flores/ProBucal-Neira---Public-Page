# Usar imagen base de PHP con Apache
FROM php:8.1-apache

# Metadatos
LABEL maintainer="ProBucal Neira"
LABEL description="PHP Application with Apache and PostgreSQL support"

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    wget \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && a2enmod rewrite \
    && a2enmod headers \
    && rm -rf /var/lib/apt/lists/*

# Configurar Apache para servir desde /var/www/html
WORKDIR /var/www/html

# Copiar la aplicación al contenedor
COPY . /var/www/html/

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
