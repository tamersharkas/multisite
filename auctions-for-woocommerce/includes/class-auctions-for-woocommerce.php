<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/includes
 */
class Auctions_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @var      Auctions_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The single instance of the class.
	 *
	 * @var WooCommerce
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * The admin plugin object
	 *
	 * @since    2.0.0
	 * @var      object
	 */
	public $plugin_admin;

	/**
	 * The public plugin object
	 *
	 * @since    2.0.0
	 * @var      object
	 */
	public $plugin_public;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->define_constants();

		$this->plugin_name = 'auctions-for-woocommerce';
		$this->version     = AFW_PLUGIN_VERSION;

		$this->load_dependencies();
		$this->register_auctions_taxonomies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	/**
	 * Define AFW Constants.
	 */
	private function define_constants() {
		if ( ! defined( 'AFW_PLUGIN_BASENAME' ) ) {
			define( 'AFW_PLUGIN_BASENAME', plugin_basename( AFW_PLUGIN_FILE ) );
		}
		if ( ! defined( 'AFW_ABSPATH' ) ) {
			define( 'AFW_ABSPATH', dirname( AFW_PLUGIN_FILE ) . '/' );
		}
	}

	/**
	 * Main Plugin Instance.
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 *
	 * @since 2.0.0
	 * @return Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Auctions_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Auctions_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Auctions_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Auctions_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once AFW_ABSPATH . 'includes/class-auctions-for-woocommerce-install.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once AFW_ABSPATH . 'includes/class-auctions-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once AFW_ABSPATH . 'includes/class-auctions-for-woocommerce-i18n.php';

		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			/**
			 * The class responsible for bidding
			 */
			require_once AFW_ABSPATH . 'includes/class-auctions-for-woocommerce-bid.php';
		}

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once AFW_ABSPATH . 'admin/class-auctions-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once AFW_ABSPATH . 'public/class-auctions-for-woocommerce-public.php';

		$this->loader = new Auctions_For_Woocommerce_Loader();

		/**
		 * The class responsible for defining auction product.
		 */
		require_once AFW_ABSPATH . 'includes/class-wc-product-auction.php';

		/**
		 * The class responsible for defining auction functions.
		 */
		require_once AFW_ABSPATH . 'includes/auctions-for-woocommerce-functions.php';

		/**
		 * The class responsible for cronjobs.
		 */
		require_once AFW_ABSPATH . 'includes/class-auctions-for-woocommerce-cronjobs.php';

		/**
		 * The class responsible for endpoints.
		 */
		require_once AFW_ABSPATH . 'public/class-auctions-for-woocommerce-endpoints.php';

		if ( ! is_admin() ) {
			/**
			 * The class responsible for modifiying query
			 */
			require_once AFW_ABSPATH . 'public/class-auctions-for-woocommerce-query.php';

			/**
			 * The class responsible for shortcodes
			 */
			require_once AFW_ABSPATH . 'public/class-auctions-for-woocommerce-shortcodes.php';
		}

		/**
		 * The class responsible for deprecated filter hooks.
		 */
		require_once AFW_ABSPATH . 'includes/class-auctions-for-woocommerce-ajax.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Auctions_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Auctions_For_Woocommerce_I18n();

		$this->loader->add_action( 'wp_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_admin_hooks() {

		$this->plugin_admin = new Auctions_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		if ( is_admin() ) {

			$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_scripts' );
			$this->loader->add_action( 'admin_init', $this->plugin_admin, 'check_environment' );
			$this->loader->add_action( 'admin_notices', $this->plugin_admin, 'admin_notices' );
			$this->loader->add_action( 'wp_loaded', $this->plugin_admin, 'hide_notices' );
			$this->loader->add_action( 'woocommerce_product_data_panels', $this->plugin_admin, 'product_write_panel' );
			$this->loader->add_action( 'woocommerce_process_product_meta_auction', $this->plugin_admin, 'product_save_data', 80, 1 );
			$this->loader->add_action( 'woocommerce_duplicate_product', $this->plugin_admin, 'duplicate_product' );

			$this->loader->add_action( 'woocommerce_order_status_processing', $this->plugin_admin, 'auction_payed' );
			$this->loader->add_action( 'woocommerce_order_status_completed', $this->plugin_admin, 'auction_payed' );
			$this->loader->add_action( 'woocommerce_order_status_cancelled', $this->plugin_admin, 'auction_order_canceled' );
			$this->loader->add_action( 'woocommerce_order_status_refunded', $this->plugin_admin, 'auction_order_canceled' );
			$this->loader->add_action( 'manage_product_posts_custom_column', $this->plugin_admin, 'render_product_columns', 10 );
			$this->loader->add_action( 'add_meta_boxes', $this->plugin_admin, 'auctions_for_woocommerce_meta' );
			$this->loader->add_action( 'add_meta_boxes', $this->plugin_admin, 'auctions_for_woocommerce_automatic_relist' );
			$this->loader->add_action( 'restrict_manage_posts', $this->plugin_admin, 'admin_posts_filter_restrict_manage_posts' );
			$this->loader->add_action( 'parse_query', $this->plugin_admin, 'admin_posts_filter' );
			$this->loader->add_action( 'admin_menu', $this->plugin_admin, 'add_auction_activity_page' );
			$this->loader->add_action( 'delete_post', $this->plugin_admin, 'del_auction_logs' );

			$this->loader->add_filter( 'woocommerce_get_settings_pages', $this->plugin_admin, 'auctions_settings_class' );
			$this->loader->add_filter( 'plugin_row_meta', $this->plugin_admin, 'add_support_link', 10, 2 );
			$this->loader->add_filter( 'woocommerce_product_data_tabs', $this->plugin_admin, 'product_write_panel_tab' );
			$this->loader->add_filter( 'product_type_selector', $this->plugin_admin, 'add_product_type' );
			$this->loader->add_filter( 'set-screen-option', $this->plugin_admin, 'auctions_for_woocommerce_set_option', 10, 3 );

		}
		$this->loader->add_action( 'widgets_init', $this->plugin_admin, 'register_widgets' );
		$this->loader->add_action( 'woocommerce_email', $this->plugin_admin, 'add_to_mail_class' );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $this->plugin_admin, 'auction_order', 10, 2 );

		// [vendors] tag email integration
		$this->loader->add_filter( 'woocommerce_email_recipient_auction_fail', $this->plugin_admin, 'add_vendor_to_email_recipients', 10, 2 );
		$this->loader->add_filter( 'woocommerce_email_recipient_auction_finished', $this->plugin_admin, 'add_vendor_to_email_recipients', 10, 2 );
		$this->loader->add_filter( 'woocommerce_email_recipient_auction_relist', $this->plugin_admin, 'add_vendor_to_email_recipients', 10, 2 );
		$this->loader->add_filter( 'woocommerce_email_recipient_bid_note', $this->plugin_admin, 'add_vendor_to_email_recipients', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_public_hooks() {

		$this->plugin_public = new Auctions_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $this->plugin_public, 'auctions_for_woocommerce_place_bid' );
		$this->loader->add_action( 'wp_loaded', $this->plugin_public, 'add_product_to_cart' );
		$this->loader->add_action( 'template_redirect', $this->plugin_public, 'track_auction_view' );
		$this->loader->add_action( 'woocommerce_product_tabs', $this->plugin_public, 'auction_tab' );
		$this->loader->add_action( 'woocommerce_product_tab_panels', $this->plugin_public, 'auction_tab_panel' );
		$this->loader->add_action( 'woocommerce_after_shop_loop_item', $this->plugin_public, 'add_pay_button', 60 );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item_title', $this->plugin_public, 'add_winning_bage', 60 );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item_title', $this->plugin_public, 'add_auction_bage', 60 );
		$this->loader->add_action( 'woocommerce_login_form_end', $this->plugin_public, 'add_redirect_previous_page' );
		$this->loader->add_action( 'auctions_for_woocommerce_place_bid', $this->plugin_public, 'add_auction_to_user_metafield' );
		$this->loader->add_action( 'auctions_for_woocommerce_place_bid', $this->plugin_public, 'change_last_activity_timestamp', 1 );
		$this->loader->add_action( 'auctions_for_woocommerce_delete_bid', $this->plugin_public, 'change_last_activity_timestamp', 1 );
		$this->loader->add_action( 'auctions_for_woocommerce_close', $this->plugin_public, 'change_last_activity_timestamp', 1 );
		$this->loader->add_action( 'auctions_for_woocommerce_started', $this->plugin_public, 'change_last_activity_timestamp', 1 );
		$this->loader->add_action( 'woocommerce_auction_add_to_cart', $this->plugin_public, 'woocommerce_auction_add_to_cart', 30 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $this->plugin_public, 'woocommerce_auction_bid', 30 );
		if ( is_user_logged_in() ) {
			$this->loader->add_action( 'woocommerce_single_product_summary', $this->plugin_public, 'woocommerce_auction_pay', 30 );
		}
		$this->loader->add_action( 'auctions_for_woocommerce_outbid', $this->plugin_public, 'auctions_extend_time', 1 );
		$this->loader->add_action( 'auctions_for_woocommerce_proxy_outbid', $this->plugin_public, 'auctions_extend_time', 1 );
		$this->loader->add_action( 'template_redirect', $this->plugin_public, 'auction_page_description' );

		$this->loader->add_filter( 'woocommerce_locate_template', $this->plugin_public, 'woocommerce_locate_template', 10, 3 );
		$this->loader->add_filter( 'woocommerce_is_purchasable', $this->plugin_public, 'auction_is_purchasable', 10, 2 );
		$this->loader->add_filter( 'pre_get_document_title', $this->plugin_public, 'auction_filter_wp_title', 10 );
		$this->loader->add_filter( 'template_include', $this->plugin_public, 'auctions_page_template', 99 );
		$this->loader->add_filter( 'body_class', $this->plugin_public, 'output_body_class', 99 );
		$this->loader->add_filter( 'woocommerce_page_title', $this->plugin_public, 'auction_page_title' );
		$this->loader->add_filter( 'woocommerce_get_breadcrumb', $this->plugin_public, 'woocommerce_get_breadcrumb', 1, 2 );
		$this->loader->add_filter( 'woocommerce_catalog_orderby', $this->plugin_public, 'auction_woocommerce_catalog_orderby', 10 );
		$this->loader->add_filter( 'wp_nav_menu_objects', $this->plugin_public, 'wsa_nav_menu_item_classes', 10 );
		$this->loader->add_filter( 'woocommerce_product_related_posts_query', $this->plugin_public, 'remove_finished_auctions_from_related_products' );
		$this->loader->add_filter( 'woocommerce_product_is_visible', $this->plugin_public, 'add_out_of_stock_items', 10, 2 );

		if ( 'yes' === get_option( 'auctions_for_woocommerce_watchlists', 'yes' ) ) {
			$this->loader->add_action( 'woocommerce_after_bid_form', $this->plugin_public, 'add_watchlist_link', 10 );
			$this->loader->add_action( 'woocommerce_after_shop_loop_item', $this->plugin_public, 'add_to_watchlist', 10 );
		}
		if ( 'yes' === get_option( 'auctions_for_woocommerce_countdown_loop', 'no' ) ) {
			$this->loader->add_action( 'woocommerce_after_shop_loop_item_title', $this->plugin_public, 'show_counter_in_loop', 50 );
		}

		if ( 'yes' === get_option( 'auctions_for_woocommerce_mask_displaynames', 'no' ) ) {
			$this->loader->add_action( 'auctions_for_woocommerce_displayname', $this->plugin_public, 'mask_displayname', 10 );
		}

		$languages = apply_filters( 'wpml_active_languages', null, 'orderby=id&order=desc' );

		if ( ! empty( $languages ) ) {
			$this->loader->add_action( 'auctions_for_woocommerce_place_bid', $this->plugin_public, 'sync_metadata_wpml', 1 );
			$this->loader->add_action( 'auctions_for_woocommerce_close', $this->plugin_public, 'sync_metadata_wpml', 1 );
			$this->loader->add_action( 'woocommerce_process_product_meta', $this->plugin_public, 'sync_metadata_wpml', 85 );
			$this->loader->add_action( 'auctions_for_woocommerce_outbid', $this->plugin_public, 'add_language_wpml_meta', 99 );

			$this->loader->add_filter( 'woocommerce_get_auction_page_id', $this->plugin_public, 'auctionbase_page_wpml', 10, 1 );
			$this->loader->add_filter( 'icl_ls_languages', $this->plugin_public, 'translate_ls_auction_url', 80, 1 );
		}

		$email_actions = array(
			'auctions_for_woocommerce_outbid',
			'auctions_for_woocommerce_won',
			'auctions_for_woocommerce_fail',
			'auctions_for_woocommerce_reserve_fail',
			'auctions_for_woocommerce_pay_reminder',
			'auctions_for_woocommerce_close_buynow',
			'auctions_for_woocommerce_close',
			'auctions_for_woocommerce_place_bid',
			'woocomerce_before_relist_failed_auction',
			'woocomerce_before_relist_not_paid_auction',
			'auctions_for_woocommerce_closing_soon',
		);
		foreach ( $email_actions as $action ) {
			add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 80 );
		}

		add_action( 'template_redirect', 'auctions_for_woocommerce_winning_bid_message', 99 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Auctions_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', AFW_PLUGIN_FILE ) );
	}

	public function send_reminders_email() {

		$remind_to_pay_settings = get_option( 'woocommerce_remind_to_pay_settings' );
		if ( 'yes' !== $remind_to_pay_settings['enabled'] ) {
			exit();
		}

		$interval    = ( isset( $remind_to_pay_settings['interval'] ) && ! empty( $remind_to_pay_settings['interval'] ) ) ? (int) $remind_to_pay_settings['interval'] : 7;
		$stopsending = ( isset( $remind_to_pay_settings['stopsending'] ) && ! empty( $remind_to_pay_settings['stopsending'] ) ) ? (int) $remind_to_pay_settings['stopsending'] : 5;
		$args        = array(
			'post_type'              => 'product',
			'posts_per_page'         => '100',
			'show_past_auctions'     => true,
			'tax_query'              => array(

				self::show_only_tax_query( array( 'sold' ) ),
			),
			'meta_query'             => array(
				'relation' => 'AND',
				array(
					'key'     => '_auction_payed',
					'compare' => 'NOT EXISTS',
				),

				array(
					'key'     => '_stop_mails',
					'compare' => 'NOT EXISTS',
				),

			),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		);

		$the_query = new WP_Query( $args );
		$posts_ids = $the_query->posts;

		if ( is_array( $posts_ids ) ) {

			foreach ( $posts_ids as $posts_id ) {

				$number_of_sent_mail = (int) get_post_meta( $posts_id, '_number_of_sent_mails', true );
				$dates_of_sent_mail  = get_post_meta( $posts_id, '_dates_of_sent_mails', false );
				$n_days              = (int) $remind_to_pay_settings['interval'];

				if ( (int) $number_of_sent_mail >= $stopsending ) {

					update_post_meta( $posts_id, '_stop_mails', '1' );

				} elseif ( ! $dates_of_sent_mail || ( (int) end( $dates_of_sent_mail ) < strtotime( '-' . $interval . ' days' ) ) ) {

					update_post_meta( $posts_id, '_number_of_sent_mails', $number_of_sent_mail + 1 );
					add_post_meta( $posts_id, '_dates_of_sent_mails', time(), false );
					do_action( 'auctions_for_woocommerce_pay_reminder', $posts_id );

				}
			}
		}
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

	public function remove_from_tax_query( $type ) {
		$tax_query                 = array();
		$auction_visibility_not_in = array();
		$auction_visibility_terms  = wc_get_auction_visibility_term_ids();
		if ( is_array( $type ) ) {
			foreach ( $type as $value ) {
				$auction_visibility_not_in[] = $auction_visibility_terms[ $value ];
			}
		} else {
			$auction_visibility_not_in = array( $auction_visibility_terms[ $type ] );
		}

		if ( ! empty( $auction_visibility_not_in ) ) {
			$tax_query[] = array(
				'taxonomy' => 'auction_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $auction_visibility_not_in,
				'operator' => 'NOT IN',
			);
		}
		return $tax_query;
	}
	public function show_only_tax_query( $type ) {
		$tax_query                = array();
		$auction_visibility_in    = array();
		$auction_visibility_terms = wc_get_auction_visibility_term_ids();
		if ( is_array( $type ) ) {
			foreach ( $type as $value ) {
				$auction_visibility_in[] = $auction_visibility_terms[ $value ];
			}
		} else {
			$auction_visibility_in = array( $auction_visibility_terms[ $type ] );
		}

		if ( ! empty( $auction_visibility_in ) ) {
			$tax_query[] = array(
				'taxonomy' => 'auction_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $auction_visibility_in,
				'operator' => 'IN',
			);
		}
		return $tax_query;
	}

}

