<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Customer Outbid Email
 *
 * Customer note emails are sent for remind to pay.
 *
 * @class WC_Email_Auction_Reminde_To_Pay
 * @extends WC_Email
 */

class WC_Email_Auction_Reminde_To_Pay extends WC_Email {

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

		$this->id             = 'remind_to_pay';
		$this->title          = __( 'Reminder to Pay', 'auctions-for-woocommerce' );
		$this->description    = __( 'Reminder for the customer that won the auction to pay.', 'auctions-for-woocommerce' );
		$this->customer_email = true;
		$this->template_html  = 'emails/auction_remind_to_pay.php';
		$this->template_plain = 'emails/plain/auction_remind_to_pay.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'Payment reminder won on {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'Reminder for you to pay the auction that you won.', 'auctions-for-woocommerce' );
		$this->interval       = '7';
		$this->stopsending    = '5';
		$this->checkout_url   = auctions_for_woocommerce_get_checkout_url();

		// Triggers
		add_action( 'auctions_for_woocommerce_pay_reminder_notification', array( $this, 'trigger' ) );

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
			$product_data = wc_get_product( $product_id );

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
	  /**
	 * Initialise Settings Form Fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce' ),
				'default' => 'yes',
			),
			'interval' => array(
				'title'             => __( 'Send mail intervals in days', 'woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '0',
				),
				/* translators: %s: number of days*/
				'description'       => sprintf( __( 'Send reminder mail intervals in days default is <code>%s</code>.', 'woocommerce' ), $this->interval ),
				'placeholder'       => '',
				'default'           => '',
			),
			'stopsending' => array(
				'title'             => __( 'Stop sending reminder', 'woocommerce' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '0',
				),
				/* translators: %s: number of mails sent */
				'description'       => sprintf( __( 'Stop sending reminder mail after number of emails is sent  default is <code>%s</code>.', 'woocommerce' ), $this->stopsending ),
				'placeholder'       => '',
				'default'           => '',
			),
			'subject' => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				/* translators: %s: mail subject */
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'woocommerce' ),
				'type'        => 'text',
				/* translators: %s: mail heading */
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
