<?php

class Naguro_Modules_Loader {
	public function __construct() {
		$modules = Naguro_Modules_Repository::get_active_modules();

		/** @var Naguro_Module $module */
		foreach ( $modules as $module ) {
			$module->load();
		}
	}
}