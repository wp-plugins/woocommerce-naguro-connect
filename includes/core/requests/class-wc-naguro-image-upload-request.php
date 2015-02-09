<?php

class WC_Naguro_Image_Upload_Request extends Naguro_Request {
	public function output() {
		foreach ( $_FILES as $key => $file ) {
			if ( ! empty( $file['name'] ) && 4 != $file['error'] ) {
				$id = media_handle_upload( $key, 0 );
				update_post_meta($id, '_naguro_image_session_id', absint($_POST['session']));

				$image_src = wp_get_attachment_image_src( $id, 'full' );

				echo json_encode( array(
					'id' => $id,
					'width' => $image_src[1],
					'height' => $image_src[2],
				) );
			}
		}
		die();
	}
}