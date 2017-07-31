<?php

namespace ResumeNext\AutoLoader;

/**
 * PSR-4 compliant autoloader
 *
 * This class can be hydrated by assigning properties.
 * Assign namespaces as properties having string path
 * arrays as their value.
 */
class Psr4AutoLoader implements AutoLoaderInterface {

	const NAMESPACE_SEPARATOR = "\\";

	public function load(string $qcn) {
		$path = $this->searchClass(
			...$this->splitNamespace($qcn)
		);

		if ($path) {
			$this->requireFile($path);
		}
	}

	/**
	 * require a file
	 *
	 * @param string $file   Path of file to require
	 * @param string $helper QCN of IncludeHelper class
	 *
	 * @return void
	 */
	protected function requireFile(string $file, string $helper = IncludeHelper::class) {
		call_user_func([$helper, "requireFile"], $file);
	}

	/**
	 * Locates the include file for $class in $path
	 *
	 * @param string $path
	 * @param string $class
	 *
	 * @return string|false Full path or false if not found
	 */
	protected function resolveInclude(string $path, string $class) {
		return stream_resolve_include_path(
			$path . DIRECTORY_SEPARATOR . $class . ".php"
		);
	}

	/**
	 * Locate filesystem path of given namespace/class combination
	 *
	 * @param string $namespace
	 * @param string $class
	 *
	 * @return string|false Path to include file, or false on error
	 */
	protected function searchClass(string $namespace, string $class) {
		if ($namespace === "") {
			return false;
		}

		if (isset($this->$namespace)) {
			foreach ($this->$namespace as $path) {
				$file = $this->resolveInclude($path, $class);

				if ($file) {
					return $file;
				}
			}
		}

		$split = $this->splitNamespace($namespace);

		return $this->searchClass(
			$split[0],
			$split[1] . DIRECTORY_SEPARATOR . $class
		);
	}

	/**
	 * Splits a namespace string on the last sub-namespace separator
	 *
	 * @param string $namespace E.g. "Foo\\Bar\\Quux"
	 *
	 * @return string[] E.g. ["Foo\\Bar", "Quux"]
	 */
	protected function splitNamespace(string $namespace) {
		$pos = strrpos($namespace, static::NAMESPACE_SEPARATOR);

		return $pos !== false
			? [
				substr($namespace, 0, $pos),
				substr($namespace, $pos + 1),
			]
			: ["", $namespace];
	}

}

/* vi:set ts=4 sw=4 noet: */
