# source environment variables to use php 7.3
source /etc/profile.d/modules.sh
module load php80


cd '/var/www/nmsprime'

systemctl reload httpd

rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache
systemctl restart nmsprimed
systemd-tmpfiles --create

# reread supervisor config and restart affected processes
/usr/bin/supervisorctl update

# restart all laravel background jobs to make sure all workers use current code
/opt/remi/php80/root/usr/bin/php artisan queue:restart
/opt/remi/php80/root/usr/bin/php artisan websockets:restart
