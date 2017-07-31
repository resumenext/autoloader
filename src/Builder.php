<?php

namespace ResumeNext\AutoLoader;

/**
 * Builds an AutoLoaderInterface for use with PSR-4 or equivalent
 */
class Builder implements BuilderInterface {

	const DEFAULT_PRODUCT = Psr4AutoLoader::class;

	/** @var string Class name of product to build */
	protected $product;

	/** @var array[] Namespace (string) => Paths (string[]) */
	protected $namespaces = [];

	/**
	 * Constructor
	 *
	 * @param string $product (Optional) Class name of AutoLoaderInterface to build
	 */
	public function __construct(string $product = "") {
		$this->setProduct($product ?: static::DEFAULT_PRODUCT);
	}

	public function add(string $namespace, string $path, bool $prepend = false): BuilderInterface {
		$namespace = $this->canonicalizeNamespace($namespace);
		$path = $this->canonicalizePath($path);
		$paths = $this->namespaces[$namespace] ?? [];

		if (!$prepend) {
			array_push($paths, $path);
		} else {
			array_unshift($paths, $path);
		}

		$this->namespaces[$namespace] = $paths;

		return $this;
	}

	public function build(): AutoLoaderInterface {
		return $this->configureLoader(
			$this->createLoader()
		);
	}

	/**
	 * Get class name of product (AutoLoaderInterface implementation)
	 *
	 * @return string
	 */
	public function getProduct(): string {
		return $this->product;
	}

	public function set(string $namespace, string $path): BuilderInterface {
		$namespace = $this->canonicalizeNamespace($namespace);
		$path = $this->canonicalizePath($path);

		$this->namespaces[$namespace] = [$path];

		return $this;
	}

	/**
	 * Set class name of AutoLoaderInterface to build
	 *
	 * @param string $product
	 *
	 * @return $this
	 * @throws \ResumeNext\AutoLoader\Exception\InvalidAutoLoaderException
	 */
	public function setProduct(string $product): Builder {
		if (!is_subclass_of($product, AutoLoaderInterface::class)) {
			throw new Exception\InvalidAutoLoaderException(sprintf(
				"AutoLoaderInterface not implemented by \"%s\"",
				$product
			));
		}

		$this->product = $product;

		return $this;
	}

	/**
	 * Configure an AutoLoaderInterface instance
	 *
	 * @param \ResumeNext\AutoLoader\AutoLoaderInterface $loader
	 *
	 * @return \ResumeNext\AutoLoader\AutoLoaderInterface
	 */
	protected function configureLoader(AutoLoaderInterface $loader): AutoLoaderInterface {
		/* Assumes the loader ingests its configuration as properties.
		 * This is implementation-specific, as the interface does not
		 * define how this should be done (at the time of writing).
		 */
		foreach ($this->getAll() as $namespace => $paths) {
			$loader->$namespace = $paths;
		}

		return $loader;
	}

	/**
	 * Create new instance of AutoLoaderInterface
	 *
	 * @return \ResumeNext\AutoLoader\AutoLoaderInterface
	 */
	protected function createLoader(): AutoLoaderInterface {
		$product = $this->getProduct();

		return new $product();
	}

	/**
	 * Get paths for all namespaces
	 *
	 * @return array[]
	 */
	protected function getAll(): array {
		return $this->namespaces;
	}

	/**
	 * Return canonical namespace identifier
	 *
	 * @param string $namespace
	 *
	 * @return string
	 */
	protected function canonicalizeNamespace(string $namespace): string {
		return trim($namespace, "\\");
	}

	/**
	 * Return canonical path
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	protected function canonicalizePath(string $path): string {
		return rtrim($path, "\\/");
	}

}

/* vi:set ts=4 sw=4 noet: */
