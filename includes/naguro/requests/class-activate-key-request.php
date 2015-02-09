<?php

class Naguro_Activate_Key_Request extends Naguro_Request {
	public function do_request() {
		$this->handler->handle_request( 'key/activate', $this->params, 'post' );
		return $this->handler->get_data();
	}
}