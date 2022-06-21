<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Customer Outbid Email
 *
 * Customer note emails are sent when you add a note to an order.
 *
 * @class WC_Email_Auction_Outbid_Note
 * @extends WC_Email
 */

class WC_Email_Auction_Outbid_Note extends WC_Email {

	/** 
	 * Current bid
	 * 
	 * @var float
	 */
	public $current_bid;

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
	 * Oudbid user id
	 * 
	 * @var int
	 */
	public $outbiddeduser_id;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->id             = 'outbid_note';
		$this->title          = __( 'Outbid note', 'auctions-for-woocommerce' );
		$this->description    = __( 'Outbid emails are sent when your users bid has been outbid.', 'auctions-for-woocommerce' );
		$this->customer_email = true;
		$this->template_html  = 'emails/outbid.php';
		$this->template_plain = 'emails/plain/outbid.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'Outbid item on {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'You have been outbid', 'auctions-for-woocommerce' );

		// Triggers
		add_action( 'auctions_for_woocommerce_outbid_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	* Trigger function.
	 *
	 * @return void
	 */
	public function trigger( $args ) {
		if ( $args ) {
			if ( $args['outbiddeduser_id'] ) {
				$this->outbiddeduser_id = $args['outbiddeduser_id'];
				$this->object           = new WP_User( $args['outbiddeduser_id'] );
				$this->recipient        = $this->object->user_email;
			}
			if ( $args['product_id'] ) {
				$product_data      = wc_get_product( $args['product_id'] );
				$this->auction_id  = $args['product_id'];
				$this->current_bid = $product_data->get_curent_bid();
				if ( $args['outbiddeduser_id'] === $product_data->get_auction_current_bider() ) {
					return;
				}
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
				'email_heading'    => $this->get_heading(),
				'blogname'         => $this->get_blogname(),
				'current_bid'      => $this->current_bid,
				'product_id'       => $this->auction_id,
				'outbiddeduser_id' => $this->outbiddeduser_id,
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
				'current_bid'   => $this->current_bid,
				'product_id'    => $this->auction_id,
				'email'         => $this,
			)
		);
		return ob_get_clean();
	}
}
