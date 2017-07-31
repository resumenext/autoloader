<?php

namespace ResumeNext\AutoLoader;

/**
 * For registering AutoLoaderInterface instances
 */
interface ManagerInterface {

	/**
	 * Test if a given identifier has been registered
	 *
	 * @param string $identifier Result of register()
	 *
	 * @return bool
	 */
	public function has(string $identifier): bool;

	/**
	 * Register an instance of AutoLoaderInterface
	 *
	 * @param \ResumeNext\AutoLoader\AutoLoaderInterface $autoloader
	 * @param bool                                           $prepend
	 *
	 * @return string Unique identifier for the instance
	 * @throws \ResumeNext\AutoLoader\Exception\AlreadyRegisteredException
	 */
	public function register(AutoLoaderInterface $autoloader, bool $prepend = false): string;

	/**
	 * Unregister an instance of AutoLoaderInterface
	 *
	 * The identifier may be reused for newly registered instances.
	 *
	 * @param string $identifier
	 *
	 * @return \ResumeNext\AutoLoader\ManagerInterface
	 * @throws \ResumeNext\AutoLoader\Exception\InvalidIdentifierException
	 */
	public function unregister(string $identifier): ManagerInterface;

}

/* vi:set ts=4 sw=4 noet: */
