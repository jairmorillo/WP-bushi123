<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://cylwebservices.com
 * @since      1.0.0
 *
 * @package    Wp_Crawller_Plugin
 * @subpackage Wp_Crawller_Plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Crawller_Plugin
 * @subpackage Wp_Crawller_Plugin/includes
 * @author     Jair Morillo <jairantoniom@gmail.com>
 */
class Wp_Crawller_Plugin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-crawller-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
