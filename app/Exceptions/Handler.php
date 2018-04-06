<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

use App\Exceptions\AuthException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
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
		// Auth Error Messages
		if ($exception instanceof AuthException)
		{
			$msg = "AUTH failed: ";
			$msg .= \Request::getClientIP()." tried to access ".\Request::getRequestUri();
			$msg .= " (".$exception->getMessage().")";
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
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest('login');
    }

}
