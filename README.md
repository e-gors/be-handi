copy env.example to .env
php artisan migrate
php artisan db:seed
php artisan passport:install --force
