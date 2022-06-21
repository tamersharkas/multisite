<?php
/**
 * Customer buy now email
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly
$product_data = wc_get_product( $product_id );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>
	<?php
		printf(
			// translators: 1) Auction title
			esc_html__( 'Hi there. Item that you are bidding for (%s) was sold for buy now price. Better luck next time! ', 'auctions-for-woocommerce' ),
			esc_html( $product_data->get_title() )
		);
		?>
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
