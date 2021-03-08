<!doctype html>
<html>

    <head>
        <meta charset="utf-8">
        <title>NMS</title>
        @include ('bootstrap.header')
    </head>

    <body class="pace-top">
        <div class="error">
            <div class="error-code m-b-10">{{ $error ?? 404 }}<i class="fa fa-warning m-l-10"></i></div>
            <div class="error-content">
                <div class="error-message mb-4">{!! $message ?? 'Page not found' !!}</div>
                @if ($error == 403)
                    <div class="error-desc m-b-20">
                        <b>Permission denied!</b> <br />
                    </div>
                @elseif ($error == 404)
                    <div class="error-desc m-b-20">
                        The page you're looking for doesn't exist. <br />
                    </div>
                @endif
                <div>
                    <a href="{{ $link ?? route('Home') }}" class="btn btn-success">Go Back to Home Page</a><br><br>
                    <a href="{{ URL::previous() }}" class="btn btn-success">Go Back to previous page.</a>
                </div>
            </div>
        </div>
        <!-- end error -->
    </body>

    @include ('bootstrap.footer')
</html>
