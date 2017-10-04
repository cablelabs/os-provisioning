dir="/var/www/lara"
cd $dir

# access rights
chown -R apache $dir/storage/ $dir/bootstrap/cache/
