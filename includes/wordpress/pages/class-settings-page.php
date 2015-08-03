<?php

class Naguro_Settings_Page extends Abstract_Naguro_WordPress_Settings_Page {
	public function __construct() {
		$this->output_tabs( 'settings' );
		$this->output_settings();
	}

	private function output_settings() {
		echo '<form action="options.php" method="post">';
		settings_fields( 'naguro' );
		do_settings_sections( 'naguro' );
		submit_button();
		echo '</form>';
	}
}