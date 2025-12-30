
# Запустить все сидеры (из DatabaseSeeder):
docker compose exec app php artisan db:seed
или (v1)
docker-compose exec app php artisan db:seed


# Полный сброс БД и загрузка всех сидов заново:
docker compose exec app php artisan migrate:fresh --seed


# Запустить конкретный сидер:
docker compose exec app php artisan db:seed --class=Database\\Seeders\\CashTypesSeeder



php8.4 artisan config:clear
php8.4 artisan route:clear
php8.4 artisan view:clear
php8.4 artisan cache:clear
php8.4 artisan optimize:clear
php8.4 artisan clear-compiled
php8.4 artisan config:cache
php8.4 artisan route:cache
php8.4 artisan view:cache
php8.4 artisan optimize

php8.4 artisan route:list


php8.4 artisan migrate --path=database/migrations/2025_09_14_000026_create_avr_employees_table.php