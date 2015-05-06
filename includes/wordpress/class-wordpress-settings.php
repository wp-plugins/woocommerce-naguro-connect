<?php

class Naguro_WordPress_Settings_Init {
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_settings() {
		register_setting( 'naguro', 'naguro_settings' );

		add_settings_section(
			'naguro_dimensions',
			'Dimensions',
			array( $this, 'settings_section_callback' ),
			'naguro'
		);

		add_settings_field(
			'dimension_unit',
			'Dimension unit',
			array( $this, 'dimension_unit_render' ),
			'naguro',
			'naguro_dimensions'
		);

		add_settings_field(
			'dpi',
			'DPI',
			array( $this, 'dimension_dpi_render' ),
			'naguro',
			'naguro_dimensions'
		);
	}

	public function settings_section_callback() {
		echo 'Settings related to the dimensions being used in the Naguro products.';
	}

	public function dimension_unit_render() {
		$options = get_option( 'naguro_settings' );

		$dimensions = array(
			'mm'   => 'mm',
			'cm'   => 'cm',
			'inch' => 'inch',
			'yard' => 'yard'
		);

		echo '<select name="naguro_settings[dimension_unit]">';

		foreach ( $dimensions as $key => $value ) {
			echo '<option' . selected( $key, $options['dimension_unit'], false ) . ' value="' . $key . '">' . $value . '</option>';
		}

		echo '</select>';
	}

	public function dimension_dpi_render() {
		$options = get_option( 'naguro_settings' );
		$dpi = isset( $options['dpi'] ) ? intval( $options['dpi'] ) : 300;
		echo '<input type="text" name="naguro_settings[dpi]" value="'.$dpi.'">';
	}
}