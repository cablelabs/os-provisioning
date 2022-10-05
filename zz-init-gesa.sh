#!/bin/bash

SEPDIR="/var/www/nmsprime/storage/app/data/smartont/sep"

rm -f $SEPDIR/sep.csv.sha1sum

cp -f $SEPDIR/sep.csv.old $SEPDIR/sep.csv
/usr/bin/env php /var/www/nmsprime/artisan smartont:update_oto || exit 1

cp -f $SEPDIR/sep.csv.new $SEPDIR/sep.csv
/usr/bin/env php /var/www/nmsprime/artisan smartont:update_oto || exit 1

/usr/bin/env php /var/www/nmsprime/artisan smartont:get_dfsubscriptions all || exit 1
