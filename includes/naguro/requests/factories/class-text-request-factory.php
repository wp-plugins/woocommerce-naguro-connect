<?php

class Naguro_Text_Request_Factory implements Naguro_Request_Factory {
	public function get_request_by_method( $method ) {
		if ( 'getimage' === $method ) {
			return new Naguro_Text_Image_Get_Request( $_POST );
		}

		throw new Exception('Invalid method.');
	}
}