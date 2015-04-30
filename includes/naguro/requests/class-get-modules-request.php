<?php

class Naguro_Get_Modules_Request extends Naguro_Request {
	public function do_request() {
		$this->handler->handle_request( 'modules', $this->params, 'get' );
		return $this->handler->get_data();
	}

	public function output() {
		echo $this->do_request();
	}
}