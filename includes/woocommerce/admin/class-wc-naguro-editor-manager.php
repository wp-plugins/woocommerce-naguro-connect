<?php

class WC_Naguro_Editor_Manager {
	public function __construct() {
		add_action( 'save_post', array( $this, 'save_post' ), 11, 1 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'product_data_visibility' ) );
	}

	public function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'product' != get_post_type($post_id ) ) {
			return;
		}

		$design_areas = get_post_meta( $post_id, 'naguro_design_area' );

		$use_overlay_module = false;

		foreach ( $design_areas as $design_area ) {
			if ( ! empty( $design_area['product_overlay_id'] ) && 0 != $design_area['product_overlay_id'] ) {
				$use_overlay_module = true;
				break;
			}
		}

		if ( $use_overlay_module ) {
			$params['modules'] = 'main,overlay';
		} else {
			$params['modules'] = 'main';
		}

		$params['version'] = '1.1';
		$params['theme'] = 'standard';
		$params['language'] = 'en_GB';
		$params['callback'] = home_url('naguro-editor/download');
		$hash = $this->generate_editor_hash($params);

		if ( ! $this->does_editor_exist($hash)) {
			$request = new Naguro_Editor_Request($params);
			$request->get('editor/request');
		}

		update_post_meta( $post_id, 'naguro_editor_hash', $hash );
	}

	private function generate_editor_hash( $params ) {
		return md5(implode(',', $params));
	}

	private function does_editor_exist( $hash ) {
		$editor_option = get_option( 'naguro_editor_' . $hash, false );
		return ( false === $editor_option ) ? false : true;
	}

	public function product_data_visibility() {
		global $post;

		if ( 'product' != $post->post_type ) {
			return;
		}

		$editor_hash = get_post_meta( $post->ID, 'naguro_editor_hash', true);

		if ( false == $editor_hash || empty( $editor_hash ) ) {
			return;
		}

		echo '<div class="misc-pub-section" id="naguro-editor-availability">';

		if ( $this->does_editor_exist( $editor_hash ) ) {
			echo '<p>Naguro editor is available</p>';
		} else {
			echo '<p>Naguro editor is not available yet</p>';
		}

		echo '</div>';
	}
}