<?php
/**
 * Installation related functions and actions.
 *
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Install Class.
 */
class Auctions_For_Woocommerce_Install {

	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @var array
	 */
	private static $db_updates = array(
		'2.0.0' => array(
			'wsa_update_200_auctions_visibility',
			'wsa_update_200_db_version',
		),
	);

	/**
	 * Background update class.
	 *
	 * @var object
	 */
	private static $background_updater;

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 50 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
	}

	/**
	 * Check WooCommerce Simple auctions version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'auctions_for_woocommerce_version' ), AFW()->get_version(), '<' ) ) {
			self::install();
			do_action( 'auctions_for_woocommerce_updated' );
		}

	}

	/**
	 * Install actions when a update button is clicked within the admin area.
	 *
	 * This function is hooked into admin_init to affect admin only.
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_wsa'] ) ) { // WPCS: input var ok.
			// check_admin_referer( 'wsa_db_update', 'wsa_db_update_nonce' );
			self::update();
			self::remove_admin_notices();
			ob_start();
			include AFW_ABSPATH . '/admin/partials/notices/html-notice-update-finish.php';
			$message = ob_get_clean();
			WC_Admin_Notices::add_custom_notice( 'wsa_update', $message );
			wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=auctions_for_woocommerce' ) );
			exit;
		}
	}

	/**
	 * Install WC.
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'wsa_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'wsa_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		wc_maybe_define_constant( 'AFW_INSTALLING', true );
		self::remove_admin_notices();
		self::create_options();
		self::create_tables();
		AFW()->register_auctions_taxonomies();
		AFW()->create_terms();
		self::update_wsa_version();
		self::maybe_update_db_version();
		delete_transient( 'wsa_installing' );
		do_action( 'auctions_for_woocommerce_flush_rewrite_rules' );
		do_action( 'auctions_for_woocommerce_installed' );
	}

	/**
	 * Reset any notices added to admin.
	 *
	 * @since 3.2.0
	 */
	private static function remove_admin_notices() {
		include_once WC()->plugin_path() . '/includes/admin/class-wc-admin-notices.php';
		WC_Admin_Notices::remove_all_notices();
	}

	/**
	 * Is this a brand new WC install?
	 *
	 * @since 3.2.0
	 * @return boolean
	 */
	private static function is_new_install() {
		return is_null( get_option( 'auctions_for_woocommerce_version', null ) ) && is_null( get_option( 'auctions_for_woocommerce_database_version', null ) );
	}

	/**
	 * Is a DB update needed?
	 *
	 * @since 3.2.0
	 * @return boolean
	 */
	private static function needs_db_update() {
		$current_db_version = get_option( 'auctions_for_woocommerce_database_version', null );
		$updates            = self::get_db_update_callbacks();
		return ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since 3.2.0
	 */
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			ob_start();
			include AFW_ABSPATH . '/admin/partials/notices/html-notice-updating.php';
			$message = ob_get_clean();
			WC_Admin_Notices::add_custom_notice( 'wsa_needs_update', $message );
		} else {
			self::update_db_version();
		}
	}

	/**
	 * Update WC version to current.
	 */
	private static function update_wsa_version() {
		delete_option( 'auctions_for_woocommerce_version' );
		add_option( 'auctions_for_woocommerce_version', AFW()->get_version() );
	}

	/**
	 * Get list of DB update callbacks.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {
		$current_db_version = get_option( 'auctions_for_woocommerce_database_version' );
		$logger             = wc_get_logger();
		include AFW_ABSPATH . '/includes/auctions-for-woocommerce-updater-functions.php';
		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					$logger->info(
						sprintf( 'Queuing %s - %s', $version, $update_callback ),
						array( 'source' => 'wsa_db_updates' )
					);
					call_user_func( $update_callback );
				}
			}
		}

	}

	/**
	 * Update DB version to current.
	 *
	 * @param string|null $version New WooCommerce DB version or null.
	 */
	public static function update_db_version( $version = null ) {
		delete_option( 'auctions_for_woocommerce_database_version' );
		add_option( 'auctions_for_woocommerce_database_version', is_null( $version ) ? AFW()->get_version() : $version );
	}


	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	private static function create_options() {
		include_once WC()->plugin_path() . '/includes/admin/class-wc-admin-settings.php';
		$settings = WC_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			if ( ! ( $section instanceof Auctions_For_Woocommerce_Settings ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );
			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}

	}


	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *      woocommerce_attribute_taxonomies - Table for storing attribute taxonomies - these are user defined
	 *      woocommerce_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
	 *      woocommerce_downloadable_product_permissions - Table for storing user and guest download permissions.
	 *          KEY(order_id, product_id, download_id) used for organizing downloads on the My Account page
	 *      woocommerce_order_items - Order line items are stored in a table to make them easily queryable for reports
	 *      woocommerce_order_itemmeta - Order line item meta is stored in a table for storing extra data.
	 *      woocommerce_tax_rates - Tax Rates are stored inside 2 tables making tax queries simple and efficient.
	 *      woocommerce_tax_rate_locations - Each rate can be applied to more than one postcode/city hence the second table.
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( self::get_schema() );

	}

	/**
	 * Get Table schema.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$data_table = $wpdb->prefix . 'auctions_for_woocommerce_log';
		$tables     = "CREATE TABLE $data_table (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`userid` bigint(20) unsigned NOT NULL,
				`auction_id` bigint(20) unsigned DEFAULT NULL,
				`bid` decimal(32,4) DEFAULT NULL,
				`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`proxy` tinyint(1) DEFAULT NULL,
				PRIMARY KEY (`id`)
				);";
		return $tables;
	}

	/**
	 * Return a list of WooCommerce tables. Used to make sure all WC tables are dropped when uninstalling the plugin
	 * in a single site or multi site environment.
	 *
	 * @return array WC tables.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}auctions_for_woocommerce_log",
		);

		$tables = apply_filters( 'auctions_for_woocommerce_install_get_tables', $tables );

		return $tables;
	}

	/**
	 * Drop WooCommerce tables.
	 *
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}auctions_for_woocommerce_log" );
	}

	/**
	 * Uninstall tables when MU blog is deleted.
	 *
	 * @param  array $tables List of tables that will be deleted by WP.
	 * @return string[]
	 */
	public static function wpmu_drop_tables( $tables ) {
		return array_merge( $tables, self::get_tables() );
	}


}

Auctions_For_Woocommerce_Install::init();
