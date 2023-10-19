<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
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
function getConfig($dir_root, $rpm_dir, $module = 'base', $options = '')
{
    $dir = $dir_root.'/Install';
    $file = $dir.'/config.cfg';
    $os_license = 'ASL 2.0';

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

    if (isset($cfg['license']) && $cfg['license'] == $os_license) {
        $rpm_dir .= '/os';
    } else {
        $rpm_dir .= '/prime';
    }

    if (isset($cfg['depends'])) {
        $depends = '--depends "'.implode('" --depends "', explode(';', $cfg['depends'])).'"';
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
    return '-p '.$rpm_dir.' '.$depends.' '.$name.' '.$description.' '.$exclude.' '.$configfiles.' '.$scripts.' '.$options.' '.$cf.' '.$dir_root.'/'.'='.$dest.' '.$f;
}

/*
 * Make fpm Command Line for Execution
 */
function fpm($dir, $version, $rpm_dir, $module = 'base', $options = '')
{
    $config = getConfig($dir, $rpm_dir, $module, $options);

    if (! $config) {
        return false;
    }

    // Note: --edit let's you see and edit the SPEC file before the package is built - see https://github.com/jordansissel/fpm/issues/199
    $cmd = 'fpm -s dir -t rpm -v '.$version.' '.' --architecture all --force --verbose ';

    // Specify all directories of a package so that these are deleted when removing or updating a package
    // (in case the other package doesn't have these files and folders as well)
    // This is a bug of fpm - see https://github.com/jordansissel/fpm/issues/199 and https://github.com/jordansissel/fpm/issues/701
    // Note: using --rpm-auto-add-directories the installation fails
    $nmsRootDir = '/var/www/nmsprime';

    if ($module == 'base') {
        foreach (glob(getcwd().'/*', GLOB_ONLYDIR) as $dirPath) {
            $dirName = basename($dirPath);

            if (in_array($dirName, ['modules', 'Install'])) {
                continue;
            }

            $cmd .= "--directories '$nmsRootDir/$dirName' ";
        }
    } else {
        $cmd .= "--directories '$nmsRootDir/modules/".basename($dir)."' ";
    }

    return $cmd.$config;
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
 * Foreach Module
 */
foreach (array_slice(scandir($dir.'/modules'), 2) as $mod) {
    call(fpm($dir.'/modules/'.$mod, $version, $rpm_dir, $mod), $mod);
}

echo "\n";
