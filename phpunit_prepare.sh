#!/bin/bash

# Helper to clean environment if there are strange problems
# like ExtendedValidator not found or wrongly built URLs like https://localhost/admin/Contract/create
# if that doesn't help try deleting bootstrap/cache/*

declare -a CMDS=(
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
