<?php
/**
 * Customer remind to pay email
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$product_data = wc_get_product( $product_id );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>
	<?php
	printf(
		// translators: 1) auction url 2) auction title 3) number of hours
		wp_kses_post( __( "The auction for <a href='%1\$s'>%2\$s</a>.  has been relisted. Reason: auction not paid for %3\$s hours", 'auctions-for-woocommerce' ) ),
		esc_url( get_permalink( $product_id ) ),
		esc_html( $product_data->get_title() ),
		esc_html( $product_data->get_auction_relist_not_paid_time() )
	);
	?>
</p>



<?php do_action( 'woocommerce_email_footer', $email ); ?>
