<?php

class AffiliateWP_Allow_Own_Referrals_Gravity_Forms extends AffiliateWP_Allow_Own_Referrals_Base {

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.1
	*/
	public function init() {

		if ( ! class_exists( 'GFFormsModel' ) || ! class_exists( 'GFCommon' ) ) {
			return;
		}

		$this->context = 'gravityforms';

		add_action( 'gform_entry_created', array( $this, 'set_ref_for_affiliate_purchase' ), 9, 2 );
		
	}

	/**
	 * Set the referral for the current affiliate making the purchase
	 *
	 * @since 1.1
	 */

	public function set_ref_for_affiliate_purchase( $entry, $form ) {
		
		// Make sure affiliate auto detection is enabled
		if ( ! affiliate_wp()->settings->get( 'allow_own_referrals_auto_detect' ) ) {
			return;
		}	

		if ( ! rgar( $form, 'affwp_allow_referrals' ) ) {
			return;
		}

		// Start checking the order
		$email_fields = GFCommon::get_email_fields( $form );
        $field_id = '';

        // Get value of first email field. The form should only have 1 email field if it's a product form
        if ( $email_fields ) {
            foreach ( $email_fields as $email_field ) {
                $field_id = $email_field['id'];
                break;
            }
        }

		$order_email  = $entry[$field_id];
		
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
new AffiliateWP_Allow_Own_Referrals_Gravity_Forms;