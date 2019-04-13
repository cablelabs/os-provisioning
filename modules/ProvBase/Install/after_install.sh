dir="/var/www/nmsprime"
# unfortunately dhcpd does not support hmacs other than hmac-md5
# see: https://bugs.centos.org/view.php?id=12107
pw=$(ddns-confgen -a hmac-md5 -r /dev/urandom | grep secret)

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
