<?php

class Naguro_Order_Request_Factory implements Naguro_Request_Factory {
	public function get_request_by_method( $method ) {
		if ( 'preview' === $method ) {
			return new Naguro_Order_Preview_Get_Request( $_POST );
		} elseif ( 'placeorder' == $method ) {
			return new Naguro_Order_Place_Request( $_POST );
		}

		throw new Exception('Invalid method.');
	}
}