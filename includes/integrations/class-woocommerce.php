<?php

class AffiliateWP_Allow_Own_Referrals_WooCommerce extends AffiliateWP_Allow_Own_Referrals_Base {

	/**
	 * The order object
	 *
	 * @access  public
	 * @since   1.1
	*/
	public $order;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'woocommerce';

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_ref_for_affiliate_purchase' ), 9, 1 );
		
	}

	/**
	 * Set the referral for the current affiliate making the purchase
	 *
	 * @since 1.1
	 */

	public function set_ref_for_affiliate_purchase( $order_id = 0 ) {

		// Make sure affiliate auto detection is enabled
		if ( ! affiliate_wp()->settings->get( 'allow_own_referrals_auto_detect' ) ) {
			return;
		}	

		// Start checking the order
		$this->order     = apply_filters( 'affwp_get_woocommerce_order', new WC_Order( $order_id ) );
		$order_email     = $this->order->billing_email;
		
		// Get current logged-in affiliate
		$affiliate_id = affwp_get_affiliate_id();
		
		if ( $this->is_affiliate_purchase( $order_email, $affiliate_id ) ) {
		
			add_filter( 'affwp_was_referred', '__return_true' );
		
			// Set the referral cookie
			affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );
			
			// Set the current affiliate as the referrer
			add_filter( 'affwp_get_referring_affiliate_id', function( $arg ) use ( $affiliate_id ) {
					return $affiliate_id;
				}
			);

		}	
	}
	
}
new AffiliateWP_Allow_Own_Referrals_WooCommerce;