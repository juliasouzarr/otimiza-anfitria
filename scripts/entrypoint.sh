#!/usr/bin/env bash
set -e

# Garantir diretórios de cache e logs com permissões corretas
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Verificar configurações de banco
if [ -n "$DATABASE_URL" ]; then
    echo "==> DATABASE_URL detectada."
elif [ -n "$DB_URL" ]; then
    echo "==> DB_URL detectada."
elif [ -n "$DB_HOST" ]; then
    echo "==> DB_HOST detectado: $DB_HOST"
else
    echo "==> AVISO: Nenhuma variável de banco (DATABASE_URL / DB_URL / DB_HOST) detectada!"
fi

# Executar migrações do banco de dados com retentativas
echo "==> Executando migrações do banco de dados..."
for i in 1 2 3 4 5; do
    if php artisan migrate --force; then
        echo "==> Migrações concluídas com sucesso!"
        break
    else
        echo "Tentativa $i/5 falhou. Aguardando banco responder..."
        sleep 3
    fi
done

# Executar seeds se solicitado (ou padrão na primeira inicialização)
if [ "$SEED_DATABASE" = "true" ]; then
    echo "==> Executando seeds iniciais..."
    php artisan db:seed --force || echo "Seeds já aplicadas ou ignoradas."
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

