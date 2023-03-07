<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Customer Outbid Email
 *
 * Customer note emails are sent when you add a note to an order.
 *
 * @class WC_Email_Auction_Relist
 * @extends WC_Email
 */

class WC_Email_Auction_Relist extends WC_Email {

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

		$this->id             = 'auction_relist';
		$this->title          = __( 'Auction relist admin notice', 'auctions-for-woocommerce' );
		$this->description    = __( 'Auction relist admin emails are sent to admin when auction is relisted.', 'auctions-for-woocommerce' );
		$this->template_html  = 'emails/auction_relist_admin.php';
		$this->template_plain = 'emails/plain/auction_relist_admin.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'Auction Relisted on {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'Auction relisted!', 'auctions-for-woocommerce' );

		// Triggers
		add_action( 'woocomerce_before_relist_failed_auction_notification', array( $this, 'trigger' ) );
		add_action( 'woocomerce_before_relist_not_paid_auction_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}

	/**
	* Trigger function.
	 *
	 * @return void
	 */
	public function trigger( $auction_id ) {

		$product = wc_get_product( $auction_id );

		if ( $auction_id ) {

			$this->auction_id = $auction_id;
			$this->object     = $product;

		}

		if ( $product->get_auction_closed() === 1 ) {
			/* translators: %s: number of hours */
			$this->reason = sprintf( esc_html__( 'Failed auction relisted after %s hours', 'auctions-for-woocommerce' ), $product->get_auction_relist_fail_time() );
		} else {
			/* translators: %1$s: username  %2$s number of hours*/
			$this->reason = sprintf( esc_html__( ' user %1$s has not paid for auction in %2$s hours.', 'auctions-for-woocommerce' ), get_userdata( $product->get_auction_current_bider() )->username, $product->get_auction_relist_not_paid_time() );
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
				'reason'        => $this->reason,
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
				'product_id'    => $this->auction_id,
				'reason'        => $this->reason,
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
