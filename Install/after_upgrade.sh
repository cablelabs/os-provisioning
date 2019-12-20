# source environment variables to use php 7.1
source scl_source enable rh-php71

cd '/var/www/nmsprime'

systemctl reload httpd

rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache /var/log/nmsprime
systemctl restart nmsprimed
systemd-tmpfiles --create
