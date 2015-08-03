<?php

class Naguro_Dashboard_Page extends Abstract_Naguro_WordPress_Settings_Page {
	private $errors = array();

	public function __construct() {
		$this->maybe_save_settings();
		$this->output_tabs( 'dashboard' );

		$activation_key = get_option( 'naguro_activation_key', false );

		if ( false === $activation_key ) {
			$this->display_api_settings();
		} else {
			$this->display_authenticated_message();
		}
	}

	private function display_authenticated_message() {
		echo '<h3>Congratulations, Naguro is ready to use</h3>';
		echo '<p>It\'s pretty easy to configure Naguro for your products and review ordered items.</p>';
		echo '<p>If you need any help on how to do this, please visit our <a href="https://www.naguro.com/installation">Installation page</a>.</p>';
	}

	private function maybe_save_settings() {
		if ( isset( $_POST['naguro_trial_email'] ) ) {
			$request = new Naguro_Request_Trial_Key_Request( array(
				'email' => sanitize_text_field( $_POST['naguro_trial_email'] ),
				'home_url' => get_home_url(),
			) );

			$data = $request->do_request();
			$data = json_decode( $data['body'] );
			if ( isset( $data->error ) ) {
				$this->add_error_message( $data->error->message );
			} else {
				$key = $data->key->api_key;
				$this->request_activation_key( $key );
			}
		} elseif ( isset( $_POST['naguro_api_key'] ) ) {
			$this->request_activation_key( $_POST['naguro_api_key'] );
		}
	}

	private function add_error_message($message) {
		if ( is_object( $message ) ) {
			foreach ( $message as $error ) {
				foreach ( $error as $ding ) {
					$this->errors[] = $ding;
				}
			}

			return;
		}

		$this->errors[] = $message;
	}

	private function display_api_settings() {
		if ( ! empty( $this->errors ) ) {
			echo '<div id="message" class="error">';
			echo '<p>' . implode( ', ', $this->errors ) . '</p>';
			echo '</div>';
		}

		echo '<form action="" method="POST">';
		echo '<h3>Request trial API key</h3>';
		echo '<p>You can request a 30 day trial key here, so you can try Naguro for free.</p>';
		echo '<label for="naguro_trial_email">Email address: </label>';
		echo '<input type="text" id="naguro_trial_email" name="naguro_trial_email">';
		echo '<input type="submit" class="button" value="Request">';
		echo '</form>';

		echo '<form action="" method="POST">';
		echo '<h3>Authentication</h3>';
		echo '<p>Please enter your Naguro API key in the field below to authenticate with the API and activate your purchased modules.</p>';
		echo '<label for="naguro_api_key">API key: </label>';
		echo '<input type="text" value="' . esc_attr( get_option( 'naguro_api_key' ) ) . '" id="naguro_api_key" name="naguro_api_key">';
		echo '<input type="submit" class="button" value="Save">';
		echo '</form>';
	}

	private function request_activation_key( $key ) {
		$key = sanitize_text_field( $key );

		$request = new Naguro_Activate_Key_Request( array(
			'api_key'  => $key,
			'home_url' => get_home_url(),
		) );

		$data = $request->do_request();
		$data = json_decode( $data['body'] );

		if ( isset( $data->error ) ) {
			$this->add_error_message( $data->error->message );
		} else {
			update_option( 'naguro_api_key', $key );
			$key = $data->activation->activation_key;
			update_option( 'naguro_activation_key', $key );
		}
	}
}