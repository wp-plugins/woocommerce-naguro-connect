<?php

class Naguro_Modules_Repository extends Naguro_Repository {
	public static function get_available_modules() {
		$core = new Naguro_Module_Model();
		$core->name = 'Naguro core';
		$core->description = 'The core of Naguro, always active.';
		$core->active = true;

		$shirts = new Naguro_Module_Model();
		$shirts->description = 'Allow your customers to design t-shirts with ease.';
		$shirts->name = 'Shirt module';

		return array(
			$core,
			$shirts,
		);
	}
}