<?php

abstract class Naguro_Module_Model extends Naguro_Model implements Naguro_Module {
	public $slug = '';
	public $name = '';
	public $active = false;
	public $unlocked = false;
	public $always_on = false;
	public $description = '';
	public $purchase_url = '';

	/**
	 * @return bool
	 */
	public function activate() {
		if ( $this->active ) {
			return true;
		}

		$active_modules_array = get_option('naguro_active_modules', array() );

		if ( ! in_array( $this->slug, $active_modules_array ) ) {
			array_push( $active_modules_array, $this->slug );
			update_option( 'naguro_active_modules', $active_modules_array );
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function deactivate() {
		if ( ! $this->active ) {
			return true;
		}

		$active_modules_array = get_option('naguro_active_modules', array() );

		$module_key = array_search( $this->slug, $active_modules_array );

		if ( false === $module_key ) {
			return true;
		}

		unset( $active_modules_array[ $module_key ] );

		update_option( 'naguro_active_modules', $active_modules_array );
		return true;
	}
}