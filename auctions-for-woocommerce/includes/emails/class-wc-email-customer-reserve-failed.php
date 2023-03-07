<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Customer Outbid Email
 *
 * Customer note emails are sent when you add a note to an order.
 *
 * @class WC_Email_SA_Outbid
 * @extends WC_Email
 */

class WC_Email_Customer_Reserve_Failed extends WC_Email {

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
	 * Faild reason
	 * 
	 * @var string
	 */
	public $reason;


	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->id             = 'Reserve_fail';
		$this->title          = __( 'Reserve Fail', 'auctions-for-woocommerce' );
		$this->description    = __( 'Reserve Fail emails are sent to user when the auction is finished but didn\'t make the reserve price', 'auctions-for-woocommerce' );
		$this->customer_email = true;
		$this->template_html  = 'emails/reserve_fail.php';
		$this->template_plain = 'emails/plain/reserve_fail.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'Auction didn\'t succeed {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'Auction didn\'t make it to the reserve price!', 'auctions-for-woocommerce' );

		// Triggers
		add_action( 'auctions_for_woocommerce_reserve_fail_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	* Trigger function.
	 *
	 * @return void
	 */
	public function trigger( $args ) {
		if ( $args['product_id'] ) {

			if ( get_post_meta( $args['product_id'], '_' . $this->id . '_email_sent', true ) ) {
				return;
			}

			update_post_meta( $args['product_id'], '_' . $this->id . '_email_sent', '1' );

			if ( $args['user_id'] ) {
				$this->object    = new WP_User( $args['$user_id'] );
				$this->recipient = $this->object->user_email;
			}
			if ( $args['product_id'] ) {
				$product_data      = wc_get_product( $args['product_id'] );
				$this->auction_id  = $args['product_id'];
				$this->current_bid = $product_data->get_curent_bid();
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
				'product_id'    => $this->auction_id,
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
		wc_get_template($this->template_plain,
			array(
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'product_id'    => $this->auction_id,
				'email'         => $this,
			)
		);
		return ob_get_clean();
	}
}
