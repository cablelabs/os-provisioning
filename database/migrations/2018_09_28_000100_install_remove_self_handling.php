<?php

class InstallRemoveSelfHandling extends BaseMigration
{
    protected $tablename = '';

    protected $files = [
        '/modules/BillingBase/Console/accountingCommand.php',
        '/modules/HfcCustomer/Console/MpsCommand.php',
        '/modules/ProvBase/Console/configfileCommand.php',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->files as $file) {
            $file = base_path().$file;
            if (! file_exists($file)) {
                continue;
            }

            $str = file_get_contents($file);
            $str = preg_replace(
                "/use Illuminate\\\\Console\\\\Command;\nuse Illuminate\\\\Contracts\\\\Bus\\\\SelfHandling;/",
                'use Illuminate\\Console\\Command;',
                $str
            );
            $str = preg_replace('/implements SelfHandling, ShouldQueue/', 'implements ShouldQueue', $str);
            file_put_contents($file, $str);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->files as $file) {
            $file = base_path().$file;
            if (! file_exists($file)) {
                continue;
            }

            $str = file_get_contents($file);
            $str = preg_replace(
                '/use Illuminate\\\\Console\\\\Command;/',
                "use Illuminate\\Console\\Command;\nuse Illuminate\\Contracts\\Bus\\SelfHandling;",
                $str
            );
            $str = preg_replace('/implements ShouldQueue/', 'implements SelfHandling, ShouldQueue', $str);
            file_put_contents($file, $str);
        }
    }
}
