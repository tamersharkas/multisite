<?php
/**
 * Email auction won
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$product_data = wc_get_product( $product_id );
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<?php if ( ( $product_data->get_auction_type() === 'reverse' ) && ( get_option( 'auctions_for_woocommerce_remove_pay_reverse' ) === 'yes' ) ) { ?>
	<p>
		<?php
		printf(
			// translators: 1) auction url 2) auction title 3) bid value
			wp_kses_post( __( "Congratulations. You have won the auction for <a href='%1\$s'>%2\$s</a>. Your bid was: %3\$s.", 'auctions-for-woocommerce' ) ),
			esc_url( get_permalink( $product_id ) ),
			esc_html( $product_data->get_title() ),
			wp_kses_post( wc_price( $current_bid ) )
		);
		?>
	</p>
<?php } else { ?>
	<p>
		<?php
		printf(
			// translators: 1) auction url 2) auction title 3) bid value 4) payment link
			wp_kses_post( __( "Congratulations. You have won the auction for <a href='%1\$s'>%2\$s</a>. Your bid was: %3\$s. Please click on this link to pay for your auction %4\$s ", 'auctions-for-woocommerce' ) ),
			esc_url( get_permalink( $product_id ) ),
			esc_html( $product_data->get_title() ),
			wp_kses_post( wc_price( $current_bid ) ),
			'<a href="' . esc_url( add_query_arg( 'pay-auction', $product_id, $checkout_url ) ) . '">' . esc_html__( 'payment', 'auctions-for-woocommerce' ) . '</a>'
		);
		?>
	</p>
<?php } ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
