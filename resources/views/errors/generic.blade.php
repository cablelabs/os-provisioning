<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')
	</head>

<?php
	if (!isset($error))
		$error = 404;

	if (!isset($message))
		$message = 'Page not found';

	if (!isset($link))
		$link = Request::root().'/'.\BaseRoute::$admin_prefix;
?>

	@include ('bootstrap.header')

	<body class="pace-top">

        <div class="error">
            <div class="error-code m-b-10">{{$error}}<i class="fa fa-warning"></i></div>
            <div class="error-content">
                <div class="error-message">{{$message}}</div>
                <div class="error-desc m-b-20">
                    The page you're looking for doesn't exist. <br />
                    <!-- Perhaps, there pages will help find what you're looking for. -->
                </div>
                <div>
					<a href="{{$link}}" class="btn btn-success">Go Back to Home Page</a><br><br>
					<a href="javascript:history.back()" class="btn btn-success">Go Back to previous page.</a>
                </div>
            </div>
        </div>
        <!-- end error -->

    </body>

	@include ('bootstrap.footer')
</html>
