<?php

if ($argc != 4) {
    echo "Install.php <version> <build from directory> <rpm target directory>\n";

    return;
}

$version = $argv[1];
$dir = $argv[2];
$rpm_dir = $argv[3];

/*
 * Parse Config for FPM
 * Requires Install/config.cfg
 */
function config($dir_root, $module = 'base', $options = '')
{
    $dir = $dir_root.'/Install';
    $file = $dir.'/config.cfg';

    // Config Exists ?
    if (! file_exists($file)) {
        return false;
    }

    // Parse Config to Arrays
    $cfg = parse_ini_file($file, true) ['config'];
    $files = parse_ini_file($file, true) ['files'];

    // Prepare Command Line Stuff ..
    if (isset($cfg['name'])) {
        $name = ' -n '.$cfg['name'];
    }

    if (isset($cfg['description'])) {
        $description = ' --description "'.$cfg['description'].'"';
    }

    if (isset($cfg['depends'])) {
        $depends = '--depends '.str_replace(' ', ' --depends ', $cfg['depends']);
    }

    if (isset($cfg['exclude'])) {
        $exclude = '-x '.str_replace(' ', ' -x ', $cfg['exclude']);
    }

    // files
    $configfiles = '';
    if (isset($cfg['destination'])) {
        $dest = $cfg['destination'];
        if (isset($cfg['configfiles'])) {
            $configfiles = "--config-files $dest/".str_replace(' ', " --config-files $dest/", $cfg['configfiles']);
        }
    }

    // set config files
    $cf = '';
    $f = '';
    foreach ($files as $f_from => $f_to) {
        $cf .= ' --config-files '.$f_to;
        $f .= ' '.$dir.'/files/'.$f_from.'='.$f_to;
    }

    // prepare install config scripts
    if ($module == 'base') {
        // nmsprime-base
        system("cp $dir/before_install.sh /tmp/fpm-base-bi.txt");
        system("cp $dir/after_install.sh /tmp/fpm-base-ai.txt");
        system("cp $dir/before_upgrade.sh /tmp/fpm-base-bu.txt");
        system("cp $dir/after_upgrade.sh /tmp/fpm-base-au.txt");
    } else {
        // nmsprime-<modules>
        system("cat $dir/before_install.sh $dir/../../../Install/module_before_install.sh > /tmp/fpm-$module-bi.txt");
        system("cat $dir/after_install.sh $dir/../../../Install/module_after_install.sh > /tmp/fpm-$module-ai.txt");
        system("cat $dir/before_upgrade.sh $dir/../../../Install/module_before_upgrade.sh > /tmp/fpm-$module-bu.txt");
        system("cat $dir/after_upgrade.sh $dir/../../../Install/module_after_upgrade.sh > /tmp/fpm-$module-au.txt");
    }

    // use file parameters
    $scripts = " --before-install /tmp/fpm-$module-bi.txt";
    $scripts .= " --after-install /tmp/fpm-$module-ai.txt";
    $scripts .= " --before-upgrade /tmp/fpm-$module-bu.txt";
    $scripts .= " --after-upgrade /tmp/fpm-$module-au.txt";

    // config fil
    return $depends.' '.$name.' '.$description.' '.$exclude.' '.$configfiles.' '.$scripts.' '.$options.' '.$cf.' '.$dir_root.'/'.'='.$dest.' '.$f;
}

/*
 * Make fpm Command Line for Execution
 */
function fpm($dir, $version, $rpm_dir, $module = 'base', $options = '')
{
    if ($module === 'Debug') {
        $config = '-n debug-nmsprime --description "NMS Prime Debug Package" .git/=/var/www/nmsprime/.git .gitignore=/var/www/nmsprime/.gitignore';
    } else {
        $config = config($dir, $module, $options);
    }

    if (! $config) {
        return false;
    }

    return 'fpm -s dir -t rpm -v '.$version.' '.' --architecture all --force --verbose -p '.$rpm_dir.' '.$config;
}

/*
 * Call & Print
 */
function call($cmd, $module = 'Base')
{
    echo "\n\n================================== $module ================================================\n\n";
    echo str_replace('--', "\ \n--", $cmd)."\n\n";
    echo "\nFPM Returns: -----\n";
    system($cmd);
}

/*
 * Build Main package
 */
call(fpm($dir, $version, $rpm_dir));

/*
 * Build Debug package, containing the .git folder
 */
call(fpm($dir, $version, $rpm_dir, 'Debug'), 'Debug');

/*
 * Foreach Module
 */
foreach (array_slice(scandir($dir.'/modules'), 2) as $mod) {
    call(fpm($dir.'/modules/'.$mod, $version, $rpm_dir, $mod), $mod);
}

echo "\n";
