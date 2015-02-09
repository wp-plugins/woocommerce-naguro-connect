<?php

class Naguro_WordPress_Settings_Dispatcher {
	public function load_settings_page() {
		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'dashboard';

		switch( $tab ) {
			case 'dashboard':
				new Naguro_WordPress_Dashboard();
				break;
			case 'modules':
				new Naguro_WordPress_Modules();
				break;
			case 'settings':
				new Naguro_WordPress_Settings_Page();
				break;
		}
	}
}