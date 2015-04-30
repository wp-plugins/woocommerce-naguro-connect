<?php

interface Naguro_API_Handler {
	public function handle_request( $endpoint, $params = array(), $type = 'post', $request_params = array() );

	public function is_error();

	public function get_data();
}