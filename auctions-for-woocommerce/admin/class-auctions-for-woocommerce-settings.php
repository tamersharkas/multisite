<?php
/**
 * WooCommerce Account Settings
 *
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( class_exists( 'WC_Settings_Page' ) ) :


	class Auctions_For_Woocommerce_Settings extends WC_Settings_Page {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id    = 'auctions_for_woocommerce';
			$this->label = esc_html__( 'Auctions', 'auctions-for-woocommerce' );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		}

		/**
		 * Get settings array
		 *
		 * @return array
		 */
		public function get_settings() {

			return apply_filters(
				'woocommerce_' . $this->id . '_settings',
				array(
					array(
						'title' => esc_html__( 'General options', 'auctions-for-woocommerce' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'auctions_for_woocommerce_options',
					),
					array(
						'title'    => esc_html__( 'Default Auction Sorting', 'auctions-for-woocommerce' ),
						'desc'     => esc_html__( 'This controls the default sort order of the auctions.', 'auctions-for-woocommerce' ),
						'id'       => 'auctions_for_woocommerce_default_orderby',
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width:300px;',
						'default'  => 'menu_order',
						'type'     => 'select',
						'options'  => apply_filters(
							'auctions_for_woocommerce_default_orderby_options',
							array(
								'menu_order'       => esc_html__( 'Default sorting (custom ordering + name)', 'woocommerce' ),
								'date'             => esc_html__( 'Sort by most recent', 'woocommerce' ),
								'bid_asc'          => esc_html__( 'Sort by current bid: Low to high', 'auctions-for-woocommerce' ),
								'bid_desc'         => esc_html__( 'Sort by current bid: High to low', 'auctions-for-woocommerce' ),
								'auction_end'      => esc_html__( 'Sort auction by ending soonest', 'auctions-for-woocommerce' ),
								'auction_started'  => esc_html__( 'Sort auction by recently started', 'auctions-for-woocommerce' ),
								'auction_activity' => esc_html__( 'Sort auction by most active', 'auctions-for-woocommerce' ),
								'rand'             => esc_html__( 'Random', 'auctions-for-woo+commerce' ),
							)
						),
						'desc_tip' => true,
					),
					array(
						'title'   => esc_html__( 'Past auctions', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Show finished auctions.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_finished_enabled',
						'default' => 'no',
					),
					array(
						'title'   => esc_html__( 'Future auctions', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Show auctions that did not start yet.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_future_enabled',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Do not show auctions on shop page', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Do not mix auctions and regular products on shop page. Just show auctions on the auction page (auctions base page)', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_dont_mix_shop',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Do not show auctions on product category page', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Do not mix auctions and regular products on product category page. Just show auctions on the auction page (auctions base page)', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_dont_mix_cat',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Do not show auctions on product tag page', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Do not mix auctions and regular products on product tag page. Just show auctions on the auction page (auctions base page)', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_dont_mix_tag',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Do not show auctions on product search page', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Do not mix auctions and regular products on product search page.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_dont_mix_search',
						'default' => 'no',
					),
					array(
						'title'    => esc_html__( 'Auctions Base Page', 'auctions-for-woocommerce' ),
						'desc'     => esc_html__( 'Set the base page for your auctions - this is where your auction archive will be.', 'auctions-for-woocommerce' ),
						'id'       => 'woocommerce_auction_page_id',
						'type'     => 'single_select_page',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'css'      => 'min-width:300px;',
						'desc_tip' => true,
					),

					array(
						'title'   => esc_html__( 'Allow highest bidder to outbid himself', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_curent_bidder_can_bid',
						'default' => 'no',
					),

					array(
						'title'   => esc_html__( 'Allow watchlists', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_watchlists',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Max bid amount', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Maximum value for single bid. Default value is ', 'auctions-for-woocommerce' ) . wc_price( '99999999999.99' ),
						'type'    => 'number',
						'id'      => 'auctions_for_woocommerce_max_bid_amount',
						'default' => '',
					),
					array(
						'title'   => esc_html__( 'Allow Buy It Now after bidding has started', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'For auction listings with the Buy It Now option, you have the chance to purchase an item immediately, before bidding starts. After someone bids, the Buy It Now option disappears and bidding continues until the listing ends, with the item going to the highest bidder. If not enabled Buy It Now disappears after first bid has been placed.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_alow_buy_now',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Set proxy auctions on by default', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Check box for proxy auction is on by default. You have to uncheck this option for normal auctions', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_proxy_auction_on',
						'default' => 'no',
					),
					array(
						'title'   => esc_html__( 'Enable sealed auctions', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Click here to enable sealed auctions.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_sealed_on',
						'default' => 'no',
					),
					array(
						'title'   => esc_html__( 'Remove pay button from reverse auctions.', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Click here to enable removing pay functionality for reverse auctions.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_remove_pay_reverse',
						'default' => 'no',
					),
					array(
						'title'   => esc_html__( 'Mask bidder name from history tab.', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Bidder names in history tabs will be masked with *. For example B*******e', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_mask_displaynames',
						'default' => 'no',
					),

					array(
						'type' => 'sectionend',
						'id'   => 'auctions_for_woocommerce_options',
					),
					array(
						'title' => esc_html__( 'Countdown options', 'auctions-for-woocommerce' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'auctions_for_woocommerce_countdown_section',
					),
					array(
						'title'    => esc_html__( 'Countdown format', 'auctions-for-woocommerce' ),
						'desc'     => esc_html__( 'The format for the countdown display. Default is yowdHMS', 'auctions-for-woocommerce' ),
						'desc_tip' => esc_html__( "Use the following characters (in order) to indicate which periods you want to display: 'Y' for years, 'O' for months, 'W' for weeks, 'D' for days, 'H' for hours, 'M' for minutes, 'S' for seconds. Use upper-case characters for mandatory periods, or the corresponding lower-case characters for optional periods, i.e. only display if non-zero. Once one optional period is shown, all the ones after that are also shown.", 'auctions-for-woocommerce' ),
						'type'     => 'text',
						'id'       => 'auctions_for_woocommerce_countdown_format',
						'default'  => 'yowdHMS',
					),
					array(
						'title'   => esc_html__( 'Use compact countdown ', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Indicate whether or not the countdown should be displayed in a compact format.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_compact_countdown',
						'default' => 'no',
					),
					array(
						'title'   => esc_html__( 'Use countdown in loop', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Display counter in shop loop.', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_countdown_loop',
						'default' => 'no',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'auctions_for_woocommerce_countdown_section',
					),
					array(
						'title' => esc_html__( 'Ajax check bid', 'auctions-for-woocommerce' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'auctions_for_woocommerce_ajax_check_bid_section',
					),
					array(
						'title'   => esc_html__( 'Use ajax bid check', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Enables / disables ajax current bid checker (refresher) for auction - updates current bid value without refreshing page (increases server load, disable for best performance)', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_live_check',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Ajax bid check interval', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Time between two ajax requests in seconds (bigger intervals means less load for server)', 'auctions-for-woocommerce' ),
						'type'    => 'text',
						'id'      => 'auctions_for_woocommerce_live_check_interval',
						'default' => '1',
					),
					array(
						'title'   => esc_html__( 'Ajax bid check only when in focus', 'auctions-for-woocommerce' ),
						'desc'    => esc_html__( 'Ajax bid check only when page / browser is in focus, if this is off it will trigger ajax request if page is open and user is not looking at it (increases server load).', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_focus',
						'default' => 'yes',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'auctions_for_woocommerce_ajax_check_bid_section',
					),
					array(
						'title' => esc_html__( 'Live bid notifications', 'auctions-for-woocommerce' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'auctions_for_woocommerce_live_bid_notifications',
					),
					array(
						'title'   => esc_html__( 'Enable live bid notification', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						'id'      => 'auctions_for_woocommerce_live_bid_notifications_enable',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Live notifications position', 'auctions-for-woocommerce' ),
						'type'    => 'select',
						'id'      => 'auctions_for_woocommerce_live_notifications_position',
						'default' => 'topRight',
						'options' => array(
							'top'          => esc_html__( 'Top', 'auctions-for-woocommerce' ),
							'topLeft'      => esc_html__( 'Top left', 'auctions-for-woocommerce' ),
							'topCenter'    => esc_html__( 'Top center', 'auctions-for-woocommerce' ),
							'topRight'     => esc_html__( 'Top Right', 'auctions-for-woocommerce' ),
							'center'       => esc_html__( 'Centert', 'auctions-for-woocommerce' ),
							'centerLeft'   => esc_html__( 'Center Left', 'auctions-for-woocommerce' ),
							'centerRight'  => esc_html__( 'Center right', 'auctions-for-woocommerce' ),
							'bottom'       => esc_html__( 'Bottom', 'auctions-for-woocommerce' ),
							'bottomLeft'   => esc_html__( 'Bottom left', 'auctions-for-woocommerce' ),
							'bottomCenter' => esc_html__( 'Bottom center', 'auctions-for-woocommerce' ),
							'bottomRight'  => esc_html__( 'Botom right', 'auctions-for-woocommerce' ),
						),

					),
					array(
						'title'   => esc_html__( 'Live notifications theme', 'auctions-for-woocommerce' ),
						/* translators: 1) themes url */
						'desc'    => sprintf( wp_kses_post( __( 'Live notification theme. Here you can find theme previews <a href="%1$s"> %2$s</a>', 'auctions-for-woocommerce' ) ), 'https://ned.im/noty/#/themes', 'https://ned.im/noty/#/themes' ),
						'type'    => 'select',
						'id'      => 'auctions_for_woocommerce_live_notifications_theme',
						'default' => 'bootstrap-v4',
						'options' => array(
							'bootstrap-v4' => esc_html__( 'Bootstrap-v4', 'auctions-for-woocommerce' ),
							'mint'         => esc_html__( 'Mint', 'auctions-for-woocommerce' ),
							'sunset'       => esc_html__( 'Sunset', 'auctions-for-woocommerce' ),
							'relax'        => esc_html__( 'Relax', 'auctions-for-woocommerce' ),
							'nest'         => esc_html__( 'Nest', 'auctions-for-woocommerce' ),
							'semanticui'   => esc_html__( 'Semanticui', 'auctions-for-woocommerce' ),
							'light'        => esc_html__( 'Light', 'auctions-for-woocommerce' ),
							'bootstrap-v3' => esc_html__( 'Bootstrap-v3', 'auctions-for-woocommerce' ),
						),
					),
					array(
						'title'   => esc_html__( 'Enable sound notification', 'auctions-for-woocommerce' ),
						'type'    => 'checkbox',
						/* translators: 1) caniuse.com url */
						'desc'    => sprintf( wp_kses_post( __( 'Check browser support <a href="%1$s"> %2$s</a>', 'auctions-for-woocommerce' ) ), 'https://caniuse.com/#search=audio', 'https://caniuse.com/#search=audio' ),
						'id'      => 'auctions_for_woocommerce_sound_notifications_enable',
						'default' => 'yes',
					),
					array(
						'title'   => esc_html__( 'Live notifications sound', 'auctions-for-woocommerce' ),
						'type'    => 'select',
						'id'      => 'auctions_for_woocommerce_sound_notifications_file',
						'default' => 'light',
						'options' => array(
							'light.mp3'              => esc_html__( 'Light', 'auctions-for-woocommerce' ),
							'intuition.mp3'          => esc_html__( 'Intuition', 'auctions-for-woocommerce' ),
							'appointed.mp3'          => esc_html__( 'Appointed', 'auctions-for-woocommerce' ),
							'filling-your-inbox.mp3' => esc_html__( 'Filling your inbox', 'auctions-for-woocommerce' ),
						),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'auctions_for_woocommerce_live_bid_notifications',
					),

				)
			); // End pages settings
		}
	}
	return new Auctions_For_Woocommerce_Settings();

endif;
