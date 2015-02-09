<?php

interface Naguro_API_Handler {
	public function handle_request( $endpoint, $params = array(), $type = 'post' );

	public function is_error();

	public function get_data();
}