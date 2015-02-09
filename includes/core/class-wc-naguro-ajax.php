<?php

class WC_Naguro_Ajax {
	public function __construct() {
		// Add the dispatch action for both logged in and not logged in visitors
		add_action( 'wp_ajax_naguro', array( $this, 'dispatch' ) );
		add_action( 'wp_ajax_nopriv_naguro', array( $this, 'dispatch' ) );
	}

	/**
	 * Fires up the correct request handler based on the posted model and method
	 */
	public function dispatch() {
		$session = absint( $_POST['session'] );
		$model = $_POST['model'];
		$method = $_POST['method'];

		if ( 'session' === $model && 'get' === $method ) {
			$request = new WC_Naguro_Session_Get_Request( array('session_id' => $session ) );
			$request->output();
		} elseif ( 'font' === $model && 'getavailablefonts' === $method ) {
			$request = new WC_Naguro_Fonts_Get_Request( array('session_id' => $session ) );
			$request->output();
		} elseif ( 'text' === $model && 'getimage' === $method ) {
			$request = new WC_Naguro_Text_Image_Get_Request( $_POST );
			$request->output();
		} elseif( 'order' === $model && 'preview' === $method ) {
			$request = new WC_Naguro_Order_Preview_Get_Request( $_POST );
			$request->output();
		} elseif( 'order' === $model && 'placeorder' === $method ) {
			$request = new WC_Naguro_Order_Place_Request( $_POST );
			$request->output();
		} elseif( 'image' === $model && 'upload' === $method ) {
			$request = new WC_Naguro_Image_Upload_Request( $_POST );
			$request->output();
		} elseif( 'image' === $model && 'getsrc' === $method ) {
			$request = new WC_Naguro_Image_Get_Request( $_POST );
			$request->output();
		}
	}
}