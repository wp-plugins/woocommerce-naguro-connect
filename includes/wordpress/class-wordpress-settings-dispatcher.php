<?php

class Naguro_WordPress_Settings_Dispatcher {
	public function load_settings_page() {
		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'dashboard';

		switch( $tab ) {
			case 'dashboard':
				new Naguro_Dashboard_Page();
				break;
			case 'modules':
				new Naguro_Modules_Page();
				break;
			case 'settings':
				new Naguro_Settings_Page();
				break;
		}
	}
}