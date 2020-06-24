#!/bin/bash

# Helper to clean environment if there are strange problems
# like ExtendedValidator not found or wrongly built URLs like https://localhost/admin/Contract/create
# if that doesn't help try deleting bootstrap/cache/*

declare -a CMDS=(
	"git submodule update --init --recursive"
	"mkdir -p /var/www/nmsprime/storage/framework/cache"
	"mkdir -p /var/www/nmsprime/storage/framework/sessions"
	"mkdir -p /var/www/nmsprime/storage/framework/views"
	"chown -R apache /var/www/nmsprime/storage/framework"
	"composer dump-autoload"
	"composer update"
	"php artisan route:clear"
	"php artisan cache:clear"
	"php artisan config:clear"
	"php artisan view:clear"
	)

clear

for CMD in "${CMDS[@]}"; do
	echo
	echo "Running $CMD…"
	$CMD
done
echo
echo
