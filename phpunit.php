#!/usr/bin/env php

<?php

/**
 * This class holds all functionality to run our test suite.
 * Derive own classes to e.g. run subsets.
 *
 * @author Patrick Reichel
 */
class UnitTestStarter {

	// directory to store phpunit output in; will be created if not existing
	// sshfs this on your PC for a more comfortable view on logs and testing output
	protected $out_dir = "";

	// files to store phpunit output in
	protected $logfile = "";
	protected $outfile = "";

	// path to phpunit to use
	protected $phpunit = "";

	// options to pass to phpunit (give array or string; will be converted to string automatically)
	protected $opts = null;


	/**
	 * Constructor
	 *
	 * @author Patrick Reichel
	 */
	public function __construct() {

		$this->_set_environment();
		$this->_convert_opts_array_to_string();
		$this->_define_test_circuits();
		$this->_clear_old_output();

		$this->_run_tests();
	}


	/**
	 * Setting up the environmental data
	 *
	 * @author Patrick Reichel
	 */
	protected function _set_environment() {

		$this->out_dir = "/var/www/nmsprime/phpunit_output";
		$this->out_dir = "/var/www/nmsprime";

		$this->logfile = "$this->out_dir/phpunit_log.htm";
		$this->outfile = "$this->out_dir/phpunit_output.htm";

		$this->phpunit = "/usr/bin/phpunit";

		$this->opts = [
			"--debug",
			"--testdox-html $this->logfile",
			"--colors",
			"--stop-on-failure",
		];
	}


	/**
	 * Makes normalized string out from options
	 *
	 * @author Patrick Reichel
	 */
	protected function _convert_opts_array_to_string() {

		if ($this->opts) {
			if (is_array($this->opts)) {
				$this->opts = " ".implode(" ", $this->opts);
			}
			elseif (is_string($this->opts)) {
				$this->opts = " ".strip($opts);
			}
			else {
				echo 'ERROR: $this->opts needs to be either string or array. Cannot proceed.';
				abort(1);
			}
		}
		else {
			$this->opts = "";
		}
	}


	/**
	 * This method defines which tests are to be run and which modules shall be enabled.
	 *
	 * @author Patrick Reichel
	 */
	protected function _define_test_circuits() {

		$this->circuits = [
			'all_modules_enabled' => [
				'modules' => $this->_get_modules(),
				'tests' => "modules/ProvBase/Tests/ContractLifecycleTest.php",
			],
			'disabled_provvoip' => [
				'modules' => $this->_get_modules(['Provvoip']),
				'tests' => "modules/ProvBase/Tests/ContractLifecycleTest.php",
			],
			'disabled_billingbase' => [
				'modules' => $this->_get_modules(['Billingbase']),
				'tests' => "modules/ProvBase/Tests/ContractLifecycleTest.php",
			]
		];
	}


	/**
	 * It may be confusing to find old logs and output – so we delete all
	 *
	 * @author Patrick Reichel
	 */
	protected function _clear_old_output() {

		$logfile_pattern = "*".array_pop(explode("/", $this->logfile));
		$logfiles = glob("$this->out_dir/$logfile_pattern");
		foreach ($logfiles as $_) {
			if (is_file($_)) {
				unlink($_);
			}
		}

		$outfile_pattern = "*".array_pop(explode("/", $this->outfile));
		$outfiles = glob("$this->out_dir/$outfile_pattern");
		foreach ($outfiles as $_) {
			if (is_file($_)) {
				unlink($_);
			}
		}
	}

	/**
	 * This method finally runs the tests for all circuits.
	 *
	 * @author Patrick Reichel
	 */
	protected function _run_tests() {
	}

}

// fire the test suite
$t = new UnitTestStarter();
