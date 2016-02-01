dir="/var/www/lara"
cd $dir

# access rights
chown -R $dir/apache $dir/storage/ $dir/bootstrap/cache/


# adapt .env file
echo "
APP_ENV=local
APP_DEBUG=true
APP_KEY=

DB_HOST=localhost
DB_DATABASE=db_lara
DB_USERNAME=root
DB_PASSWORD=TODO

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
" > $dir/.env

# key
php artisan key:generate
