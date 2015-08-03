<?php

class WC_Naguro_Designer {
	/**
	 * @var WC_Product
	 */
	private $product;

	/** @var Naguro_Session_Model */
	private $session;

	/** @var string */
	private $hash;

	/**
	 * @param $product WC_Product
	 */
	public function __construct( $product ) {
		$this->product = $product;
		if ( $this->prepare_request() ) {
			$this->html = $this->get_html();
			wp_enqueue_script('naguro-designer', $this->get_js_url());
			wp_enqueue_style('naguro-designer', $this->get_css_url());
		} else {
			$this->html = 'The editor is not loaded yet.';
		}
	}

	/**
	 * @return bool
	 */
	private function prepare_request() {
		$this->session = new Naguro_Session_Model();
		$this->session->set( 'product_id', $this->product->id);
		WC()->session->set('naguro_session_id', $this->session->get_id());
		$this->session->save_data();

		$this->hash = $this->product_hash( $this->product );

		return ( $this->does_editor_exist($this->hash ) );
	}

	private function does_editor_exist( $hash ) {
		$editor_option = get_option( 'naguro_editor_' . $hash, false );
		return ( false === $editor_option ) ? false : true;
	}

	private function product_hash( $product ) {
		$design_areas = get_post_meta( $product->id, 'naguro_design_area' );

		$use_overlay_module = false;

		foreach ( $design_areas as $design_area ) {
			if ( ! empty( $design_area['product_overlay_id'] ) && 0 != $design_area['product_overlay_id'] ) {
				$use_overlay_module = true;
				break;
			}
		}

		if ( $use_overlay_module ) {
			$params['modules'] = 'main,overlay';
		} else {
			$params['modules'] = 'main';
		}

		$params['version'] = '1.1';
		$params['theme'] = 'standard';
		$params['language'] = 'en_GB';
		$params['callback'] = home_url('naguro-editor/download');

		return md5( implode( ',', $params ) );
	}

	private function get_html() {
		$editor_option = get_option( 'naguro_editor_' . $this->hash );

		$html = file_get_contents($editor_option['html']);
		$html = str_replace('{{endpoint}}', get_home_url() . '/wp-admin/admin-ajax.php', $html );
		$html = str_replace('{{session-id}}', $this->session->get_id(), $html );
		return $html;
	}

	private function get_css_url() {
		$editor_option = get_option( 'naguro_editor_' . $this->hash );
		return $editor_option['css'];
	}

	private function get_js_url() {
		$editor_option = get_option( 'naguro_editor_' . $this->hash );
		return $editor_option['js'];
	}

	public function output() {
		echo $this->html;
	}
}