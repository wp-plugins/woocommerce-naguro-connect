<?php

class WC_Size_Calculator {
	private $dpi;
	private $cm_to_in_conversion = 0.393700787; // 1cm = 0.393700787 inches
	private $yard_to_in_conversion = 36; // 1 yard = 36 inches

	public function __construct( $dpi = 300 ) {
		$this->dpi = intval( $dpi );
	}

	public function cm_to_px( $cm ) {
		$inch = $this->cm_to_in( $cm );
		return round( $inch * $this->dpi );
	}

	public function cm_to_in( $cm ) {
		return $cm * $this->cm_to_in_conversion;
	}

	public function in_to_px( $inch ) {
		return $inch * $this->dpi;
	}

	public function mm_to_cm( $cm ) {
		return $cm / 10;
	}

	public function mm_to_px( $mm ) {
		return $this->cm_to_px( $this->mm_to_cm( $mm ) );
	}

	public function yard_to_in( $yard ) {
		return $yard * $this->yard_to_in_conversion;
	}

	public function yard_to_px( $yard ) {
		return $this->yard_to_in( $yard ) * $this->dpi;
	}
}