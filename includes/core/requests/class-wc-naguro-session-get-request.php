<?php

class WC_Naguro_Session_Get_Request extends Naguro_Request {
	private function get_product() {
		return wc_get_product( $this->session->get('product_id'));
	}

	/**
	 * @param $product WC_Product
	 * @return array
	 */
	private function get_design_areas( $product ) {
		return get_post_meta($product->id, 'naguro_design_area', false);
	}

	private function get_product_data_array() {
		$product = $this->get_product();
		$design_areas = $this->get_design_areas( $product );

		$design_areas_array = array();
		$subtypes_array = array();
		$i = 1;
		foreach ( $design_areas as $design_area ) {
			$image_src = wp_get_attachment_image_src( $design_area['product_image_id'], 'full' );
			$image_src = $image_src[0];

			$design_areas_array[] = array(
				'product_design_area_id' => $i,
				'name' => $design_area['name'],
				'sort_order' => $i,
			);

			$subtypes_array[] = array(
				'product_subtype_id' => $i,
				'product_design_area_id' => $i,
				'is_circle' => false, // @todo implementation plz
				'output_width' => $design_area['output_width'],
				'output_height' => $design_area['output_height'],
				'print_width' => $design_area['print_width'],
				'print_height' => $design_area['print_height'],
				'left' => $design_area['left'],
				'top' => $design_area['top'],
				'size_description' => $design_area['size_description'],
				'image_src' => $image_src,
				'overlay_src' => '',
			);

			$i++;
		}

		return array(
			'product_id' => $product->id,
			'name' => $product->get_title(),
			'design_areas' => $design_areas_array,
			'subtypes' => $subtypes_array,
		);
	}

	public function output() {
		$object = new StdClass();
		$object->session_id = $this->session->get_id();
		$object->locale = 'nl_NL';
		$object->start_product_subtype_id = 1;
		$object->product = $this->get_product_data_array();

		echo json_encode( $object ); die();
	}
}