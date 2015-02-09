<?php

class WC_Naguro_Designer {
	/**
	 * @var WC_Product
	 */
	private $product;

	/** @var Naguro_Session_Model */
	private $session;

	/** @var array */
	private $designer_data = array();

	/** @var WC_Naguro_HTML_Get_Request */
	private $request;

	/**
	 * @param $product WC_Product
	 */
	public function __construct( $product ) {
		$this->product = $product;
		$this->prepare_request();
		$this->html = $this->get_html();

		wp_enqueue_script('naguro-designer', $this->get_js_url());
		wp_enqueue_style('naguro-designer', $this->get_css_url());
	}

	private function prepare_request() {
		$this->session = new Naguro_Session_Model();
		$this->session->set( 'product_id', $this->product->id);
		WC()->session->set('naguro_session_id', $this->session->get_id());

		$this->request = new WC_Naguro_HTML_Get_Request(array( 'session_id' => $this->session->get_id() ) );
	}

	private function get_html() {
		if ( false === ( $file = get_transient('naguro-html' ) ) ) {
			if ( empty( $this->designer_data ) ) {
				$this->designer_data = $this->request->get();
			}

			$file = $this->save_file_to_disk( 'naguro-html', 'html', $this->designer_data['html'] );
		}

		$html = file_get_contents($file['file']);
		$html = str_replace('{{endpoint}}', get_home_url() . '/wp-admin/admin-ajax.php', $html );
		$html = str_replace('{{session-id}}', $this->session->get_id(), $html );
		return $html;
	}

	private function save_file_to_disk( $handle, $type, $contents ) {
		$file = wp_upload_bits( $handle . '.' . $type, null, $contents, date( 'Y/m' ) );
		set_transient( $handle, $file, 24 * HOUR_IN_SECONDS );
		return $file;
	}

	private function get_css_url() {
		if ( false === ( $file = get_transient('naguro-stylesheet' ) ) ) {
			if ( empty( $this->designer_data ) ) {
				$this->designer_data = $this->request->get();
			}

			$file = $this->save_file_to_disk( 'naguro-stylesheet', 'css', $this->designer_data['css'] );
		}
		return $file['url'];
	}

	private function get_js_url() {
		if ( false === ( $file = get_transient('naguro-javascript' ) ) ) {
			if ( empty( $this->designer_data ) ) {
				$this->designer_data = $this->request->get();
			}

			$file = $this->save_file_to_disk( 'naguro-javascript', 'js', $this->designer_data['js'] );
		}
		return $file['url'];
	}

	public function output() {
		echo $this->html;
	}
}