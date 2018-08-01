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
					@if (isset($status))
						{{ $status }}
					@endif

	                @if (session('status'))
					    {{ session('status') }}
					@endif

                    @if ( !empty($message))
                        {{ $message }} <a href="{{ route('Auth.logout') }}" class="btn btn-success">Logout</a>
                    @endif
                    <br><br>
                </div>
                <div>
                    <a href="{{\BaseRoute::get_base_url()}}" class="btn btn-success">Go Back to Home Page</a>
                </div>
            </div>
        </div>
        {{-- end error --}}

    </body>

	@include ('bootstrap.footer')
</html>


