<?php
/**
 * Customer outbid email
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
		// translators: 1) auction url 2) auction title 3) bid amount
		wp_kses_post( __( "Hi there. You have placed a bid for item <a href='%1\$s'>%2\$s</a>. Bid amount is: %3\$s", 'auctions-for-woocommerce' ) ),
		esc_url( get_permalink( $product_id ) ),
		esc_html( $product_data->get_title() ),
		wp_kses_post( wc_price( $product_data->get_curent_bid() ) )
	);
	?>
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
