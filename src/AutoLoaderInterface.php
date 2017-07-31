<?php

namespace ResumeNext\AutoLoader;

/**
 * Allows ManagerInterface to register any implementation
 */
interface AutoLoaderInterface {

	/**
	 * Attempt to load (include/require) $qcn
	 *
	 * @param string $qcn Qualified Class Name
	 *
	 * @return void
	 */
	public function load(string $qcn);

}

/* vi:set ts=4 sw=4 noet: */
