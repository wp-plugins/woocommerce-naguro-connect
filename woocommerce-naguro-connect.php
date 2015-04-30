<?php

/*
 * Plugin Name: WooCommerce Naguro Connect
 * Description: Connects your WooCommerce powered store to Naguro.
 * Version: 1.1.0
 * Author: Radish Concepts
 * Author URI: http://radishconcepts.com
 */

define( 'NAGURO_PLUGIN_PATH', trailingslashit( dirname( __FILE__ ) ) );
define( 'NAGURO_PLUGIN_URL', plugins_url( "/", __FILE__ ));

add_action('plugins_loaded', 'wc_naguro_connect_init', 10);

function wc_naguro_connect_init() {
	// Load PHP 5.2 compatible autoloader if required
	if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		include('vendor/autoload.php');
	} else {
		include('vendor/autoload_52.php');
	}

	// Setup the core class instance
	global $wc_naguro_connect;
	$wc_naguro_connect = new WC_Naguro();
}
