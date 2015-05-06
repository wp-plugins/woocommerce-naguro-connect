<?php

class Naguro_Editor_Request extends Naguro_Request {
	public function output() {
		return '';
	}

	public function get( $endpoint ) {
		$this->handler->handle_request($endpoint, $this->params, 'post' );
		$data = $this->handler->get_data();
		$body = json_decode( $data['body'] );
	}
}