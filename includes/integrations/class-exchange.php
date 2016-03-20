<?php

class AffiliateWP_Allow_Own_Referrals_Exchange extends AffiliateWP_Allow_Own_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'it-exchange';

		if( ! class_exists( 'IT_Exchange' ) ) {
			return;
		}

		add_action( 'it_exchange_add_transaction_success', array( $this, 'set_ref_for_affiliate_purchase' ), 9 );
		
	}

	/**
	 * Set the referral for the current affiliate making the purchase
	 *
	 * @since 1.1
	 */

	public function set_ref_for_affiliate_purchase( $transaction_id = 0 ) {

		// Make sure affiliate auto detection is enabled
		if ( ! affiliate_wp()->settings->get( 'allow_own_referrals_auto_detect' ) ) {
			return;
		}	

		// Start checking the transaction
		$this->transaction = apply_filters( 'affwp_get_it_exchange_transaction', get_post_meta( $transaction_id, '_it_exchange_cart_object', true ) );
		$guest_checkout_email = it_exchange_get_transaction_customer_email( $transaction_id );
		$order_email          = isset( $guest_checkout_email ) ? $guest_checkout_email : $this->transaction->shipping_address['email'];
		
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
new AffiliateWP_Allow_Own_Referrals_Exchange;