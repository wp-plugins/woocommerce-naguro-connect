<?php

class Naguro_Request_Trial_Key_Request extends Naguro_Request {
	public function do_request() {
		$this->handler->handle_request( 'key/create-trial', $this->params, 'post' );
		return $this->handler->get_data();
	}
}