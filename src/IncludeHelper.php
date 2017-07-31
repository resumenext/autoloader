<?php

namespace ResumeNext\AutoLoader;

/**
 * Wrappers for include/require statements, to
 * avoid leaking the $this context in the file
 */
abstract class IncludeHelper {

	/**
	 * include a file or URL
	 *
	 * @param string $file
	 *
	 * @return mixed Result of include
	 */
	public static function includeFile(string $file) {
		return include $file;
	}

	/**
	 * include_once a file or URL
	 *
	 * @param string $file
	 *
	 * @return mixed Result of include_once
	 */
	public static function includeFileOnce(string $file) {
		return include_once $file;
	}

	/**
	 * require a file or URL
	 *
	 * @param string $file
	 *
	 * @return mixed Result of require
	 */
	public static function requireFile(string $file) {
		return require $file;
	}

	/**
	 * require_once a file or URL
	 *
	 * @param string $file
	 *
	 * @return mixed Result of require_once
	 */
	public static function requireFileOnce(string $file) {
		return require_once $file;
	}

}

/* vi:set ts=4 sw=4 noet: */
