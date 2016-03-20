<?php

class AffiliateWP_Allow_Own_Referrals_Base {

	/**
	 * The context for referrals. This refers to the integration that is being used.
	 *
	 * @access  public
	 * @since   1.1
	 */
	public $context;

	/**
	 * The ID of the referring affiliate
	 *
	 * @access  public
	 * @since   1.1
	 */
	public $affiliate_id;
	
	public function __construct() {
	
		$this->affiliate_id = affiliate_wp()->tracking->get_affiliate_id();
		$this->init();
	}

	/**
	 * Gets things started
	 *
	 * @access  public
	 * @since   1.1
	 * @return  void
	 */
	public function init() {
		
	}

	/**
	 * Determine if the passed email belongs to the affiliate
	 *
	 * @since 1.1
	 */
	public function is_affiliate_email( $email, $affiliate_id = 0 ) {
		$is_affiliate_email = false;
		// Allow an affiliate ID to be passed in
		$affiliate_id = isset( $affiliate_id ) ? $affiliate_id : $this->affiliate_id;
		// Get affiliate emails
		$user_email  = affwp_get_affiliate_email( $affiliate_id );
		$payment_email = affwp_get_affiliate_payment_email( $affiliate_id );
		// True if the email is valid and matches affiliate user email or payment email, otherwise false
		$is_affiliate_email = ( is_email( $email ) && ( $user_email === $email || $payment_email === $email ) );
		return (bool) apply_filters( 'affwp_aor_is_customer_email_affiliate_email', $is_affiliate_email, $email, $affiliate_id );
	}

	/**
	 * Check for logged-in affiliate during purchase
	 *
	 * @since 1.1
	 */
	public function is_affiliate_purchase( $customer_email = '', $affiliate_id = 0 ) {
		
		if ( !empty( $customer_email ) ) {
				
			if ( empty( $affiliate_id ) ) {
			
				// Get current logged-in affiliate
				$affiliate_id = affwp_get_affiliate_id();
				
			}
			
			if ( 'active' != affwp_get_affiliate_status( $affiliate_id ) ) {
				return false;	
			}
		
			if ( $this->is_affiliate_email( $customer_email, $affiliate_id ) ) {
			
				return true;

			} else {
				return false;
			}
		}

	}	
	
}