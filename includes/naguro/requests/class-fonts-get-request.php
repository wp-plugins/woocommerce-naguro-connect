<?php

class Naguro_Fonts_Get_Request extends Naguro_Request {
	public function output() {
		$this->handler->handle_request('fonts', $this->params, 'get' );
		$data = $this->handler->get_data();
		$fonts = json_decode( $data['body'] );

		$fonts_array = array();

		foreach ( $fonts->data as $font ) {
			array_push( $fonts_array, array(
				'id'     => $font->id,
				'name'   => $font->name,
				'bold'   => $font->bold,
				'italic' => $font->italic,
				'src' => $font->src,
			) );
		}

		echo json_encode($fonts_array); die();
	}
}