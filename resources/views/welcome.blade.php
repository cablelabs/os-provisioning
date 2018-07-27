<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')

		<script>setTimeout("document.getElementById('error').style.display='none';", 3000);</script>
	</head>

	@include ('bootstrap.header')

	<body class="pace-top">

	{{-- Background Image --}}
	<div class="login-cover">
		<div class="login-cover-image"><img alt="" data-id="login-cover-image" src="{{asset('images/main-pic-1.png')}}"></div>
		<div class="login-cover-bg"></div>
	</div>

		{{-- begin login --}}
		<div class="login login-v2 animated fadeInDown">

			{{-- begin brand --}}
			<div class="login-header">
				<div class="brand">
					<span class="logo"></span> {{ $head1 }}
					<small>{{ $head2 }}</small>
				</div>
				<div class="icon">
					<i class="fa fa-sign-in"></i>
				</div>
			</div>

			{{-- end brand --}}
			<div class="login-content">
				<div align="center">

					<div class="login-buttons">
						<a href='admin' class="btn btn-success btn-block btn-lg" role="button">Admin Center</a>
						<br><br><br>
						<a href='customer' class="btn btn-success btn-block btn-lg" role="button">Customer Control Center</a>
					</div>

					<br>
					<div class="quote">{{ Inspiring::quote() }}</div>

				</div>
			</div>

		</div>
		{{-- end login --}}

	</body>



	<body>
	</body>
	@include ('bootstrap.footer')
</html>
