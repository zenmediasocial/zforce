# Zforce Deployment Guide (AcePanel)

## Architecture

```
/opt/ace/project/zforce/        # Laravel application code
/opt/ace/sites/zforce/public    # Webroot (nginx serves this)
                                  # Symlinks to /opt/ace/project/zforce/public

/home/marshall/Code/zforce/     # Git repo (development, NEVER served)
```

## Separation of Concerns

| Path | Purpose | Persistence |
|------|---------|-------------|
| `/opt/ace/project/zforce/` | Deployed application code | Replaced on each deploy |
| `/opt/ace/project/zforce/.env` | Environment secrets | Persistent (never in git) |
| `/opt/ace/project/zforce/storage/` | Logs, cache, uploads | Persistent |
| `/opt/ace/sites/zforce/public` | Webroot symlink | Persistent |

## Deploy Steps

```bash
# 1. Clone/update code
sudo mkdir -p /opt/ace/project
sudo chown -R $USER:$USER /opt/ace/project
cd /opt/ace/project
git clone https://github.com/zenmediasocial/zforce.git

# 2. Install dependencies
cd /opt/ace/project/zforce
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Set up webroot symlink
sudo mkdir -p /opt/ace/sites/zforce
sudo ln -sf /opt/ace/project/zforce/public /opt/ace/sites/zforce/public

# 4. Create .env (copy from secure location, never commit)
cp /etc/zforce/env /opt/ace/project/zforce/.env
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run migrations
php artisan migrate --force

# 6. Set permissions
sudo chown -R www:www /opt/ace/project/zforce/storage
sudo chown -R www:www /opt/ace/project/zforce/bootstrap/cache
```

## PHP 8.5 PostgreSQL Compatibility

**Do NOT bundle libpq in the project.** Handle this at the system level:

### Option 1: Upgrade system libpq (Recommended)

```bash
# Add PostgreSQL official repo for newer libpq
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
sudo apt update
sudo apt install libpq5
```

### Option 2: Set LD_LIBRARY_PATH in PHP-FPM pool

Edit `/opt/ace/server/php/85/etc/php-fpm.d/zforce.conf`:

```ini
[zforce]
user = www
group = www
listen = /run/php/zforce.sock

; Environment
env[LD_LIBRARY_PATH] = /usr/lib/x86_64-linux-gnu
env[APP_ENV] = production
env[APP_KEY] = base64:...
```

### Option 3: Rebuild pdo_pgsql against system libpq

```bash
cd /opt/ace/server/php/85/src/ext/pdo_pgsql
/opt/ace/server/php/85/bin/phpize
./configure --with-php-config=/opt/ace/server/php/85/bin/php-config
make && sudo make install
sudo systemctl restart php-fpm-85
```

## Rollback

```bash
cd /opt/ace/project
mv zforce zforce-broken-$(date +%s)
git clone https://github.com/zenmediasocial/zforce.git zforce
# Restore .env and storage from backup
```

## What This Repo Contains

- **Application code** — Laravel app, Livewire components, Vortex services
- **Database migrations** — Schema definitions
- **Blade views** — Public pages + terminal UI
- **Config** — Framework config (no secrets)
- **Documentation** — Architecture, n8n workflows, deployment

## What This Repo Does NOT Contain

- `.env` files (secrets live in `/etc/zforce/env` or similar)
- `vendor/` (installed by Composer at deploy)
- `node_modules/` (installed by npm at deploy)
- `storage/logs/`, `storage/framework/cache/` (created at runtime)
- `public/build/` (built by Vite at deploy)
- System libraries like `libpq.so`
