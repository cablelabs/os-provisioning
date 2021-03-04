# change owner
chown -R apache:dhcpd /etc/dhcp-nmsprime
sed -i '/^#.*mta.conf";/s/^#//' /etc/dhcp-nmsprime/dhcpd.conf

firewall-cmd --reload

php artisan provvoip:update_carrier_code_database
php artisan provvoip:update_ekp_code_database
php artisan provvoip:update_trc_class_database
