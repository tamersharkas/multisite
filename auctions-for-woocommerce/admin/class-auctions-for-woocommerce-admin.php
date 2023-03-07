<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpinstitut.com/
 * @since      1.0.0
 *
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/admin
 */
class Auctions_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Notices (array).
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Auction_types (array).
	 *
	 * @var array
	 */
	public $auction_types = array();

	/**
	 * Auction_item_condition (array).
	 *
	 * @var array
	 */
	public $auction_item_condition = array();


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->auction_types = array(
			'normal'  => esc_html__( 'Normal', 'auctions-for-woocommerce' ),
			'reverse' => esc_html__( 'Reverse', 'auctions-for-woocommerce' ),
		);

		$this->auction_item_condition = array(
			'new'  => esc_html__( 'New', 'auctions-for-woocommerce' ),
			'used' => esc_html__( 'Used', 'auctions-for-woocommerce' ),
		);

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->load_dependencies();

	}
	/**
	 * Load the required dependencies for this plugin admin.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for defining auction product.
		 */
		require_once AFW_ABSPATH . 'admin/class-auctions-for-woocommerce-activity-list.php';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		if ( 'post-new.php' === $hook || 'post.php' === $hook || 'woocommerce_page_auctions-activity' === $hook ) {

			if ( 'product' === get_post_type() || 'woocommerce_page_auctions-activity' === $hook ) {

				global $post;
				if ( $post ) {
					$product_data = wc_get_product( $post->ID );
					if ( $product_data && 'auction' === $product_data->get_type() ) {
						wp_enqueue_style( 'DataTables', AFW()->plugin_url() . '/admin/js/DataTables/datatables.min.css', array(), '1' );
						wp_enqueue_style( 'DataTables-buttons', AFW()->plugin_url() . '/admin/js/DataTables/buttons.dataTables.min.css', array(), '1' );
					}
				}
				wp_enqueue_style( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'jquery-ui-timepicker-addon', AFW()->plugin_url() . '/admin/css/jquery-ui-timepicker-addon.min.css', array( 'woocommerce_admin_styles' ), $this->version, 'all' );
			}
		}
		wp_enqueue_style( $this->plugin_name . '-admin', AFW()->plugin_url() . '/admin/css/auctions-for-woocommerce-admin.css', array( 'woocommerce_admin_styles', 'jquery-ui-style' ), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		if ( 'post-new.php' === $hook || 'post.php' === $hook || 'woocommerce_page_auctions-activity' === $hook ) {

			if ( 'product' === get_post_type() || 'woocommerce_page_auctions-activity' === $hook ) {
				global $post;
				$main_script_dep = array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'timepicker-addon', 'wc-admin-meta-boxes' );
				if ( $post ) {
					$product_data = wc_get_product( $post->ID );
					if ( $product_data && 'auction' === $product_data->get_type() ) {
						wp_enqueue_script(
							'DataTables',
							AFW()->plugin_url() . '/admin/js/DataTables/datatables.min.js',
							array( 'jquery' ),
							'1.10.20',
							true
						);
						wp_enqueue_script(
							'buttons.html5',
							AFW()->plugin_url() . '/admin/js/DataTables/buttons.html5.min.js',
							array( 'jquery', 'DataTables' ),
							'1',
							true
						);
						wp_enqueue_script(
							'buttons.colVis',
							AFW()->plugin_url() . '/admin/js/DataTables/buttons.colVis.min.js',
							array( 'jquery', 'DataTables' ),
							'1',
							true
						);
						$main_script_dep [] = 'DataTables';
					}
				}

				wp_enqueue_script(
					'timepicker-addon',
					AFW()->plugin_url() . '/admin/js/jquery-ui-timepicker-addon.js',
					array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ),
					$this->version,
					true
				);

				wp_register_script(
					$this->plugin_name . '-admin',
					AFW()->plugin_url() . '/admin/js/auctions-for-woocommerce-admin.js',
					$main_script_dep,
					$this->version,
					true
				);

				wp_localize_script(
					$this->plugin_name . '-admin',
					'AFW_Ajax',
					array(
						'ajaxurl'            => admin_url( 'admin-ajax.php' ),
						'AFW_nonce'          => wp_create_nonce( 'AFWajax-nonce' ),
						'calendar_image'     => WC()->plugin_url() . '/assets/images/calendar.png',
						'datatable_language' => array(
							'sEmptyTable'     => __( 'No data available in table', 'wc_simple_auctions' ),
							'sInfo'           => __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'wc_simple_auctions' ),
							'sInfoEmpty'      => __( 'Showing 0 to 0 of 0 entries', 'wc_simple_auctions' ),
							'sInfoFiltered'   => __( '(filtered from _MAX_ total entries)', 'wc_simple_auctions' ),
							'sLengthMenu'     => __( 'Show _MENU_ entries', 'wc_simple_auctions' ),
							'sLoadingRecords' => __( 'Loading...', 'wc_simple_auctions' ),
							'sProcessing'     => __( 'Processing...', 'wc_simple_auctions' ),
							'sSearch'         => __( 'Search:', 'wc_simple_auctions' ),
							'sZeroRecords'    => __( 'No matching records found', 'wc_simple_auctions' ),
							'oPaginate'       => array(
								'sFirst'    => __( 'First', 'wc_simple_auctions' ),
								'sLast'     => __( 'Last', 'wc_simple_auctions' ),
								'sNext'     => __( 'Next', 'wc_simple_auctions' ),
								'sPrevious' => __( 'Previous', 'wc_simple_auctions' ),
							),
							'oAria'           => array(
								'sSortAscending'  => __( ': activate to sort column ascending', 'wc_simple_auctions' ),
								'sSortDescending' => __( ': activate to sort column descending', 'wc_simple_auctions' ),
							),
						),
					)
				);

				wp_enqueue_script( $this->plugin_name . '-admin' );
				wp_enqueue_style( 'jquery-ui-datepicker' );

			}
		}
		if ( 'woocommerce_page_wc-settings' === $hook ) {
			wp_enqueue_script(
				$this->plugin_name . '-settings',
				AFW()->plugin_url() . '/admin/js/auctions-for-woocommerce-settings.js',
				array( 'jquery' ),
				$this->version,
				true
			);
		}

	}



	/**
	 * Hides any admin notices.
	 *
	 * @since 4.0.0
	 * @version 4.0.0
	 */
	public function hide_notices() {
		if ( isset( $_GET['auctions-for-woocommerce-hide-notice'] ) && isset( $_GET['auctions-for-woocommerce-hide-notice-nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( $_GET['auctions-for-woocommerce-hide-notice-nonce'] ), 'auctions-for-woocommerce-hide-notice-nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'auctions-for-woocommerce' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'Cheatin&#8217; huh?', 'woocommerce-gateway-auctions-for-woocommerce' ) );
			}

			$notice = wc_clean( sanitize_key( $_GET['auctions-for-woocommerce-hide-notice'] ) );

			switch ( $notice ) {
				case 'cronjob_main':
					update_option( 'auctions_for_woocommerce_show_cron_main_notice', 'no' );
					break;
				case 'cronjob_mails':
					update_option( 'auctions_for_woocommerce_show_cron_mail_notice', 'no' );
					break;
				case 'cronjob_relist':
					update_option( 'auctions_for_woocommerce_show_cron_relist_notice', 'no' );
					break;
				case 'cronjob_closing_soon':
					update_option( 'auctions_for_woocommerce_show_cron_closing_soon_notice', 'no' );
					break;
				case 'cronjob_reminders_email':
					update_option( 'auctions_for_woocommerce_cron_reminders_email_notice', 'no' );
					break;
			}
		}
	}

	/**
	 * Allow this class and other classes to add slug keyed notices (to avoid duplication).
	 *
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $slug slug for notice.
	 * @param string $class class for notice.
	 * @param string $message message of notice.
	 * @param bolean $dismissible is notice dismissible.
	 **/
	public function add_admin_notice( $slug, $class, $message, $dismissible = false ) {
		$this->notices[ $slug ] = array(
			'class'       => $class,
			'message'     => $message,
			'dismissible' => $dismissible,
		);
	}

	/**
	 * Display any notices we've collected thus far.
	 *
	 * @since 2.0.0
	 * @version 2.0.0
	 */
	public function admin_notices() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		foreach ( (array) $this->notices as $notice_key => $notice ) {
			echo '<div class="' . esc_attr( $notice['class'] ) . '" style="position:relative;">';

			if ( $notice['dismissible'] ) { ?>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'auctions-for-woocommerce-hide-notice', $notice_key ), 'auctions-for-woocommerce-hide-notice-nonce', 'auctions-for-woocommerce-hide-notice-nonce' ) ); ?>" class="woocommerce-message-close notice-dismiss" style="position:absolute;right:1px;padding:9px;text-decoration:none;"></a>
				<?php
			}

			echo '<p>';
			echo wp_kses(
				$notice['message'],
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);
			echo '</p></div>';
		}
	}

	/**
	 * Checks the environment for compatibility problems.  Returns a string with the first incompatibility
	 * found or false if the environment has no problems.
	 *
	 * @since 1.0.0
	 * @version 4.0.0
	 */
	public function get_environment_warning() {
		if ( version_compare( phpversion(), AFW_MIN_PHP, '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = esc_html__( 'Auctions for WooCommerce - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'auctions-for-woocommerce' );

			return sprintf( $message, AFW_MIN_PHP, phpversion() );
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			return esc_html__( 'Auctions for WooCommerce requires WooCommerce to be activated to work.', 'auctions-for-woocommerce' );
		}

		if ( version_compare( WC_VERSION, AFW_MIN_WC, '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = esc_html__( 'Auctions for WooCommerce - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'auctions-for-woocommerce' );

			return sprintf( $message, AFW_MIN_WC, WC_VERSION );
		}

		return false;
	}
	/**
	 * The backup sanity check, in case the plugin is activated in a weird way,
	 * or the environment changes after activation. Also handles upgrade routines.
	 *
	 * @since 1.0.0
	 * @version 4.0.0
	 */
	public function check_environment() {

		$cronjob_documenattion = 'https://wpinstitut.com/auctions-for-woocommerce-documentation/#cronjobs';

		$environment_warning = $this->get_environment_warning();

		if ( $environment_warning && is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			$this->add_admin_notice( 'bad_environment', 'error', $environment_warning );
		}

		if ( get_option( 'auctions_for_woocommerce_cron_check' ) !== 'yes' && ! get_option( 'auctions_for_woocommerce_show_cron_main_notice' ) ) {
			$message = wp_kses(
				/* translators: 1) blog url */
				__( 'Auctions for WooCommerce recommends that you set up a cron job to check finished: <strong>%1$s/?auction-cron=check</strong>. Set it to every minute. See <a href="%2$s">documentation</a> for this!', 'auctions-for-woocommerce' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);
			$this->add_admin_notice( 'cronjob_main', 'notice notice-warning', sprintf( $message, get_bloginfo( 'url' ), $cronjob_documenattion ), true );
		}
		if ( get_option( 'auctions_for_woocommerce_cron_mail' ) !== 'yes' && ! get_option( 'auctions_for_woocommerce_show_cron_mail_notice' ) ) {
			$message = wp_kses(
				/* translators: 1) blog url */
				__( 'Auctions for WooCommerce recommends that you set up a cron job to send emails: <b>%1$s/?auction-cron=mails</b>. Set it every 2 hours. See <a href="%2$s">documentation</a> for this!', 'auctions-for-woocommerce' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);
			$this->add_admin_notice( 'cronjob_mails', 'notice notice-warning', sprintf( $message, get_bloginfo( 'url' ), $cronjob_documenattion ), true );
		}
		if ( get_option( 'auctions_for_woocommerce_cron_relist' ) !== 'yes' && ! get_option( 'auctions_for_woocommerce_show_cron_relist_notice' ) ) {
			$message = wp_kses(
				/* translators: 1) blog url */
				__( 'For automated relisting feature please setup cronjob every 1 hour: <b>%1$s/?auction-cron=relist</b>. See <a href="%2$s">documentation</a> for this!', 'auctions-for-woocommerce' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);
			$this->add_admin_notice( 'cronjob_relist', 'notice notice-warning', sprintf( $message, get_bloginfo( 'url' ), $cronjob_documenattion ), true );
		}
		$auction_closing_soon_settings = get_option( 'woocommerce_auction_closing_soon_settings' );
		if ( get_option( 'auctions_for_woocommerce_cron_relist' ) !== 'yes' && ! get_option( 'auctions_for_woocommerce_show_cron_closing_soon_notice' ) && ( isset( $auction_closing_soon_settings['enabled'] ) && 'yes' === $auction_closing_soon_settings['enabled'] ) ) {
			$message = wp_kses(
				/* translators: 1) blog url */
				__( 'For automated relisting feature please setup cronjob every 1 hour: <b>%1$s/?auction-cron=relist</b>. See <a href="%2$s">documentation</a> for this!', 'auctions-for-woocommerce' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);

			$this->add_admin_notice( 'cron_closing_soon', 'notice notice-warning', sprintf( $message, get_bloginfo( 'url' ), $cronjob_documenattion ), true );
		}
		if ( get_option( 'auctions_for_woocommerce_cron_reminders_email' ) !== 'yes' && ! get_option( 'auctions_for_woocommerce_cron_reminders_email_notice' ) ) {
			$message = wp_kses(
				/* translators: 1) blog url */
				__( 'For automated reminder to pay setup cronjob every day: <b>%1$s/?auction-cron=remind-to-pay</b>. See <a href="%2$s">documentation</a> for this!', 'auctions-for-woocommerce' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);

			$this->add_admin_notice( 'cronjob_reminders_email', 'notice notice-warning', sprintf( $message, get_bloginfo( 'url' ), $cronjob_documenattion ), true );
		}

	}
	/*
	* Auctions settings page
	 */
	public function auctions_settings_class( $settings ) {
		$settings[] = include AFW_ABSPATH . 'admin/class-auctions-for-woocommerce-settings.php';
		return $settings;
	}

	/**
	 * Add to mail class
	 *
	 * @return object
	 *
	 */
	public function add_to_mail_class( $emails ) {

		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-wining.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-failed.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-outbid-note.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-customer-reserve-failed.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-reminde-to-pay.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-buy-now.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-finished.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-bid.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-relist-user.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-relist.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-closing-soon.php';
		include_once AFW_ABSPATH . 'includes/emails/class-wc-email-customer-bid-note.php';

		$emails->emails['WC_Email_Auction_Bid']             = new WC_Email_Auction_Bid();
		$emails->emails['WC_Email_Auction_Buy_Now']         = new WC_Email_Auction_Buy_Now();
		$emails->emails['WC_Email_Auction_Closing_Soon']    = new WC_Email_Auction_Closing_Soon();
		$emails->emails['WC_Email_Auction_Failed']          = new WC_Email_Auction_Failed();
		$emails->emails['WC_Email_Auction_Finished']        = new WC_Email_Auction_Finished();
		$emails->emails['WC_Email_Auction_Relist_User']     = new WC_Email_Auction_Relist_User();
		$emails->emails['WC_Email_Auction_Relist']          = new WC_Email_Auction_Relist();
		$emails->emails['WC_Email_Auction_Reminde_To_Pay']  = new WC_Email_Auction_Reminde_To_Pay();
		$emails->emails['WC_Email_Auction_Wining']          = new WC_Email_Auction_Wining();
		$emails->emails['WC_Email_Customer_Bid_Note']       = new WC_Email_Customer_Bid_Note();
		$emails->emails['WC_Email_Customer_Reserve_Failed'] = new WC_Email_Customer_Reserve_Failed();
		$emails->emails['WC_Email_Auction_Outbid_Note']     = new WC_Email_Auction_Outbid_Note();

		return $emails;
	}

	/**
	 * Search for [vendor] tag in recipients and replace it with author email
	 *
	 */
	public function add_vendor_to_email_recipients( $recipient, $object ) {

		if ( ! is_object( $object ) ) {
			return $recipient;
		}

		$key         = false;
		$author_info = false;
		$arrayrec    = explode( ',', $recipient );
		$post_id     = method_exists( $object, 'get_id' ) ? $object->get_id() : $object->id;
		$post_author = get_post_field( 'post_author', $post_id );

		if ( ! empty( $post_author ) ) {
			$author_info = get_userdata( $post_author );
			$key         = array_search( $author_info->user_email, $arrayrec, true );
		}

		if ( ! $key && $author_info ) {
			$recipient = str_replace( '[vendor]', $author_info->user_email, $recipient );
		} else {
			$recipient = str_replace( '[vendor]', '', $recipient );
		}

		return $recipient;
	}

	public function register_widgets() {

		// Include - no need to use autoload as WP loads them anyway
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-featured-auctions.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-random-auctions.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-recent-auction.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-recently-viewed-auctions.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-ending-soon-auction.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-my-auctions.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-auction-search.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-future-auctions.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-watchlist.php';
		include_once AFW_ABSPATH . 'includes/widgets/class-auctions-for-woocommerce-widget-recent-bids.php';
		// Register widgets
		register_widget( 'Auctions_For_Woocommerce_Widget_Auction_Search' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Ending_Soon_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Recent_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Featured_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Random_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Recently_Viewed_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Ending_Soon_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_My_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Future_Auctions' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Watchlist_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Recent_Bids' );
	}

	/**
	 * Add link to plugin page
	 *
	 * @param  array, string
	 * @return array
	 *
	 */
	public function add_support_link( $links, $file ) {

		if ( AFW_PLUGIN_BASENAME === $file ) {
			$links[] = '<a href="https://wpinstitut.com/auctions-for-woocommerce-documentation/" target="_blank">' . esc_html__( 'Docs', 'auctions-for-woocommerce' ) . '</a>';
			$links[] = '<a href="https://wpinstitut.com/support/" target="_blank">' . esc_html__( 'Premium Support', 'auctions-for-woocommerce' ) . '</a>';
			$links[] = '<a href="https://wpinstitut.com" target="_blank">' . esc_html__( 'More WooCommerce Extensions', 'auctions-for-woocommerce' ) . '</a>';
			return $links;
		}

		return (array) $links;
	}

	/**
	 * Add product type
	 *
	 * @param array
	 * @return array
	 *
	 */
	public function add_product_type( $types ) {
		$types['auction'] = esc_html__( 'Auction', 'auctions-for-woocommerce' );
		return $types;
	}

	/**
	 * Adds a new tab to the Product Data postbox in the admin product interface
	 *
	 * @return array
	 *
	 */
	public function product_write_panel_tab( $product_data_tabs ) {

		$auction_tab = array(

			'auction_tab' => array(
				'label'  => esc_html__( 'Auction', 'auctions-for-woocommerce' ),
				'target' => 'auction_tab',
				'class'  => array( 'auction_tab', 'show_if_auction', 'hide_if_grouped', 'hide_if_external', 'hide_if_variable', 'hide_if_simple' ),
			),
		);

		return $auction_tab + $product_data_tabs;
	}

	/**
	 * Adds the panel to the Product Data postbox in the product interface
	 *
	 * @return void
	 *
	 */
	public function product_write_panel() {

		global $post;
		$product = wc_get_product( $post->ID );

		echo '<div id="auction_tab" class="panel woocommerce_options_panel">';

		woocommerce_wp_select(
			array(
				'id'      => '_auction_item_condition',
				'label'   => esc_html__( 'Item condition', 'auctions-for-woocommerce' ),
				'options' => apply_filters( 'auctions_for_woocommerce_item_condition', $this->auction_item_condition ),
			)
		);
		woocommerce_wp_select(
			array(
				'id'      => '_auction_type',
				'label'   => esc_html__( 'Auction type', 'auctions-for-woocommerce' ),
				'options' => apply_filters( 'auctions_for_woocommerce_type', $this->auction_types ),
			)
		);

		$proxy = in_array( get_post_meta( $post->ID, '_auction_proxy', true ), array( '0', 'yes' ), true ) ? get_post_meta( $post->ID, '_auction_proxy', true ) : get_option( 'auctions_for_woocommerce_proxy_auction_on', 'no' );

		woocommerce_wp_checkbox(
			array(
				'value'         => $proxy,
				'id'            => '_auction_proxy',
				'wrapper_class' => '',
				'label'         => esc_html__( 'Proxy bidding?', 'auctions-for-woocommerce' ),
				'description'   => esc_html__( 'Enable proxy bidding', 'auctions-for-woocommerce' ),
				'desc_tip'      => 'true',
			)
		);

		if ( get_option( 'auctions_for_woocommerce_sealed_on', 'no' ) === 'yes' ) {
			woocommerce_wp_checkbox(
				array(
					'id'            => '_auction_sealed',
					'wrapper_class' => '',
					'label'         => esc_html__( 'Sealed Bid?', 'auctions-for-woocommerce' ),
					'description'   => esc_html__( 'In this type of auction all bidders simultaneously submit sealed bids so that no bidder knows the bid of any other participant. The highest bidder pays the price they submitted. If two bids with same value are placed for auction the one which was placed first wins the auction.', 'auctions-for-woocommerce' ),
					'desc_tip'      => 'true',
				)
			);
		}

		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_start_price',
				'class'             => 'wc_input_price short',
				'label'             => esc_html__( 'Start Price', 'auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type'         => 'price',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_bid_increment',
				'class'             => 'wc_input_price short',
				'label'             => esc_html__( 'Bid increment', 'auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type'         => 'price',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_reserved_price',
				'class'             => 'wc_input_price short',
				'label'             => esc_html__( 'Reserve price', 'auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type'         => 'price',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
				'desc_tip'          => 'true',
				'description'       => esc_html__(
					'A reserve price is the lowest price at which you are willing to sell your item. If you don’t want to sell your item below a certain price, you can set a reserve price. The amount of your reserve price is not disclosed to your bidders, but they will see that your auction has a reserve price and whether or not the reserve has been met. If a bidder does not meet that price, you are not obligated to sell your item. ',
					'auctions-for-woocommerce'
				),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_regular_price',
				'name'        => '_regular_price',
				'class'       => 'wc_input_price short',
				'label'       => esc_html__( 'Buy it now price', 'auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type'   => 'price',
				'desc_tip'    => 'true',
				'description' => esc_html__( 'Buy it now disappears when bid exceeds the Buy now price for normal auction, or is lower than reverse auction', 'auctions-for-woocommerce' ),
			)
		);

		$auction_dates_from = ( get_post_meta( $post->ID, '_auction_dates_from', true ) ) ? get_post_meta( $post->ID, '_auction_dates_from', true ) : '';
		$auction_dates_to   = ( get_post_meta( $post->ID, '_auction_dates_to', true ) ) ? get_post_meta( $post->ID, '_auction_dates_to', true ) : '';

		echo '<p class="form-field auction_dates_fields">
				<label for="_auction_dates_from">' . esc_html__( 'Auction Dates', 'auctions-for-woocommerce' ) . '</label>
				<input type="text" class="short datetimepicker" name="_auction_dates_from" id="_auction_dates_from" value="' . esc_attr( $auction_dates_from ) . '" placeholder="' . esc_html__( 'From&hellip; YYYY-MM-DD HH:MM', 'auctions-for-woocommerce' ) . '" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
				<input type="text" class="short datetimepicker" name="_auction_dates_to" id="_auction_dates_to" value="' . esc_attr( $auction_dates_to ) . '" placeholder="' . esc_html__( 'To&hellip; YYYY-MM-DD HH:MM', 'auctions-for-woocommerce' ) . '" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
			</p>';
		woocommerce_wp_checkbox(
			array(
				'id'            => '_auction_extend_enable',
				'wrapper_class' => '',
				'label'         => esc_html__( 'Extend auction on bid?', 'auctions-for-woocommerce' ),
				'description'   => esc_html__( 'If a bid is placed during the final moments of an auction then the auction can be extended for a specified amount of time. This gives other bidders a chance to compete.', 'auctions-for-woocommerce' ),
				'desc_tip'      => 'true',
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_extend_in_time',
				'class'             => 'input_text',
				'size'              => '10',
				'label'             => __( 'Extend auctions in last * seconds.', 'auctions-for-woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
				'desc_tip'          => 'true',
				'description'       => __( 'Extend auction in last N seconds. Enter 0 to extend always.', 'auctions-for-woocommerce' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_extend_for_time',
				'class'             => 'input_text',
				'size'              => '10',
				'label'             => __( 'Extend auctions for * seconds.', 'auctions-for-woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
				'desc_tip'          => 'true',
				'description'       => __( 'Extend auction for number of seconds.', 'auctions-for-woocommerce' ),
			)
		);
		if ( ( $product->is_type( 'auction' ) ) && $product->get_auction_closed() && ! $product->get_auction_payed() ) {
			echo '<p class="form-field relist_dates_fields"><a class="button relist" href="#" id="relistauction">' . esc_html__( 'Relist', 'auctions-for-woocommerce' ) . '</a></p>
					<p class="form-field relist_auction_dates_fields"> <label for="_relist_auction_dates_from">' . esc_html__( 'Relist Auction Dates', 'auctions-for-woocommerce' ) . '</label>
					<input type="text" class="short datetimepicker" name="_relist_auction_dates_from" id="_relist_auction_dates_from" value="" placeholder="' . esc_html__( 'From&hellip; YYYY-MM-DD HH:MM', 'auctions-for-woocommerce' ) . '" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
					<input type="text" class="short datetimepicker" name="_relist_auction_dates_to" id="_relist_auction_dates_to" value="" placeholder="' . esc_html__( 'To&hellip; YYYY-MM-DD HH:MM', 'auctions-for-woocommerce' ) . '" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />';
			woocommerce_wp_checkbox(
				array(
					'value'         => $proxy,
					'id'            => '_auction_delete_log_on_relist',
					'wrapper_class' => 'relist_auction_dates_fields',
					'label'         => esc_html__( 'Delete logs on relist?', 'auctions-for-woocommerce' ),
					'description'   => esc_html__( "Delete all logs for this auction on relist. It can't be undone!", 'auctions-for-woocommerce' ),
					'desc_tip'      => 'true',
				)
			);
			echo '</p>';
		}

		wp_nonce_field( 'save_auction_data_' . $post->ID, 'save_auction_data' );

		do_action( 'woocommerce_product_options_auction' );

		echo '</div>';
	}

	/**
	 * Saves the data inputed into the product boxes, as post meta data
	 *
	 * @param int $post_id the post (product) identifier
	 *
	 */
	public function product_save_data( $post_id ) {

		if ( ! isset( $_POST['save_auction_data'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['save_auction_data'] ), 'save_auction_data_' . $post_id ) ) {
			wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
		} else {

			$product_type = empty( $_POST['product-type'] ) ? 'simple' : stripslashes( sanitize_text_field( $_POST['product-type'] ) );

			if ( 'auction' === $product_type ) {

				$product = wc_get_product( $post_id );

				update_post_meta( $post_id, '_manage_stock', 'yes' );
				update_post_meta( $post_id, '_stock', '1' );
				update_post_meta( $post_id, '_backorders', 'no' );
				update_post_meta( $post_id, '_sold_individually', 'yes' );

				if ( isset( $_POST['_auction_item_condition'] ) ) {
					update_post_meta( $post_id, '_auction_item_condition', sanitize_text_field( $_POST['_auction_item_condition'] ) );
				}

				if ( isset( $_POST['_auction_type'] ) ) {
					update_post_meta( $post_id, '_auction_type', sanitize_text_field( $_POST['_auction_type'] ) );
				}

				if ( isset( $_POST['_auction_proxy'] ) ) {
					update_post_meta( $post_id, '_auction_proxy', sanitize_text_field( $_POST['_auction_proxy'] ) );
				} else {
					update_post_meta( $post_id, '_auction_proxy', '0' );
				}

				if ( isset( $_POST['_auction_sealed'] ) && ! isset( $_POST['_auction_proxy'] ) ) {
					wp_set_post_terms( $post_id, array( 'sealed' ), 'auction_visibility', true );
				} else {
					wp_remove_object_terms( $post_id, array( 'sealed' ), 'auction_visibility' );
				}

				if ( isset( $_POST['_auction_start_price'] ) ) {
					update_post_meta( $post_id, '_auction_start_price', wc_format_decimal( sanitize_text_field( $_POST['_auction_start_price'] ) ) );
				}

				if ( isset( $_POST['_auction_bid_increment'] ) ) {
					update_post_meta( $post_id, '_auction_bid_increment', wc_format_decimal( sanitize_text_field( $_POST['_auction_bid_increment'] ) ) );
				}

				if ( isset( $_POST['_auction_reserved_price'] ) ) {
					update_post_meta( $post_id, '_auction_reserved_price', wc_format_decimal( sanitize_text_field( $_POST['_auction_reserved_price'] ) ) );
				}

				if ( isset( $_POST['_regular_price'] ) ) {
					update_post_meta( $post_id, '_regular_price', wc_format_decimal( sanitize_text_field( $_POST['_regular_price'] ) ) );
					update_post_meta( $post_id, '_price', wc_format_decimal( sanitize_text_field( $_POST['_regular_price'] ) ) );
				}

				if ( isset( $_POST['_auction_dates_from'] ) ) {
					update_post_meta( $post_id, '_auction_dates_from', sanitize_text_field( $_POST['_auction_dates_from'] ) );
				}

				if ( isset( $_POST['_auction_dates_to'] ) ) {
					update_post_meta( $post_id, '_auction_dates_to', sanitize_text_field( $_POST['_auction_dates_to'] ) );
				}

				if ( isset( $_POST['_relist_auction_dates_from'] ) && isset( $_POST['_relist_auction_dates_to'] ) && ! empty( $_POST['_relist_auction_dates_from'] ) && ! empty( $_POST['_relist_auction_dates_to'] ) ) {
					$this->do_relist( $post_id, sanitize_text_field( $_POST['_relist_auction_dates_from'] ), sanitize_text_field( $_POST['_relist_auction_dates_to'] ) );
				}

				if ( isset( $_POST['_auction_automatic_relist'] ) ) {
					update_post_meta( $post_id, '_auction_automatic_relist', sanitize_text_field( $_POST['_auction_automatic_relist'] ) );
				} else {
					update_post_meta( $post_id, '_auction_automatic_relist', 'no' );
				}

				if ( isset( $_POST['_auction_relist_fail_time'] ) ) {
					update_post_meta( $post_id, '_auction_relist_fail_time', sanitize_text_field( $_POST['_auction_relist_fail_time'] ) );
				}

				if ( isset( $_POST['_auction_relist_not_paid_time'] ) ) {
					update_post_meta( $post_id, '_auction_relist_not_paid_time', sanitize_text_field( $_POST['_auction_relist_not_paid_time'] ) );
				}

				if ( isset( $_POST['_auction_relist_duration'] ) ) {
					update_post_meta( $post_id, '_auction_relist_duration', sanitize_text_field( $_POST['_auction_relist_duration'] ) );
				}

				if ( isset( $_POST['_auction_delete_log_on_auto_relist'] ) ) {
					update_post_meta( $post_id, '_auction_delete_log_on_auto_relist', sanitize_text_field( $_POST['_auction_delete_log_on_auto_relist'] ) );
				}

				if ( isset( $_POST['_auction_extend_enable'] ) ) {
					update_post_meta( $post_id, '_auction_extend_enable', sanitize_text_field( $_POST['_auction_extend_enable'] ) );
				} else {
					update_post_meta( $post_id, '_auction_extend_enable', 'no' );
				}
				if ( isset( $_POST['_auction_extend_in_time'] ) ) {
					update_post_meta( $post_id, '_auction_extend_in_time', sanitize_text_field( intval( $_POST['_auction_extend_in_time'] ) ) );
				}
				if ( isset( $_POST['_auction_extend_for_time'] ) ) {
					update_post_meta( $post_id, '_auction_extend_for_time', sanitize_text_field( intval( $_POST['_auction_extend_for_time'] ) ) );
				}

				$auction_bid_count = get_post_meta( $post_id, '_auction_bid_count', true );
				if ( false === $auction_bid_count ) {
					update_post_meta( $post_id, '_auction_bid_count', '0' );
				}

				update_post_meta( $post_id, '_auction_product_version', $this->version );
				$product->auction_update_lookup_table();
			} else {
				delete_post_meta( $post_id, '_auction_item_condition' );
				delete_post_meta( $post_id, '_auction_type' );
				delete_post_meta( $post_id, '_auction_proxy' );
				delete_post_meta( $post_id, '_auction_start_price' );
				delete_post_meta( $post_id, '_auction_bid_increment' );
				delete_post_meta( $post_id, '_auction_reserved_price' );
				delete_post_meta( $post_id, '_auction_automatic_relist' );
				delete_post_meta( $post_id, '_auction_relist_fail_time' );
				delete_post_meta( $post_id, '_auction_relist_not_paid_time' );
				delete_post_meta( $post_id, '_auction_relist_duration' );
				delete_post_meta( $post_id, '_auction_delete_log_on_auto_relist' );
				delete_post_meta( $post_id, '_auction_current_bid' );
				delete_post_meta( $post_id, '_auction_current_bider' );
				delete_post_meta( $post_id, '_auction_max_bid' );
				delete_post_meta( $post_id, '_auction_max_current_bider' );
				delete_post_meta( $post_id, '_auction_bid_count' );
				delete_post_meta( $post_id, '_auction_closed' );
				delete_post_meta( $post_id, '_auction_started' );
				delete_post_meta( $post_id, '_auction_has_started' );
				delete_post_meta( $post_id, '_auction_fail_reason' );
				delete_post_meta( $post_id, '_auction_dates_to' );
				delete_post_meta( $post_id, '_auction_dates_from' );
				delete_post_meta( $post_id, '_order_id' );
				delete_post_meta( $post_id, '_stop_mails' );
				delete_post_meta( $post_id, '_auction_payed' );
				delete_post_meta( $post_id, '_auction_sealed' );
			}
		}
	}

	public function do_relist( $post_id, $relist_from, $relist_to, $automatic = false ) {

		global $wpdb;

		update_post_meta( $post_id, '_auction_dates_from', wc_clean( $relist_from ) );
		update_post_meta( $post_id, '_auction_dates_to', wc_clean( $relist_to ) );
		update_post_meta( $post_id, '_auction_relisted', current_time( 'mysql' ) );
		update_post_meta( $post_id, '_manage_stock', 'yes' );
		update_post_meta( $post_id, '_stock', '1' );
		update_post_meta( $post_id, '_stock_status', 'instock' );
		update_post_meta( $post_id, '_backorders', 'no' );
		update_post_meta( $post_id, '_sold_individually', 'yes' );
		delete_post_meta( $post_id, '_auction_closed' );
		delete_post_meta( $post_id, '_auction_started' );
		delete_post_meta( $post_id, '_auction_fail_reason' );
		delete_post_meta( $post_id, '_auction_current_bid' );
		delete_post_meta( $post_id, '_auction_current_bider' );
		delete_post_meta( $post_id, '_auction_max_bid' );
		delete_post_meta( $post_id, '_auction_max_current_bider' );
		delete_post_meta( $post_id, '_stop_mails' );
		delete_post_meta( $post_id, '_stop_mails' );
		delete_post_meta( $post_id, '_auction_bid_count' );
		delete_post_meta( $post_id, '_auction_sent_closing_soon' );
		delete_post_meta( $post_id, '_auction_sent_closing_soon2' );
		delete_post_meta( $post_id, '_auction_fail_email_sent' );
		delete_post_meta( $post_id, '_Reserve_fail_email_sent' );
		delete_post_meta( $post_id, '_auction_win_email_sent' );
		delete_post_meta( $post_id, '_auction_finished_email_sent' );
		delete_post_meta( $post_id, '_auction_has_started' );
		delete_post_meta( $post_id, '_auction_payed' );

		wp_remove_object_terms( $post_id, array( 'future', 'finished', 'sold', 'buy-now', 'started' ), 'auction_visibility' );

		$order_id = get_post_meta( $post_id, '_order_id', true );
		// check if the custom field has a value.
		if ( false !== $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$order->update_status( 'failed', esc_html__( 'Failed because off relisting', 'auctions-for-woocommerce' ) );
			}
			delete_post_meta( $post_id, '_order_id' );
		}

		$wpdb->delete(
			$wpdb->usermeta,
			array(
				'meta_key'   => 'wsa_my_auctions',
				'meta_value' => $post_id,
			),
			array( '%s', '%s' )
		);

		if ( ! empty( $_POST['_auction_delete_log_on_relist'] ) ) {

			if ( ! isset( $_POST['save_auction_data'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['save_auction_data'] ), 'save_auction_data_' . $post_id ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			} else {
				if ( 'yes' === sanitize_text_field( $_POST['_auction_delete_log_on_relist'] ) ) {
					$this->del_auction_logs( $post_id );
				}
			}
		}
		do_action( 'auctions_for_woocommerce_do_relist', $post_id, $relist_from, $relist_to );
	}

	public function relist_auction( $post_id ) {

		$product = wc_get_product( $post_id );

		if ( 'yes' === $product->get_auction_automatic_relist() && $product->is_finished() && $product->get_auction_relist_duration() ) {

			$from_time = gmdate( 'Y-m-d H:i', current_time( 'timestamp' ) );
			$to_time   = gmdate( 'Y-m-d H:i', current_time( 'timestamp' ) + ( $product->get_auction_relist_duration() * 3600 ) );

			if ( 1 === $product->get_auction_closed() && $product->get_auction_relist_fail_time() && $product->auction_relist_duration ) {

				if ( current_time( 'timestamp' ) > ( strtotime( $product->get_auction_dates_to() ) + ( $product->get_auction_relist_fail_time() * 3600 ) ) ) {

					do_action( 'woocomerce_before_relist_failed_auction', $post_id );
					$this->do_relist( $post_id, $from_time, $to_time, true );
					if ( $product->get_auction_delete_log_on_auto_relist() ) {
						$this->del_auction_logs( $post_id );
					}
					do_action( 'woocomerce_after_relist_failed_auction', $post_id );
					return;

				}
			}
			if ( 2 === $product->get_auction_closed() && $product->get_auction_relist_not_paid_time() && $product->get_auction_relist_duration() ) {

				if ( current_time( 'timestamp' ) > ( strtotime( $product->get_auction_dates_to() ) + ( $product->get_auction_relist_not_paid_time() * 3600 ) ) ) {

					do_action( 'woocomerce_before_relist_not_paid_auction', $post_id );
					$this->do_relist( $post_id, $from_time, $to_time, true );
					if ( $product->get_auction_delete_log_on_auto_relist() ) {
						$this->del_auction_logs( $post_id );
					}
					do_action( 'woocomerce_after_relist_not_paid_auction', $post_id );
					return;

				}
			}
		}
	}

	/**
	 * Auction order
	 *
	 * Checks for auction product in order and assign order id to auction product
	 *
	 * @param int, array
	 * @return void
	 */
	public function auction_order( $order_id, $posteddata ) {

		$order = wc_get_order( $order_id );

		if ( $order ) {

			$order_items = $order->get_items();

			if ( $order_items ) {
				foreach ( $order_items as $item_id => $item ) {
					if ( function_exists( 'wc_get_order_item_meta' ) ) {
						$item_meta = wc_get_order_item_meta( $item_id, '' );
					} else {
						$item_meta = method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					}
					$product_id   = $item_meta['_product_id'][0];
					$product_data = wc_get_product( $product_id );
					if ( $product_data && $product_data->is_type( 'auction' ) ) {
						update_post_meta( $order_id, '_auction', '1' );
						update_post_meta( $product_id, '_order_id', $order_id, true );
						update_post_meta( $product_id, '_stop_mails', '1' );
						if ( ! $product_data->is_finished() ) {
							wp_set_post_terms( $product_id, array( 'buy-now', 'finished' ), 'auction_visibility', true );
							update_post_meta( $product_id, '_buy_now', '1' );
							update_post_meta( $product_id, '_auction_dates_to', gmdate( 'Y-m-h h:s' ) );
							do_action( 'auctions_for_woocommerce_close_buynow', $product_id );
						}
					}
				}
			}
		}
	}

	/**
	 * Auction paid
	 *
	 * Checks for a auction product in order to verify that it was paid and assign order id to auction product and auction paid meta
	 *
	 * @param int
	 * @return void
	 *
	 */
	public function auction_payed( $order_id ) {

			$order = wc_get_order( $order_id );

		if ( isset( $order ) && $order ) {
			$order_items = $order->get_items();

			if ( $order_items ) {

				foreach ( $order_items as $item_id => $item ) {

					$item_meta    = method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					$product_data = wc_get_product( $item_meta['_product_id'][0] );

					if ( $product_data && $product_data->is_type( 'auction' ) ) {

							update_post_meta( $item_meta['_product_id'][0], '_auction_payed', 1, true );
							update_post_meta( $item_meta['_product_id'][0], '_order_id', $order_id, true );
							update_post_meta( $item_meta['_product_id'][0], '_stop_mails', '1' );

					}
				}
			}
		}

	}

	/**
	 * Auction canceled
	 *
	 * Checks for a auction product in canceled order
	 *
	 * @param int
	 * @return void
	 *
	 */
	public function auction_order_canceled( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( isset( $order ) && $order ) {
			$order_items = $order->get_items();

			if ( $order_items ) {

				foreach ( $order_items as $item_id => $item ) {

					$item_meta    = method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					$product_data = wc_get_product( $item_meta['_product_id'][0] );

					if ( $product_data && $product_data->is_type( 'auction' ) ) {

							delete_post_meta( $item_meta['_product_id'][0], '_auction_payed' );

					}
				}
			}
		}

	}

	/**
	 * Duplicate post
	 *
	 * Clear metadata when copy auction
	 *
	 * @param  array
	 * @return string
	 *
	 */
	public function duplicate_product( $postid ) {

		$product = wc_get_product( $postid );

		if ( ! $product ) {
			return false;
		}

		if ( ! $product->is_type( 'auction' ) ) {
			return false;
		}

		delete_post_meta( $postid, '_auction_current_bid' );
		delete_post_meta( $postid, '_auction_current_bider' );
		delete_post_meta( $postid, '_auction_max_bid' );
		delete_post_meta( $postid, '_auction_max_current_bider' );
		delete_post_meta( $postid, '_auction_bid_count' );
		delete_post_meta( $postid, '_auction_closed' );
		delete_post_meta( $postid, '_auction_started' );
		delete_post_meta( $postid, '_auction_has_started' );
		delete_post_meta( $postid, '_auction_fail_reason' );
		delete_post_meta( $postid, '_auction_dates_to' );
		delete_post_meta( $postid, '_auction_dates_from' );
		delete_post_meta( $postid, '_order_id' );
		delete_post_meta( $postid, '_stop_mails' );
		delete_post_meta( $postid, '_auction_payed' );
		delete_post_meta( $postid, '_stock_status' );
		update_post_meta( $postid, '_stock_status', 'instock' );
		update_post_meta( $postid, '_stock', '1' );

		return true;
	}


	/**
	 * Ouput custom columns for products.
	 *
	 * @param string $column
	 * @return void
	 */
	public function render_product_columns( $column ) {

		global $post, $the_product;

		if ( empty( $the_product ) || $the_product->get_id() !== $post->ID ) {
			$the_product = wc_get_product( $post );
		}

		if ( 'product_type' === $column ) {

			if ( $the_product && $the_product->is_type( 'auction' ) ) {
					$class = '';
					$title = '';
				if ( $the_product->is_closed() ) {
					$class .= ' finished ';
					$title .= ' finished ';
				}
				if ( 1 === $the_product->get_auction_fail_reason() ) {
					$class .= ' no_bid fail ';
					$title .= ' no bid, fai ';
				}
				if ( 2 === $the_product->get_auction_fail_reason() ) {
					$class .= ' no_reserve fail';
					$title .= ' no reserve , fail ';
				}
				if ( 3 === $the_product->get_auction_closed() ) {
					$class .= ' sold ';
					$title .= ' sold ';
				}
				if ( $the_product->get_auction_payed() ) {
					$class .= ' payed ';
					$title .= ' payed ';
				}
				// translators: 1) Auction state
				echo '<span class="auction-status ' . esc_attr( $class ) . '" title="' . sprintf( esc_html__( 'Auction %s', 'auctions-for-woocommerce' ), esc_attr( $title ) ) . '" ></span>';
			}
		}
	}

	/**
	 *  Add meta box to the product editing screen
	 *
	 */
	public function auctions_for_woocommerce_meta() {

		global $post;
		$product_data = wc_get_product( $post->ID );

		if ( $product_data && $product_data->is_type( 'auction' ) ) {
			add_meta_box( 'Auction', esc_html__( 'Auction', 'auctions-for-woocommerce' ), array( $this, 'auctions_for_woocommerce_meta_callback' ), 'product', 'normal' );
		}

	}

	/**
	 *  Callback for adding a meta box to the product editing screen used in auctions_for_woocommerce_meta
	 *
	 */
	public function auctions_for_woocommerce_meta_callback() {

		global $post;

		$product_data     = wc_get_product( $post->ID );
		$auction_relisted = $product_data->get_auction_relisted();

		if ( isset( $auction_relisted ) && ( ! empty( $auction_relisted ) ) ) {
			echo '<p>' . esc_html__( 'Auction has been relisted on:', 'auctions-for-woocommerce' ) . ' ' . esc_html( $auction_relisted ) . '</p>';
		}
		if ( ( true === $product_data->is_closed() ) && ( true === $product_data->is_started() ) ) {
			echo '<p>' . esc_html__( 'Auction has finished', 'auctions-for-woocommerce' ) . '</p>';
			if ( 1 === $product_data->get_auction_fail_reason() ) {
				echo '<p>' . esc_html__( 'Auction failed because there were no bids', 'auctions-for-woocommerce' ) . '</p>';
			} elseif ( 2 === $product_data->get_auction_fail_reason() ) {
				echo '<p class="reservefail">' . esc_html__( 'Auction failed because item did not make it to reserve price', 'auctions-for-woocommerce' ) . ' <a class="removereserve" href="#" data-postid="' . intval( $post->ID ) . '">' . esc_html__( 'Remove reserve price', 'auctions-for-woocommerce' ) . '</a></p>';
			}
			if ( 3 === $product_data->get_auction_closed() ) {
				echo '<p>' . esc_html__( 'Product sold for buy now price', 'auctions-for-woocommerce' ) . ': <span>' . wp_kses_post( wc_price( $product_data->get_regular_price() ) ) . '</span></p>';
			} elseif ( $product_data->get_auction_current_bider() ) {

					echo '<p>' . esc_html__( 'Highest bidder was', 'auctions-for-woocommerce' ) . ': <span class="higestbider"><a href="' . esc_url( get_edit_user_link( $product_data->get_auction_current_bider() ) ) . '">' . esc_html( get_userdata( $product_data->get_auction_current_bider() )->display_name ) . '</a></span></p>';
					echo '<p>' . esc_html__( 'Highest bid was', 'auctions-for-woocommerce' ) . ': <span class="higestbid" >' . wp_kses_post( wc_price( $product_data->get_curent_bid() ) ) . '</span></p>';

				if ( $product_data->get_auction_payed() ) {
					echo '<p>' . esc_html__( 'Order has been paid, order ID is', 'auctions-for-woocommerce' ) . ': <span><a href="post.php?&action=edit&post=' . intval( $product_data->get_order_id() ) . '">' . intval( $product_data->get_order_id() ) . '</a></span></p>';
				} elseif ( $product_data->get_order_id() ) {
					$order = wc_get_order( $product_data->get_order_id() );
					if ( $order ) {
						$order_status = $order->get_status() ? $order->get_status() : esc_html__( 'unknown', 'auctions-for-woocommerce' );
						echo '<p>' . esc_html__( 'Order has been made, order status is', 'auctions-for-woocommerce' ) . ': <a href="post.php?&action=edit&post=' . intval( $product_data->get_order_id() ) . '">' . esc_html( $order_status ) . '</a><span>';
					}
				} elseif ( 2 === $product_data->get_auction_closed() ) {
					echo '<p><button class="button button-primary" href="#"" id="wsa-resend-winning-email" data-product_id="' . intval( $product_data->get_id() ) . '">' . esc_html__( 'Send another winning mail to user?', 'auctions-for-woocommerce' ) . '</button></p>';
					echo '<div id="resend-status"></div>';
				}
			}
			if ( $product_data->get_number_of_sent_mails() ) {
				$dates_of_sent_mail = get_post_meta( $product_data->get_id(), '_dates_of_sent_mails', false );
				echo '<p>' . esc_html__( 'Number of sent reminder emails', 'auctions-for-woocommerce' ) . ': <span> ' . intval( $product_data->get_number_of_sent_mails() ) . '</span></p>';
				echo '<p>' . esc_html__( 'Last reminder mail was sent on', 'auctions-for-woocommerce' ) . ': <span> ' . esc_html( gmdate( 'Y-m-d', end( $dates_of_sent_mail ) ) ) . '</span></p>';
				echo '<p class="reminder-status">' . esc_html__( 'Reminder status', 'auctions-for-woocommerce' ) . ':';
				if ( $product_data->get_stop_mails() ) {
					echo '<span class="error">' . esc_html__( 'Stopped', 'auctions-for-woocommerce' ) . '</span>';
				} else {
					echo '<span class="ok">' . esc_html__( 'Running', 'auctions-for-woocommerce' ) . '</span>';
				}
				echo '</p>';
			}
		}
		if ( ( false === $product_data->is_closed() ) && ( true === $product_data->is_started() ) ) {
			if ( $product_data->get_auction_proxy() ) {
				echo '<p>' . esc_html__( 'This is proxy auction', 'auctions-for-woocommerce' ) . '</p>';
				if ( $product_data->get_auction_max_bid() && $product_data->get_auction_max_current_bider() ) {
					echo '<p>' . esc_html__( 'Maximum bid is', 'auctions-for-woocommerce' ) . ' ' . floatval( $product_data->get_auction_max_bid() ) . ' ' . esc_html__( 'by', 'auctions-for-woocommerce' ) . ' <a href="' . esc_url( get_edit_user_link( $product_data->get_auction_max_current_bider() ) ) . '">' . esc_html( get_userdata( $product_data->get_auction_max_current_bider() )->display_name ) . '</a></p>';
				}
			}
		}
		echo '<h2>' . esc_html( apply_filters( 'woocommerce_auction_history_heading', esc_html__( 'Auction History', 'auctions-for-woocommerce' ) ) ) . '</h2>';
		echo '<table class="auction-table widefat fixed">';

		$auction_history = apply_filters( 'woocommerce__auction_history_data', $product_data->auction_history() );

		if ( ! empty( $auction_history ) ) {
			echo '<thead><tr>';
			echo '<th>' . esc_html__( 'Date', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'Bid', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'User', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'Email', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'First name', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'Last name', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'Address', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'Auto', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th class="actions">' . esc_html__( 'Actions', 'auctions-for-woocommerce' ) . '</th>';

			do_action( 'auctions_for_woocommerce_admin_history_header', $product_data, $auction_history );

			echo '</tr></thead>';

			foreach ( $auction_history as $history_value ) {
				if ( $history_value->date < $product_data->get_auction_relisted() && ! isset( $displayed_relist ) ) {
					echo '<tfoot>';
					echo '<tr>';
					echo '<td class="date">' . esc_html( $product_data->get_auction_start_time() ) . '</td>';
					echo '<td colspan="8"  class="relist">';
					esc_html_e( 'Auction relisted', 'wc_simple_auctions' );
					echo '</td>';
					echo '</tr>';
					echo '</tfoot>';
					echo '</table>';

					echo '<h2 class="old_auctions_data">' . esc_html__( 'Auction Data Prior Relist', 'wc_simple_auctions' ) . '</h2>';
					echo '<table class="auction-table widefat fixed">';
					echo '<thead><tr>';
					echo '<th>' . esc_html__( 'Date', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th>' . esc_html__( 'Bid', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th>' . esc_html__( 'User', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th>' . esc_html__( 'Email', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th>' . esc_html__( 'First name', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th>' . esc_html__( 'Last name', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th>' . esc_html__( 'Address', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th>' . esc_html__( 'Auto', 'auctions-for-woocommerce' ) . '</th>';
					echo '<th class="actions">' . esc_html__( 'Actions', 'auctions-for-woocommerce' ) . '</th>';
					echo '</tr></thead>';
					$displayed_relist = true;
				}
				echo '<tr>';
				echo '<td class="date">' . esc_html( $history_value->date ) . '</td>';
				echo '<td class="bid">' . esc_html( $history_value->bid ) . '</td>';
				$customer  = new WC_Customer( $history_value->userid );
				$user_data = get_userdata( $history_value->userid );
				echo "<td class='username'><a href='" . esc_url( get_edit_user_link( $history_value->userid ) ) . "'>" . esc_html( get_userdata( $history_value->userid )->display_name ) . '</a></td>';
				echo "<td class='email'>" . esc_html( $user_data ? $user_data->user_email : '' ) . '</td>';
				echo "<td class='firstname'>" . esc_html( $customer ? $customer->get_first_name() : '' ) . '</td>';
				echo "<td class='lastname'>" . esc_html( $customer ? $customer->get_last_name() : '' ) . '</td>';
				echo '<td class="addres">' . esc_html( $customer ? $customer->get_billing_address() : '' ) . esc_html( $customer && $customer->get_billing_city() ? ', ' . $customer->get_billing_city() : '' ) . esc_html( $customer && $customer->get_billing_postcode() ? ', ' . $customer->get_billing_postcode() : '' ) . esc_html( $customer && $customer->get_billing_country() ? ', ' . $customer->get_billing_country() : '' ) . '</td>';
				if ( 1 === intval( $history_value->proxy ) ) {
					echo " <td class='proxy'>" . esc_html__( 'Auto', 'auctions-for-woocommerce' ) . '</td>';
				} else {
					echo " <td class='proxy'></td>";
				}

				echo '<td class="action"> <a href="#" data-id="' . intval( $history_value->id ) . '" data-postid="' . intval( $post->ID ) . '" >' . esc_html__( 'Delete', 'auctions-for-woocommerce' ) . '</a></td>';

				do_action( 'auctions_for_woocommerce_admin_history_row', $product_data, $history_value );

				echo '</tr>';

			}
		}

		echo '<tfoot><tr class="start">';
		if ( $product_data->is_started() === true ) {
			echo '<td class="date">' . esc_html( $product_data->get_auction_start_time() ) . '</td>';
			echo '<td colspan="8"  class="started">';
			echo esc_html( apply_filters( 'auction_history_started_text', esc_html__( 'Auction started', 'auctions-for-woocommerce' ), $product_data ) );
			echo '</td>';

		} else {
			echo '<td  class="date">' . esc_html( $product_data->get_auction_start_time() ) . '</td>';
			echo '<td colspan="8"  class="starting">';
			echo esc_html( apply_filters( 'auction_history_starting_text', esc_html__( 'Auction starting', 'auctions-for-woocommerce' ), $product_data ) );
			echo '</td>';
		}
		echo '</tr></tfoot></table></ul>';
	}

	/**
	 *  Add auction relist meta box to the product editing screen
	 *
	 */
	public function auctions_for_woocommerce_automatic_relist() {

		add_meta_box( 'Automatic_relist_auction', esc_html__( 'Automatic relist auction', 'auctions-for-woocommerce' ), array( $this, 'auctions_for_woocommerce_automatic_relist_callback' ), 'product', 'normal' );

	}
	/**
	 *  Callback for adding a meta box to the product editing screen used for automatic relist
	 *
	 */
	public function auctions_for_woocommerce_automatic_relist_callback() {

		global $post;
		$product_data = wc_get_product( $post->ID );
		$heading      = esc_html( apply_filters( 'woocommerce_auction_history_heading', __( 'Auction automatic relist', 'auctions-for-woocommerce' ) ) );

		echo '<div class="woocommerce_options_panel ">';
		woocommerce_wp_checkbox(
			array(
				'id'            => '_auction_automatic_relist',
				'wrapper_class' => '',
				'label'         => esc_html__( 'Automatic relist auction', 'auctions-for-woocommerce' ),
				'description'   => esc_html__( 'Enable automatic relisting', 'auctions-for-woocommerce' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_relist_fail_time',
				'class'             => 'wc_input_price short',
				'label'             => esc_html__( 'Relist if fail after n hours', 'auctions-for-woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_relist_not_paid_time',
				'class'             => 'wc_input_price short',
				'label'             => esc_html__( 'Relist if not paid after n hours', 'auctions-for-woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => '_auction_relist_duration',
				'class'             => 'wc_input_price short',
				'label'             => esc_html__( 'Relist auction duration in h', 'auctions-for-woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '0',
				),
			)
		);
		woocommerce_wp_checkbox(
			array(
				'id'            => '_auction_delete_log_on_auto_relist',
				'wrapper_class' => '',
				'label'         => esc_html__( 'Delete logs?', 'auctions-for-woocommerce' ),
				'description'   => esc_html__( "Delete all logs for this auction on automatic relist. It can't be undone!", 'auctions-for-woocommerce' ),
				'desc_tip'      => 'true',
			)
		);

		echo '</div>';
	}

	/**
	 * Add dropdown to filter auctions
	 *
	 * @return void
	 */
	public function admin_posts_filter_restrict_manage_posts() {

		if ( isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) {
			$values = array(
				esc_html__( 'Active', 'auctions-for-woocommerce' )   => 'active',
				esc_html__( 'Finished', 'auctions-for-woocommerce' ) => 'finished',
				esc_html__( 'Fail', 'auctions-for-woocommerce' )     => 'fail',
				esc_html__( 'Sold', 'auctions-for-woocommerce' )     => 'sold',
				esc_html__( 'Paid', 'auctions-for-woocommerce' )     => 'payed',
			);
			?>
			<select name="wsa_filter">
			<option value=""><?php esc_html_e( 'Auction filter By ', 'auctions-for-woocommerce' ); ?></option>
						<?php
						$current_v = isset( $_GET['wsa_filter'] ) ? sanitize_text_field( $_GET['wsa_filter'] ) : '';
						foreach ( $values as $label => $value ) {
							printf( '<option value="%s"%s>%s</option>', esc_attr( $value ), $value === $current_v ? ' selected="selected"' : '', esc_html( $label ) );
						}
						?>
			</select>
			<?php
		}
	}

	/**
	 * If submitted filter by post meta
	 *
	 * Make sure to change META_KEY to the actual meta key
	 * and POST_TYPE to the name of your custom post type
	 *
	 * @param  (wp_query object) $query
	 * @return void
	 */
	public function admin_posts_filter( $query ) {
		global $pagenow;

		if ( isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] && is_admin() && 'edit.php' === $pagenow && ! empty( $_GET['wsa_filter'] ) ) {

			$taxquery = $query->get( 'tax_query' );
			if ( ! is_array( $taxquery ) ) {
				$taxquery = array();
			}

			$taxquery[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'auction',
			);
			if ( ! is_array( $taxquery ) ) {
				$tax_query = array(
					'relation' => 'AND',
				);
			}
			$auction_visibility_terms  = wc_get_auction_visibility_term_ids();
			$product_visibility_not_in = array();
			$product_visibility_in     = array();

			switch ( $_GET['wsa_filter'] ) {
				case 'active':
					$product_visibility_not_in[] = $auction_visibility_terms['finished'];
					$product_visibility_not_in[] = $auction_visibility_terms['sold'];
					$product_visibility_not_in[] = $auction_visibility_terms['buy-now'];
					break;
				case 'finished':
					$product_visibility_in[] = $auction_visibility_terms['finished'];
					$product_visibility_in[] = $auction_visibility_terms['sold'];
					$product_visibility_in[] = $auction_visibility_terms['buy-now'];
					break;
				case 'fail':
					$product_visibility_in[]     = $auction_visibility_terms['finished'];
					$product_visibility_not_in[] = $auction_visibility_terms['sold'];
					$product_visibility_not_in[] = $auction_visibility_terms['buy-now'];
					break;
				case 'sold':
					$product_visibility_in[]         = $auction_visibility_terms['sold'];
					$query->query_vars['meta_query'] = array(
						array(
							'key'     => '_auction_payed',
							'compare' => 'NOT EXISTS',
						),
					);
					break;
				case 'payed':
					$query->query_vars['meta_key']   = '_auction_payed';
					$query->query_vars['meta_value'] = '1';
					break;
			}

			if ( ! empty( $product_visibility_not_in ) ) {
				$taxquery[] = array(
					'taxonomy' => 'auction_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_not_in,
					'operator' => 'NOT IN',
				);
			}
			if ( ! empty( $product_visibility_in ) ) {
				$taxquery[] = array(
					'taxonomy' => 'auction_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_in,
					'operator' => 'IN',
				);
			}
			$query->set( 'tax_query', $taxquery );
		}
	}

	/**
	 *
	 * Add auctions activity page
	 *
	 * @param void
	 * @return void
	 *
	*/
	public function add_auction_activity_page() {

		$hook = add_submenu_page( 'woocommerce', esc_html__( 'Auctions activity', 'auctions-for-woocommerce' ), esc_html__( 'Auctions activity', 'auctions-for-woocommerce' ), 'manage_woocommerce', 'auctions-activity', array( $this, 'create_admin_page' ) );

		add_action( "load-$hook", array( $this, 'log_list_add_options' ) );
	}

	/**
	*
	* Options page callback
	*
	* @param void
	* @return void
	*
	*/
	public function create_admin_page() {

		if ( ! empty( $_GET['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {

			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( sanitize_text_field( $_SERVER['REQUEST_URI'] ) ) ) );
			exit;
		}

		echo '<div class="wrap" id="wpse-list-table"><h2>' . esc_html__( 'Auction activity', 'auctions-for-woocommerce' ) . '</h2>';
		echo '<form id="wpse-list-table-form" method="get">';
		$wp_list_table = new Auctions_For_Woocommerce_Activity_List();
		$wp_list_table->prepare_items();

		if ( ! empty( $_GET['s'] ) ) {
			echo '<input type="hidden" name="s" value="' . esc_attr( sanitize_text_field( $_GET['s'] ) ) . '" />';
		}
		$wp_list_table->search_box( 'search', 'search_id' );
		if ( ! empty( $_GET['page'] ) ) {
			echo '<input type="hidden" name="page" value="' . esc_attr( sanitize_text_field( $_GET['page'] ) ) . '" />';
		}
		$wp_list_table->datepicker();
		$wp_list_table->display();
		echo '</form>';
		echo '</div>';
		echo '<div class="clear"></div>';

	}

	public function log_list_add_options() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Logs',
			'default' => 20,
			'option'  => 'logs_per_page',
		);

		add_screen_option( $option, $args );
	}

	public function auctions_for_woocommerce_set_option( $status, $option, $value ) {
		if ( 'logs_per_page' === $option ) {
			return $value;
		}

		return $status;
	}


	/**
	 * Delete logs when auction is deleted
	 *
	 * @param  string
	 * @return void
	 *
	 */
	public function del_auction_logs( $post_id ) {
		global $wpdb;

		$logs = $wpdb->get_var( $wpdb->prepare( 'SELECT auction_id FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE auction_id = %d', $post_id ) );

		if ( $logs ) {
			return $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE auction_id = %d', $post_id ) );
		}

		return true;
	}


}
