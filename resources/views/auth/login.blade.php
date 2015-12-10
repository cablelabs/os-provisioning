<!doctype html>
<html>
<head>
<title>Login</title>
</head>
<body style="padding: 50px">

{{ Form::open(array('url' => 'login')) }}
<h1>Welcome</h1>

<h3>Please log in to proceed</h3>

<!-- if there are login errors, show them here -->
<p>
	{{ $errors->first('login_name') }}
	{{ $errors->first('password') }}
</p>

<table>
	<tr>
		<td>{{ Form::label('login_name', 'Username') }}</td>
		<td>{{ Form::text('login_name', Input::old('login_name'), array('autofocus'=>'autofocus')) }}</td>
	</tr>
	<tr>
		<td>{{ Form::label('password', 'Password') }}</td>
		<td>{{ Form::password('password') }}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>{{ Form::submit('Submit!') }}</td>
	</tr>
</table>
{{ Form::close() }}

</body>
</html>
