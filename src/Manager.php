<?php

namespace ResumeNext\AutoLoader;

/**
 * Registers (and unregisters) AutoLoaderInterface instances
 */
class Manager implements ManagerInterface {

	/** @var \ResumeNext\AutoLoader\AutoLoaderInterface[] */
	protected $loaders = [];

	public function has(string $identifier): bool {
		return isset($this->autoloaders[$identifier]);
	}

	public function register(AutoLoaderInterface $autoloader, bool $prepend = false): string {
		$ret = $this->getIdentifier($autoloader);

		if (!$this->has($ret)) {
			$this->loaders[$ret] = $autoloader;

			spl_autoload_register(
				$this->getAutoloadFunction($autoloader),
				true,
				$prepend
			);

			return $ret;
		}

		throw new Exception\AlreadyRegisteredException(
			"Instance of AutoLoaderInterface already registered"
		);
	}

	public function unregister(string $identifier): ManagerInterface {
		if ($this->has($identifier)) {
			$autoloader = $this->loaders[$identifier];

			unset($this->loaders[$identifier]);

			spl_autoload_unregister(
				$this->getAutoloadFunction($autoloader)
			);

			return $this;
		}

		throw new Exception\InvalidIdentifierException(sprintf(
			"Invalid identifier \"%s\".",
			$identifier
		));
	}

	/**
	 * Get the autoload function of an AutoLoaderInterface
	 *
	 * @param \ResumeNext\AutoLoader\AutoLoaderInterface $autoloader
	 *
	 * @return callable
	 */
	protected function getAutoloadFunction(AutoLoaderInterface $autoloader): callable {
		return [$autoloader, "load"];
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

}

/* vi:set ts=4 sw=4 noet: */
