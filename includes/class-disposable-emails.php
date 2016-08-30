<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Disposable Email Class
 *
 * @package	WC_Integration_Disposable_Emails
 * @category Integration
 * @author	 Simon Prosser
 */

if ( ! class_exists( 'WC_Integration_Disposable_Emails' ) ) :

class WC_Integration_Disposable_Emails extends WC_Integration {
	/**
	* Init and hook in the integration.
	*/
	public function __construct() {
		$this->id								 = 'woo-disposable-emails';
		$this->method_title			 = __( 'Disposable Emails', 'woo-disposable-emails' );
		$this->method_description = __( 'Filter out unwanted users of disposable emails.', 'woo-disposable-emails' );

		// Load the settings.
		$this->init_form_fields();

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .	$this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'registration_errors', array( $this, 'registration_errors' ), 10, 3 );
		add_filter( 'woocommerce_registration_errors', array( $this, 'registration_errors' ), 10, 3 );
	}

	function registration_errors( $errors, $sanitized_user_login, $user_email ) {

		if( empty( $errors->errors ) && 'yes' === $this->get_option( 'enabled' ) ) {
			if( $this->is_temp_mail( $user_email ) ) {
				$logger = new WC_Logger();
				$errors->add( 'invalid_email', $this->get_option( 'error' ) );
				$logger->add( $this->id, sprintf( 'Registration Blocked: User %s | Email: %s.', $sanitized_user_login, $user_email ) );
			}
		}
		return $errors;
	}
	function is_temp_mail($mail) {

		// get the domain from the email
		$domain = explode('@', $mail)[1];

		$whitelist = array_map('trim', explode( ',', $this->get_option( 'whitelist' ) ) );

		// domain is in user whitelist, return false.
		if( in_array( $domain, $whitelist, true ) ) {
			return false;
		}

		$mail_domains = file( WC_Disposable_Emails::path() . 'vendor/pross/disposable-email-domains/disposable_email_blacklist.conf', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		// add user blacklist to main blocklist.
		$blacklist = array_map('trim', explode( ',', $this->get_option( 'blacklist' ) ) );

		// if domain is in the user blacklist OR the main blocklist then return true.
		if( in_array( $domain, array_merge( $mail_domains, $blacklist ), true ) ) {
			return true;
		}

		return false;
	}

	/**
	* Initialize integration settings form fields.
	*/
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable Disposable Email Filter', 'woo-disposable-emails' ),
				'type' => 'checkbox',
				'description' => __( 'All signups will be checked against the email list', 'woo-disposable-emails' ),
				'default' => 'no'
			),
			'error' => array(
			 'title' => __( 'Error Message', 'woo-disposable-emails' ),
			 'type' => 'text',
			 'default' => __( "<strong>Sorry no disposable emails allowed.</strong>", "woo-disposable-emails" ),
			 'description' => __( 'Error to display on the signup page when user is blocked.', 'woo-disposable-emails' )
			),
			'whitelist' => array(
			 'title' => __( 'Domain Whitelist', 'woo-disposable-emails' ),
			 'type' => 'textarea',
			 'default' => '',
			 'description' => __( 'Comma separated list of email domains to NEVER block.', 'woo-disposable-emails' )
			),
			'blacklist' => array(
			 'title' => __( 'User List', 'woo-disposable-emails' ),
			 'type' => 'textarea',
			 'default' => '',
			 'description' => __( 'Comma separated list of email domains to add to main block list.', 'woo-disposable-emails' )
			),
		);
	}

}

endif;
