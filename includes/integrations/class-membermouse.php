<?php

class AffiliateWP_Allow_Own_Referrals_Membermouse extends AffiliateWP_Allow_Own_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		$this->context = 'membermouse';

		add_action( 'mm_member_add', array( $this, 'set_ref_for_free_affiliate_purchase' ), 9 );
		add_action( 'mm_commission_initial', array( $this, 'set_ref_for_affiliate_purchase' ), 9 );
		
	}

	/**
	 * Set the referral for the current affiliate purchasing a free product/membership
	 *
	 * @since 1.1
	 */

	public function set_ref_for_free_affiliate_purchase( $member_data ) {

		// Make sure affiliate auto detection is enabled
		if ( ! affiliate_wp()->settings->get( 'allow_own_referrals_auto_detect' ) ) {
			return;
		}	

		$membership = new MM_MembershipLevel( $member_data['membership_level'] );

		if( ! $membership->isFree() ) {
			return;
		}

		// Start checking the order
		$order_email  = $member_data['email'];
		
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

	/**
	 * Set the referral for the current affiliate making the purchase
	 *
	 * @since 1.1
	 */

	public function set_ref_for_affiliate_purchase( $affiliate_data ) {

		// Make sure affiliate auto detection is enabled
		if ( ! affiliate_wp()->settings->get( 'allow_own_referrals_auto_detect' ) ) {
			return;
		}	

		// Start checking the order
		$user = get_userdata( $affiliate_data['member_id'] );
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
new AffiliateWP_Allow_Own_Referrals_Membermouse;