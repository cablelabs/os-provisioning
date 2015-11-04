<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')
	</head>

	<body> 

		@include ('Layout.header')
		
		@yield ('content')
		
		@include ('bootstrap.footer')

	</body>

</html>
