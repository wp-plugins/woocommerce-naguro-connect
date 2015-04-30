<?php

class WC_Naguro_Cart {
	public function __construct() {
		add_filter( 'woocommerce_loop_add_to_cart_link', array($this, 'change_add_to_cart_url'), 10, 2);
		add_filter( 'woocommerce_loop_add_to_cart_link', array($this, 'change_add_to_cart_button'), 10, 2);
		add_filter( 'woocommerce_product_add_to_cart_text', array($this, 'add_to_cart_text' ), 10, 2 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array($this, 'add_to_cart_text'), 10, 2 );
		add_filter( 'template_include', array($this, 'designer_template_filter' ), 10, 1 );
		add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 10, 2 );
		add_action( 'woocommerce_simple_add_to_cart', array( $this, 'simple_add_to_cart' ) );

		add_action( 'the_content', array($this, 'output_designer' ) );

		// Meta information handlers
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session'), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta' ), 10, 2 );
	}

	public function is_purchasable( $purchasable, $product ) {
		if ( is_single() ) {
			if ( $this->is_naguro_product( $product ) ) {
				$purchasable = false;
			}
		}

		return $purchasable;
	}

	public function simple_add_to_cart() {
		global $product;

		if ( $this->is_naguro_product( $product ) ) {
			echo '<a href="'. $this->append_designer_arg_to_url( $product->get_permalink() ) .'" rel="nofollow" class="button product_type_simple">Design product</a>';
		}
	}

	public function add_order_item_meta( $item_id, $values ) {
		if ( isset( $values['naguro_session'] ) ) {
			wc_add_order_item_meta( $item_id, 'naguro_session', $values['naguro_session']['id'] );
			wc_add_order_item_meta( $item_id, 'naguro_session_object', $values['naguro_session'] );
		}
	}

	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( isset( $values['naguro_session'] ) ) {
			$cart_item['naguro_session'] = $values['naguro_session'];
		}

		return $cart_item;
	}

	public function get_item_data( $other_data, $cart_item ) {
		if ( isset( $cart_item['naguro_session'] ) ) {
			$other_data['naguro_session'] = array(
				'display' => $cart_item['naguro_session']['id'],
				'value' => $cart_item['naguro_session']['id'],
				'name' => 'Naguro session',
			);
		}

		return $other_data;
	}

	public function output_designer( $content ) {
		if ( isset( $_GET['designer'] ) ) {
			global $post;

			if ( isset($post->ID)) {
				$product = wc_get_product($post->ID);
				if ( $this->is_naguro_product($product)) {
					$designer = new WC_Naguro_Designer( $product );
					$designer->output();
				}
			}
		} else {
			return $content;
		}
	}

	/**
	 * @param $product WC_Product
	 *
	 * @return bool
	 */
	private function is_naguro_product( $product ) {
		return ( 'yes' == get_post_meta( $product->id, 'naguro_product_active', true ) );
	}

	public function change_add_to_cart_url( $button, $product ) {
		if ( $this->is_naguro_product( $product ) ) {
			preg_match( '/<a[^>]*href="([^"]*)"[^>]*>.*<\/a>/', $button, $matches );
			$button = str_replace( $matches[1], $this->append_designer_arg_to_url( $product->get_permalink() ), $button );
		}

		return $button;
	}

	private function append_designer_arg_to_url( $url ) {
		return add_query_arg( 'designer', '', $url );
	}

	/**
	 * @param $button string
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public function change_add_to_cart_button( $button, $product ) {
		if ( $this->is_naguro_product( $product ) ) {
			$button = str_replace( 'add_to_cart_button', '', $button );
		}

		return $button;
	}

	/**
	 * @param $text string
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public function add_to_cart_text( $text, $product ) {
		if ( $this->is_naguro_product( $product ) ) {
			return __( 'Design product', 'woocommerce-naguro-connect' );
		}

		return $text;
	}

	public function designer_template_filter( $template_file) {
		if ( ! isset( $_GET['designer'] ) ) {
			return $template_file;
		}

		if ( strstr( $template_file, 'single-product.php' ) ) {
			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'naguro-editor.php' ) ) {
				return trailingslashit( get_stylesheet_directory() ) . 'naguro-editor.php';
			} else {
				return apply_filters( 'naguro_editor_template_file', trailingslashit( get_stylesheet_directory() ) . 'page.php' );
			}
		}

		return $template_file;
	}
}