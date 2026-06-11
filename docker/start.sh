#!/bin/bash
set -e

echo "============================================"
echo "  FORESTWATCH — Démarrage production"
echo "============================================"

cd /var/www/html

# ── 1. Générer APP_KEY si absente ──────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "→ Génération de APP_KEY..."
    export APP_KEY=$(php artisan key:generate --show --no-ansi)
fi

# ── 2. Lien stockage public ───────────────────────────────────
echo "→ Création du lien storage..."
php artisan storage:link --force 2>/dev/null || true

# ── 3. Migrations ─────────────────────────────────────────────
echo "→ Migration de la base de données..."
php artisan migrate --force

# ── 4. Données de démonstration (premier déploiement) ─────────
if [ "${RUN_SEEDER:-false}" = "true" ]; then
    echo "→ Chargement des données de démonstration..."
    php artisan db:seed --force
fi

# ── 5. Optimisation Laravel ───────────────────────────────────
echo "→ Optimisation (config + routes + vues)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 6. Apache ─────────────────────────────────────────────────
echo "→ Démarrage Apache..."
echo "============================================"
exec apache2-foreground
