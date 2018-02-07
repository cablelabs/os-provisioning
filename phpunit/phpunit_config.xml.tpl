<?xml version="1.0" encoding="UTF-8"?>
<phpunit
	backupGlobals="false"
	backupStaticAttributes="false"
	bootstrap="/var/www/nmsprime/bootstrap/autoload.php"
	colors="true"
	convertErrorsToExceptions="true"
	convertNoticesToExceptions="true"
	convertWarningsToExceptions="true"
	processIsolation="false"
	stopOnFailure="true"
	syntaxCheck="false"
	debug="true"
	verbose="true"
>
	<logging>
		<log type="testdox-html" target="{{phpunit_html_log_file}}"/>
	</logging>
    <testsuites>
		{{testsuite_directories}}
    </testsuites>
</phpunit>
