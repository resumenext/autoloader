<?php

namespace ResumeNext\AutoLoader\Exception;

use RuntimeException;

/**
 * Occurs when specified AutoLoaderInterface instance is already registered
 */
class AlreadyRegisteredException extends RuntimeException implements ExceptionInterface {
}

/* vi:set ts=4 sw=4 noet: */
