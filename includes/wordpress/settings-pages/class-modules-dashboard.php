<?php

class Naguro_WordPress_Modules extends Abstract_Naguro_WordPress_Settings_Page {
	public function __construct() {
		$this->output_tabs( 'modules' );

		$this->display_module_list();
	}

	private function display_module_list() {
		echo '<h3>Modules</h3>';
		$list = new Naguro_Modules_List();
		$list->prepare_items();
		$list->items = Naguro_Modules_Repository::get_available_modules();
		$list->display();
	}
}