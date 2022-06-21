<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Auction Search Widget
 *
 * @extends  WC_Widget
 */
class Auctions_For_Woocommerce_Widget_Auction_Search extends WC_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_auction_search';
		$this->widget_description = __( 'A Search box for auctions only.', 'auctions-for-woocommerce' );
		$this->widget_id          = 'woocommerce_auction_search';
		$this->widget_name        = __( 'WooCommerce Auction Search', 'auctions-for-woocommerce' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'auctions-for-woocommerce' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		get_product_search_form();

		$this->widget_end( $args );
	}
}
