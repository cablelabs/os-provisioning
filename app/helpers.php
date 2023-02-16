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

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Due to the new Iginition Error Page, the ddd function exists, which does the
 * same and more than the legacy d() method. This is kept for convinience and
 * to quickly access this function.
 */
function d()
{
    $args = func_get_args();

    // write meta information about the caller
    $td = '<td style="font-size: 11px; font-family:monospace; color:#444">';
    $bt = debug_backtrace();
    echo '<table>';
    echo '<tr>';
    echo $td.'File: </td>';
    echo $td.Arr::get($bt[0], 'file', 'n/a').', line '.Arr::get($bt[0], 'line', 'n/a').'</td>';
    echo '</tr>';
    echo '<tr>';
    echo $td.'Method: </td>';
    echo $td.Arr::get($bt[1], 'class', 'n/a').'::'.Arr::get($bt[1], 'function', 'n/a').'()</td>';
    echo '</tr>';
    echo '</table>';

    echo '<hr size="1" noshade>';

    // call laravel's dd function and pass all given params
    call_user_func_array('dd', $args);
}

/**
 * Translate all validated MAC formats into a common one
 * (i.e. AA:BB:CC:DD:EE:FF)
 *
 * @author Ole Ernst
 */
function unifyMac($mac)
{
    // return since we don't want to modify null into ''
    if (! $mac) {
        return $mac;
    }

    $mac = preg_replace('/[^a-f\d]/i', '', strtolower($mac));
    $mac = wordwrap($mac, 2, ':', true);

    return $mac;
}

/**
 * Try retrieving values via SNMP without throwing exceptions.
 *
 * Multiple SNMP sessions (e.g v2 and v1) and OIDs can be supplied.
 * Once a session or OID leads to a non-exception the values will be processed
 * according to the divisor and the callback function (including its arguments).
 *
 * First all sessions will be tried for the first OID, afterwards the next OID
 * will be tried with all sessions.
 */
function snmpWrapper($trySessions, $tryOids, $div = null, $callback = null, $arg = null)
{
    // Try the first OID with the various SNMP sessions
    // if none provide a result try the next OID with all sessions
    // stop on first non-exception
    foreach ((array) $tryOids as $oid) {
        foreach (is_array($trySessions) ? $trySessions : [$trySessions] as $session) {
            try {
                $values = $session->walk($oid, true);

                // Only one value was retrieved via SNMP, thus the subtree prefix
                // couldn't be removed from keys via walk(..., true);
                if (count($values) == 1 && count($key = explode('.', array_key_first($values))) > 1) {
                    $values = [end($key) => end($values)];
                }

                // Divide all values with the common divisor if possible
                if (is_numeric($div) && $div != 0) {
                    $values = array_map(function ($value) use ($div) {
                        return $value / $div;
                    }, $values);
                }

                if (! is_callable($callback)) {
                    return $values;
                }

                // Apply callback if available
                if ($arg) {
                    return $callback($values, $arg);
                } else {
                    return $callback($values);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    return [];
}

/**
 * Simplify string for Filenames
 * Attention: Do not use full path (with directory) as slash is replaced
 *
 * @author Nino Ryschawy
 */
function sanitize_filename($string)
{
    $string = str_replace([' ', 'ß'], '_', $string);

    return preg_replace('/[^a-zA-Z0-9-_]/', '', $string);
}

/**
 * Check if at least one of the needle array keys exists in the haystack array
 *
 * @return true if one array key of needle array exists in haystack array, false otherwise
 *
 * @author Nino Ryschawy
 */
function multi_array_key_exists($needles, $haystack)
{
    foreach ($needles as $needle) {
        if (array_key_exists($needle, $haystack)) {
            return true;
        }
    }

    return false;
}

/**
 * Escape Special Characters in Latex documents (before PDF conversion)
 * Used in Invoice.php & CccUserController.php
 *
 * @author Nino Ryschawy
 */
function escape_latex_special_chars($string)
{
    if (! $string) {
        return '';
    }

    // NOTE: "\\" has to be on top as it otherwise would replace all replacements in following loop
    $map = [
        '\\' => '\\textbackslash',
        '#'  => '\\#',
        '$'  => '\$',
        '%'  => '\\%',
        '&'  => '\\&',
        '{'  => '\\{',
        '}'  => '\\}',
        '_'  => '\\_',
        '~'  => '\\~{}',
        '^'  => '\\^{}',
        '€'  => '\\euro',   // there could be products containing “€”
        '´' => '\'',
    ];

    return strtr($string, $map);
    // not working: https://stackoverflow.com/questions/2541616/how-to-escape-strip-special-characters-in-the-latex-document
    // return preg_replace( "/([\^\%~\\\\#\$%&_\{\}])/e", "\$map['$1']", $string );
}

/**
 * Concatenate a list of existing PDF Files
 *
 * @author Nino Ryschawy
 *
 * @param 	mixed  		source files
 * @param 	string 		target filename
 * @param 	bool 		run processes multithreaded in background
 * @return int PID (process ID of background process) if parallel is true, otherwise 0
 */
function concat_pdfs($sourcefiles, $target_fn, $multithreaded = false)
{
    if (is_array($sourcefiles)) {
        $cnt = count($sourcefiles);
        $sourcefiles = implode(' ', $sourcefiles);
    }
    // only for debugging - remove when sufficient tested
    else {
        $cnt = count(explode(' ', trim($sourcefiles)));
    }

    Log::channel('billing')->debug('Concat '.$cnt.' PDFs to '.$target_fn);

    $cmd_ext = $multithreaded ? '> /dev/null 2>&1 & echo $!' : '';
    exec("nice gs -dNumRenderingThreads=6 -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile='$target_fn' $sourcefiles $cmd_ext", $output, $ret);

    // Note: normally output is [] and ret is 0
    if ($ret) {
        Log::channel('billing')->error("Error concatenating target file $target_fn", [$ret]);
    }

    return $multithreaded ? (int) $output[0] : 0;
}

/**
 * Create PDF from tex template
 *
 * @param string 	directory & filename
 * @param bool 	 	start latex process in background (for faster SettlementRun)
 * @return int return value of pdflatex - 0 on success
 */
function pdflatex($dir, $filename, $background = false)
{
    chdir($dir);

    /* NOTE: returns
        * 0 on success
        * 127 if pdflatex is not installed,
        * 134 when pdflatex is called without path /usr/bin/ and path variable is not set when running from cmd line
    */

    // take care - when we start process in background we don't get the return value anymore
    $cmd = "/usr/bin/pdflatex \"$filename\" -interaction=nonstopmode &>/dev/null";
    $cmd .= $background ? ' &' : '';

    system($cmd, $ret);

    if ($ret) {
        // log error
        pdflatex_error_msg($ret, true, $dir.$filename);
    }

    return $ret;
}

/**
 * Return error message when pdflatex fails
 *
 * @param int       pdflatex return code
 * @param bool      log message or not
 * @param string    path of file
 * @return string message
 */
function pdflatex_error_msg($code, $log = false, $filename = '')
{
    switch ($code) {
        case 0:
            // success
            break;
        case 1:
            $msg_key = 'syntax';
            $msg_var = $filename ? "[$filename]" : '';

            break;
        case 127:
            $msg_key = 'missing';
            $msg_var = '';

            break;
        default:
            $msg_key = 'default';
            $msg_var = $code;

            break;
    }

    $msg = trans("messages.pdflatex.$msg_key", ['var' => $msg_var]);

    if ($log) {
        Log::error($msg);
    }

    return $msg;
}

/**
 * Format number for Billing dependent of application/billing language
 */
function number_format_lang($number)
{
    return \App::getLocale() == 'de' ? number_format($number, 2, ',', '.') : number_format($number, 2);
}

/**
 * This determines if the given locale is supported by NMS Prime. It returns a
 * two letters language ISO 639-1 code of a supported language. The default
 * is set to English, when no other configuration in 'app/config' is set.
 *
 * @param  string|null  $locale
 * @return string
 */
function checkLocale($locale = null): string
{
    return in_array($locale, config('app.supported_locales')) ?
            $locale :
            config('app.locale', config('app.fallback_locale', 'en'));
}

/**
 * Get the chained subquery for db where statements to filter by a date string column
 * where the date is larger/later or equal then the specified date
 * NOTE: For end dates an empty column is later - it's cumbersome to always write these 5 lines of code
 *
 * @param string    db column name - must be table.column in joined statements
 * @param string    date string like '2019-02-06'
 * @return function db query to use in (chained) where clause
 *
 * @author Nino Ryschawy
 */
function whereLaterOrEqual($column, $date)
{
    return function ($query) use ($column, $date) {
        $query
            ->where($column, '>=', $date)
            // ->orWhere($column, '=', '');
            ->orWhereNull($column);
    };
}

/**
 * Clear failed jobs table in database for specific command or the whole table
 *
 * @param string
 */
function clearFailedJobs($command = 'all')
{
    if ($command == 'all') {
        \DB::table('failed_jobs')->delete();

        return;
    }

    $failed_jobs = \DB::table('failed_jobs')->get();

    foreach ($failed_jobs as $failed_job) {
        $commandName = json_decode($failed_job->payload)->data->commandName;

        if (\Str::contains($commandName, $command)) {
            \Artisan::call('queue:forget', ['id' => $failed_job->id]);
        }
    }
}

/**
 * Format date string dependent of set locale language
 *
 * @param $date
 * @return false|int|string
 */
function langDateFormat($date)
{
    if (! $date) {
        return $date;
    }

    $date = is_int($date) ? $date : strtotime($date);

    switch (\App::getLocale()) {
        case 'de':
            return date('d.m.Y', $date);

        case 'es':
            return date('d/m/Y', $date);

        default:
            return date('Y-m-d', $date);
    }
}

function moneyFormat($amount)
{
    switch (\App::getLocale()) {
        case 'de':
            return number_format($amount, 2, ',', '.');

        default:
            return number_format($amount, 2);
    }
}

/**
 * Get list of entries from specific table for select formular field in edit view
 *
 * @return array
 */
function selectList($table, $columns, $empty_option = false, $separator = '--')
{
    $model = new \App\BaseModel;

    if (is_array($columns)) {
        $select = $columns;
        $select[] = 'id';
    } else {
        $select = ['id', $columns];
    }

    $entries = is_string($table) ? DB::table($table)->whereNull('deleted_at')->select($select)->get() : $table;

    return $model->html_list($entries, $columns, $empty_option, $separator);
}

// http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
function humanFilesize($bytes, $dec = 2)
{
    $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f ", $bytes / pow(1024, $factor)).@$size[$factor];
}

/**
 * Negates all values that are given as parameter
 *
 * @param  int  ...$values
 * @return array
 */
function negate(int ...$values): array
{
    return array_map(function ($value) {
        return -1 * $value;
    }, $values);
}

/**
 * Optimized algorithm from http://www.codexworld.com
 * see https://stackoverflow.com/a/40929293
 *
 * @param  float  $latitudeFrom
 * @param  float  $longitudeFrom
 * @param  float  $latitudeTo
 * @param  float  $longitudeTo
 * @return float [m]
 */
function distanceLatLong($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
{
    $rad = M_PI / 180;
    //Calculate distance from latitude and longitude
    $theta = $longitudeFrom - $longitudeTo;
    $dist = sin($latitudeFrom * $rad)
        * sin($latitudeTo * $rad) + cos($latitudeFrom * $rad)
        * cos($latitudeTo * $rad) * cos($theta * $rad);

    return acos($dist) / $rad * 60 * 1853;
}

/**
 * Check if point is within the boundaries of the given polygon.
 * Based on: https://stackoverflow.com/a/18190354
 *
 * @param  array  $p  point to check
 * @param  array  $polygon  vertices of polygon outline
 * @return bool true if point in polygon, otherwise false
 */
function pointInPolygon(array $p, array $polygon)
{
    // special case: rectangle
    if (count($polygon) == 2) {
        $inPolygon = ! (
            $p[0] > max($polygon[0][0], $polygon[1][0]) ||
            $p[0] < min($polygon[0][0], $polygon[1][0]) ||
            $p[1] > max($polygon[0][1], $polygon[1][1]) ||
            $p[1] < min($polygon[0][1], $polygon[1][1])
        );

        return $inPolygon;
    }

    // “real” polygon
    $c = 0;
    $p1 = $polygon[0];
    $n = count($polygon);

    for ($i = 1; $i <= $n; $i++) {
        $p2 = $polygon[$i % $n];
        if ($p[1] > min($p1[1], $p2[1]) &&
            $p[1] <= max($p1[1], $p2[1]) &&
            $p[0] <= max($p1[0], $p2[0]) &&
            $p1[1] != $p2[1]) {
            $xinters = ($p[1] - $p1[1]) * ($p2[0] - $p1[0]) / ($p2[1] - $p1[1]) + $p1[0];
            if ($p1[0] == $p2[0] || $p[0] <= $xinters) {
                $c++;
            }
        }
        $p1 = $p2;
    }

    // even number of edges passed -> point not in the polygon
    return $c % 2 != 0;
}

/**
 * Helper to get Syslog entries dependent on what should be searched and discarded
 *
 * @param   search      String      to search
 * @param   grep_pipes  String      restrict matches
 * @return array
 *
 * Attention: grep_pipes must not contain user input!
 */
function getSyslogEntries($search, $grep_pipes)
{
    $search = escapeshellarg($search);
    // $grep_pipes = escapeshellarg($grep_pipes);

    exec("egrep -i $search /var/log/messages $grep_pipes", $log);

    if ($log) {
        return $log;
    }

    // Logrotate was probably done during last hours -> consider older logfiles (e.g. /var/log/messages-20170904)
    $files = glob('/var/log/messages-*');
    if (! empty($files)) {
        exec('egrep -i '.$search.' '.max($files).' '.$grep_pipes, $log);
    }

    return $log;
}

function isMobileRegEx(int $check): string
{
    if ($check == 1) {
        return '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i';
    }

    if ($check == 2) {
        return '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i';
    }

    throw new InvalidArgumentException();
}

/**
 * @param  string|int|float  $value  the value to be evaluated
 * @param  string  $thresholds  someting like “..10” or “-1.5..1.5” or “-20..-10;10..20”
 * @return bool True if value in thresholds else false
 *
 * @throws \Exception if value not null and not numeric
 *
 * @author Patrick Reichel
 */
function valueInThresholdString($value, $thresholds)
{
    if (is_null($value)) {
        return false;
    }

    if (! is_numeric($value)) {
        throw new \Exception("Given value ($value) is not numeric");
    }

    $value = floatval($value);

    if (is_null($thresholds) || $thresholds === '') {
        return false;
    }

    $thresholds = explode(';', $thresholds);
    foreach ($thresholds as $threshold) {
        $minmax = explode('..', trim($threshold));
        $min = ('' == trim($minmax[0])) ? -PHP_FLOAT_MAX : floatval(trim($minmax[0])); // attention: PHP_FLOAT_MIN is greater than zero!
        $max = ('' == trim($minmax[1])) ? PHP_FLOAT_MAX : floatval(trim($minmax[1]));
        if (($value >= $min) && ($value <= $max)) {
            return true;
        }
    }

    return false;
}

/**
 * @param  string  $thresholds  someting like “..10” or “-1.5..1.5” or “-20..-10;10..20”
 * @return array of sql usable mins and maxs
 *
 * @author Patrick Reichel
 */
function valuesFromThresholdString($thresholds)
{
    $ret = [];

    if (! $thresholds) {
        return $ret;
    }

    $thresholds = explode(';', $thresholds);
    foreach ($thresholds as $threshold) {
        $minmax = explode('..', trim($threshold));
        $min = ('' == trim($minmax[0])) ? -PHP_FLOAT_MAX : floatval(trim($minmax[0]));
        $max = ('' == trim($minmax[1])) ? PHP_FLOAT_MAX : floatval(trim($minmax[1]));
        $ret[] = [$min, $max];
    }

    return $ret;
}

function snmpDateAndTimeToCarbon($snmpDateAndTime)
{
    $unpack = unpack(
        'nyear/Cmonth/Cday/Chour/Cminute/Csecond/CdeciSecond/Ctz/CoffsetHour/CoffsetMinute',
        $snmpDateAndTime
    );

    $unpack['second'] += $unpack['deciSecond'] / 10;
    unset($unpack['deciSecond']);
    $unpack['tz'] = chr($unpack['tz'])."{$unpack['offsetHour']}:{$unpack['offsetMinute']}";

    return \Carbon\Carbon::create(...array_slice($unpack, 0, 7));
}

function snmpIpAddrToInet($suffix)
{
    $str = '';

    foreach (explode('.', $suffix) as $i) {
        $str .= str_pad(dechex($i), 2, '0', STR_PAD_LEFT);
    }

    return inet_ntop(hex2bin($str));
}

/**
 * Convert sysUpTime (in hundredths of a second) into human readable string.
 *
 * @author Ole Ernst
 */
function sysUpTimeForHumans(array $hundredthSecond): array
{
    return array_map(function ($hundredthSecond) {
        return \Carbon\CarbonInterval::seconds($hundredthSecond / 100)
            ->cascade()
            ->forHumans();
    }, $hundredthSecond);
}

/**
 * Convert datarate in human readable format
 *
 * See https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
 */
function bitsForHumans($rate): string
{
    if (! $rate) {
        return '';
    }

    $units = ['Bit', 'KBit', 'MBit', 'GBit', 'TBit', 'PBit'];

    $pow = floor(($rate ? log($rate) : 0) / log(1000));
    $pow = min($pow, count($units) - 1);
    $rate /= pow(1000, $pow);

    return round($rate, 2).' '.$units[$pow];
}

/**
 * Parse and decipher system location (sysLocation) of devices and return them as an array.
 *
 * @author Farshid Ghiasimanesh
 */
function parseLocation($location)
{
    if (! $location) {
        return null;
    }

    $explodedAddress = explode(';', $location);
    if (count($explodedAddress) < 8) {
        return null;
    }

    return [
        'city' => $explodedAddress[4],
        'hub' => $explodedAddress[6],
        'market' => $explodedAddress[7],
        'site' => $explodedAddress[8],
        'state' => $explodedAddress[5],
        'street' => $explodedAddress[3],
    ];
}
