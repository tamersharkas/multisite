<?php
/**
 * User placed a bid email notification
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
		// translators: 1) auction url 2) auction title 3) bid value
		wp_kses_post( __( "Hi there. A bid was placed for <a href='%1\$s'>%2\$s</a>. Bid: %3\$s", 'auctions-for-woocommerce' ) ),
		esc_url( get_permalink( $product_id ) ),
		esc_html( $product_data->get_title() ),
		wp_kses_post( wc_price( $product_data->get_curent_bid() ) )
	);
	?>
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
