#!/bin/bash

version=$1

rm -rf /var/www/rpm/nmsprime/nmsprime-*
php Install/install.php $version /home/rpm/lara/schmto-build2/ /var/www/rpm/nmsprime/
#cp /tmp/nmsprime-base-* /var/www/rpm/nmsprime/
createrepo --update /var/www/rpm/nmsprime/
