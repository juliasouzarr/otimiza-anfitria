# Estágio 1: Build dos assets frontend (Vite / Tailwind / Alpine)
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Estágio 2: Servidor de Aplicação PHP 8.2 + Nginx
FROM php:8.2-fpm

# Instalar pacotes de sistema, bibliotecas para extensões e Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP (com suporte a MySQL e PostgreSQL)
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Obter Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código-fonte da aplicação
COPY . .

# Copiar build gerado no estágio frontend
COPY --from=frontend /app/public/build ./public/build

# Instalar dependências PHP de produção
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar o Nginx
COPY conf/nginx-site.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Normalizar quebras de linha caso o arquivo tenha sido salvo em ambiente Windows
RUN sed -i 's/\r$//' /var/www/html/scripts/entrypoint.sh

# Configurar permissões de storage e bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/scripts/entrypoint.sh

EXPOSE 80

CMD ["/var/www/html/scripts/entrypoint.sh"]
