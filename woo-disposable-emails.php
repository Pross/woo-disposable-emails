<?php
/*
Plugin Name: Woo Disposable Emails
Description: Block disposable email addresses at signup woth WooCommerce.
Author: Simon Prosser
Author URI: https://pross.org.uk
Version: 1.2.2
Text Domain: woo-disposable-emails

*/

if ( ! class_exists( 'WC_Disposable_Emails' ) ) :
	class WC_Disposable_Emails {

		/**
		* Construct the plugin.
		*/
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		* Initialize the plugin.
		*/
		public function init() {

			// Checks if WooCommerce is installed.
			if ( class_exists( 'WC_Integration' ) ) {

				// Include our integration class.
				include_once 'includes/class-disposable-emails.php';

				// Register the integration.
				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
			}
		}
		/**
		* Add a new integration to WooCommerce.
		*/
		public function add_integration( $integrations ) {
			$integrations[] = 'WC_Integration_Disposable_Emails';
			return $integrations;
		}
		public static function path() {
			return plugin_dir_path( __FILE__ );
		}
	}

	$WC_Disposable_Emails = new WC_Disposable_Emails( __FILE__ );

endif;
