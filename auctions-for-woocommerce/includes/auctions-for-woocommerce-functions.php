<?php
/**
 * Auctions for WooCommerce Functions
 *
 * Hooked-in functions for Auctions for WooCommerce related events on the front-end.
 *
 */


/**
 * Placed bid message
 *
  * @return void
 *
 */
function auctions_for_woocommerce_place_bid_message( $product_id ) {
	global $woocommerce;
	$product_data = wc_get_product( $product_id );
	$current_user = wp_get_current_user();

	if ( $current_user->ID === $product_data->get_auction_current_bider() ) {
		if ( ! $product_data->is_reserve_met() && ( 'yes' !== $product_data->get_auction_sealed() ) ) {
			/* translators: 1) Product title */
			$message = sprintf( esc_html__( 'Successfully placed bid for &quot;%1$s&quot; but is does not meet the reserve price!', 'auctions-for-woocommerce' ), $product_data->get_title() );
		} else {
			if ( $product_data->get_auction_proxy() && $product_data->get_auction_max_bid() ) {
				/* translators: 1) Product title  2) max bid*/
				$message = sprintf( esc_html__( 'Successfully placed bid for &quot;%1$s&quot;! Your max bid is %2$s.', 'auctions-for-woocommerce' ), $product_data->get_title(), wc_price( $product_data->get_auction_max_bid() ) );
			} else {
				/* translators: 1) Product title */
				$message = sprintf( esc_html__( 'Successfully placed bid for &quot;%1$s&quot;!', 'auctions-for-woocommerce' ), $product_data->get_title() );
			}
		}
	} else {
		/* translators: 1) Product title */
		$message = sprintf( __( "Your bid was successful but you've been outbid for &quot;%s&quot;!", 'auctions-for-woocommerce' ), $product_data->get_title() );
	}

	wc_add_notice( apply_filters( 'auctions_for_woocommerce_placed_bid_message', $message, $product_id ) );
}


/**
 * Your bid is winning message
 *
  * @return void
 *
 */
function auctions_for_woocommerce_winning_bid_message() {
	if ( is_product() ) {

		global $product;
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		if ( ! $product || ! $product->is_type( 'auction' ) ) {
			return false;
		}
		if ( $product->is_closed() ) {
			return false;
		}
		if ( ! get_current_user_id() ) {
			return false;
		}
		if ( $product->get_auction_sealed() === 'yes' ) {
			return false;
		}
		$message = esc_html__( 'No need to bid. Your bid is winning! ', 'auctions-for-woocommerce' );
		if ( get_current_user_id() === $product->get_auction_current_bider() && wc_notice_count() === 0 ) {
			wc_add_notice( apply_filters( 'auctions_for_woocommerce_winning_bid_message', $message ) );
		}
	}
}


/**
 * Gets the url for the checkout page
 *
 * @return string url to page
 */
function auctions_for_woocommerce_get_checkout_url() {
	$checkout_page_id = wc_get_page_id( 'checkout' );
	$checkout_url     = '';
	if ( $checkout_page_id ) {
		if ( is_ssl() || get_option( 'woocommerce_force_ssl_checkout' ) === 'yes' ) {
			$checkout_url = str_replace( 'http:', 'https:', get_permalink( $checkout_page_id ) );
		} else {
			$checkout_url = get_permalink( $checkout_page_id );
		}
	}
	return apply_filters( 'woocommerce_get_checkout_url', $checkout_url );
}

if ( ! function_exists( 'wc_get_price_decimals' ) ) {
	function wc_get_price_decimals() {
		return absint( get_option( 'wc_price_num_decimals', 2 ) );
	}
}


if ( ! function_exists( 'woocommerce_auctions_ordering' ) ) {

	/**
	 * Output the product sorting options.
	 *
	 **/
	function woocommerce_auctions_ordering() {
		global $wp_query;

		if ( 1 === $wp_query->found_posts ) {
				return;
		}

		$orderby                 = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : apply_filters( 'auctions_for_woocommerce_default_orderby', get_option( 'auctions_for_woocommerce_default_orderby' ) );
		$show_default_orderby    = 'menu_order' === apply_filters( 'auctions_for_woocommerce_default_orderby', get_option( 'auctions_for_woocommerce_default_orderby' ) );
		$catalog_orderby_options = apply_filters(
			'woocommerce_auctions_orderby',
			array(
				'menu_order'       => esc_html__( 'Default sorting', 'woocommerce' ),
				'date'             => esc_html__( 'Sort by newness', 'woocommerce' ),
				'price'            => __( 'Sort by buynow price: low to high', 'auctions-for-woocommerce' ),
				'price-desc'       => esc_html__( 'Sort by buynow price: high to low', 'auctions-for-woocommerce' ),
				'bid_asc'          => esc_html__( 'Sort by current bid: Low to high', 'auctions-for-woocommerce' ),
				'bid_desc'         => esc_html__( 'Sort by current bid: High to low', 'auctions-for-woocommerce' ),
				'auction_end'      => esc_html__( 'Sort auction by ending soonest', 'auctions-for-woocommerce' ),
				'auction_started'  => esc_html__( 'Sort auction by recently started', 'auctions-for-woocommerce' ),
				'auction_activity' => esc_html__( 'Sort auction by most active', 'auctions-for-woocommerce' ),
				'rand' => esc_html__( 'Random', 'auctions-for-woocommerce' ),
			)
		);

		if ( ! $show_default_orderby ) {
				unset( $catalog_orderby_options['menu_order'] );
		}
		wc_get_template(
			'loop/orderby.php',
			array(
				'catalog_orderby_options' => $catalog_orderby_options,
				'orderby'                 => $orderby,
				'show_default_orderby'    => $show_default_orderby,
			)
		);
	}
}
if ( ! function_exists( 'wsa_get_finished_auctions_id' ) ) {

	/**
	 * Return Finished auctions ids
	 *
	 * @subpackage  Loop
	 *
	 */
	function wsa_get_finished_auctions_id() {
		$args                      = array(
			'post_type'          => 'product',
			'posts_per_page'     => '-1',
			'show_past_auctions' => true,
			'tax_query'          => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->show_only_tax_query( array( 'finished', 'sold', 'buy-now' ) ),
			),
			'auction_arhive'     => true,
			'show_past_auctions' => true,
			'fields'             => 'ids',
		);
		$query                     = new WP_Query( $args );
		$wsa_finished_auctions_ids = $query->posts;
		return $wsa_finished_auctions_ids;
	}
}

if ( ! function_exists( 'wsa_get_future_auctions_id' ) ) {

	/**
	 * Return future auctions ids
	 *
	 * @subpackage  Loop
	 *
	 */
	function wsa_get_future_auctions_id() {
			$args                    = array(
				'post_type'            => 'product',
				'posts_per_page'       => '-1',
				'show_past_auctions'   => true,
				'tax_query'            => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'auction',
					),
					AFW()->show_only_tax_query( array( 'future' ) ),
				),
				'auction_arhive'       => true,
				'show_future_auctions' => true,
				'fields'               => 'ids',
			);
			$query                   = new WP_Query( $args );
			$wsa_future_auctions_ids = $query->posts;
			return $wsa_future_auctions_ids;
	}
}

/**
 * Get full list of auction visibilty term ids.
 *
 * @since  2.0.0
 * @return int[]
 */
function wc_get_auction_visibility_term_ids() {
	if ( ! taxonomy_exists( 'auction_visibility' ) ) {
		wc_doing_it_wrong( __FUNCTION__, 'wc_get_auction_visibility_term_ids should not be called before taxonomies are registered (woocommerce_after_register_post_type action).', '3.1' );
		return array();
	}
	return array_map(
		'absint',
		wp_parse_args(
			wp_list_pluck(
				get_terms(
					array(
						'taxonomy'   => 'auction_visibility',
						'hide_empty' => false,
					)
				),
				'term_taxonomy_id',
				'name'
			),
			array(
				'exclude-from-catalog' => 0,
				'exclude-from-search'  => 0,
				'featured'             => 0,
				'outofstock'           => 0,
				'rated-1'              => 0,
				'rated-2'              => 0,
				'rated-3'              => 0,
				'rated-4'              => 0,
				'rated-5'              => 0,
			)
		)
	);
}

if ( ! function_exists( 'woocommerce_auction_archive_description' ) ) {

	/**
	 * Show a page description on auction archives.
	 */
	function woocommerce_auction_archive_description() {
		// Don't display the description on search results page.
		if ( is_search() ) {
			return;
		}

		if ( is_post_type_archive( 'product' ) && in_array( absint( get_query_var( 'paged' ) ), array( 0, 1 ), true ) ) {
			$auctions_page = get_post( wc_get_page_id( 'auction' ) );
			if ( $auctions_page ) {
				$description = wc_format_content( $auctions_page->post_content );
				if ( $description ) {
					echo '<div class="page-description">' . wp_kses_post( $description ) . '</div>';
				}
			}
		}
	}
}
