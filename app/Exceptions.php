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

/**
 * Error to use if requested action (read, write, etc.) is not allowed
 */
class InsufficientRightsError extends PermissionDeniedError {
}
