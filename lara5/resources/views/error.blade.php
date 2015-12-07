
<?php

	if (!isset($error))
		$error = 404;

	if (!isset($message))
		$message = 'Page not found';

	if (!isset($link))
		$link = Request::root();
	
?>

<h1>
Error: {{$error}}
</h1>

<h2>
Message: {{$message}}
</h2>

<h3>
Where to continue: <a href={{$link}}>Link</a>
</h3>