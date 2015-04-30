<?php

class Naguro_Session_Get_Request extends Naguro_Request {
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

			$overlay_src = wp_get_attachment_image_src( $design_area['product_overlay_id'], 'full' );
			$overlay_src = $overlay_src[0];

			$design_areas_array[] = array(
				'product_design_area_id' => $i,
				'name' => $design_area['name'],
				'sort_order' => $i,
			);

			$settings = get_option( 'naguro_settings' );
			$dimension = $settings['dimension_unit'];
			$dpi = isset( $settings['dpi'] ) ? intval( $settings['dpi'] ) : 300;

			$calulator = new WC_Size_Calculator( $dpi );

			switch( $dimension ) {
				case 'yard':
					$src_width = $calulator->yard_to_px($design_area['output_width']);
					$src_height = $calulator->yard_to_px($design_area['output_height']);
					break;
				case 'cm':
					$src_width = $calulator->cm_to_px($design_area['output_width']);
					$src_height = $calulator->cm_to_px($design_area['output_height']);
					break;
				case 'inch':
					$src_width = $calulator->cm_to_px($design_area['output_width']);
					$src_height = $calulator->cm_to_px($design_area['output_height']);
					break;
				default: // mm
					$src_width = $calulator->mm_to_px($design_area['output_width']);
					$src_height = $calulator->mm_to_px($design_area['output_height']);
					break;
			}

			$subtypes_array[] = array(
				'product_subtype_id' => $i,
				'product_design_area_id' => $i,
				'is_circle' => false, // @todo implementation plz
				'output_width' => $design_area['output_width'],
				'output_height' => $design_area['output_height'],
				'print_width' => $design_area['print_width'],
				'print_height' => $design_area['print_height'],
				'src_width' => $src_width,
				'src_height' => $src_height,
				'left' => $design_area['left'],
				'top' => $design_area['top'],
				'size_description' => $design_area['size_description'] . ' ('.$src_width.' x '. $src_height . ' px)',
				'image_src' => $image_src,
				'overlay_src' => $overlay_src,
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