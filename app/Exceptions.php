<?php

/**
 * Basic permission error
 */
class PermissionDeniedError extends Exception {

	/**
	 * Constructor.
	 * Adds logging
	 */
	public function __construct($message=null, $code=0, Exception $previous=null) {

		parent::__construct($message, $code, $previous);

		// Log every problem with permission
		Log::warning($message);
	}
}

/**
 * Error to use if user is not logged in
 */
class NoAuthenticatedUserError extends PermissionDeniedError {
}

<<<<<<< HEAD
/**
 * Error to use if something is wrong with the request
 */
class InvalidPermissionsRequest extends PermissionDeniedError {
}

/**
 * Error to use if access to model is not allowed
 */
class NoModelPermissionError extends PermissionDeniedError {
}

/**
 * Error to use if access to net is not allowed
 */
class NoNetPermissionError extends PermissionDeniedError {
}

=======
>>>>>>> d18569773f09123c6990e86ca53f6387b9901ad7
/**
 * Error to use if requested action (read, write, etc.) is not allowed
 */
class InsufficientRightsError extends PermissionDeniedError {
}
