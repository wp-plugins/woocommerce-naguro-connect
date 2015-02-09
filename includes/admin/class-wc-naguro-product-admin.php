<?php

/**
 * Class WC_Naguro_Product_Admin
 * Responsible for settings related to products in the administration panel
 */
class WC_Naguro_Product_Admin {
	/** @var \WC_Naguro_Settings_Panel */
	private $product_panel;

	public function __construct() {
		add_action( 'woocommerce_process_product_meta', array( $this, 'save' ), 10, 2 );
		$this->product_panel = new WC_Naguro_Settings_Panel();
	}

	public function save( $post_id, $post ) {
		$is_naguro = ( isset( $_POST['_naguro'] ) && 'on' == $_POST['_naguro'] ) ? 'yes' : 'no';

		update_post_meta( $post_id, '_naguro', $is_naguro );
	}
}