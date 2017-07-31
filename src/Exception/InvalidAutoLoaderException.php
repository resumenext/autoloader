<?php

namespace ResumeNext\AutoLoader\Exception;

use RuntimeException;

/**
 * Occurs when specified class name does not implement AutoLoaderInterface
 */
class InvalidAutoLoaderException extends RuntimeException implements ExceptionInterface {
}

/* vi:set ts=4 sw=4 noet: */
