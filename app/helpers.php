<?php

/**
 * An improved version of laravel's dd() function
 * This will first print some meta information about the caller of dd
 * and then passes all given arguments to the original dd() function
 *
 * Background: I tend to have multiple dd() calls on debugging. Sometimes
 * it is hard to find all of those again ;-)
 *
 * To enable functions within this file run composer dump-auto (if it is autoloaded by composer.json)
 *
 * @author Patrick Reichel
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
    echo $td.array_get($bt[0], 'file', 'n/a').', line '.array_get($bt[0], 'line', 'n/a').'</td>';
    echo '</tr>';
    echo '<tr>';
    echo $td.'Method: </td>';
    echo $td.array_get($bt[1], 'class', 'n/a').'::'.array_get($bt[1], 'function', 'n/a').'()</td>';
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
function unify_mac($data)
{
    $data['mac'] = preg_replace('/[^a-f\d]/i', '', $data['mac']);
    $data['mac'] = wordwrap($data['mac'], 2, ':', true);

    return $data;
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
 * @return 	int 	    PID (process ID of background process) if parallel is true, otherwise 0
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

    ChannelLog::debug('billing', 'Concat '.$cnt.' PDFs to '.$target_fn);

    $cmd_ext = $multithreaded ? '> /dev/null 2>&1 & echo $!' : '';
    exec("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile='$target_fn' $sourcefiles $cmd_ext", $output, $ret);

    // Note: normally output is [] and ret is 0
    if ($ret) {
        ChannelLog::error('billing', "Error concatenating target file $target_fn", [$ret]);
    }

    return $multithreaded ? (int) $output[0] : 0;
}

/**
 * Create PDF from tex template
 *
 * @param string 	directory & filename
 * @param bool 	 	start latex process in background (for faster SettlementRun)
 * @return int      return value of pdflatex - 0 on success
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
 * @return string   message
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
 * @param string|null $locale
 * @return string
 */
function checkLocale($locale = null) : string
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
            ->orWhereNull($column)
            ->orWhere($column, '=', '');
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
            return date('d-m-Y', $date);
    }
}
