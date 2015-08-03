<?php

class Naguro_Modules_Page extends Abstract_Naguro_WordPress_Settings_Page {
	public function __construct() {
		$this->output_tabs( 'modules' );

		$this->display_module_list();
	}

	private function display_module_list() {
		if ( isset( $_GET['naguro-module-deactivated'] ) ) {
			$this->display_module_deactivated_message();
		} elseif ( isset( $_GET['naguro-module-activated'] ) ) {
			$this->display_module_activated_message();
		}

		echo '<h3>Unlocked modules</h3>';
		$list = new Naguro_Modules_List();
		$list->prepare_items();
		$list->items = Naguro_Modules_Repository::get_unlocked_modules();
		$list->display();

		echo '<h3>Available modules</h3>';
		echo '<p>These modules have not yet been unlocked. You can unlock them via your account on the Naguro website.</p>';
		$list = new Naguro_Modules_List();
		$list->prepare_items();
		$list->items = Naguro_Modules_Repository::get_locked_modules();
		$list->display();
	}

	private function display_module_deactivated_message() {
		echo '<div id="message" class="updated">';
		echo '<p>Module has been deactivated</p>';
		echo '</div>';
	}

	private function display_module_activated_message() {
		echo '<div id="message" class="updated">';
		echo '<p>Module has been activated</p>';
		echo '</div>';
	}
}