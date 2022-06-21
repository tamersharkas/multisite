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

class Auctions_For_Woocommerce_Widget_My_Auction extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_my_auctions';
		$this->widget_description = esc_html__( 'Display a list of auctions user participate.', 'auctions-for-woocommerce' );
		$this->widget_id          = 'woocommerce_my_auctions';
		$this->widget_name        = esc_html__( 'WooCommerce my Auction', 'auctions-for-woocommerce' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'My auctions', 'auctions-for-woocommerce' ),
				'label' => __( 'Title', 'auctions-for-woocommerce' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => esc_html__( 'Number of auctions to show:', 'auctions-for-woocommerce' ),
			),
			'hide_time' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => esc_html__( 'Hide time left', 'auctions-for-woocommerce' ),
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

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

		$title       = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'My Auctions', 'auctions-for-woocommerce' ) : $instance['title'], $instance, $this->id_base );
		$user_id     = get_current_user_id();
		$postids     = array();
		$userauction = $wpdb->get_results( $wpdb->prepare( 'SELECT  DISTINCT auction_id FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE userid = %d', $user_id ), ARRAY_N );

		if ( ! empty( $userauction ) ) {
			foreach ( $userauction as $auction ) {
				$postids[] = $auction[0];
			}
		} else {
			return;
		}

		$query_args = array(
			'posts_per_page' => $number,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
		);

		$query_args['post__in']   = $postids;
		$query_args['meta_query'] = WC()->query->get_meta_query();

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'auction',
			),
			AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now' ) ),
		);

		$query_args['auction_arhive'] = true;

		$r = new WP_Query( $query_args );

		if ( $r->have_posts() ) {

			$this->widget_start( $args, $instance );

			echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );

			$template_args = array(
				'widget_id'   => $args['widget_id'],
				'hide_time' => empty( $instance['hide_time'] ) ? 0 : 1,
			);

			while ( $r->have_posts() ) {
				$r->the_post();
				wc_get_template( 'content-widget-auction-product.php', $template_args );
			}

			echo wp_kses_post( apply_filters( 'woocommerce_after_widget_product_list', '</ul>' ) );

			$this->widget_end( $args );
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		echo wp_kses_post( $content );

		$this->cache_widget( $args, $content );
	}
}
