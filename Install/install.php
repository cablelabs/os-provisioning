<?php

if ($argc != 4)
{
	echo "Install.php <version> <build from directory> <rpm target directory>\n";
	return;
}

$version = $argv[1];
$dir     = $argv[2];
$rpm_dir = $argv[3];


/*
 * Parse Config for FPM
 * Requires Install/config.cfg
 */
function config ($dir_root)
{
	$dir  = $dir_root.'/Install';
	$file = $dir.'/config.cfg';

	// Config Exists ?
	if (!file_exists($file))
		return false;

	// Parse Config to Arrays
	$cfg = parse_ini_file($file, TRUE) ['config'];
	$files = parse_ini_file($file, TRUE) ['files'];

	// Prepare Command Line Stuff ..
	if (isset($cfg['name']))
		$name = ' -n '.$cfg['name'];

	if (isset($cfg['description']))
		$description = ' --description "'.$cfg['description'].'"';

	if (isset($cfg['depends']))
		$depends = '--depends '.str_replace (' ', ' --depends ', $cfg['depends']);

	if (isset($cfg['exclude']))
		$exclude = '-x '.str_replace (' ', ' -x ', $cfg['exclude']);

	// files
	if (isset($cfg['destination']))
		$dest = $cfg['destination'];

	$f = '';
	foreach ($files as $f_from => $f_to)	
		$f .= ' '.$dir.'/files/'.$f_from.'='.$f_to;

	// laod install scripts
	$scripts = '';
	if (file_exists($dir.'/after_install.sh'))
		$scripts .= ' --after-install '.$dir.'/after_install.sh';
	if (file_exists($dir.'/before_install.sh'))
		$scripts .= ' --before-install '.$dir.'/before_install.sh';

	// config fil
	return $depends.' '.$name.' '.$description.' '.$exclude.' '.$scripts.' '.$dir_root.'/'.'='.$dest.' '.$f;
}


/*
 * Make fpm Command Line for Execution
 */
function fpm ($dir, $version, $rpm_dir)
{
	$config = config($dir);

	if (!$config)
		return false;

	return 'fpm -s dir -t rpm -v '.$version.' '.' --architecture all --force --verbose -p '.$rpm_dir.' '.$config;
}


/*
 * Build Main package
 */
$cmd = fpm($dir, $version, $rpm_dir);

echo $cmd;
system ($cmd);


/*
 * Foreach Module
 */
foreach (array_slice(scandir($dir.'/modules'), 2) as $mod)
{
	$cmd = fpm($dir.'/modules/'.$mod, $version, $rpm_dir);

	echo "\n".$cmd;
	system ($cmd);
}

echo "\n";
?>
