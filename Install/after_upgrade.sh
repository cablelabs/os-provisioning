# source environment variables to use php 7.1
source scl_source enable rh-php71

dir="/var/www/nmsprime"
cd "$dir"

# see https://laravel.com/docs/5.5/upgrade#upgrade-5.5.42
key=$(/opt/rh/rh-php71/root/usr/bin/php artisan key:generate | grep -o '\[.*\]' | tr -d '[]')
sed -i "s|APP_KEY=.*|APP_KEY=$key|" /etc/nmsprime/env/global.env

/opt/rh/rh-php71/root/usr/bin/php artisan clear-compiled
/opt/rh/rh-php71/root/usr/bin/php artisan optimize
/opt/rh/rh-php71/root/usr/bin/php artisan migrate
/opt/rh/rh-php71/root/usr/bin/php artisan queue:restart
/opt/rh/rh-php71/root/usr/bin/php artisan route:cache
/opt/rh/rh-php71/root/usr/bin/php artisan view:clear

chown -R apache storage bootstrap/cache /var/log/nmsprime

systemctl reload httpd
systemctl restart nmsprimed
