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
		$model = $_POST['model'];
		$method = $_POST['method'];

		try {
			$request = $this->get_request_by_model_method( $model, $method );
			$request->output();
		} catch ( Exception $e ) {
			return;
		}
	}

	/**
	 * @param $model
	 * @param $method
	 *
	 * @return Naguro_Request
	 * @throws Exception
	 */
	private function get_request_by_model_method( $model, $method ) {
		$available_model_factories = array( 'session', 'font', 'text', 'order', 'image' );

		if ( ! in_array( $model, $available_model_factories ) ) {
			throw new Exception('Invalid model.');
		}

		$factory = $this->get_factory_by_model( $model );

		try {
			$request = $factory->get_request_by_method( $method );
			return $request;
		} catch( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * @param string $model
	 * @return Naguro_Request_Factory
	 */
	private function get_factory_by_model( $model ) {
		$factory_class_name = 'Naguro_' . ucwords( $model ) . '_Request_Factory';
		return new $factory_class_name();
	}
}