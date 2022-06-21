<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wpinstitut.com/
 * @since      1.0.0
 *
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/includes
 */
class Auctions_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		global $wp_version;

		$flag = false;
		if ( version_compare( PHP_VERSION, AFW_MIN_PHP, '<' ) ) {
			$flag = 'PHP';
		} elseif ( version_compare( $wp_version, AFW_MIN_WP, '<' ) ) {
			$flag = 'WordPress';
		}
		if ( $flag ) {
			$version = AFW_MIN_PHP;
			if ( 'WordPress' === $flag ) {
				$version = AFW_MIN_WP;
			}
			deactivate_plugins( basename( __FILE__ ) );
			wp_die(
				'<p>The <strong>Auctions for WooCommerce</strong> plugin requires ' . esc_html( $flag ) . '  version ' . esc_html( $version ) . ' or greater. If you need secure hosting with all requirements for this plugin contact us at <a href="mailto:info@wpinstitut.com">info@wpinstitut.com</a></p>',
				'Plugin Activation Error',
				array(
					'response'  => 200,
					'back_link' => true,
				)
			);
		}

		$data_table = $wpdb->prefix . 'auctions_for_woocommerce_log';
		$sql        = " CREATE TABLE IF NOT EXISTS $data_table (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`userid` bigint(20) unsigned NOT NULL,
				`auction_id` bigint(20) unsigned DEFAULT NULL,
				`bid` decimal(32,4) DEFAULT NULL,
				`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`proxy` tinyint(1) DEFAULT NULL,
				PRIMARY KEY (`id`)
				);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		wp_insert_term( 'auction', 'product_type' );

		if ( get_option( 'auctions_for_woocommerce_finished_enabled' ) === false ) {
			add_option( 'auctions_for_woocommerce_finished_enabled', 'no' );
		}

		if ( get_option( 'auctions_for_woocommerce_future_enabled' ) === false ) {
			add_option( 'auctions_for_woocommerce_future_enabled', 'yes' );
		}

		if ( get_option( 'auctions_for_woocommerce_dont_mix_shop' ) === false ) {
			add_option( 'auctions_for_woocommerce_dont_mix_shop', 'yes' );
		}

		if ( get_option( 'auctions_for_woocommerce_countdown_format' ) === false ) {
			add_option( 'auctions_for_woocommerce_countdown_format', 'yowdHMS' );
		}

		if ( get_option( 'auctions_for_woocommerce_live_check' ) === false ) {
			add_option( 'auctions_for_woocommerce_live_check', 'yes' );
		}

		if ( get_option( 'auctions_for_woocommerce_live_check_interval' ) === false ) {
			add_option( 'auctions_for_woocommerce_live_check_interval', '1' );
		}

		if ( get_option( 'auctions_for_woocommerce_curent_bidder_can_bid' ) === false ) {
			add_option( 'auctions_for_woocommerce_curent_bidder_can_bid', 'no' );
		}

		update_option( 'auctions_for_woocommerce_database_version', AFW_DB_VERSION );
		update_option( 'auctions_for_woocommerce_version', AFW_PLUGIN_VERSION );

		self::register_auctions_taxonomies();
		self::create_terms();

		flush_rewrite_rules();
	}

	public static function register_auctions_taxonomies() {
		register_taxonomy(
			'auction_visibility',
			'product',
			array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false,
			)
		);
	}
	public static function create_terms() {
		$taxonomies = array(
			'auction_visibility' => array(
				'finished',
				'started',
				'sold',
				'buy-now',
				'future',
				'sealed',
				'relisted',
			),
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'name', $term, $taxonomy ) ) { // @codingStandardsIgnoreLine.
					wp_insert_term( $term, $taxonomy );
				}
			}
		}
	}


}
