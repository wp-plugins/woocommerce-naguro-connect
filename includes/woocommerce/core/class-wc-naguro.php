<?php

class WC_Naguro {
	/** @var WC_Naguro_Ajax */
	private $ajax_handler;

	/** @var string */
	static $prefix = "naguro_";

	/** @var WC_Naguro_Product_Meta_Box */
	private $product_data_meta_box;

	public function __construct() {
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || false == DOING_AJAX ) ) {
			$this->admin_init();
		} else {
			add_action( 'init', array( $this, 'conditional_include' ) );
			$this->frontend_init();
		}

		// Setup the Ajax dispatcher
		$this->ajax_handler = new WC_Naguro_Ajax();

		add_action( 'naguro_api_handler_class', array( $this, 'api_handler_class' ), 10, 0 );

		new WC_Naguro_Downloader();
		new Naguro_Modules_Loader();
		new Naguro_WordPress_Menu();
		new Naguro_WordPress_Settings_Init();
		new Naguro_WordPress_Action_Listener();
	}

	public function api_handler_class() {
		return 'WordPress_API_Handler';
	}

	/**
	 * Prepare the administration panel specific files and classes
	 */
	private function admin_init() {
		new WC_Naguro_Order_Admin();
		new WC_Naguro_Editor_Manager();

		$this->product_data_meta_box = new WC_Naguro_Product_Meta_Box();
		add_action( 'save_post', array( $this->product_data_meta_box, 'save' ), 10, 1 );

		// Meta boxes loaded at priority 31 so the WooCommerce "Product Data" meta box is right above it
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 31 );
	}

	public function add_meta_boxes() {
		add_meta_box( 'woocommerce-naguro-product-data', __( 'Naguro', 'woocommerce_naguro_connect' ), array( $this->product_data_meta_box, 'output' ), 'product', 'normal', 'high' );
	}

	/**
	 * Prepare frontend specific classes
	 */
	private function frontend_init() {
		new WC_Naguro_Cart();
	}

	/**
	 * Prepare the specific files and classes who will be loaded based on WooCommerce conditionals
	 */
	public function conditional_include() {
		if ( is_checkout() || is_checkout_pay_page() ) {
			new WC_Naguro_Checkout();
		}
	}
}