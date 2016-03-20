<?php
/**
 * Plugin Name: AffiliateWP - Allow Own Referrals
 * Plugin URI: http://affiliatewp.com/addons/allow-own-referrals
 * Description: Allows an affiliate to earn commission on their own referrals
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.1
 * Text Domain:
 * Domain Path: languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Allow_Own_Referrals' ) ) {

	final class AffiliateWP_Allow_Own_Referrals {

		/**
		 * Plugin instance.
		 *
		 * @see instance()
		 * @type object
		 */
		private static $instance;

		/**
		 * URL to this plugin's directory.
		 *
		 * @type string
		 */
		public static  $plugin_dir;
		public static  $plugin_url;
		private static $version;

		/**
		 * The settings instance variable
		 *
		 * @var AffiliateWP_Allow_Own_Referrals_Settings
		 * @since 1.1
		 */
		public $settings;
	
		/**
		 * The integrations handler instance variable
		 *
		 * @var AffiliateWP_Allow_Own_Referrals_Base
		 * @since 1.1
		 */
		public $integrations;

		/**
		 * Main AffiliateWP_Allow_Own_Referrals Instance
		 *
		 * Insures that only one instance of AffiliateWP_Allow_Own_Referrals exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.1
		 * @static
		 * @staticvar array $instance
		 * @return The one true AffiliateWP_Allow_Own_Referrals
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Allow_Own_Referrals ) ) {
				
				self::$instance = new AffiliateWP_Allow_Own_Referrals;
				self::$version  = '1.1';

				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->setup_objects();
				self::$instance->hooks();
				

			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.1
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-allow-own-referrals' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.1
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-allow-own-referrals' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.1
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.1
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}
		
		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.1
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			if ( ! defined( 'AFFWP_AOR_VERSION' ) ) {
				define( 'AFFWP_AOR_VERSION', self::$version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_AOR_PLUGIN_DIR' ) ) {
				define( 'AFFWP_AOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_AOR_PLUGIN_URL' ) ) {
				define( 'AFFWP_AOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_AOR_PLUGIN_FILE' ) ) {
				define( 'AFFWP_AOR_PLUGIN_FILE', __FILE__ );
			}

		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.1
		 * @return      void
		 */
		private function includes() {

				require_once AFFWP_AOR_PLUGIN_DIR . 'includes/admin/class-settings.php';

				require_once AFFWP_AOR_PLUGIN_DIR . 'includes/integrations/class-base.php';

			/* Load the class for each integration enabled */
			foreach ( affiliate_wp()->integrations->get_enabled_integrations() as $filename => $integration ) {

				if ( file_exists( AFFWP_AOR_PLUGIN_DIR . 'includes/integrations/class-' . $filename . '.php' ) ) {
					require_once AFFWP_AOR_PLUGIN_DIR . 'includes/integrations/class-' . $filename . '.php';
				}

			}
			
		}

		/**
		 * Setup all objects
		 *
		 * @access public
		 * @since 1.1
		 * @return void
		 */
		public function setup_objects() {
			self::$instance->settings = new AffiliateWP_Allow_Own_Referrals_Settings;
			self::$instance->integrations = new AffiliateWP_Allow_Own_Referrals_Base;
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.1
		 *
		 * @return void
		 */
		private function hooks() {
		
			// Plugin meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );
			
			// Enable referrals for affiliate's purchase
			add_filter( 'affwp_is_customer_email_affiliate_email', '__return_false' );
			
			add_filter( 'affwp_tracking_is_valid_affiliate', '__return_true' );		

		}
		
		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.1
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliate-wp-allow-own-referrals' ) . '" href="http://theperfectplugin.com/downloads/category/affiliatewp" target="_blank">' . __( 'Get add-ons', 'affiliate-wp-allow-own-referrals' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}
		
	}
	
	/**
	 * The main function responsible for returning the one true AffiliateWP_Allow_Own_Referrals
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_allow_own_referrals = affiliatewp_allow_own_referrals(); ?>
	 *
	 * @since 1.1
	 * @return object The one true AffiliateWP_Allow_Own_Referrals Instance
	 */
	function affiliatewp_allow_own_referrals() {

	    if ( ! class_exists( 'Affiliate_WP' ) ) {
	    	
	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	    } else {
	        return AffiliateWP_Allow_Own_Referrals::instance();
	    }
	}
	add_action( 'plugins_loaded', 'affiliatewp_allow_own_referrals', 100 );
	
}