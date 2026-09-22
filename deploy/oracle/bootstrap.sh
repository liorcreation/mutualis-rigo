#!/usr/bin/env bash

set -Eeuo pipefail

readonly APP_DIR="/var/www/mutualis-app"
readonly APP_USER="www-data"
readonly REPOSITORY_URL="${REPOSITORY_URL:-https://github.com/liorcreation/mutualis-rigo.git}"
readonly APP_BRANCH="${APP_BRANCH:-main}"

if [[ "${EUID}" -ne 0 ]]; then
    printf 'Ce script doit être exécuté avec sudo ou en root.\n' >&2
    exit 1
fi

if [[ -z "${APP_DOMAIN:-}" ]]; then
    read -r -p 'Domaine public de Mutualis (ex. mutualis.example.com) : ' APP_DOMAIN
fi

if [[ -z "${APP_DOMAIN}" ]]; then
    printf 'Le domaine est obligatoire.\n' >&2
    exit 1
fi

export DEBIAN_FRONTEND=noninteractive

apt-get update
apt-get install -y \
    ca-certificates \
    certbot \
    curl \
    git \
    nginx \
    openssl \
    postgresql \
    postgresql-contrib \
    php8.3-bcmath \
    php8.3-cli \
    php8.3-curl \
    php8.3-fpm \
    php8.3-gd \
    php8.3-intl \
    php8.3-mbstring \
    php8.3-pgsql \
    php8.3-xml \
    php8.3-zip \
    unzip \
    ufw

if ! command -v node >/dev/null 2>&1; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y nodejs
fi

if ! command -v composer >/dev/null 2>&1; then
    curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
    expected_checksum="$(curl -fsSL https://composer.github.io/installer.sig)"
    actual_checksum="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"

    if [[ "${expected_checksum}" != "${actual_checksum}" ]]; then
        printf 'La vérification de Composer a échoué.\n' >&2
        rm -f /tmp/composer-setup.php
        exit 1
    fi

    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm -f /tmp/composer-setup.php
fi

systemctl enable --now postgresql nginx php8.3-fpm

DB_PASSWORD="${DB_PASSWORD:-$(openssl rand -hex 24)}"

if runuser -u postgres -- psql -tAc "SELECT 1 FROM pg_roles WHERE rolname = 'mutualis'" | grep -q 1; then
    runuser -u postgres -- psql -v db_password="${DB_PASSWORD}" \
        -c "ALTER ROLE mutualis WITH PASSWORD :'db_password';"
else
    printf 'Création du compte PostgreSQL mutualis.\n'
    runuser -u postgres -- psql -v db_password="${DB_PASSWORD}" \
        -c "CREATE ROLE mutualis LOGIN PASSWORD :'db_password';"
fi

if ! runuser -u postgres -- psql -tAc "SELECT 1 FROM pg_database WHERE datname = 'mutualis'" | grep -q 1; then
    runuser -u postgres -- createdb --owner=mutualis mutualis
fi

install -d -o "${APP_USER}" -g "${APP_USER}" "${APP_DIR}"

if [[ -d "${APP_DIR}/.git" ]]; then
    git -C "${APP_DIR}" fetch origin "${APP_BRANCH}"
    git -C "${APP_DIR}" reset --hard "origin/${APP_BRANCH}"
else
    rm -rf "${APP_DIR}"
    git clone --branch "${APP_BRANCH}" --depth 1 "${REPOSITORY_URL}" "${APP_DIR}"
fi

cd "${APP_DIR}"

if [[ ! -f .env ]]; then
    cp .env.example .env
fi

set_env() {
    local key="$1"
    local value="$2"

    if grep -qE "^${key}=" .env; then
        sed -i "s#^${key}=.*#${key}=${value}#" .env
    else
        printf '%s=%s\n' "${key}" "${value}" >> .env
    fi
}

set_env APP_NAME Mutualis
set_env APP_ENV production
set_env APP_DEBUG false
set_env APP_URL "https://${APP_DOMAIN}"
set_env DB_CONNECTION pgsql
set_env DB_HOST 127.0.0.1
set_env DB_PORT 5432
set_env DB_DATABASE mutualis
set_env DB_USERNAME mutualis
set_env DB_PASSWORD "${DB_PASSWORD}"
set_env DB_SSLMODE prefer
set_env SESSION_DRIVER database
set_env CACHE_STORE database
set_env QUEUE_CONNECTION database
set_env FILESYSTEM_DISK local
set_env RIGO_PRIVATE_DISK local
set_env LOG_LEVEL warning

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build

if ! grep -qE '^APP_KEY=base64:.+$' .env; then
    php artisan key:generate --force
fi
php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan view:cache

chown -R "${APP_USER}:${APP_USER}" "${APP_DIR}"
chmod -R ug+rwx storage bootstrap/cache

install -D -o root -g root -m 0644 \
    deploy/oracle/nginx/mutualis.conf \
    /etc/nginx/sites-available/mutualis.conf
sed -i "s#__DOMAIN__#${APP_DOMAIN}#g" /etc/nginx/sites-available/mutualis.conf
ln -sfn /etc/nginx/sites-available/mutualis.conf /etc/nginx/sites-enabled/mutualis.conf
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

install -D -o root -g root -m 0644 \
    deploy/oracle/systemd/mutualis-queue.service \
    /etc/systemd/system/mutualis-queue.service
install -D -o root -g root -m 0644 \
    deploy/oracle/systemd/mutualis-scheduler.service \
    /etc/systemd/system/mutualis-scheduler.service
install -D -o root -g root -m 0644 \
    deploy/oracle/systemd/mutualis-scheduler.timer \
    /etc/systemd/system/mutualis-scheduler.timer

systemctl daemon-reload
systemctl enable --now mutualis-queue.service mutualis-scheduler.timer

ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

printf '\nMutualis est installé.\n'
printf 'Étape suivante : pointer le domaine %s vers l’IP publique de cette VM.\n' "${APP_DOMAIN}"
printf 'Puis activer HTTPS avec : sudo certbot --nginx -d %s\n' "${APP_DOMAIN}"
