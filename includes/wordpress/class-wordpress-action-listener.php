<?php

class Naguro_WordPress_Action_Listener {
	public function __construct() {
		if ( ! isset( $_GET['naguro-action'] ) ) {
			return;
		}

		switch( $_GET['naguro-action'] ) {
			case 'activate-module':
				$this->activate_module();
				break;
			case 'deactivate-module':
				$this->deactivate_module();
		}
	}

	private function activate_module() {
		if ( ! isset( $_GET['naguro-module'] ) ) {
			return;
		}

		$module = Naguro_Modules_Repository::get_module_by_slug( $_GET['naguro-module'] );

		if ( false === $module ) {
			return;
		}

		$module->activate();
		wp_redirect( admin_url('?page=woocommerce-naguro&tab=modules&naguro-module-activated='.$module->slug) );
		exit;
	}

	private function deactivate_module() {
		if ( ! isset( $_GET['naguro-module'] ) ) {
			return;
		}

		$module = Naguro_Modules_Repository::get_module_by_slug( $_GET['naguro-module'] );

		if ( false === $module ) {
			return;
		}

		$module->deactivate();
		wp_redirect( admin_url('?page=woocommerce-naguro&tab=modules&naguro-module-deactivated='.$module->slug) );
		exit;
	}
}