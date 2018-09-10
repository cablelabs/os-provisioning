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
		<div class="login-cover-image">
			<img alt=""  data-id="login-cover-image" src="{{asset('images/'.$image)}}">
		</div>
		<div class="login-cover-bg"></div>
	</div>

		{{-- begin login --}}
		<div class="login login-v2 animated fadeInDown">


			{{-- end brand --}}
			<div class="login-content">

			{{-- begin brand --}}
				<div class="login-header" style="margin-top:120px;">
					<div class="icon">
						<i class="fa fa-sign-in" style="font-color:#b7b7b7;"></i>
					</div>
				</div>
				<div class="brand m-b-20">
					<img src="{{asset('images/nmsprime-logo-white.png')}}" class="img-fluid">
				</div>
				<div>
					{{ Form::open(array('url' => $prefix.'/login')) }}

					{{-- Username --}}
					<div class="form-group m-b-20">
					{{ Form::text('login_name', Input::old('login_name'), array('autofocus'=>'autofocus', 'class' => "form-control input-lg", 'placeholder' => \App\Http\Controllers\BaseViewController::translate_label('Username'), 'style' => 'simple')) }}
					</div>

					{{-- Password --}}
					<div class="form-group m-b-20">
					{{ Form::password('password', array('autofocus'=>'autofocus', 'class' => "form-control input-lg", 'placeholder' => \App\Http\Controllers\BaseViewController::translate_label('Password'), 'style' => 'simple')) }}
					</div>

					{{-- Remember Checkbox --}}
					<div class="form-group m-b-20 d-flex align-items-center">
						<input align="left" class="mt-0 mb-2" name="remember" type="checkbox" value="1">
						<label for="remember" class="control-label px-2">
							{{ \App\Http\Controllers\BaseViewController::translate_label('Remember Me') . '!' }}
						</label>
					</div>

					{{-- Error Message --}}
					<div class="m-t-20">
						<p align="center"><font id="error" color="yellow">
							@foreach ($errors->all() as $error)
				                {{ $error }}
				            @endforeach
						</font></p>
					</div>
					<br>
					{{-- Login Button --}}
					<div class="login-buttons">
						<button type="submit" class="btn btn-success btn-block btn-lg">{{ \App\Http\Controllers\BaseViewController::translate_label('Sign me in') }}</button>
					</div>

				{{ Form::close() }}
				</div>
			</div>
		</div>
		{{-- end login --}}

	</body>

	@include ('bootstrap.footer')
</html>


