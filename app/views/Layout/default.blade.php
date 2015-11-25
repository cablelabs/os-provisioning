<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')
	</head>

	<body <?php if(isset($body_onload)) echo "onload=$body_onload()";?> > 

		<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in">

			@include ('Layout.header')

			@include ('bootstrap.sidebar')
			
			<div id="content" class="content">
				@yield ('content')
			</div>
			
			@include ('bootstrap.footer')	
		</div>

	</body>

</html>
