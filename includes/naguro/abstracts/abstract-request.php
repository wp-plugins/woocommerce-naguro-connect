<?php

abstract class Naguro_Request implements Naguro_Request_Interface {
	/** @var array */
	protected $params;

	/** @var Naguro_Session_Model */
	protected $session;

	/** @var Naguro_API_Handler */
	protected $handler;

	/**
	 * Fire up the required session and handler
	 * @param $params array
	 */
	public function __construct( $params ) {
		$this->params = $params;

		if ( ! isset( $this->params['session'] ) && ! isset( $this->params['session_id'] ) ) {
			$this->session = new Naguro_Session_Model();
		} else {
			// This variable needs to be available with both keys. Don't ask.
			if ( isset( $this->params['session'] ) ) {
				$this->params['session_id'] = $this->params['session'];
			}

			$this->session = new Naguro_Session_Model( $this->params['session_id'] );
		}

		$this->handler = Naguro_API_Handlers_Repository::get_handler();
	}
}