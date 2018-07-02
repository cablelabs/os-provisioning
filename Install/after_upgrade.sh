# source environment variables to use php 7.1
source scl_source enable rh-php71

dir="/var/www/nmsprime"

cd "$dir"
php artisan clear-compiled
php artisan optimize
php artisan migrate
php artisan queue:restart

systemctl reload httpd
