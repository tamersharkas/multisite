<?php
/** The plugin main file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpinstitut.com/
 * @since             1.0.0
 * @package           auctions_for_woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Auctions for WooCommerce
 * Plugin URI:        https://wpinstitut.com/auctions-for-woocommerce/
 * Description:       Easily extend WooCommerce with auction features and functionalities.
 * Version:           3.7
 * Author:            WPInstitut
 * Author URI:        https://wpinstitut.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       auctions-for-woocommerce
 * Domain Path:       /languages
 * WC requires at least: 4.0
 * WC tested up to: 7.0
 * Woo: 4922919:91c5df0095e2fe6c043b9c07ff419c49
 */

// If this file is called directly, abort.
// update
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! defined( 'AFW_PLUGIN_VERSION' ) ) {
	define( 'AFW_PLUGIN_VERSION', '1.6' );
}
if ( ! defined( 'AFW_DB_VERSION' ) ) {
	define( 'AFW_DB_VERSION', '1.0.0' );
}
if ( ! defined( 'AFW_MIN_WP' ) ) {
	define( 'AFW_MIN_WP', '4.0' );
}
if ( ! defined( 'AFW_MIN_PHP' ) ) {
	define( 'AFW_MIN_PHP', '5.5' );
}
if ( ! defined( 'AFW_MIN_WC' ) ) {
	define( 'AFW_MIN_WC', '3.0' );
}
if ( ! defined( 'AFW_PLUGIN_FILE' ) ) {
	define( 'AFW_PLUGIN_FILE', __FILE__ );
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-auctions-for-woocommerce-activator.php
	 */
	function activate_auctions_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-auctions-for-woocommerce-activator.php';
		Auctions_For_Woocommerce_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-auctions-for-woocommerce-deactivator.php
	 */
	function deactivate_auctions_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-auctions-for-woocommerce-deactivator.php';
		Auctions_For_Woocommerce_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_auctions_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_auctions_for_woocommerce' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-auctions-for-woocommerce.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    2.0.0
	 */
	function run_auctions_for_woocommerce() {
		global $woocommerce_auctions;

		$woocommerce_auctions = new Auctions_For_Woocommerce();
		$woocommerce_auctions->run();
	}

	add_action( 'woocommerce_init', 'run_auctions_for_woocommerce' );

	function AFW() {
		return Auctions_For_Woocommerce::instance();
	}
} else {
	add_action( 'admin_notices', 'afw_error_notice' );
	/**
	 * Display error message if WooCommerce isn't active.
	 */
	function afw_error_notice() {
		global $current_screen;
		if ( 'plugins' === $current_screen->parent_base ) {
			echo '<div class="error"><p>Auctions for WooCommerce ';
			$adminurl = admin_url( 'plugin-install.php?tab=search&type=term&s=WooCommerce' );
			echo wp_kses_post( sprintf( __( 'requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> to be activated in order to work. Please install and activate <a href="#s" target="_blank">WooCommerce</a> first.', 'auctions-for-woocommerce' ), esc_url( $adminurl ) ) );
			echo '</p></div>';
		}
	}

	$custom_plugin = plugin_basename( __FILE__ );

	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( is_plugin_active( $custom_plugin ) ) {
		deactivate_plugins( $custom_plugin );
	}

	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}
