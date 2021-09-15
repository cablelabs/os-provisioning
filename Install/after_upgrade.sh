# source environment variables to use php 7.3
source scl_source enable rh-php73

cd '/var/www/nmsprime'

systemctl reload httpd

rm -f storage/framework/sessions/*
chown -R apache storage bootstrap/cache
systemctl restart nmsprimed
systemd-tmpfiles --create

# reread and deploy supervisor config
/usr/bin/supervisorctl update

# restart all jobs to make sure all workers use current code
/usr/bin/supervisorctl restart all
