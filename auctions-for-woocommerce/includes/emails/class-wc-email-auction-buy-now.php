<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Customer Buy Now
 *
 * Customer note emails are sent to winning bidder when someone buy item for buy now price
 *
 * @class WC_Email_Auction_Buy_Now
 * @extends WC_Email
 */

class WC_Email_Auction_Buy_Now extends WC_Email {

	/**
	 * Winning bid
	 * 
	 * @var float
	 */
	public $winning_bid;

	/**
	 * Title
	 * 
	 * @var string
	 */
	public $title;

	/** 
	 * Auction Id
	 * 
	 * @var int
	 */
	public $auction_id;

	/** 
	 * Checkout url
	 * 
	 * @var string
	 */
	public $checkout_url;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->id             = 'auction_buy_now';
		$this->title          = __( 'Auction Buy Now', 'auctions-for-woocommerce' );
		$this->description    = __( 'Auction buy now emails are sent to winning bidder when someone buys the item for the buy now price.', 'auctions-for-woocommerce' );
		$this->customer_email = true;
		$this->template_html  = 'emails/auction_buy_now.php';
		$this->template_plain = 'emails/plain/auction_buy_now.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'Item sold on {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'Item sold for buy now price', 'auctions-for-woocommerce' );
		$this->checkout_url   = auctions_for_woocommerce_get_checkout_url();

		// Triggers
		add_action( 'auctions_for_woocommerce_close_buynow_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	* Trigger function.
	 *
	 * @return void
	 */
	public function trigger( $product_id ) {

		if ( $product_id ) {
			$product_data  = wc_get_product( $product_id );
			$customer_user = absint( get_post_meta( $product_id, '_auction_current_bider', true ) );

			if ( $product_data ) {
				if ( $customer_user ) {
					$this->object    = new WP_User( $customer_user );
					$this->recipient = $this->object->user_email;
				}

				$this->auction_id  = $product_id;
				$this->winning_bid = $product_data->get_curent_bid();
			}
		}
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

	}

	/**
	* Get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'current_bid'   => $this->winning_bid,
				'product_id'    => $this->auction_id,
				'checkout_url'  => $this->checkout_url,
				'email'         => $this,
			)
		);

		return ob_get_clean();
	}

	/**
	* Get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'current_bid'   => $this->winning_bid,
				'product_id'    => $this->auction_id,
				'checkout_url'  => $this->checkout_url,
				'email'         => $this,
			)
		);
		return ob_get_clean();
	}
}
