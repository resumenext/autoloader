<?php

namespace ResumeNext\AutoLoader;

/**
 * Abstracts building AutoLoaderInterface products
 */
interface BuilderInterface {

	/**
	 * Add a path to the set of paths for the given namespace
	 *
	 * @param string $namespace e.g. "Foo\\Bar"
	 * @param string $path      e.g. "./vendor/foo/bar/src"
	 * @param bool   $prepend   Whether to prepend or append the path
	 *
	 * @return \ResumeNext\AutoLoader\BuilderInterface
	 */
	public function add(string $namespace, string $path, bool $prepend = false): BuilderInterface;

	/**
	 * Build an instance of AutoLoaderInterface
	 *
	 * @return \ResumeNext\AutoLoader\AutoLoaderInterface
	 */
	public function build(): AutoLoaderInterface;

	/**
	 * Set the path for the given namespace
	 *
	 * Will override the path set for the given namespace.
	 *
	 * @param string $namespace e.g. "Foo\\Bar"
	 * @param string $path      e.g. "./vendor/foo/bar/src"
	 *
	 * @return \ResumeNext\AutoLoader\BuilderInterface
	 */
	public function set(string $namespace, string $path): BuilderInterface;

}

/* vi:set ts=4 sw=4 noet: */
