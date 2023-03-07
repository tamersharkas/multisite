<?php
/**
 * My Auctions Widget
 *
 * Gets and displays featured auctions in an unordered list
 *
* @package Widgets
 * @version 1.0.0
 * @extends WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Auctions_For_Woocommerce_Widget_Recent_Bids extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_recent_bids';
		$this->widget_description = esc_html__( 'Display a list of recent bids.', 'auctions-for-woocommerce' );
		$this->widget_id          = 'widget_recent_bids';
		$this->widget_name        = esc_html__( 'WooCommerce Recent Bids', 'auctions-for-woocommerce' );
		$this->settings           = array(
			'title'     => array(
				'type'  => 'text',
				'std'   => __( 'Latest bids', 'auctions-for-woocommerce' ),
				'label' => __( 'Title', 'auctions-for-woocommerce' ),
			),
			'number'    => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => esc_html__( 'Number of bids to show:', 'auctions-for-woocommerce' ),
			),
			'date_format'     => array(
				'type'  => 'text',
				'std'   => __( 'Y-m-d H:i:s', 'auctions-for-woocommerce' ),
				'label' => __( 'Date format', 'auctions-for-woocommerce' ),
			),
			'hide_time' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => esc_html__( 'Hide date column', 'auctions-for-woocommerce' ),
			),
			'hide_usernames' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => esc_html__( 'Hide names', 'auctions-for-woocommerce' ),
			),
			'hide_status' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => esc_html__( 'Hide status column', 'auctions-for-woocommerce' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		global $wpdb;

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		
		$limit            = 0 === absint( $instance['number'] ) ? 10 : ( absint( $instance['number'] ) > 15 ? 15 : absint( $instance['number'] ) );
		$user_id          = get_current_user_id();
		$date_format      = isset( $instance['date_format'] ) ? esc_attr( $instance['date_format'] ) : 'Y-m-d H:i:s';
		$auction_activity = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log ORDER BY date DESC limit %d ', $limit ) );
		$hide_usernames   = empty( $instance['hide_usernames'] ) ? false : true; 
		$hide_status      = empty( $instance['hide_status'] ) ? false : true; 
		$hide_time        = empty( $instance['hide_time'] ) ? false : true; 
		

		if ( $auction_activity ) {


			$this->widget_start( $args, $instance );

			echo '<table class="auctions_activity">';
			echo '<tr>';
			if ( true !== $hide_time ) {
				echo '<th>' . esc_html__( 'Date', 'auctions-for-woocommerce' ) . '</th>';
			}
			echo '<th>' . esc_html__( 'Auction', 'auctions-for-woocommerce' ) . '</th>';
			if ( true !== $hide_usernames ) {
				echo '<th>' . esc_html__( 'Username', 'auctions-for-woocommerce' ) . '</th>';
			}
			echo '<th>' . esc_html__( 'Bid', 'auctions-for-woocommerce' ) . '</th>';
			if ( true !== $hide_status ) {
				echo '<th>' . esc_html__( 'Status', 'auctions-for-woocommerce' ) . '</th>';
			}
			echo '</tr>';

			foreach ( $auction_activity as $value ) {
				if ( get_post_status( $value->auction_id ) === 'publish' ) {
					$class   = '';
					$product = wc_get_product( $value->auction_id );

					if ( $product && $product->is_type( 'auction' ) && ! $product->is_sealed() ) {
						if ( $product->is_closed() ) {
							$class .= 'closed ';
						}

						if ( $product->get_auction_current_bider() === $user_id && ! $product->is_sealed() ) {
							$class .= 'winning ';
						}

						if ( $product->get_auction_current_bider() === $user_id && ! $product->is_reserve_met() ) {
							$class .= 'reserved ';
						}

						if ( strtotime( $product->get_auction_relisted() ) > strtotime( $value->date ) ) {
							$class .= 'relisted ';
						}

						echo '<tr class="' . esc_attr( $class ) . '">';
						if ( true !== $hide_time ) {
							echo '<td>' . esc_html( gmdate( $date_format, strtotime( $value->date ) ) ) . '</td>';
						}
						echo '<td><a href="' . esc_url( get_permalink( $value->auction_id ) ) . '">' . esc_html( get_the_title( $value->auction_id ) ) . '</a></td>';
						if ( true !== $hide_usernames ) {
							echo '<td>';
							$userdata = get_userdata( $value->userid );
							if ( $userdata ) {
								echo esc_attr( $userdata->user_nicename );
							} else {
								esc_html_e( 'n/a', 'auctions-for-woocommerce' );
							}
							echo '</td>';
						}
						echo '<td>' . wp_kses_post( wc_price( $value->bid ) ) . '</td>';
						if ( true !== $hide_status ) {
							echo '<td>' . wp_kses_post( $product->get_price_html() ) . '</td>';
						}
						echo '</tr>';
					}
				}
			}
			echo '</table>';

			$this->widget_end( $args );
		} else {
			echo '<div class="woocommerce"><p class="woocommerce-info">' . esc_html__( 'There is not any bids at the moment!.', 'auctions-for-woocommerce' ) . '</p></div>';
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		echo wp_kses_post( $content );

		$this->cache_widget( $args, $content );
	}
}
