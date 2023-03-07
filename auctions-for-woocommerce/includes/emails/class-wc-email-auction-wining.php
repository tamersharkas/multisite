<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Auction won emails
 * Auction won emails are sent when a user wins the auction.
 * 
 * @class WC_Email_SA_Outbid
 * @extends WC_Email
 */

class WC_Email_Auction_Wining extends WC_Email {

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

		$this->id             = 'auction_win';
		$this->title          = __( 'Auction Won', 'auctions-for-woocommerce' );
		$this->description    = __( 'Auction won emails are sent when a user wins the auction.', 'auctions-for-woocommerce' );
		$this->customer_email = true;
		$this->template_html  = 'emails/auction_win.php';
		$this->template_plain = 'emails/plain/auction_win.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'Auction won on {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'You have won the auction!', 'auctions-for-woocommerce' );
		$this->checkout_url   = auctions_for_woocommerce_get_checkout_url();

		// Triggers
		add_action( 'auctions_for_woocommerce_won_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	* Trigger function.
	 *
	 * @return void
	 */
	public function trigger( $product_id, $force = false ) {

		if ( ! $this->is_enabled() ) {
			return;
		}

		if ( $product_id ) {
			if ( get_post_meta( $product_id, '_' . $this->id . '_email_sent', true ) && false === $force ) {
				return;
			}

			update_post_meta( $product_id, '_' . $this->id . '_email_sent', '1' );

			$product_data = wc_get_product( $product_id );

			$customer_user = get_post_meta( $product_id, '_auction_current_bider', true );

			if ( $product_data ) {

				if ( $customer_user ) {

					if ( is_array( $customer_user ) ) {
						foreach ( $customer_user as $key => $user ) {
							$this->object      = new WP_User( absint( $user ) );
							$this->recipient   = $this->object->user_email;
							$this->auction_id  = $product_id;
							$this->winning_bid = get_post_meta( $product_id, '_auction_winning_bid_' . absint( $user ), true );

							$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
						}
					} else {
						$this->object      = new WP_User( absint( $customer_user ) );
						$this->recipient   = $this->object->user_email;
						$this->auction_id  = $product_id;
						$this->winning_bid = $product_data->get_curent_bid();

						$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
					}
				}
			}
		}
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
