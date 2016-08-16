<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Disposable Email Class
 *
 * @package  WC_Integration_Disposable_Emails
 * @category Integration
 * @author   Simon Prosser
 */

if ( ! class_exists( 'WC_Integration_Disposable_Emails' ) ) :

class WC_Integration_Disposable_Emails extends WC_Integration {

 /**
  * Init and hook in the integration.
  */
 public function __construct() {
   global $woocommerce;

   $this->id                 = 'integration-disposable-emails';
   $this->method_title       = __( 'Disposable Emails', 'integration-disposable-emails' );
   $this->method_description = __( 'Filter out unwanted users of disposable emails.', 'integration-disposable-emails' );

   // Load the settings.
   $this->init_form_fields();

   // Actions.
   add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );

  // add_filter( 'is_email', array( $this, 'is_email' ) );

   add_filter( 'registration_errors', array( $this, 'registration_errors' ), 10, 3 );
   add_filter( 'woocommerce_registration_errors', array( $this, 'registration_errors' ), 10, 3 );

 }

 function registration_errors( $errors, $sanitized_user_login, $user_email ) {

   global $woocommerce;

   if( empty( $errors->errors ) && 'yes' === $this->get_option( 'enabled' ) ) {
     if( $this->is_temp_mail( $user_email ) ) {
       $logger = new WC_Logger();
       $errors->add( 'invalid_email', $this->get_option( 'error' ) );
       $logger->add( $this->id, sprintf( '%s : Email Blocked.', $user_email ) );
     }
   }
   return $errors;
 }

 function is_temp_mail($mail) {

    $mail_domains_ko = file( WC_Disposable_Emails::path() . 'vendor/pross/disposable-email-domains/disposable_email_blacklist.conf', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    //Need to ensure the mail contains an @ to avoid undefined offset
    return in_array(explode('@', $mail)[1], $mail_domains_ko);
 }

 /**
  * Initialize integration settings form fields.
  */
 public function init_form_fields() {
   $this->form_fields = array(
     'enabled' => array(
       'title'             => __( 'Enable Disposable Email Filter', 'integration-disposable-emails' ),
       'type'              => 'checkbox',
       'description'       => __( 'All signups will be checked against the email list', 'integration-disposable-emails' ),
       'desc_tip'          => true,
       'default'           => 'no'
     ),
     'error' => array(
       'title'             => __( 'Error Message', 'integration-disposable-emails' ),
       'type'              => 'text',
       'default'           => __( "<strong>Sorry no disposable emails allowed.</strong>", "integration-disposable-emails" )
     )
   );
 }

}

endif;
