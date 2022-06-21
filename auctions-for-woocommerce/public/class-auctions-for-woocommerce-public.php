<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpinstitut.com/
 * @since      1.0.0
 *
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/public
 */
class Auctions_For_Woocommerce_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( get_option( 'auctions_for_woocommerce_live_bid_notifications_enable', 'yes' ) === 'yes' ) {
			wp_enqueue_style( 'noty', AFW()->plugin_url() . '/public/js/noty/noty.css', array(), '3.2.0' );
			wp_enqueue_style( 'noty-theme', AFW()->plugin_url() . '/public/js/noty/themes/' . get_option( 'auctions_for_woocommerce_live_notifications_theme', 'bootstrap-v4' ) . '.css', array( 'noty' ), '3.2.0' );
		}
		wp_enqueue_style( $this->plugin_name, AFW()->plugin_url() . '/public/css/auctions-for-woocommerce-public.css', array( 'dashicons' ), $this->version );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( 'autoNumeric', AFW()->plugin_url() . '/public/js/autoNumeric.min.js', array( 'jquery' ), '2.0.13', false );
		$currency_pos = get_option( 'woocommerce_currency_pos' );
		switch ( $currency_pos ) {
			case 'left':
				$currency_symbol_placement = 'p';
				$currency_symbol           = get_woocommerce_currency_symbol();
				break;
			case 'right':
				$currency_symbol_placement = 's';
				$currency_symbol           = get_woocommerce_currency_symbol();
				break;
			case 'left_space':
				$currency_symbol_placement = 'p';
				$currency_symbol           = get_woocommerce_currency_symbol() . ' ';
				break;
			case 'right_space':
				$currency_symbol_placement = 's';
				$currency_symbol           = ' ' . get_woocommerce_currency_symbol();
				break;
		}
		$currency_data = array(
			'currencySymbolPlacement' => $currency_symbol_placement,
			'digitGroupSeparator'     => wc_get_price_thousand_separator(),
			'decimalCharacter'        => wc_get_price_decimal_separator(),
			'currencySymbol'          => $currency_symbol,
			'decimalPlacesOverride'   => wc_get_price_decimals(),
		);
		wp_localize_script( 'autoNumeric', 'autoNumericdata', $currency_data );

		wp_enqueue_script( 'jquery-plugin', AFW()->plugin_url() . '/public/js/jquery.plugin.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name . '-countdown', AFW()->plugin_url() . '/public/js/jquery.countdown.min.js', array( 'jquery', 'jquery-plugin' ), $this->version, false );

		wp_register_script( $this->plugin_name . '-countdown-language', AFW()->plugin_url() . '/public/js/jquery.countdown.language.js', array( 'jquery', $this->plugin_name . '-countdown' ), $this->version, false );

		$language_data = array(
			'labels'        => array(
				'Years'   => esc_html__( 'Years', 'auctions-for-woocommerce' ),
				'Months'  => esc_html__( 'Months', 'auctions-for-woocommerce' ),
				'Weeks'   => esc_html__( 'Weeks', 'auctions-for-woocommerce' ),
				'Days'    => esc_html__( 'Days', 'auctions-for-woocommerce' ),
				'Hours'   => esc_html__( 'Hours', 'auctions-for-woocommerce' ),
				'Minutes' => esc_html__( 'Minutes', 'auctions-for-woocommerce' ),
				'Seconds' => esc_html__( 'Seconds', 'auctions-for-woocommerce' ),
			),
			'labels1'       => array(
				'Year'   => esc_html__( 'Year', 'auctions-for-woocommerce' ),
				'Month'  => esc_html__( 'Month', 'auctions-for-woocommerce' ),
				'Week'   => esc_html__( 'Week', 'auctions-for-woocommerce' ),
				'Day'    => esc_html__( 'Day', 'auctions-for-woocommerce' ),
				'Hour'   => esc_html__( 'Hour', 'auctions-for-woocommerce' ),
				'Minute' => esc_html__( 'Minute', 'auctions-for-woocommerce' ),
				'Second' => esc_html__( 'Second', 'auctions-for-woocommerce' ),
			),
			'compactLabels' => array(
				'y' => esc_html__( 'y', 'auctions-for-woocommerce' ),
				'm' => esc_html__( 'm', 'auctions-for-woocommerce' ),
				'w' => esc_html__( 'w', 'auctions-for-woocommerce' ),
				'd' => esc_html__( 'd', 'auctions-for-woocommerce' ),
			),
		);

		wp_localize_script( $this->plugin_name . '-countdown-language', 'wc_auctions_language_data', $language_data );
		wp_enqueue_script( $this->plugin_name . '-countdown-language' );

		$frontend_deps = array( 'jquery', $this->plugin_name . '-countdown' );
		if ( is_product() ) {
			$frontend_deps[] = 'autoNumeric';
		}

		if ( get_option( 'auctions_for_woocommerce_live_bid_notifications_enable', 'yes' ) === 'yes' ) {
			wp_register_script( 'noty', AFW()->plugin_url() . '/public/js/noty/noty.min.js', array( 'jquery' ), '3.2.0', false );

			if ( get_option( 'auctions_for_woocommerce_sound_notifications_enable', 'yes' ) === 'yes' ) {
				$sound = apply_filters( 'auctions_for_woocommerce_notification_sound_url', AFW()->plugin_url() . '/public/js/noty/sounds/' . get_option( 'auctions_for_woocommerce_sound_notifications_file', 'light.mp3' ) );
			}

			$noty_data = array(
				'layout' => get_option( 'auctions_for_woocommerce_live_notifications_position', 'topRighr' ),
				'sound'  => $sound,
				'theme'  => get_option( 'auctions_for_woocommerce_live_notifications_theme', 'bootstrap-v4' ),
			);
			wp_localize_script( 'noty', 'notydata', $noty_data );
			$frontend_deps[] = 'noty';
		}

		wp_register_script( $this->plugin_name . '-frontend', AFW()->plugin_url() . '/public/js/auctions-for-woocommerce-public.js', $frontend_deps, $this->version, false );
		$custom_data = array(
			'ajax_nonce'         => wp_create_nonce( 'woocommerce_auction_nonce' ),
			'ajaxurl'            => add_query_arg( 'afw-ajax', '' ),
			'najax'              => true,
			'last_activity'      => get_option( 'auctions_for_woocommerce_last_activity', '0' ),
			'focus'              => get_option( 'auctions_for_woocommerce_focus', 'yes' ),
			'finished'           => esc_html__( 'Auction has finished!', 'auctions-for-woocommerce' ),
			'gtm_offset'         => get_option( 'gmt_offset' ),
			'started'            => esc_html__( 'Auction has started! Please refresh your page.', 'auctions-for-woocommerce' ),
			'no_need'            => esc_html__( 'No need to bid. Your bid is winning! ', 'auctions-for-woocommerce' ),
			'compact_counter'    => get_option( 'auctions_for_woocommerce_compact_countdown', 'no' ),
			'outbid_message'     => wc_get_template_html(
				'notices/error.php',
				array(
					'messages' => array( __( "You've been outbid!", 'auctions-for-woocommerce' ) ),
					'notices'  => array_filter( array( 'error' => array( 'notice' => __( "You've been outbid!", 'auctions-for-woocommerce' ) ) ) ),
				)
			),
			'live_notifications' => get_option( 'auctions_for_woocommerce_live_bid_notifications_enable', 'yes' ),
		);

		$auctions_for_woocommerce_live_check          = get_option( 'auctions_for_woocommerce_live_check' );
		$auctions_for_woocommerce_live_check_interval = get_option( 'auctions_for_woocommerce_live_check_interval' );

		if ( 'yes' === $auctions_for_woocommerce_live_check ) {
			$custom_data['interval'] = isset( $auctions_for_woocommerce_live_check_interval ) && is_numeric( $auctions_for_woocommerce_live_check_interval ) ? $auctions_for_woocommerce_live_check_interval : '1';
		}
		wp_localize_script(
			$this->plugin_name . '-frontend',
			'afw_data',
			$custom_data
		);

		wp_enqueue_script( $this->plugin_name . '-frontend' );

	}

	public function register_widgets() {
		// Include - no need to use autoload as WP loads them anyway
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-featured-auctions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-random-auctions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-recent-auction.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-recently-viewed-auctions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-ending-soon-auction.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-my-auctions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-wc-widget-auction-search.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-future- auctions.php';
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-auctions-for-woocommerce-widget-watchlist.php';
		// Register widgets
		register_widget( 'Auctions_For_Woocommerce_Widget_Recent_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Featured_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Random_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Recently_Viewed_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Ending_Soon_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_My_Auction' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Auction_Search' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Future_Auctions' );
		register_widget( 'Auctions_For_Woocommerce_Widget_Watchlist_Auction' );
	}
	/**
	 * Place bid action
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool $url (default: false)
	 * @return void
	 *
	 */
	public function auctions_for_woocommerce_place_bid( $url = false ) {

		if ( empty( $_REQUEST['place-bid'] ) || ! is_numeric( $_REQUEST['place-bid'] ) ) {
			return;
		}

		$product_id = apply_filters( 'auctions_for_woocommerce_place_bid_product_id', absint( $_REQUEST['place-bid'] ) );
		$bid_value  = isset( $_REQUEST['bid_value'] ) ? wc_format_decimal( sanitize_text_field( $_REQUEST['bid_value'] ), wc_get_price_decimals() ) : false;
		$bid        = apply_filters( 'auctions_for_woocommerce_place_bid_bidvalue', (float) $bid_value );
		$product    = wc_get_product( $product_id );

		if ( $product && $product->is_type( 'auction' ) ) {
			// Place bid
			$biddclass = new Auctions_For_Woocommerce_Bid();
			if ( $biddclass->placebid( $product_id, $bid ) ) {
				auctions_for_woocommerce_place_bid_message( $product_id );
			}
			if ( wc_notice_count( 'error' ) === 0 ) {
					wp_safe_redirect( remove_query_arg( array( 'place-bid', 'quantity', 'product_id' ), wp_get_referer() ) );
					exit;
			}
			return;

		} else {
			wc_add_notice( esc_html__( 'Item is not for auction', 'auctions-for-woocommerce' ), 'error' );
			return;
		}
	}

	/**
	 * Templating with plugin folder
	 *
	 * @param int $post_id the post (product) identifier
	 * @param stdClass $post the post (product)
	 *
	 */
	public function woocommerce_locate_template( $template, $template_name, $template_path ) {

		if ( ! $template_path ) {
			$template_path = WC()->template_path();
		}

		$plugin_path     = AFW_ABSPATH . 'templates/';
		$template_locate = locate_template( array( $template_path . $template_name, $template_name ) );

		if ( ! $template_locate && file_exists( $plugin_path . $template_name ) ) {

			return $plugin_path . $template_name;

		} else {

			return $template;

		}
	}

	/**
	 * Is auction payable
	 *
	 * Checks for a valid user who have won auction
	 *
	 * @param bool object (default: false)
	 * @return bool
	 *
	 */
	public function auction_is_purchasable( $is_purchasable, $product ) {

		if ( $product->is_type( 'auction' ) ) {

			if ( ! $product->get_auction_closed() && $product->get_auction_type() === 'normal' && ( $product->get_price() < $product->get_auction_current_bid() ) ) {
				return false;
			} elseif ( ! $product->get_auction_closed() && $product->get_auction_type() === 'reverse' && ( $product->get_price() > $product->get_auction_current_bid() ) ) {
				return false;
			}

			if ( 'no' === get_option( 'auctions_for_woocommerce_alow_buy_now', 'yes' ) && 0 !== $product->get_auction_bid_count() && 2 !== $product->get_auction_closed() ) {
				return false;
			}
			if ( ! $product->get_auction_closed() && ! $product->get_auction_closed() && $product->get_price() !== '' ) {
				return true;
			}

			if ( ! is_user_logged_in() ) {
				return false;
			}

			$current_user = wp_get_current_user();
			if ( intval( $product->get_auction_current_bider() ) !== $current_user->ID ) {
				return false;
			}

			if ( ! $product->get_auction_closed() ) {
				return false;
			}
			if ( 2 !== $product->get_auction_closed() ) {
				return false;
			}
			if ( 'reverse' === $product->get_auction_type() && 'yes' === get_option( 'auctions_for_woocommerce_remove_pay_reverse' ) ) {
				return false;
			}

			return true;
		}
		return $is_purchasable;
	}

	/**
	 *
	 * Track auction views
	 *
	 * @param void
	 * @return int
	 *
	 */
	public function track_auction_view() {

		if ( ! is_singular( 'product' ) || ! is_active_widget( false, false, 'recently_viewed_auctions', true ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed_auctions'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode( ' | ', sanitize_text_field( $_COOKIE['woocommerce_recently_viewed_auctions'] ) );
		}

		if ( ! in_array( $post->ID, $viewed_products, true ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed_auctions', implode( ' | ', $viewed_products ) );
	}

	public function auction_filter_wp_title( $title ) {

		if ( ! get_query_var( 'is_auction_archive', false ) ) {
			return $title;
		}

		$auction_page_id = wc_get_page_id( 'auction' );
		$title           = get_the_title( $auction_page_id );

		return $title;
	}

	/**
	 * Write the auction tab on the product view page
	 * In WooCommerce these are handled by templates.
	 *
	 * @param  array
	 * @return array
	 *
	 */
	public function auction_tab( $tabs ) {
		global $product;
		if ( $product && $product->is_type( 'auction' ) ) {
			$tabs['auctions_for_woocommerce_history'] = array(
				'title'    => esc_html__( 'Auction history', 'auctions-for-woocommerce' ),
				'priority' => 25,
				'callback' => array( $this, 'auction_tab_callback' ),
				'content'  => 'auction-history',
			);
		}
		return $tabs;
	}
	/**
	 * Auction call back from auction_tab
	 *
	 * @param  array
	 * @return void
	 *
	 */
	public function auction_tab_callback( $tabs ) {
		wc_get_template( 'single-product/tabs/auction-history.php' );
	}

	/**
	 *  Add pay button for auctions that user won
	 *
	 *
	 */
	public function add_pay_button() {
		if ( is_user_logged_in() ) {
			wc_get_template( 'loop/pay-button.php' );
		}

	}

	/**
	 *  Add pay button for auctions that user won
	 *
	 *
	 */
	public function add_to_watchlist() {

		global $watchlist;

		if ( isset( $watchlist ) && true === $watchlist ) {
			wc_get_template( 'single-product/watchlist-link.php' );
		}

	}

	/**
	 *  Add winning badge for auctions that current user is winning
	 *
	 *
	 */
	public function add_winning_bage() {
		if ( is_user_logged_in() ) {
			wc_get_template( 'loop/winning-bage.php' );
		}

	}

	/**
	 *   Add auction badge for auction product
	 *
	 *
	 */
	public function add_auction_bage() {
		wc_get_template( 'loop/auction-bage.php' );
	}
	/**
	 *   Add auction badge for auction product
	 *
	 *
	 */
	public function add_watchlist_link() {
		wc_get_template( 'single-product/watchlist-link.php' );
	}

	/**
	 * Close auction action
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool $url (default: false)
	 * @return void;
	 *
	 */
	public function add_product_to_cart() {

		if ( ! is_admin() ) {

			if ( ! empty( $_GET['pay-auction'] ) ) {

				$current_user = wp_get_current_user();

				if ( apply_filters( 'auctions_for_woocommerce_empty_cart', false ) ) {
					WC()->cart->empty_cart();
				}
				$product_id   = intval( $_GET['pay-auction'] );
				$product_data = wc_get_product( $product_id );

				if ( ! $product_data ) {
					wp_redirect( home_url() );
					exit;
				}
				if ( ! is_user_logged_in() ) {
					header( 'Location: ' . wp_login_url( wc_get_checkout_url() . '?pay-auction=' . $product_id ) );
					exit;
				}
				if ( $current_user->ID !== $product_data->get_auction_current_bider() ) {
					wc_add_notice( sprintf( esc_html__( 'You can not buy this item because you did not win the auction! ', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );
					return false;
				}
				WC()->cart->add_to_cart( $product_id );
				wp_safe_redirect( remove_query_arg( array( 'pay-auction', 'quantity', 'product_id' ), wc_get_checkout_url() ) );
				exit;
			}
		}
	}

	public function auctions_page_template( $template ) {

		global $wp_version;

		if ( get_query_var( 'is_auction_archive', false ) ) {

			$template = locate_template( WC()->template_path() . 'archive-product-auctions.php' );

			if ( $template ) {
				wc_get_template( 'archive-product-auctions.php' );
			} else {
				wc_get_template( 'archive-product.php' );
			}

			return false;
		}
		return $template;
	}

	/**
	 * Output body classes for auctions archive page
	 *
	 * @param array
	 * @return array
	 *
	 */
	public function output_body_class( $classes ) {
		if ( is_page( wc_get_page_id( 'auction' ) ) ) {
			$classes[] = 'woocommerce auctions-page';
		}
		return $classes;
	}

	/**
	*
	* Fix for auction base page title
	*
	* @param string
	* @return string
	*
	*/
	public function auction_page_title( $title ) {
		if ( get_query_var( 'is_auction_archive', false ) === 'true' ) {

				$auction_page_id = wc_get_page_id( 'auction' );
				$title           = get_the_title( $auction_page_id );

		}

		return $title;
	}

	/**
	 *
	 * Fix for auction base page breadcrumbs
	 *
	 * @param string
	 * @return string
	 *
	 */
	public function woocommerce_get_breadcrumb( $crumbs, $wc_breadcrumb ) {

		if ( get_query_var( 'is_auction_archive', false ) === 'true' ) {

				$auction_page_id = wc_get_page_id( 'auction' );
				$crumbs[1]       = array( get_the_title( $auction_page_id ), get_permalink( $auction_page_id ) );
		}

		return $crumbs;
	}

	/**
	 *
	 *  Show a shop page description on product archives.
	 *
	 * @param string
	 * @return string
	 *
	 */
	public function auction_page_description() {

		if ( get_query_var( 'is_auction_archive', false ) === 'true' ) {
			remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
			add_action( 'woocommerce_archive_description', 'woocommerce_auction_archive_description', 10 );
		}

	}

	/**
	*
	* Add ordering for auctions
	*
	* @param array
	* @return array
	*
	*/
	public function auction_woocommerce_catalog_orderby( $data ) {

		$auctions_for_woocommerce_dont_mix_shop = get_option( 'auctions_for_woocommerce_dont_mix_shop' );
		$auctions_for_woocommerce_dont_mix_cat  = get_option( 'auctions_for_woocommerce_dont_mix_cat' );
		$auctions_for_woocommerce_dont_mix_tag  = get_option( 'auctions_for_woocommerce_dont_mix_tag' );

		$is_auction_archive = get_query_var( 'is_auction_archive', false );
		if ( $is_auction_archive ) {
			unset( $data['popularity'] );
			unset( $data['rating'] );

			$data['price']            = esc_html__( 'Sort by buynow price: low to high', 'auctions-for-woocommerce' );
			$data['price-desc']       = esc_html__( 'Sort by buynow price: high to low', 'auctions-for-woocommerce' );
			$data['bid_asc']          = esc_html__( 'Sort by current bid: Low to high', 'auctions-for-woocommerce' );
			$data['bid_desc']         = esc_html__( 'Sort by current bid: High to low', 'auctions-for-woocommerce' );
			$data['auction_end']      = esc_html__( 'Sort auction by ending soonest', 'auctions-for-woocommerce' );
			$data['auction_started']  = esc_html__( 'Sort auction by recently started', 'auctions-for-woocommerce' );
			$data['auction_activity'] = esc_html__( 'Sort auction by most active', 'auctions-for-woocommerce' );
			return $data;
		}

		if ( ( is_shop() && 'yes' === $auctions_for_woocommerce_dont_mix_shop ) && 'true' !== $is_auction_archive ) {
				return $data;
		}
		if ( ( is_product_category() && ( 'yes' === $auctions_for_woocommerce_dont_mix_shop || 'yes' === $auctions_for_woocommerce_dont_mix_cat ) ) && 'true' !== $is_auction_archive ) {
				return $data;
		}
		if ( ( is_product_tag() && ( 'yes' === $auctions_for_woocommerce_dont_mix_shop || 'yes' === $auctions_for_woocommerce_dont_mix_tag ) ) && 'true' !== $is_auction_archive ) {
				return $data;
		}

		$data['bid_asc']          = esc_html__( 'Sort by current bid: Low to high', 'auctions-for-woocommerce' );
		$data['bid_desc']         = esc_html__( 'Sort by current bid: High to low', 'auctions-for-woocommerce' );
		$data['auction_end']      = esc_html__( 'Sort auction by ending soonest', 'auctions-for-woocommerce' );
		$data['auction_started']  = esc_html__( 'Sort auction by recently started', 'auctions-for-woocommerce' );
		$data['auction_activity'] = esc_html__( 'Sort auction by most active', 'auctions-for-woocommerce' );

		return $data;
	}

	/**
	 *
	 * Fix active class in nav for auction  page.
	 *
	 * @param array $menu_items
	 * @return array
	 *
	 */
	public function wsa_nav_menu_item_classes( $menu_items ) {

		if ( ! get_query_var( 'is_auction_archive', false ) ) {
			return $menu_items;
		}

		$auction_page = (int) wc_get_page_id( 'auction' );

		foreach ( (array) $menu_items as $key => $menu_item ) {

			$classes = (array) $menu_item->classes;

			// Unset active class for blog page

			$menu_items[ $key ]->current = false;

			if ( in_array( 'current_page_parent', $classes, true ) ) {
				unset( $classes[ array_search( 'current_page_parent', $classes, true ) ] );
			}

			if ( in_array( 'current-menu-item', $classes, true ) ) {
				unset( $classes[ array_search( 'current-menu-item', $classes, true ) ] );
			}

			if ( in_array( 'current_page_item', $classes, true ) ) {
				unset( $classes[ array_search( 'current_page_item', $classes, true ) ] );
			}

			// Set active state if this is the shop page link
			if ( $auction_page === $menu_item->object_id && 'page' === $menu_item->object ) {
				$menu_items[ $key ]->current = true;
				$classes[]                   = 'current-menu-item';
				$classes[]                   = 'current_page_item';

			}

			$menu_items[ $key ]->classes = array_unique( $classes );

		}

		return $menu_items;
	}

	public function add_redirect_previous_page() {
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			echo '<input type="hidden" name="redirect" value="' . esc_url( sanitize_text_field( $_SERVER['HTTP_REFERER'] ) ) . '" >';
		}
	}

	public function add_auction_to_user_metafield( $data ) {
		if ( isset( $data['product_id'] ) && $data['product_id'] ) {
			$user_id         = get_current_user_id();
			$wsa_my_auctions = get_user_meta( $user_id, 'wsa_my_auctions', false );
			if ( ! in_array( $data['product_id'], $wsa_my_auctions, true ) ) {
				add_user_meta( $user_id, 'wsa_my_auctions', $data['product_id'], false );
			}
		}

	}

	/**
	 * Sync meta with wpml
	 *
	 * Sync meta trough translated post
	 *
	 * @param bool (default: false)
	 * @return void
	 *
	 */
	public function sync_metadata_wpml( $data ) {

		$deflanguage = apply_filters( 'wpml_default_language', null );
		if ( is_array( $data ) ) {
			$product_id = $data['product_id'];
		} else {
			$product_id = $data;
		}

		$meta_values = get_post_meta( $product_id );
		$trid        = apply_filters( 'wpml_element_trid', null, $product_id, 'post_product' );
		$all_posts   = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_product' );

		unset( $all_posts[ $deflanguage ] );

		if ( ! empty( $all_posts ) ) {
			foreach ( $all_posts as $key => $translatedpost ) {
				if ( isset( $meta_values['_auction_current_bid'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_current_bid', $meta_values['_auction_current_bid'][0] );
				}

				if ( isset( $meta_values['_auction_current_bider'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_current_bider', $meta_values['_auction_current_bider'][0] );
				}

				if ( isset( $meta_values['_auction_max_bid'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_max_bid', $meta_values['_auction_max_bid'][0] );
				}

				if ( isset( $meta_values['_auction_max_current_bider'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_max_current_bider', $meta_values['_auction_max_current_bider'][0] );
				}

				if ( isset( $meta_values['_auction_bid_count'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_bid_count', $meta_values['_auction_bid_count'][0] );
				}

				if ( isset( $meta_values['_auction_closed'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_closed', $meta_values['_auction_closed'][0] );
				}

				if ( isset( $meta_values['_auction_started'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_started', $meta_values['_auction_started'][0] );
				}

				if ( isset( $meta_values['_auction_has_started'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_has_started', $meta_values['_auction_has_started'][0] );
				}

				if ( isset( $meta_values['_auction_fail_reason'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_fail_reason', $meta_values['_auction_fail_reason'][0] );
				}

				if ( isset( $meta_values['_auction_dates_to'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_dates_to', $meta_values['_auction_dates_to'][0] );
				}

				if ( isset( $meta_values['_auction_dates_from'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_dates_from', $meta_values['_auction_dates_from'][0] );
				}

				if ( isset( $meta_values['_order_id'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_order_id', $meta_values['_order_id'][0] );
				}

				if ( isset( $meta_values['_stop_mails'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_stop_mails', $meta_values['_stop_mails'][0] );
				}

				if ( isset( $meta_values['_auction_item_condition'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_item_condition', $meta_values['_auction_item_condition'][0] );
				}

				if ( isset( $meta_values['_auction_type'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_type', $meta_values['_auction_type'][0] );
				}

				if ( isset( $meta_values['_auction_proxy'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_proxy', $meta_values['_auction_proxy'][0] );
				}

				if ( isset( $meta_values['_auction_start_price'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_start_price', $meta_values['_auction_start_price'][0] );
				}

				if ( isset( $meta_values['_auction_bid_increment'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_bid_increment', $meta_values['_auction_bid_increment'][0] );
				}

				if ( isset( $meta_values['_auction_reserved_price'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_reserved_price', $meta_values['_auction_reserved_price'][0] );
				}

				if ( isset( $meta_values['_regular_price'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_regular_price', $meta_values['_regular_price'][0] );
				}

				if ( isset( $meta_values['_auction_wpml_language'][0] ) ) {
					update_post_meta( $translatedpost->element_id, '_auction_wpml_language', $meta_values['_auction_wpml_language'][0] );
				}
			}
		}
	}

	/**
	 *
	 * Add last alnguage in use to custom meta of auction
	 *
	 * @param int
	 * @return void
	 *
	 */
	public function add_language_wpml_meta( $data ) {
		update_post_meta( $data['product_id'], '_auction_wpml_language', ICL_LANGUAGE_CODE );
	}



	public function get_main_wpml_id( $id ) {

		return apply_filters( 'wpml_object_id', $id, 'page', false, apply_filters( 'wpml_default_language', null ) );

	}

	/**
	 * Translate auction base page url
	 */
	public function translate_ls_auction_url( $languages, $debug_mode = false ) {
		global $wp_query;

		$auction_page = (int) wc_get_page_id( 'auction' );

		foreach ( $languages as $language ) {
			if ( get_query_var( 'is_auction_archive', false ) || $debug_mode ) {
					wpml_switch_language_action( $language['language_code'] );
					$url = get_permalink( apply_filters( 'translate_object_id', $auction_page, 'page', true, $language['language_code'] ) );
					wpml_switch_language_action();
					$languages[ $language['language_code'] ]['url'] = $url;
			}
		}

		return $languages;
	}

	/**
	 *
	 * Add wpml support for auction base page
	 *
	 * @param int
	 * @return int
	 *
	 */
	public function auctionbase_page_wpml( $page_id ) {

		if ( function_exists( 'icl_object_id' ) ) {
			$id = icl_object_id( $page_id, 'page', false );

		} else {
			$id = $page_id;
		}
		return $id;

	}

	public function change_last_activity_timestamp( $data ) {
		$product_id   = is_array( $data ) ? $data['product_id'] : $data;
		$current_time = current_time( 'timestamp' );
		update_option( 'auctions_for_woocommerce_last_activity', $current_time );
		update_post_meta( $product_id, '_auction_last_activity', $current_time );
	}

	/**
	 * Remove finished auctions from related products
	 *
	 * @return var
	 *
	 */
	public function remove_finished_auctions_from_related_products( $query ) {
		$auctions_for_woocommerce_finished_enabled = get_option( 'auctions_for_woocommerce_finished_enabled', 'no' );
		$auctions_for_woocommerce_future_enabled   = get_option( 'auctions_for_woocommerce_future_enabled', 'no' );
		if ( 'no' === $auctions_for_woocommerce_finished_enabled ) {
			$finished_auctions = wsa_get_finished_auctions_id();
			if ( count( $finished_auctions ) > 0 ) {
					$query['where'] .= ' AND p.ID NOT IN ( ' . implode( ',', array_map( 'absint', $finished_auctions ) ) . ' )';
			}
		}
		if ( 'no' === $auctions_for_woocommerce_future_enabled ) {
			$future_auctions = wsa_get_future_auctions_id();
			if ( count( $future_auctions ) > 0 ) {
					$query['where'] .= ' AND p.ID NOT IN ( ' . implode( ',', array_map( 'absint', $future_auctions ) ) . ' )';
			}
		}
		return $query;
	}

	/**
	 * Output the auction product add to cart area.
	 *
	 * @subpackage  Product
	 * @return void
	 *
	 */
	public function woocommerce_auction_add_to_cart() {
		global $product;

		if ( $product && $product->is_type( 'auction' ) ) {
			wc_get_template( 'single-product/add-to-cart/auction.php' );
		}
	}

	/**
	 * Output the to bid block.
	 *
	 * @subpackage  Product
	 * @return void
		 *
	 */
	public function woocommerce_auction_bid() {
		global $product;

		if ( $product && $product->is_type( 'auction' ) ) {
			wc_get_template( 'single-product/bid.php' );
		}
	}

	/**
	 * Output the to pay block.
	 *
	 * @subpackage  Product
	 * @return void
		 *
	 */
	public function woocommerce_auction_pay() {
		global $product;

		if ( $product && $product->is_type( 'auction' ) ) {
			wc_get_template( 'single-product/pay.php' );
		}
	}

	/**
	 * Change out of stock item visibility
	 *
	 * @subpackage  Product
	 * @return void
		 *
	 */

	public function add_out_of_stock_items( $visible, $product_id ) {
		if ( false == $visible && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$product = wc_get_product( $product_id );
			if ( $product->is_type( 'auction' ) && ! $product->is_in_stock() ) {

				$visible = 'visible' === $product->get_catalog_visibility() || ( is_search() && 'search' === $product->get_catalog_visibility() ) || ( ! is_search() && 'catalog' === $product->get_catalog_visibility() );

				if ( 'trash' === $product->get_status() ) {
					$visible = false;
				} elseif ( 'publish' !== $product->get_status() && ! current_user_can( 'edit_post', $product->get_id() ) ) {
					$visible = false;
				}

				if ( $product->get_parent_id() ) {
					$parent_product = wc_get_product( $product->get_parent_id() );

					if ( $parent_product && 'publish' !== $parent_product->get_status() ) {
						$visible = false;
					}
				}
			}
		}
		return $visible;

	}

	public function show_counter_in_loop() {
		wc_get_template( 'global/counter.php' );
	}

	public function mask_displayname( $displayname ) {
		if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
				return $displayname;
		} else {
			$length = strlen( $displayname );

			if ($length > 1) {

				$displayname = $displayname[0] . str_repeat( '*', $length - 2 ) . $displayname[ $length - 1 ];
			
			} else {

				$displayname = $displayname[0] . str_repeat( '*', 3 ) . $displayname[ $length - 1 ];
			}
			
		}

		return $displayname;

	}

	public function auctions_extend_time( $data ) {

		$product = wc_get_product( $data['product_id'] );
		if ( ! $product->get_auction_extend_enable() ) {
			return;
		}

		if ( $product->is_type( 'auction' ) ) {

			$auctionend       = new DateTime( $product->get_auction_dates_to() );
			$auctionendformat = $auctionend->format( 'Y-m-d H:i:s' );
			$time             = current_time( 'timestamp' );
			$timeplus         = gmdate( 'Y-m-d H:i:s', $time + $product->get_auction_extend_in_time() );

			if ( $timeplus > $auctionendformat || $product->get_auction_extend_in_time() === 0 ) {
				$auctionend->add( new DateInterval( 'PT' . $product->get_auction_extend_for_time() . 'S' ) );
				$newdate = $auctionend->format( 'Y-m-d H:i:s' );
				update_post_meta( $data['product_id'], '_auction_dates_to', $newdate );
				add_post_meta( $data['product_id'], '_auction_extension_dates', $time );
				do_action( 'auctions_for_woocommerce_after_time_extension', $product, $newdate );
			}
		}

	}

}
