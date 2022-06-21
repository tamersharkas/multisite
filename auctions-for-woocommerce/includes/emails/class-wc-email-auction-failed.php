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

class WC_Email_Auction_Failed extends WC_Email {

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

		$this->id             = 'auction_fail';
		$this->title          = __( 'Auction Fail', 'auctions-for-woocommerce' );
		$this->description    = __( 'Auction Fail emails are sent when auction fails.', 'auctions-for-woocommerce' );
		$this->template_html  = 'emails/auction_fail.php';
		$this->template_plain = 'emails/plain/auction_fail.php';
		$this->template_base  = AFW_ABSPATH . 'templates/';
		$this->subject        = __( 'Auction Failed on {site_title}', 'auctions-for-woocommerce' );
		$this->heading        = __( 'No interest in this auction!', 'auctions-for-woocommerce' );

		// Triggers
		add_action( 'auctions_for_woocommerce_fail_notification', array( $this, 'trigger' ) );

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
	public function trigger( $args ) {

		if ( isset( $args['auction_id'] ) ) {

			if ( get_post_meta( $args['auction_id'], '_' . $this->id . '_email_sent', true ) ) {
				return;
			}

			update_post_meta( $args['auction_id'], '_' . $this->id . '_email_sent', '1' );
			$this->auction_id = $args['auction_id'];
			$this->object     = wc_get_product( $args['auction_id'] );
			$this->reason     = $args['reason'];
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
