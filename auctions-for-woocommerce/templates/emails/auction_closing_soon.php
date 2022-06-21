<?php
/**
 * Email notification template (HTML) for auctions closing soon.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$product_data = wc_get_product( $product_id );

do_action( 'woocommerce_email_header', $email_heading, $email );

?>

<p>
<?php
	// translators: 1) Auction url 2) Auction title 3) Auction end time 4) Current bid
	printf(
		wp_kses_post( __( "Auction <a href='%1\$s'>%2\$s</a> is going to be closed at %3\$s. Current bid is %4\$s", 'auctions-for-woocommerce' ) ),
		esc_url( get_permalink( $product_id ) ),
		esc_html( $product_data->get_title() ),
		esc_html(
			date_i18n(
				get_option( 'date_format' ),
				strtotime( $product_data->get_auction_end_time() )
			)
		) . ' ' . esc_html(
			date_i18n(
				get_option( 'time_format' ),
				strtotime( $product_data->get_auction_end_time() )
			)
		),
		wp_kses_post( wc_price( $product_data->get_curent_bid() ) )
	);
	?>
</p>
<p><small>
	<?php
	printf(
		// translators: 1) unsuscribe link
		wp_kses_post( __( "To unsubscribe from ending soon emails <a href='%s'>click here</a>", 'auctions-for-woocommerce' ) ),
		esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '/auctions-endpoint/' )
	);
	?>
	</small>
</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
