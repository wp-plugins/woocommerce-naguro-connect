<?php

class Naguro_Font_Request_Factory implements Naguro_Request_Factory {
	public function get_request_by_method( $method ) {
		if ( 'getavailablefonts' == $method ) {
			return new Naguro_Fonts_Get_Request( $_POST );
		}

		throw new Exception('Invalid method.');
	}
}