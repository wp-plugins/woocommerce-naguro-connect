<?php

class Naguro_Text_Image_Get_Request extends Naguro_Request {
	public function output() {
		if ( strstr( $this->params['colour'], '#' ) ) {
			$this->params['colour'] = str_replace('#', '', $this->params['colour']);
		}

		$this->handler->handle_request('text-image', $this->params, 'post' );
		$data = $this->handler->get_data();
		$output_data = json_decode( $data['body'] );

		$output_array = array(
			'src' => $output_data->image,
			'width' => $output_data->width,
			'height' => $output_data->height,
		);

		echo json_encode($output_array); die();
	}
}