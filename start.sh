#!/bin/sh
php artisan migrate --force || true
php artisan db:seed --force || true
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
