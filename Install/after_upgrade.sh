# source environment variables to use php 8
source /etc/profile.d/modules.sh
module load php80


cd '/var/www/nmsprime'

systemctl reload httpd

rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache
systemctl restart nmsprimed
systemd-tmpfiles --create
php artisan optimize:clear
php artisan optimize
php artisan migrate

# reread supervisor config and restart affected processes
# /usr/bin/supervisorctl update
