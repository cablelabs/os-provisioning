<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')
	</head>

	@include ('bootstrap.header')	

	<body class="pace-top">

	    <!-- begin login -->
        <div class="login bg-black animated fadeInDown">

            <!-- begin brand -->
            <div class="login-header">
                <div class="brand">
                    <span class="logo"></span> Das Monster
                    <small>The next Generation NMS</small>
                </div>
                <div class="icon">
                    <i class="fa fa-sign-in"></i>
                </div>
            </div>

            <!-- end brand -->
            <div class="login-content">
                {{ Form::open(array('url' => 'auth/login')) }}

 					<div class="form-group m-b-20">
                    {{ Form::text('login_name', Input::old('login_name'), array('autofocus'=>'autofocus', 'class' => "form-control input-lg", 'placeholder' => 'Username', 'style' => 'simple')) }}
                    </div>

                    <div class="form-group m-b-20">
                    {{ Form::password('password', array('autofocus'=>'autofocus', 'class' => "form-control input-lg", 'placeholder' => 'Password', 'style' => 'simple')) }}
                    </div>
            <!--
                    <div class="checkbox m-b-20">
                        <label>
                            <input type="checkbox" /> Remember Me
                        </label>
                    </div>
            -->
                    <div class="login-buttons">
                        <button type="submit" class="btn btn-success btn-block btn-lg">Sign me in</button>
                    </div>


                {{ Form::close() }}
            </div>
        </div>
        <!-- end login -->
        
    </body>

	@include ('bootstrap.footer')	
</html>


