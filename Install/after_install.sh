#
# variables
#
dir="/var/www/nmsprime"
env="/etc/nmsprime/env/global.env"
pw=$(tr -dc '[:alnum:]' < /dev/urandom | head -c 12) # SQL password for user nmsprime


#
# disable SE linux
#
sed -i "s/^SELINUX=enforcing$/SELINUX=disabled/" /etc/sysconfig/selinux
setenforce  0


#
# HTTP
#
# SSL demo certificate
mkdir /etc/httpd/ssl
openssl req -new -x509 -days 365 -nodes -batch -out /etc/httpd/ssl/httpd.pem -keyout /etc/httpd/ssl/httpd.key

# reload apache config
systemctl start httpd
systemctl enable httpd


#
# firewalld
#
# enable admin interface
firewall-cmd --add-port=8080/tcp --zone=public --permanent
firewall-cmd --reload


#
# mariadb
#
systemctl start mariadb
systemctl enable mariadb

# create mysql db
mysql -u root -e "CREATE DATABASE nmsprime;"

mysql -u root -e "GRANT ALL ON nmsprime.* TO 'nmsprime'@'localhost' IDENTIFIED BY '$pw'";
sed -i "s/^DB_PASSWORD=$/DB_PASSWORD=$pw/" "$env"


#
# Laravel
#
cd "$dir"
ln -sr "$env" "$dir/.env" # TODO: force L5 to use global env file - remove this line

# L5 setup
php artisan clear-compiled
php artisan optimize
php artisan key:generate
php artisan migrate

# Note: needs to run last. storage/logs is only available after artisan optimize
mkdir $dir/storage/logs
chown -R apache $dir/storage/ $dir/bootstrap/cache/
