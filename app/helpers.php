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
function d() {

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
	call_user_func_array('dd', func_get_args());
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
 *
 * @author Nino Ryschawy
 */
function str_sanitize($string)
{
	$string = str_replace(' ', '_', $string);
	return preg_replace("/[^a-zA-Z0-9.\/_-]/", "", $string);
}


/**
 * Check if at least one of the needle array keys exists in the haystack array
 *
 * @return true if one array key of needle array exists in haystack array, false otherwise
 * @author Nino Ryschawy
 */
function multi_array_key_exists($needles, $haystack)
{
	foreach ($needles as $needle)
	{
		if (array_key_exists($needle, $haystack))
			return true;
	}

	return false;
}


/**
 * Escape Special Characters in Latex documents (before PDF conversion)
 * Used in Invoice.php & CccAuthuserController.php
 *
 * @author Nino Ryschawy
 */
function escape_latex_special_chars($string)
{
	if (!$string)
		return '';

	// NOTE: "\\" has to be on top as it otherwise would replace all replacements in following loop
	$map = array(
			"\\" => "\\textbackslash",
			"#"  => "\\#",
			"$"  => "\\$",
			"%"  => "\\%",
			"&"  => "\\&",
			"{"  => "\\{",
			"}"  => "\\}",
			"_"  => "\\_",
			"~"  => "\\~{}",
			"^"  => "\\^{}",
	);

	foreach ($map as $search => $replace)
		$string = str_replace($search, $replace, $string);

	return $string;

	// not working: https://stackoverflow.com/questions/2541616/how-to-escape-strip-special-characters-in-the-latex-document
	// return preg_replace( "/([\^\%~\\\\#\$%&_\{\}])/e", "\$map['$1']", $string );
}


/**
 * Concatenate a list of existing PDF Files
 *
 * @author Nino Ryschawy
 *
 * @param mixed  source files
 * @param string target filename
 */
function concat_pdfs($sourcefiles, $target_fn)
{
	if (is_array($sourcefiles))	{
		$cnt = count($sourcefiles);
		$sourcefiles = implode(' ', $sourcefiles);
	}
	// only for debugging - remove when sufficient tested
	else
		$cnt = count(explode(' ', trim($sourcefiles)));

	\ChannelLog::debug('billing', 'Concat '.$cnt. ' PDFs to '.$target_fn);

	exec("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=$target_fn $sourcefiles", $output, $ret);

	if ($ret)
		\ChannelLog::error('billing', "Error concatenating target file $target_fn", [$ret]);
}
