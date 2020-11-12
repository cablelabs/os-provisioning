<?php

class InstallKea extends BaseMigration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        // Add directory structure
        if (! is_dir('/etc/kea/gateways6')) {
            mkdir('/etc/kea/gateways6', 0750, true);
        }

        // create wrong directory due to bug https://gitlab.isc.org/isc-projects/kea/-/issues/1144 - Remove on CentOS 8!
        if (! is_dir('/var/lib/lib/kea/')) {
            mkdir('/var/lib/lib/kea/', 0750, true);
        }

        system('chown -R apache /etc/kea/');

        // Create kea DB and grant all permissions to user nmsprime
        $rootConf = DB::connection('mysql-root')->getConfig();
        $conf = DB::connection('mysql')->getConfig();

        $rootDbUser = $rootConf['username'];
        $rootDbPassword = $rootConf['password'];
        $dbUser = $conf['username'];
        $dbPassword = $conf['password'];

        system("mysql -u '$rootDbUser' --password='$rootDbPassword' --exec=\"CREATE DATABASE kea; GRANT ALL ON kea.* TO 'nmsprime'@'localhost'\"");
        system("/usr/sbin/kea-admin db-init mysql -u '$dbUser' -p '$dbPassword' -n kea");

        $find = [
            '<DB_USERNAME>',
            '<DB_PASSWORD>',
        ];

        $replace = [
            $dbUser,
            $dbPassword,
        ];

        $filename = '/etc/kea/dhcp6-nmsprime.conf';
        $content = file_get_contents($filename);
        preg_replace($find, $replace, $content);
        file_put_contents($filename, $content);

        system('systemctl enable kea-dhcp6');
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
    }
}
