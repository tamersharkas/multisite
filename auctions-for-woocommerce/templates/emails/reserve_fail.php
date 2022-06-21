<?php
/**
 * Admin auction failed email
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
			// translators: 1) auction url 2) auction title
			wp_kses_post( __( "Sorry. The auction for <a href='%1\$s'>%2\$s</a> has failed because it did not make the reserve price.", 'auctions-for-woocommerce' ) ),
			esc_url( get_permalink( $product_id ) ),
			esc_html( $product_data->get_title() )
		);
		?>
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
