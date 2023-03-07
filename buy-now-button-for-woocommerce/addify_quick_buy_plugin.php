<?php
/*
 * Plugin Name:       Quick Buy Now Button for WooCommerce
 * Plugin URI:        https://woocommerce.com/products/quick-buy-now-button-for-woocommerce/
 * Description:       Buy Now plugin enables you to add a new or replace add to cart with a quick buy button and redirect users to cart, checkout or custom link. (PLEASE TAKE BACKUP BEFORE UPDATING THE PLUGIN).
 * Version:           1.3.7
 * Author:            Addify
 * Developed By:      Addify
 * Author URI:        http://www.addifypro.com
 * Support:           http://www.addifypro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       addify_TextDomain
 *
 * Woo: 4923077:e832aeb790a28b829c0a9c844e23cf55
 *
 * WC requires at least: 3.0.9
 * WC tested up to: 5.*.*
 */


//Deny Direct Acess to file
if (!defined('ABSPATH')) {
	exit;
}

if ( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {

	function aqbp_admin_notice() {

		$aqbp_allowed_tags = array(
			'a' => array(
				'class' => array(),
				'href' => array(),
				'rel' => array(),
				'title' => array(),
			),
			'b' => array(),

			'div' => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'p' => array(
				'class' => array(),
			),
			'strong' => array(),

		);

		// Deactivate the plugin
		deactivate_plugins(__FILE__);

		$aqbp_woo_check = '<div id="message" class="error">
			<p><strong>Quick Buy Now Button for WooCommerce Plugin is inactive.</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> must be active for this plugin to work. Please install &amp; activate WooCommerce »</p></div>';
		echo wp_kses(__($aqbp_woo_check, 'addify_rfq'), $aqbp_allowed_tags);

	}

	add_action('admin_notices', 'aqbp_admin_notice');
}
if (!class_exists('Class_Addify_Quick_Buy')) {
	class Class_Addify_Quick_Buy {

		public function __construct() {

			//Define Global Constants
			$this->apbgp_global_constents_vars();
			//load Text Domain
			add_action('wp_loaded', array( $this, 'aqbp_init' ));
			//registration hook setting
			register_activation_hook( __FILE__, array( $this, 'aqbp_install_settings' ) );
			//Include other Files
			if (is_admin() ) {
				//include Admin Class
				include_once AQBP_PLUGIN_DIR . 'admin_quick_buy.php';
			} else {
				//include front class
				include_once AQBP_PLUGIN_DIR . 'front_quick_buy.php';
			}
		}

		//define GLobal varibles function
		public function apbgp_global_constents_vars() {

			if (!defined('AQBP_URL') ) {
				define('AQBP_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AQBP_BASENAME') ) {
				define('AQBP_BASENAME', plugin_basename(__FILE__));
			}

			if (! defined('AQBP_PLUGIN_DIR') ) {
				define('AQBP_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}

		//Plugin Settings
		public function aqbp_install_settings() {
			//default setting for plugin
			add_option('aqbp_but_label' , 'Quick Buy');
			add_option('aqbp_multiselect_product_cats' , serialize(array('all')));
			add_option('aqbp_multiselect_product_types' , serialize(array('all')));
			add_option('aqbp_multiselect_products' , serialize(array('simple')));
			add_option('aqbp_set_cart_quan' , '1');
			add_option('aqbp_set_cus_url' , 'http://your-site-url');
			add_option('aqbp_set_Red_loc' , 'cart');
			add_option('aqbp_shop_button' , 'yes');
			add_option('aqbp_shop_button_pos' , 'after-button');
			add_option('aqbp_single_button' , 'yes');
			add_option('aqbp_single_button_pos' , 'after-button');
			add_option('aqbp_style_button' , 'theme');	
		}

		//load text domain
		public function aqbp_init() {
			if ( function_exists('load_plugin_textdomain') ) {
				load_plugin_textdomain('addify _TextDomain', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			}
		}

	}
	new Class_Addify_Quick_Buy();
}
