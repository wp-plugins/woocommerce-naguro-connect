<?php

class WC_Naguro_HTML_Get_Request extends Naguro_Request {
	public function get() {
		$this->handler->handle_request('get-html', $this->params, 'get' );
		$data = $this->handler->get_data();
		$body = json_decode( $data['body'] );

		return array(
			'html' => $body->html,
			'css'  => $body->css,
			'js'   => $body->js,
		);
	}
}