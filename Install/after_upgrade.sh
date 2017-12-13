dir="/var/www/nmsprime"

cd "$dir"
php artisan clear-compiled
php artisan optimize
php artisan migrate
