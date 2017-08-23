<?php

namespace ResumeNext\AutoLoader;

/**
 * Registers (and unregisters) AutoLoaderInterface instances
 */
class Manager implements ManagerInterface {

	/** @var \ResumeNext\AutoLoader\AutoLoaderInterface[] */
	protected $loaders = [];

	public function has(string $identifier): bool {
		return isset($this->loaders[$identifier]);
	}

	public function register(AutoLoaderInterface $autoloader, bool $prepend = false): string {
		$ret = $this->getIdentifier($autoloader);

		if (!$this->has($ret)) {
			$this->setLoader($ret, $autoloader);

			$this->wrapSplAutoloadRegister(
				$this->getAutoloadFunction($autoloader),
				$prepend
			);

			return $ret;
		}

		throw new Exception\AlreadyRegisteredException(
			"Instance of AutoLoaderInterface already registered",
			17
		);
	}

	public function unregister(string $identifier): ManagerInterface {
		if ($this->has($identifier)) {
			$autoloader = $this->getLoader($identifier);

			$this->removeLoader($identifier);

			$this->wrapSplAutoloadUnregister(
				$this->getAutoloadFunction($autoloader)
			);

			return $this;
		}

		throw new Exception\InvalidIdentifierException(
			sprintf("Invalid identifier \"%s\".", $identifier),
			2
		);
	}

	/**
	 * Get the autoload function of an AutoLoaderInterface
	 *
	 * @param \ResumeNext\AutoLoader\AutoLoaderInterface $autoloader
	 *
	 * @return callable
	 */
	protected function getAutoloadFunction(
		AutoLoaderInterface $autoloader
	): callable {
		return [$autoloader, "load"];
	}

	/**
	 * Get an instance of AutoLoaderInterface by identifier
	 *
	 * @param string $identifier
	 *
	 * @return \ResumeNext\AutoLoader\AutoLoaderInterface
	 */
	protected function getLoader(string $identifier): AutoLoaderInterface {
		return $this->loaders[$identifier];
	}

	/**
	 * Create a unique identifier for a given object
	 *
	 * @param object $object
	 *
	 * @return string
	 */
	protected function getIdentifier($object): string {
		return spl_object_hash($object);
	}

	/**
	 * Remove an instance of AutoLoaderInterface
	 *
	 * @param string $identifier
	 *
	 * @return void
	 */
	protected function removeLoader(string $identifier) {
		unset($this->loaders[$identifier]);
	}

	/**
	 * Set an instance of AutoLoaderInterface by identifier
	 *
	 * @param string                                     $identifier
	 * @param \ResumeNext\AutoLoader\AutoLoaderInterface $loader
	 *
	 * @return void
	 */
	protected function setLoader(
		string $identifier,
		AutoLoaderInterface $loader
	) {
		$this->loaders[$identifier] = $loader;
	}

	/**
	 * Wraps spl_autoload_register
	 *
	 * @param callable $autoload_function
	 * @param bool     $prepend
	 *
	 * @return void
	 */
	protected function wrapSplAutoloadRegister(
		callable $autoload_function,
		bool $prepend = false
	) {
		spl_autoload_register($autoload_function, true, $prepend);
	}

	/**
	 * Wraps spl_autoload_unregister
	 *
	 * @param callable $autoload_function
	 *
	 * @return void
	 */
	protected function wrapSplAutoloadUnregister(callable $autoload_function) {
		spl_autoload_unregister($autoload_function);
	}

}

/* vi:set ts=4 sw=4 noet: */
