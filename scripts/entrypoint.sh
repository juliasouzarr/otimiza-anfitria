#!/usr/bin/env bash
set -e

# Garantir diretórios de cache e logs com permissões corretas
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Executar migrações do banco de dados
echo "==> Executando migrações do banco de dados..."
php artisan migrate --force || echo "Aviso: Migrações falharam ou banco ainda não está acessível."

# Executar seeds se solicitado (ou padrão na primeira inicialização)
if [ "$SEED_DATABASE" = "true" ]; then
    echo "==> Executando seeds iniciais..."
    php artisan db:seed --force || echo "Aviso: Seeds falharam ou já existem."
fi

# Otimizações de cache do Laravel para produção
echo "==> Otimizando caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Criar link simbólico de storage
php artisan storage:link || true

echo "==> Iniciando PHP-FPM e Nginx..."
php-fpm -D
nginx -g "daemon off;"
