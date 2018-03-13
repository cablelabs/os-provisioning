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
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		// Auth Error Messages
		if ($e instanceof AuthException)
		{
			$msg = "AUTH failed: ";
			$msg .= \Request::getClientIP()." tried to access ".\Request::getRequestUri();
			$msg .= " (".$e->getMessage().")";
			\Log::error($msg);

			/* abort(403, $e->getMessage()); */
			return response()->view('auth.denied', ['status' => $e->getMessage()], 403);
		}

		// catch CSRF token timeouts
		if ($e instanceof TokenMismatchException) {
			return response()->view('auth.denied', ['status' => 'Session expired – please log in again'], 403);
			/* return redirect(route('Auth.login'))->with('message', 'You page session expired. Please try again'); */
		}

		return parent::render($request, $e);
	}

}
