<?php
/**
 * Recently Viewed Auctions Widget
 *
 * @package    Widgets
 * @version     1.0.0
 * @extends     WP_Widget
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Auctions_For_Woocommerce_Widget_Recently_Viewed_Auction extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_recently_viewed_auctions';
		$this->widget_description = esc_html__( 'Display a list of recently viewed auctions.', 'auctions-for-woocommerce' );
		$this->widget_id          = 'woocommerce_recently_viewed_auctions';
		$this->widget_name        = esc_html__( 'WooCommerce Recently Viewed Auctions', 'auctions-for-woocommerce' );
		$this->settings           = array(
			'title'     => array(
				'type'  => 'text',
				'std'   => __( 'Recent viewed auctions', 'auctions-for-woocommerce' ),
				'label' => __( 'Title', 'auctions-for-woocommerce' ),
			),
			'number'    => array(
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

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed_auctions'] ) ? (array) explode( '|', sanitize_text_field( $_COOKIE['woocommerce_recently_viewed_auctions'] ) ) : array();
		$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

		if ( empty( $viewed_products ) ) {
			return;
		}

		ob_start();

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

		$query_args = array(
			'posts_per_page' => $number,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'post__in'       => $viewed_products,
			'orderby'        => 'rand',
		);

		$query_args['meta_query']     = array();
		$query_args['meta_query'][]   = $woocommerce->query->stock_status_meta_query();
		$query_args['meta_query']     = array_filter( $query_args['meta_query'] );
		$query_args['tax_query']      = array(
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'auction',
			),
		);
		$query_args['auction_arhive'] = true;
		$r                            = new WP_Query( $query_args );

		if ( $r->have_posts() ) {

			$this->widget_start( $args, $instance );

			echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );

			$template_args = array(
				'widget_id' => $args['widget_id'],
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
