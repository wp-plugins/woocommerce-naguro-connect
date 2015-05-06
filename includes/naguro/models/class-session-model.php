<?php

class Naguro_Session_Model extends Naguro_Model {
	/** @var int */
	private $id;

	/** @var bool */
	private $saved;

	/** @var array */
	private $data = array();

	public function __construct( $id = null ) {
		$this->saved = true;

		if ( null === $id ) {
			$this->id = $this->generate_session_id();
		} else {
			$this->id = $id;
			$this->data = $this->get_session();
		}

		add_action( 'shutdown', array( $this, 'save_data' ), 20 );
	}

	public function get_id() {
		return $this->id;
	}

	private function generate_session_id() {
		$max = get_option( 'naguro_max_session_id', 0 );
		$max = $max + 1;
		update_option( 'naguro_max_session_id', $max );
		return $max;
	}

	private function get_session() {
		return (array) get_option( 'naguro_session_' . $this->id, array() );
	}

	public function get( $key ) {
		if ( isset( $this->data[ $key ] ) ) {
			return $this->data[ $key ];
		}

		return null;
	}

	public function set( $key, $value ) {
		$this->data[ $key ] = $value;
		$this->saved = false;
		return true;
	}

	public function save_data() {
		if ( ! $this->saved ) {
			update_option( 'naguro_session_' . $this->id, $this->data );
			$this->saved = true;
		}
	}
}