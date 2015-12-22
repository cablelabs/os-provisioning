<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')
	</head>

	@include ('bootstrap.header')	

	<body class="pace-top">

        <div class="error">
            <div class="error-code m-b-10">Access Denied <i class="fa fa-warning"></i></div>
            <div class="error-content">
                <div class="error-message">
	                @if (session('status'))
					    {{ session('status') }}
					@endif
                </div>
                <div>
                    <a href="{{Request::root()}}" class="btn btn-success">Go Back to Home Page</a>
                </div>
            </div>
        </div>
        <!-- end error -->
        
    </body>

	@include ('bootstrap.footer')	
</html>


