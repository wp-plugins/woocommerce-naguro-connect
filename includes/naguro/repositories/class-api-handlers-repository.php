<?php

class Naguro_API_Handlers_Repository extends Naguro_Repository {

	/**
	 * @return Naguro_API_Handler
	 */
	public static function get_handler() {
		$class_name = apply_filters( 'naguro_api_handler_class', '' );
		return new $class_name();
	}
}