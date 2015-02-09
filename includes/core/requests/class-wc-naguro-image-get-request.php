<?php

class WC_Naguro_Image_Get_Request extends Naguro_Request {
	public function output() {
		if ( isset( $this->params['image_id'] ) ) {
			$id = absint( $this->params['image_id'] );

			if ( get_post_meta( $id, '_naguro_image_session_id', true ) == $this->params['session'] ) {
				$param_width = absint( $this->params['width'] );
				$image_src = wp_get_attachment_image_src( $id, 'full' );

				$src = $image_src[0];
				$width = $image_src[1];
				$height = $image_src[2];

				$this->params['src'] = base64_encode(file_get_contents($src));
				$this->handler->handle_request('resize-image', $this->params, 'post' );
				$data = $this->handler->get_data();
				$body = json_decode( $data['body'] );
				$src = $body->filename;

				$tmp = download_url( $src );
				$file_array = array();

				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					@unlink($file_array['tmp_name']);
					$file_array['tmp_name'] = '';
				}

				// Set variables for storage
				// fix file filename for query strings
				preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $src, $matches);
				$file_array['name'] = basename($matches[0]);
				$file_array['tmp_name'] = $tmp;

				$new_id = media_handle_sideload( $file_array, 0 );
				update_post_meta( $new_id, '_naguro_image_session_id', $this->params['session']);
				$image_src = wp_get_attachment_image_src( $new_id, 'full' );
				$src = $image_src[0];

				echo json_encode( array(
					'id' => $id,
					'src' => $src,
					'width' => $width,
					'height' => $height,
				) );
			}
		}
		die();
	}
}