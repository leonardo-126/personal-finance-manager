#!/usr/bin/env bash
set -e

cd /app

# Garante que as dependências existam (caso o volume esteja vazio)
if [ ! -f vendor/autoload.php ]; then
    echo "==> Instalando dependências do Composer..."
    composer install --no-interaction --prefer-dist --no-progress
fi

# Serviços secundários (ex.: worker de fila) apenas esperam o backend
# preparar o ambiente, sem recriar .env / key / migrations.
if [ "${SKIP_SETUP:-false}" = "true" ]; then
    echo "==> Aguardando o backend preparar o ambiente..."
    until [ -f .env ] && grep -q '^APP_KEY=base64:' .env; do
        sleep 2
    done
    exec "$@"
fi

# Cria o .env a partir do exemplo, se necessário
if [ ! -f .env ]; then
    echo "==> Criando .env a partir de .env.example..."
    cp .env.example .env
fi

# Banco SQLite: garante que o arquivo exista
if grep -q '^DB_CONNECTION=sqlite' .env; then
    mkdir -p database
    touch database/database.sqlite
fi

# Gera a APP_KEY se ainda não houver
if ! grep -q '^APP_KEY=base64:' .env; then
    echo "==> Gerando APP_KEY..."
    php artisan key:generate --force
fi

# Permissões de escrita para storage e cache
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chmod -R ug+rw storage bootstrap/cache || true

# Link público para os arquivos enviados (avatares, etc.)
echo "==> Garantindo o symlink public/storage..."
php artisan storage:link --force

# Executa as migrações
echo "==> Rodando migrations..."
php artisan migrate --force

exec "$@"
