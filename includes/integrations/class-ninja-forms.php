<?php

class AffiliateWP_Allow_Own_Referrals_Ninja_Forms extends AffiliateWP_Allow_Own_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'ninja-forms';

		add_action( 'nf_save_sub', array( $this, 'set_ref_for_affiliate_purchase' ), 9 );
		
	}

	/**
	 * Set the referral for the current affiliate making the purchase
	 *
	 * @since 1.1
	 */

	public function set_ref_for_affiliate_purchase( $sub_id ) {

		// Make sure affiliate auto detection is enabled
		if ( ! affiliate_wp()->settings->get( 'allow_own_referrals_auto_detect' ) ) {
			return;
		}	

		global $ninja_forms_processing;

		if ( ! $ninja_forms_processing->get_form_setting( 'affwp_allow_referrals' ) ) {
			return;
		}

		// Start checking the order
		$user_info = $ninja_forms_processing->get_user_info();
		
		if ( isset ( $user_info['billing']['email'] ) ) {
			$email = $user_info['billing']['email'];
		} else {
			$email = '';
		}
		
		$order_email  = $email;
		
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
new AffiliateWP_Allow_Own_Referrals_Ninja_Forms;