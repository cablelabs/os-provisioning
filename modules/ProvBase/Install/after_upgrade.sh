# source environment variables to use php 7.1
source scl_source enable rh-php71

cd '/var/www/nmsprime'

echo '\Modules\ProvBase\Entities\RadGroupReply::repopulate();' | /opt/rh/rh-php71/root/usr/bin/php artisan tinker
