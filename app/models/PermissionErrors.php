<?php

/**
 * Basic permission error
 */
class PermissionDeniedError() extends Exception {
}


/**
 * Error to use if user is not logged in
 */
class NoLoginError() extends PermissionDeniedError {
}


/**
 * Error to use if access to model is not allowed
 */
class NoModelPermissionError() extends PermissionDeniedError {
}

/**
 * Error to use if access to net is not allowed
 */
class NoNetPermissionError() extends PermissionDeniedError {
}


/**
 * Error to use if reading is not allowed
 */
class NoReadPermissionError() extends PermissionDeniedError {
}

/**
 * Error to use if writing is not allowed
 */
class NoWritePermissionError() extends PermissionDeniedError {
}

/**
 * Error to use if deleting is not allowed
 */
class NoDeletePermissionError() extends PermissionDeniedError {
}

/**
 * Error to use if creating is not allowed
 */
class NoCreatePermissionError() extends PermissionDeniedError {
}
