# create folders
mkdir -p /etc/dhcp/nms
mkdir -p /tftpboot/cm

# change owner
chown -R apache /etc/dhcp/nms /tftpboot
chmod o+rx /etc/dhcp/
chown -R apache /etc/dhcp/
