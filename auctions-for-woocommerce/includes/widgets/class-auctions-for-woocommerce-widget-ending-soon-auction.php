<?php
/**
 * Ending Soon Auctions Widget
 *
* @package Widgets
 * @version 1.0.0
 * @extends WP_Widget
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Widget top rated products class.
 */
class Auctions_For_Woocommerce_Widget_Ending_Soon_Auction extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget__ending_soon_auctions';
		$this->widget_description = esc_html__( 'Display a list of your ending soon auctions on your site.', 'auctions-for-woocommerce' );
		$this->widget_id          = 'woocommerce_ending_soon_auctions';
		$this->widget_name        = esc_html__( 'WooCommerce Ending Soon Auctions', 'auctions-for-woocommerce' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Ending soon auctions', 'auctions-for-woocommerce' ),
				'label' => __( 'Title', 'woocommerce' ),
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
			'furure_auctions' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => esc_html__( 'Show future auctions', 'auctions-for-woocommerce' ),
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

		ob_start();

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

		$query_args = array(
			'posts_per_page' => $number,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
		);

		$query_args['meta_query']   = array();
		$query_args['meta_query'][] = WC()->query->stock_status_meta_query();

		$query_args['meta_query'] = array_filter( $query_args['meta_query'] );
		$query_args['tax_query']  = array(
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'auction',
			),
		);
		if ( empty( $instance['furure_auctions'] ) ) {
			$query_args['tax_query'][] = AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now', 'future' ) );
		} else {
			$query_args['tax_query'][] = AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now' ) );
		}
		$query_args['auction_arhive'] = true;
		$query_args['meta_key']       = '_auction_dates_to';
		$query_args['orderby']        = 'meta_value';
		$query_args['order']          = 'ASC';



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
