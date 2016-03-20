<?php

class AffiliateWP_Allow_Own_Referrals_Settings {

	public function __construct() {

		add_filter( 'affwp_settings_integrations', array( $this, 'settings' ), 10, 1 );

	}
	
	/**
	 * Register Allow Own Referrals Settings
	 *
	 * @since 1.1
	 */
	public function settings( $settings = array() ) {

		$settings[ 'allow_own_referrals_header' ] = array(
			'name' => __( 'Allow Own Referrals', 'affiliate-wp-allow-own-referrals' ),
			'type' => 'header'
			
		);

		$settings[ 'allow_own_referrals_auto_detect' ] = array(
			'name' => __( 'Detect Logged-In Affiliates?', 'affiliate-wp-allow-own-referrals' ),
			'desc' => __( 'Check this box if you would like to award referrals to logged-in affiliates when they make purchases.' ),
			'type' => 'checkbox'
		);
		
		return $settings;
	}

}