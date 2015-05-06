<?php

class Naguro_Image_Request_Factory implements Naguro_Request_Factory {
	public function get_request_by_method( $method ) {
		if ( 'upload' === $method ) {
			return new Naguro_Image_Upload_Request( $_POST );
		} elseif ( 'getsrc' === $method ) {
			return new Naguro_Image_Get_Request( $_POST );
		}

		throw new Exception( 'Invalid method.' );
	}
}