<!doctype html>
<html>

    <head>
        <meta charset="utf-8">
        <title>NMS</title>
        @include ('bootstrap.header')
    </head>

    <body class="pace-top">
        <div class="error">
            <div class="error-code mb-3">{{ $error ?? 404 }}<i class="fa fa-warning m-l-10"></i></div>
            <div class="error-content">
                @if ($error == 403)
                    <div class="my-5">
                        <div class="error-message mb-3">{{ trans('auth.permissionDenied') }}!</div>
                        <div class="error-desc">
                            <b>{{ trans('auth.'.$message) }}!</b> <br />
                        </div>
                    </div>
                @elseif ($error == 404)
                    <div class="error-message my-5">{{ trans('view.error.pageNotFound') }}!</div>
                @else
                    <div class="error-message my-5">{{ $message }}</div>
                @endif
                <div class="mt-4 d-flex flex-column align-items-center">
                    <a href="{{ $link ?? route('Home') }}" class="btn btn-lg btn-success mb-4">
                        <i class="fa fa-home"></i>
                        {{ trans('view.error.backToHomePage') }}
                    </a>
                    @if(auth()->user())
                        <a class="btn btn-lg btn-success" href="{{ URL::previous() }}">
                            <i class="fa fa-arrow-left"></i>
                            {{ trans('view.error.backToPreviousPage') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <!-- end error -->
    </body>

    @include ('bootstrap.footer')
</html>
