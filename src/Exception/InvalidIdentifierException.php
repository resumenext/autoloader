<?php

namespace ResumeNext\AutoLoader\Exception;

use OutOfBoundsException;

/**
 * Occurs when specified identifier is not registered
 */
class InvalidIdentifierException extends OutOfBoundsException implements ExceptionInterface {
}

/* vi:set ts=4 sw=4 noet: */
