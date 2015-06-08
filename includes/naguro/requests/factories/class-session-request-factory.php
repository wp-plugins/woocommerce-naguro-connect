<?php

class Naguro_Session_Request_Factory implements Naguro_Request_Factory {
	public function get_request_by_method( $method ) {
		if ( 'get' === $method ) {
			return new Naguro_Session_Get_Request( $_POST );
		}

		throw new Exception('Invalid method.');
	}
}