# source environment variables to use php 7.1
source scl_source enable rh-php71

dir="/var/www/nmsprime"

cd "$dir"
sed -i "s|'timezone' *=>.*|'timezone' => '$(timedatectl | grep 'Time zone' | cut -d':' -f2 | cut -d' ' -f2)',|" config/app.php
/opt/rh/rh-php71/root/usr/bin/php artisan clear-compiled
/opt/rh/rh-php71/root/usr/bin/php artisan optimize
/opt/rh/rh-php71/root/usr/bin/php artisan migrate
/opt/rh/rh-php71/root/usr/bin/php artisan queue:restart
/opt/rh/rh-php71/root/usr/bin/php artisan route:cache

systemctl reload httpd
systemctl restart nmsprimed
