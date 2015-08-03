<?php

class WC_Naguro_Overlay {
	function __construct() {
		add_action("naguro_woocommerce_before_printable_area_button", array($this, "add_design_area_overlay_upload"), 10, 2);
		add_action("naguro_woocommerce_after_printable_area_image", array($this, "add_overlay_image"));

		add_filter("naguro_woocommerce_design_area_data", array($this, "handle_design_area_data"));
		add_filter("naguro_woocommerce_save_keys", array($this, "add_overlays_to_keys"));
		add_filter("naguro_woocommerce_file_keys", array($this, "add_overlay_to_keys"));
		add_filter("naguro_woocommerce_filter_save_image", array($this, "save_image"), 10, 2 );
	}

	function add_design_area_overlay_upload($rand, $design_area) {
		$name = WC_Naguro::$prefix . "designarea[overlay][" . $rand . "]";
		WC_Naguro_Product_Meta_Box::upload_field($name,
			"Overlay image",
			"Upload an image that will serve as the overlay image that will display on top of the designer",
			( isset( $design_area['product_overlay_id'] ) ? $design_area['product_overlay_id'] : "" ),
			"naguro_designarea[product_overlay_id][]");
	}

	function add_overlay_image($design_area) {
		if ( isset( $design_area['product_overlay_id'] ) ) {
			$this->add_design_area_overlay_id($design_area['product_overlay_id']);
		} else {
			$this->add_design_area_overlay_id(0);
		}

		if ( isset( $design_area['product_overlay'] ) ) {
			echo '<img class="overlay-image" src="' . $design_area['product_overlay'] . '" />';
		} else {
			echo '<img class="overlay-image" src="" />';
		}
	}

	function add_design_area_overlay_id($id) {
		$this->hidden_input(
			WC_Naguro::$prefix . "designarea[product_overlay_id][]",
			$id,
			WC_Naguro::$prefix . "product_overlay_id"
		);
	}

	function handle_design_area_data($design_area_data) {
		if ( isset( $design_area_data['product_overlay_id'] ) ) {
			$image_src = wp_get_attachment_image_src( $design_area_data['product_overlay_id'], 'full' );
			$design_area_data['product_overlay'] = $image_src[0];
		}

		return $design_area_data;
	}

	function add_overlay_to_keys($keys) {
		array_push($keys, "overlay");
		return $keys;
	}

	function add_overlays_to_keys($keys) {
		array_push($keys, "product_overlay_id");
		return $keys;
	}

	function save_image($design_area, $image_ids) {
		if ( isset( $image_ids['overlay'][ $design_area['upload_key'] ] ) ) {
			$image_id = $image_ids['overlay'][ $design_area['upload_key']];
		} elseif ( isset( $design_area['product_overlay_id'] ) ) {
			$image_id = $design_area['product_overlay_id'];
		} else {
			$image_id = 0;
		}

		if ( 0 != $image_id ) {
			$design_area['product_overlay_id'] = $image_id;
		}

		return $design_area;
	}

	public function hidden_input($name, $value, $class = "") {
		echo '<input type="hidden" name="' . $name . '" value="' . $value . '" class="' . $class . '" />';
	}
}