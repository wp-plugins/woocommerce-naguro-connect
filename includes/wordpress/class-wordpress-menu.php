<?php

class Naguro_WordPress_Menu {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	public function add_menu() {
		add_menu_page( 'Naguro', 'Naguro', 'manage_options', 'woocommerce-naguro', array( $this, 'menu_callback' ), null, 27 );
	}

	public function menu_callback() {
		$dispatcher = new Naguro_WordPress_Settings_Dispatcher();
		$dispatcher->load_settings_page();
	}
}