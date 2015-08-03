<?php

class Naguro_Overlay_Module extends Naguro_Module_Model {
	public function load() {
		new WC_Naguro_Overlay();
	}
}