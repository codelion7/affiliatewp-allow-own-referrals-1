<?php
/**
 * Plugin Name: AffiliateWP - Allow Own Referrals
 * Plugin URI: http://affiliatewp.com/addons/allow-own-referrals
 * Description: Allows an affiliate to earn commission on their own referrals
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.0.1
 * Text Domain:
 * Domain Path: languages
 */

add_filter( 'affwp_is_customer_email_affiliate_email', '__return_false' );
add_filter( 'affwp_tracking_is_valid_affiliate', '__return_true' );
