<?php

class WC_Naguro_Downloader {
	public function __construct() {
		add_action( 'init', array( $this, 'rewrite_rules' ), 1 );
		add_action( 'template_redirect', array( $this, 'listener' ) );
	}

	public function upload_mimes( $types ) {
		$types['html'] = 'text/html';
		return $types;
	}

	public function rewrite_rules() {
		add_rewrite_endpoint( 'naguro-editor', EP_ROOT );
	}

	public function listener() {
		add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );

		if ( 'download' != get_query_var( 'naguro-editor' ) ) {
			return;
		}

		include(ABSPATH . 'wp-admin/includes/file.php' );

		$data = $_POST['data'];
		$store_array = array();

		$type_arr = array( 'html' => 'url', 'css' => 'url', 'js' => 'url' );

		foreach ( $type_arr as $key => $value_to_store ) {
			$assets_api_url = apply_filters( 'wc_naguro_assets_api_endpoint_url', 'http://api.naguro.com/' );
			$file_url = $assets_api_url . $data[$key];
			$temp_file = download_url( $file_url, 600 );

			// array based on $_FILE as seen in PHP file uploads
			$file = array(
				'name' => basename($file_url),
				'type' => 'image/png',
				'tmp_name' => $temp_file,
				'error' => 0,
				'size' => filesize($temp_file),
			);

			$overrides = array(
				'test_form' => false,
				'test_size' => true,
				'test_upload' => true,
			);

			// move the temporary file into the uploads directory
			$results = wp_handle_sideload( $file, $overrides );

			if (!empty($results['error'])) {
				exit;
			} else {
				$store_array[$key] = $results[ $value_to_store ];
			}
		}

		update_option( 'naguro_editor_' . $data['hash'], $store_array );

		remove_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );

		exit;
	}
}