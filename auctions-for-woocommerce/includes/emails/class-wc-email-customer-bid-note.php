<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Customer Outbid Email
 *
 * Customer note emails are sent when you add a note to an order.
 *
 * @class WC_Email_Customer_Bid_Note
 * @extends WC_Email
 */

class WC_Email_Customer_Bid_Note extends WC_Email {

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
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->id             = 'customer_bid_note';
		$this->title          = __( 'Customer bid notification', 'auctions-for-woocommerce' );
		$this->description    = __( 'Customer bid emails are sent to customer when customer places bid (confirmation email)', 'auctions-for-woocommerce' );
		$this->customer_email = true;
		$this->template_html  = 'emails/customerbid.php';
		$this->template_plain = 'emails/plain/customerbid.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'You have placed a bid on {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'You have placed a bid on {site_title}', 'auctions-for-woocommerce' );
		$this->proxy          = $this->get_option( 'proxy' );

		// Triggers
		add_action( 'auctions_for_woocommerce_place_bid_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	* Trigger function.
	 *
	 * @return void
	 */
	public function trigger( $args ) {

		if ( isset( $args['product_id'] ) ) {
			$customer_user = absint( get_post_meta( $args['product_id'], '_auction_current_bider', true ) );
			$autobid       = get_post_meta( $args['product_id'], '_auction_current_bid_proxy', true );

			if ( 'yes' === $autobid && 'no' === $this->proxy ) {
				return;
			}

			if ( $customer_user ) {
				$this->object    = new WP_User( $customer_user );
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
				'current_bid'   => $this->current_bid,
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
		/**
		 * Initialise Settings Form Fields
		 *
		 * @return void
		 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce' ),
				'default' => 'yes',
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'woocommerce' ),
				'type'        => 'text',
				/* translators: %s: admin email */
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => '',
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				/* translators: %s: mail subject */
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'woocommerce' ),
				'type'        => 'text',
				/* translators: %s: mail headingl */
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
				'placeholder' => '',
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'woocommerce' ),
					'html'      => __( 'HTML', 'woocommerce' ),
					'multipart' => __( 'Multipart', 'woocommerce' ),
				),
			),
		);
	}
}
