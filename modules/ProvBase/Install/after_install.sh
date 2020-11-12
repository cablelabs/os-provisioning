dir='/var/www/nmsprime'
env='/etc/nmsprime/env'
source "$env/root.env"

# unfortunately dhcpd does not support hmacs other than hmac-md5
# see: https://bugs.centos.org/view.php?id=12107
pw=$(ddns-confgen -a hmac-md5 -r /dev/urandom | grep secret)
mysql_radius_psw=$(pwgen 12 1)

# create folders
install -dm750 /etc/dhcp-nmsprime/cmts_gws

# change owner
chown -R apache:dhcpd /etc/dhcp-nmsprime
chown -R apache /tftpboot
chown -R named:named /var/named/dynamic

sed -i "s|^.*secret \"<DNS-PASSWORD>\";|$pw|" /etc/dhcp-nmsprime/dhcpd.conf
sed -i "s|^.*secret \"<DNS-PASSWORD>\";|$pw|" /etc/named-nmsprime.conf
sed -i "s/<hostname>/$(hostname | cut -d '.' -f1)/" /var/named/dynamic/{nmsprime.test,in-addr.arpa}.zone

echo $'\ninclude /etc/chrony.d/*.conf' >> /etc/chrony.conf

systemctl daemon-reload

systemctl enable chronyd
systemctl enable dhcpd
systemctl enable named
systemctl enable nmsprimed
systemctl enable xinetd

# starting dhcpd won't work now, because not all files have been populated
systemctl start chronyd
systemctl start named
systemctl start nmsprimed
systemctl start xinetd

firewall-cmd --reload

# create freeradius DB and user
mysql -u "$ROOT_DB_USERNAME" --password="$ROOT_DB_PASSWORD" << EOF
CREATE DATABASE radius CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
GRANT ALL ON radius.* TO 'radius'@'localhost' IDENTIFIED BY '$mysql_radius_psw';
EOF

sed -i "s/RADIUS_DB_PASSWORD=$/RADIUS_DB_PASSWORD=$mysql_radius_psw/" "$env/provbase.env"
