#!/usr/bin/env bash
# render-build.sh

# Salir en caso de error
set -e

# Instalar dependencias de PHP
composer install --optimize-autoloader --no-dev

# Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Instalar Node.js y compilar assets (Vite)
npm install
npm run build

# Ejecutar migraciones (Fuerza la ejecución en producción)
php artisan migrate --force

# Crear enlace simbólico del storage
php artisan storage:link
