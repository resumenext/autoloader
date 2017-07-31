<?php

namespace ResumeNext\AutoLoader;

/**
 * Wraps instances of BuilderInterface and ManagerInterface.
 * Facilitates easy configuration using convenience methods.
 */
class Container {

	/** @var \ResumeNext\AutoLoader\BuilderInterface */
	protected $builder;

	/** @var \ResumeNext\AutoLoader\ManagerInterface */
	protected $manager;

	/**
	 * Constructor
	 *
	 * @param \ResumeNext\AutoLoader\BuilderInterface $builder
	 * @param \ResumeNext\AutoLoader\ManagerInterface $manager
	 */
	public function __construct(BuilderInterface $builder, ManagerInterface $manager) {
		$this->setBuilder($builder);
		$this->setManager($manager);
	}

	/**
	 * Return a copy of BuilderInterface
	 *
	 * @return \ResumeNext\AutoLoader\BuilderInterface
	 */
	public function getBuilder(): BuilderInterface {
		return clone $this->builder;
	}

	/**
	 * Return instance of ManagerInterface
	 *
	 * @return \ResumeNext\AutoLoader\ManagerInterface
	 */
	public function getManager(): ManagerInterface {
		return $this->manager;
	}

	/**
	 * Convenience method
	 *
	 * Registers an instance of AutoLoaderInterface with the manager.
	 *
	 * @param \ResumeNext\AutoLoader\AutoLoaderInterface $loader
	 *
	 * @return string Unique identifier for the AutoLoaderInterface object
	 */
	public function register(AutoLoaderInterface $loader): string {
		return $this
			->getManager()
			->register($loader);
	}

	/**
	 * Change the BuilderInterface instance
	 *
	 * @param \ResumeNext\AutoLoader\BuilderInterface $builder
	 *
	 * @return $this
	 */
	public function setBuilder(BuilderInterface $builder): Container {
		$this->builder = $builder;

		return $this;
	}

	/**
	 * Change the ManagerInterface instance
	 *
	 * @param \ResumeNext\AutoLoader\ManagerInterface $manager
	 *
	 * @return $this
	 */
	public function setManager(ManagerInterface $manager): Container {
		$this->manager = $manager;

		return $this;
	}

	/**
	 * Convenience method
	 *
	 * Pass an array or Traversable with arrays that have
	 * a namespace as the first value and a corresponding
	 * path as the second value. E.g.: [["Foo", "./src"]]
	 *
	 * @param array|\Traversable $config
	 *
	 * @return string Unique identifier for the AutoLoaderInterface object
	 */
	public function setup($config): string {
		$builder = $this->getBuilder();

		foreach ($config as list($namespace, $path)) {
			$builder->add($namespace, $path);
		}

		return $this->register(
			$builder->build()
		);
	}

}

/* vi:set ts=4 sw=4 noet: */
