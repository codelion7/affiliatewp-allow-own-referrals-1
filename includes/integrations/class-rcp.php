<?php

class AffiliateWP_Allow_Own_Referrals_RCP extends AffiliateWP_Allow_Own_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'rcp';

		add_action( 'rcp_form_processing', array( $this, 'set_ref_for_affiliate_purchase' ), 9, 2 );
		
	}

	/**
	 * Set the referral for the current affiliate making the purchase
	 *
	 * @since 1.1
	 */

	public function set_ref_for_affiliate_purchase( $post_data, $user_id ) {

		// Make sure affiliate auto detection is enabled
		if ( ! affiliate_wp()->settings->get( 'allow_own_referrals_auto_detect' ) ) {
			return;
		}
		
		// Start checking the order
		$user = get_userdata( $user_id );
		$order_email  = $user->user_email;
		
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
new AffiliateWP_Allow_Own_Referrals_RCP;