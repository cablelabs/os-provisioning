<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        return parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Allow user to log-in
        if ($request->is('admin/api/v0/*') || $request->wantsJson()) {
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
                return response()
                    ->v0ApiReply(['messages' => ['errors' => ['Unauthenticated']]], false, null, 401)
                    ->header('WWW-Authenticate', 'Basic');
            }

            $response = ['messages' => ['errors' => ['Sorry, something went wrong.']]];

            // If the app is in debug mode
            if (config('app.debug')) {
                // Add the exception class name, message and stack trace to response
                // Reflection might be better here
                $response['exception'] = get_class($exception);
                if ($exceptionMsg = $exception->getMessage()) {
                    $response['messages']['errors'] = [$exceptionMsg];
                }
                $response['trace'] = $exception->getTrace();
            }

            // Default response is 400
            $statusCode = 400;
            // If this exception is an instance of HttpException
            if ($this->isHttpException($exception)) {
                // Grab the HTTP status code from the Exception
                $statusCode = $exception->getStatusCode();
            }

            return response()->v0ApiReply($response, false, null, $statusCode);
        }

        // Auth Error Messages
        if ($exception instanceof AuthException) {
            $msg = 'AUTH failed: ';
            $msg .= \Request::getClientIP().' tried to access '.\Request::getRequestUri();
            $msg .= ' ('.$exception->getMessage().')';
            \Log::error($msg);

            /* abort(403, $exception->getMessage()); */
            return response()->view('auth.denied', ['status' => $exception->getMessage()], 403);
        }

        // catch CSRF token timeouts
        if ($exception instanceof TokenMismatchException) {
            return response()->view('auth.denied', ['status' => 'Session expired – please log in again'], 403);
            /* return redirect(route('Auth.login'))->with('message', 'You page session expired. Please try again'); */
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->v0ApiReply(['messages' => ['errors' => ['Unauthenticated']]], false, null, 401);
        }

        $server_port = $request->getPort();
        $admin_port = env('HTTPS_ADMIN_PORT', '8080');
        $ccc_port = env('HTTPS_CCC_PORT', '443');

        if ($server_port == $admin_port) {
            return redirect()->guest(route('adminLogin'));
        }

        if ($server_port == $ccc_port) {
            return redirect()->guest(route('customerLogin'));
        }
    }
}
