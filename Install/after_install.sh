dir="/var/www/lara"
cd $dir

# access rights
chown -R apache $dir/storage/ $dir/bootstrap/cache/


# generate .env.demo file
# NOTE: This is required because gitignore will ignore .env.demo file
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
" > $dir/.env.demo
