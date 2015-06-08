<?php

class WC_Naguro_Order_Admin {
	public function __construct() {
		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'output_line_item' ), 10, 3 );
	}

	public function output_line_item( $item_id, $item, $product ) {
		if ( isset( $item['naguro_session_object'] ) ) {
			$session_object = unserialize( $item['naguro_session_object'] );
			if ( isset( $session_object['preview'] ) && is_array( $session_object['preview'] ) ) {
				$design_areas   = get_post_meta( $product->id, 'naguro_design_area' );

				$output_array[] = '<b>Download designs:</b> ';

				foreach ( $session_object['preview'] as $key => $objects ) {
					// Check if 'full_size_src' is set, or else return just 'src' for backwards compatibility
					$download_link = ( isset( $objects['full_size_src'] ) ) ? $objects['full_size_src'] : $objects['src'];
					$output_array[] = '<a href="' . $download_link . '">' . $design_areas[ $key ]['name'] . '</a>';
				}

				echo array_shift( $output_array );
				echo implode( ', ', $output_array );
			}
		}
	}
}