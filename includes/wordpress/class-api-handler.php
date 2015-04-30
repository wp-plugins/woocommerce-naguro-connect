<?php

class WordPress_API_Handler implements Naguro_API_Handler {
	/** @var string */
	private $activation_key;

	/** @var string */
	private $api_url;

	/** @var mixed */
	private $data;

	public function __construct() {
		$this->activation_key = get_option( 'naguro_activation_key' );
		$this->api_url = apply_filters( 'wc_naguro_api_endpoint_url', 'https://api.naguro.com/api/v1/' );
	}

	public function handle_request( $endpoint, $params = array(), $type = 'post', $request_params = array() ) {
		$params = array_merge( $request_params, $this->setup_parameters( $params ) );
		$url = $this->generate_api_endpoint_url( $endpoint );

		if ( 'get' == $type ) {
			$data = $this->get_request( $url, $params );
		} else {
			$data = $this->post_request( $url, $params );
		}

		$this->data = $data;
	}

	public function is_error() {
		return is_wp_error( $this->data );
	}

	public function get_data() {
		return $this->data;
	}

	private function generate_api_endpoint_url( $endpoint ) {
		return trailingslashit( trailingslashit( $this->api_url ) . $endpoint );
	}

	private function setup_parameters( $params ) {
		$params = $this->transform_params_to_body_param( $params );
		return $this->add_auth_token_to_header( $params );
	}

	private function add_auth_token_to_header( $params ) {
		$params['headers']['X-Auth-Token'] = $this->activation_key;
		return $params;
	}

	private function transform_params_to_body_param( $params ) {
		return array( 'body' => $params );
	}

	private function get_request( $url, $params ) {
		return wp_remote_get( $url, $params );
	}

	private function post_request( $url, $params ) {
		return wp_remote_post( $url, $params );
	}
}