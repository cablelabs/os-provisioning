source /etc/profile.d/modules.sh
module load php80

# change owner
chown -R apache:dhcpd /etc/dhcp-nmsprime
sed -i '/^#.*mta.conf";/s/^#//' /etc/dhcp-nmsprime/dhcpd.conf

firewall-cmd --reload

/opt/remi/php80/root/usr/bin/php /var/www/nmsprime/artisan module:migrate
/opt/remi/php80/root/usr/bin/php /var/www/nmsprime/artisan provvoip:update_carrier_code_database
/opt/remi/php80/root/usr/bin/php /var/www/nmsprime/artisan provvoip:update_ekp_code_database
# Currently only implemented for use with ProvVoipEnvia module - this would fail as it would run before envia module is installed
# php /var/www/nmsprime/artisan provvoip:update_trc_class_database
