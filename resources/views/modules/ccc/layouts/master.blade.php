<!DOCTYPE html>

<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Module Ccc</title>
		@include ('bootstrap.header')
	</head>

	<body <?php if(isset($body_onload)) echo "onload=$body_onload()";?> >
		<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in">
			<div id="content" class="content">

				@include ('ccc::layouts.header')

				@yield('content')

				@include ('bootstrap.footer')

			</div>
		</div>
	</body>

</html>