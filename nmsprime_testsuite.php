#!/opt/rh/rh-php71/root/usr/bin/php

<?php

/**
 * Class to run all unit tests.
 *
 * Within there are circuits defined – e.g. to enable/disable modules.
 *
 * Tests should be run with a freshly migrated and seeded database to prevent
 * side effects.
 *
 * @author Patrick Reichel
 */
class UnitTestStarter
{
    protected $basepath = '/var/www/nmsprime';
    protected $phpunit = 'source scl_source enable rh-php71; vendor/bin/phpunit';

    protected $modules_disabled_for_all_circuits = [
        'Mail',
        'ProvMon',
        'VoipMon',
    ];

    // the test circuits
    // each circuits holds an array with modules to disable
    protected $circuits = [
        'all_modules_enabled' => [],
        'no_envia' => ['ProvVoipEnvia'],
        'no_voip' => ['ProvVoip', 'ProvVoipEnvia', 'VoipMon'],
    ];

    /**
     * Constructor (also triggers execution of all testing circuits).
     *
     * @author Patrick Reichel
     */
    public function __construct()
    {
        chdir($this->basepath);

        $this->initial_module_information = $this->_get_module_information();
        $this->modules_available = [];
        foreach ($this->initial_module_information as $module => $_) {
            array_push($this->modules_available, $module);
        }

        $this->_read_config_template();

        $this->_run_tests();

        $this->_restore_initial_module_state();

        $this->_print_lifecycle_test_coverage();

        echo "\n\n";
        echo 'Finished! Check *.htm files in '.$this->basepath."/phpunit for data collected during the tests.\n\n";
    }

    /**
     * Reads the template for phpunit*.xml files.
     *
     * @author Patrick Reichel
     */
    protected function _read_config_template()
    {
        $this->config_template = file_get_contents('phpunit/phpunit_config.xml.tpl');
    }

    /**
     * Creates phpunit*.xml file from template and substitutions.
     *
     * @author Patrick Reichel
     */
    protected function _write_config_file($configfile, $substitutions)
    {
        $config = $this->config_template;
        foreach ($substitutions	as $placeholder => $value) {
            $config = str_replace($placeholder, $value, $config);
        }
        file_put_contents($configfile, $config);
        echo `sudo chmod 644 $configfile`;
    }

    /**
     * Wrapper to run all testing circuits.
     *
     * @author Patrick Reichel
     */
    protected function _run_tests()
    {
        $basepath = $this->basepath;

        // make directory writable for apache (who runs the tests)
        echo `sudo chgrp apache $basepath/phpunit`;
        echo `sudo chmod 775 $basepath/phpunit`;

        // delete old data (to prevent confusion)
        echo `sudo rm -f $basepath/phpunit/*.htm`;
        echo `sudo rm -f $basepath/phpunit/*.xml`;

        foreach ($this->circuits as $circuit => $modules_disable) {
            $success = $this->_run_circuit($circuit, $modules_disable);

            // stop execution on first failing circuit
            if (! $success) {
                echo "\n\nFailing test in circuit $circuit. Will now exit…";
                break;
            }
        }
        echo `sudo chmod -R o+rX $basepath/phpunit`;
    }

    /**
     * Runs a testing circuit.
     *
     * @author Patrick Reichel
     */
    protected function _run_circuit($circuit, $modules_disable)
    {
        echo "\n\nRunning circuit $circuit";
        echo "\n";

        $configfile = $this->basepath.'/phpunit/phpunit_'.$circuit.'.xml';
        $logfile = $this->basepath.'/phpunit/phpunit_'.$circuit.'_log.htm';
        $outfile = $this->basepath.'/phpunit/phpunit_'.$circuit.'_output.htm';

        // add all modules disabled for all circuits
        foreach ($this->modules_disabled_for_all_circuits as $m) {
            array_push($modules_disable, $m);
        }

        $modules_enable = [];
        foreach ($this->modules_available as $m) {
            if (! in_array($m, $modules_disable)) {
                array_push($modules_enable, $m);
            }
        }

        $this->_current_module_information = $this->_get_module_information();
        $this->_enable_modules($modules_enable);
        $this->_disable_modules($modules_disable);

        $modules_to_test = [];
        foreach ($this->_current_module_information as $module => $data) {
            if (! in_array($module, $modules_disable)) {
                array_push($modules_to_test, $data[0]);
            }
        }

        $substitutions = [
            '{{phpunit_html_log_file}}' => $logfile,
        ];
        $test_dirs = [];

        // add module level test dirs (for all enabled modules)
        foreach ($modules_to_test as $m) {
            array_push($test_dirs, "<testsuite name=\"$m\"><directory>".$m.'/Tests</directory></testsuite>');
        }

        // add additional tests
        array_push($test_dirs, '<testsuite name="Route auth tests"><file>'.$this->basepath.'/tests/RoutesAuthTest.php</file></testsuite>');

        $substitutions['{{testsuite_directories}}'] = implode("\n", $test_dirs);

        $this->_write_config_file($configfile, $substitutions);

        /* exec("sudo -u apache phpunit --configuration $configfile | tee $outfile", $output, $return_var); */
        /* passthru("sudo -u apache ".$this->phpunit." --configuration $configfile | tee $outfile", $exit_code); */
        file_put_contents($outfile, "<pre>\n\n");
        passthru($this->phpunit." --configuration $configfile | tee -a $outfile", $exit_code);

        // check for errors in outfile (unfortunately phpunit exits with “0” even on failures and errors)
        // this is used to skip testing of other circuits if current on failed
        $problems = system("tail -n 1 $outfile | egrep -c '(Failure|Error)'");
        if ($problems != '0') {
            return false;
        }

        return true;
    }

    /**
     * Get all modules from artisan.
     * This sets the class variable $this->modules_available with module name as key and
     * path as value.
     *
     * @author Patrick Reichel
     */
    protected function _get_module_information()
    {
        $artisan_return = `php artisan module:list`;
        $artisan_return = explode("\n", $artisan_return);

        $modules = [];
        while ($artisan_return) {
            $line = array_pop($artisan_return);
            if (strpos($line, 'modules') === false) {
                continue;
            }
            $_ = explode('|', $line);
            $module = trim($_[1]);
            $modules[$module] = [trim($_[4]), trim($_[2])];
        }

        return $modules;
    }

    /**
     * Gets all disabled modules.
     *
     * @author Patrick Reichel
     */
    protected function _get_disabled_modules($modules)
    {
        $disabled = [];
        foreach ($modules as $module => $data) {
            if ($data[1] == 'Disabled') {
                array_push($disabled, $module);
            }
        }

        return $disabled;
    }

    /**
     * Enables all modules in array.
     *
     * @author Patrick Reichel
     */
    protected function _enable_modules($modules)
    {
        foreach ($modules as $module) {
            if ($this->_current_module_information[$module][1] != 'Enabled') {
                echo `php artisan module:enable $module`;
            }
        }
    }

    /**
     * Disables all modules in array.
     *
     * @author Patrick Reichel
     */
    protected function _disable_modules($modules)
    {
        foreach ($modules as $module) {
            if ($this->_current_module_information[$module][1] != 'Disabled') {
                echo `php artisan module:disable $module`;
            }
        }
    }

    /**
     * Restores modules enable/disable state to initial setting (before running tests).
     *
     * @author Patrick Reichel
     */
    protected function _restore_initial_module_state()
    {
        echo "\n\nRestoring original module states\n";
        $this->_current_module_information = $this->_get_module_information();
        foreach ($this->initial_module_information as $module => $data) {
            if ($data[1] == 'Enabled') {
                $this->_enable_modules([$module]);
            } elseif ($data[1] == 'Disabled') {
                $this->_disable_modules([$module]);
            } else {
                echo "Unknown state $data[1] for module $module.\n";
            }
        }
        echo "\n";
    }

    /**
     * Calculates and prints lifecycle test coverage.
     *
     * @author Patrick Reichel
     */
    protected function _print_lifecycle_test_coverage()
    {
        $out = "\n\nLifecycle test coverage:\n";
        $coverage = [];
        $missing = [];
        foreach ($this->modules_available as $module) {
            $coverage[$module] = [];
            $models_raw = glob("modules/$module/Entities/*.php");
            $lifecycle_tests_raw = glob("modules/$module/Tests/*LifecycleTest.php");

            $models = [];
            $lifecycle_tests = [];
            foreach ($models_raw as $raw) {
                $_ = explode('/', $raw);
                $_ = array_pop($_);
                array_push($models, str_replace('.php', '', $_));
            }
            foreach ($lifecycle_tests_raw as $raw) {
                $_ = explode('/', $raw);
                $_ = array_pop($_);
                $_ = str_replace('LifecycleTest', '', $_);
                $_ = str_replace('.php', '', $_);
                array_push($lifecycle_tests, $_);
            }

            $coverage[$module] = $models ? count($lifecycle_tests) / count($models) : 0;
            $missing[$module] = array_diff($models, $lifecycle_tests);
        }

        // used for output alignment of percentages
        $maxlen = max(array_map('strlen', array_keys($coverage))) + 4;

        ksort($coverage);
        foreach ($coverage as $module => $percentage) {
            $missing_str = $missing[$module] ? '   (untested: '.implode(', ', $missing[$module]).')' : '';
            $out .= sprintf('%s: %'.($maxlen - strlen($module))."s%s%s\n", $module, round($percentage * 100), '%', $missing_str);
        }

        echo $out;
        file_put_contents('phpunit/lifecycle_test_coverage.txt', $out);
    }
}

$uts = new UnitTestStarter();
