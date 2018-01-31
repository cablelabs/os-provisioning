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
		<log type="testdox-html" target="{{phpunit_html_out_file}}"/>
	</logging>
    <testsuites>
		{{testsuite_directories}}
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_DRIVER" value="sync"/>
		<env name="APP_URL" value="https:/127.0.0.1:8080/nmsprime"/>
    </php>
</phpunit>
