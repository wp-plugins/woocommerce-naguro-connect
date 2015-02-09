<?php

class WC_Naguro_Settings_Panel {
	private $units = "mm";

	public function __construct() {
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tabs' ), 10, 1 );

		add_action( 'woocommerce_process_product_meta', array( $this, 'save_panel_settings' ), 10, 1 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panels' ), 10, 0 );

		add_action( 'post_edit_form_tag' , array( $this, 'post_edit_form_tag' ) );

		add_action( 'admin_enqueue_scripts' , array( $this, 'add_assets' ) );

		$options = get_option('naguro_settings');
		$this->units = ( $options['dimension_unit'] ? $options['dimension_unit'] : get_option('woocommerce_dimension_unit', $this->units) );
	}

	public function add_assets() {
		wp_enqueue_script("wc-naguro", NAGURO_PLUGIN_URL . "assets/js/wc-naguro.js", array("jquery"));
		wp_enqueue_style("wc-naguro", NAGURO_PLUGIN_URL . "assets/css/wc-naguro.css");

		wp_enqueue_script("imgareaselect", NAGURO_PLUGIN_URL . "assets/imgareaselect/jquery.imgareaselect.min.js", array("jquery"));
		wp_enqueue_style("imgareaselect", NAGURO_PLUGIN_URL . "assets/imgareaselect/imgareaselect-default.css");

		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
	}

	public function post_edit_form_tag() {
		echo ' enctype="multipart/form-data"';
	}

	public function product_data_tabs( $tabs ) {
		$tabs['naguro'] = array(
			'label'  => __( 'Naguro', 'woocommerce-naguro-connect' ),
			'target' => 'woocommerce_naguro_settings',
			'class'  => array( 'show_if_naguro' ),
		);

		return $tabs;
	}

	public function save_panel_settings( $post_id ) {
		// START checkbox save
		if ( isset( $_POST[ WC_Naguro::$prefix . "exists" ] ) && 'yes' == $_POST[ WC_Naguro::$prefix . "exists" ] ) {
			$checkbox_value = 'yes';
		} else {
			$checkbox_value = 'no';
		}

		update_post_meta( $post_id, 'naguro_product_active', $checkbox_value );
		// END checkbox save

		// START file upload handler
		$stack = $_FILES['naguro_designarea'];
		$files = array();

		$keys = array( 'name', 'type', 'tmp_name', 'error', 'size' );
		// Loop through the posted keys and collect them per design area
		foreach ( $keys as $key ) {
			foreach( $stack[ $key ]['image'] as $item_key => $item ) {
				$files[ $item_key ][ $key ] = $item;
			}
		}

		foreach ( $files as $key => $file ) {
			if ( 0 == $file['size'] && 4 == $file['error'] ) {
				unset( $files[ $key ] );
			}
		}

		$i = 0;
		$image_ids = array();
		foreach ( $files as $key => $file ) {
			if ( empty( $file['name'] ) && 4 == $file['error'] ) {
				$image_ids[$key] = 0;
			} else {
				$_FILES[ 'naguro_designarea_' . $i ] = $file;
				$image_ids[$key] = media_handle_upload( 'naguro_designarea_' . $i, $post_id );
			}
			$i++;
		}
		// END file upload handler

		$stack = $_POST['naguro_designarea'];
		$design_areas = array();

		$keys = array(
			'name',
			'output_width',
			'output_height',
			'print_width',
			'print_height',
			'left',
			'top',
			'product_image_id',
			'upload_key'
		);

		// Loop through the posted keys and collect them per design area
		foreach ( $keys as $key ) {
			if ( isset( $stack[ $key ] ) ) {
				foreach ( $stack[ $key ] as $item_key => $item ) {
					$design_areas[ $item_key ][ $key ] = $item;
				}
			}
		}

		// Remove the first item off the array, as that's the empty ghost
		array_shift( $design_areas );

		$this->remove_old_meta_fields($post_id);

		$options = get_option('naguro_settings');
		$unit = $options['dimension_unit'];

		// Save each design area as separate post meta objects
		foreach ( $design_areas as $design_area ) {
			if ( isset( $image_ids[ $design_area['upload_key'] ] ) ) {
				$image_id = $image_ids[ $design_area['upload_key']];
			} elseif ( isset( $design_area['product_image_id'] ) ) {
				$image_id = $design_area['product_image_id'];
			} else {
				$image_id = 0;
			}

			$design_area['size_description'] = $design_area['output_width'] . $unit . ' x ' . $design_area['output_height'] . $unit;

			if ( 0 != $image_id ) {
				$design_area['product_image_id'] = $image_id;
			}
			add_post_meta( $post_id, 'naguro_design_area', $design_area, false );
		}
	}

	private function remove_old_meta_fields( $post_id ) {
		delete_post_meta($post_id, 'naguro_design_area');
	}

	public function product_data_panels() {
		echo '<div id="woocommerce_naguro_settings" class="panel woocommerce_options_panel show_if_naguro">';

		echo '<div class="options_group">';
			$this->add_enable_checkbox();
		echo '</div>';

		echo '<div class="options_group">';
			$this->add_design_areas();
		echo '</div>';

		echo '</div>';
	}

	public function add_enable_checkbox() {
		global $post;
		$name = WC_Naguro::$prefix . "exists";

		woocommerce_wp_checkbox(array(
			"id"            => $name,
			"label"         => "Naguro product?",
			"name"          => $name,
			"value"         => ( 'yes' == get_post_meta( $post->ID, 'naguro_product_active', true ) ? 'yes' : 'no' ),
			"description"   => "Enable the customer to configure this product with the Naguro designer"
		));
	}

	private function get_design_areas() {
		global $post;
		$design_areas = get_post_meta($post->ID, 'naguro_design_area', false);

		foreach ( $design_areas as $key => $design_area ) {
			if ( isset( $design_area['product_image_id'] ) ) {
				$image_src = wp_get_attachment_image_src( $design_area['product_image_id'], 'full' );
				$design_areas[ $key ]['product_image'] = $image_src[0];
			}
		}

		return $design_areas;
	}

	public function add_design_areas() {
		$design_areas = $this->get_design_areas();

		echo "<div class='wc-metaboxes naguro-design-areas'>";

		if (0 === sizeof( $design_areas )) {
			echo "<p>No design areas found for this product.</p>";
		}

		echo '<section class="naguro-design-areas-container-ghost">';
		$this->add_design_area(array());
		echo '</section>';

		echo '<button type="button" class="button button-primary" id="naguro-add-new-design-area">Add new design area</button>';

		echo '<section class="naguro-design-areas-container">';

		foreach ( $design_areas as $design_area ) {
			$this->add_design_area($design_area);
		}

		echo '</section>';

		echo "</div>";
	}

	public function add_design_area($design_area = array()) {
		echo '<article class="naguro-design-area">';

		$this->add_remove_button();

		$this->add_design_area_name($design_area);
		$this->add_design_area_output_width($design_area);
		$this->add_design_area_output_height($design_area);
		$this->add_design_area_background($design_area);

		echo '</article>';
	}

	public function add_remove_button() {
		echo '<button type="button" class="remove_row button">Remove</button>';
	}

	public function add_design_area_name($design_area = array()) {
		$name = WC_Naguro::$prefix . "designarea[name][]";

		woocommerce_wp_text_input(array(
			"id"            => $name,
			"label"         => "Name",
			"placeholder"   => "Name of the design area",
			"name"          => $name,
			"class"         => "",
			"value"         => (isset($design_area["name"]) ? $design_area["name"] : "" )
		));
	}

	public function add_design_area_output_width($design_area = array()) {
		$name = WC_Naguro::$prefix . "designarea[output_width][]";

		woocommerce_wp_text_input(array(
			"id"            => $name,
			"label"         => "Print width",
			"placeholder"   => "Width of the printable area",
			"description"   => "Width of the printable area in " . $this->units . " without the unit (eg '25')",
			"name"          => $name,
			"class"         => "naguro-float-val",
			"value"         => (isset($design_area["output_width"]) ? $design_area["output_width"] : "10" )
		));
	}

	public function add_design_area_output_height($design_area = array()) {
		$name = WC_Naguro::$prefix . "designarea[output_height][]";

		woocommerce_wp_text_input(array(
			"id"            => $name,
			"label"         => "Print height",
			"placeholder"   => "Height of the printable area",
			"description"   => "Height of the printable area in " . $this->units . " without the unit (eg '12.5')",
			"name"          => $name,
			"class"         => "naguro-float-val",
			"value"         => (isset($design_area["output_height"]) ? $design_area["output_height"] : "10" )
		));
	}

	public function add_design_area_background($design_area = array()) {
		$rand = rand(10000, 99999);
		$this->add_design_area_upload_key($rand);

		$this->add_design_area_background_upload($rand);

		echo "<p class='form-field'><a class='button naguro-define-image-area' data-id='" . $rand . "'>Edit printable area</a></p>";

		$this->add_design_area_printable_area($design_area, $rand);
	}

	public function add_design_area_background_upload($rand) {
		$name = WC_Naguro::$prefix . "designarea[image][" . $rand . "]";

		woocommerce_wp_text_input(array(
			"id"            => $name,
			"label"         => "Design area image",
			"description"   => "Upload an image that will serve as the image that will be designed on",
			"name"          => $name,
			"value"         => "",
			"class"         => "",
			"type"          => "file"
		));
	}

	public function add_design_area_upload_key($rand) {
		$this->hidden_input(
			WC_Naguro::$prefix . "designarea[upload_key][]",
			$rand,
			WC_Naguro::$prefix . "designarea_upload_key"
		);
	}

	public function add_design_area_printable_area($design_area = array(), $rand) {
		echo '<div class="naguro-printable-product" id="' . $rand . '">';

		$this->add_design_area_print_width($design_area);
		$this->add_design_area_print_height($design_area);
		$this->add_design_area_left($design_area);
		$this->add_design_area_top($design_area);

		if ( isset( $design_area['product_image_id'] ) ) {
			$this->add_design_area_image_id($design_area['product_image_id']);
		} else {
			$this->add_design_area_image_id(0);
		}

		if ( isset( $design_area['product_image'] ) ) {
			echo '<img src="' . $design_area['product_image'] . '" />';
		} else {
			echo '<img src="" />';
		}

		echo '<a href="#" class="button naguro-printable-area-save-button">OK</a>';

		echo '</div>';
	}

	public function add_design_area_print_width($design_area = array()) {
		$this->hidden_input(
			WC_Naguro::$prefix . "designarea[print_width][]",
			(isset($design_area["print_width"]) ? $design_area["print_width"] : "0" ),
			WC_Naguro::$prefix . "designarea_print_width"
		);
	}

	public function add_design_area_print_height($design_area = array()) {
		$this->hidden_input(
			WC_Naguro::$prefix . "designarea[print_height][]",
			(isset($design_area["print_height"]) ? $design_area["print_height"] : "0" ),
			WC_Naguro::$prefix . "designarea_print_height"
		);
	}

	public function add_design_area_left($design_area = array()) {
		$this->hidden_input(
			WC_Naguro::$prefix . "designarea[left][]",
			(isset($design_area["left"]) ? $design_area["left"] : "0" ),
			WC_Naguro::$prefix . "designarea_left"
		);
	}

	public function add_design_area_top($design_area = array()) {
		$this->hidden_input(
			WC_Naguro::$prefix . "designarea[top][]",
			(isset($design_area["top"]) ? $design_area["top"] : "0" ),
			WC_Naguro::$prefix . "designarea_top"
		);
	}

	public function add_design_area_image_id($design_area_product_image_id) {
		$this->hidden_input(
			WC_Naguro::$prefix . "designarea[product_image_id][]",
			$design_area_product_image_id,
			WC_Naguro::$prefix . "product_image_id"
		);
	}

	public function hidden_input($name, $value, $class = "") {
		echo '<input type="hidden" name="' . $name . '" value="' . $value . '" class="' . $class . '" />';
	}
}