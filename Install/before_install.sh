cd /tmp

# add IUS repo
wget https://centos7.iuscommunity.org/ius-release.rpm
rpm -Uvh ius-release.rpm
 
# update php version with yum replace plugin
yum install yum-plugin-replace
yum replace php --replace-with php56u


#
# SSL
# Self Signed Certificat
#
mkdir /etc/httpd/ssl
openssl req -new -x509 -days 365 -nodes -out /etc/httpd/ssl/httpd.pem -keyout /etc/httpd/ssl/httpd.key
