<?php
/**
 * Email auction won (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$product_data = wc_get_product( $product_id );

echo '= ' . wp_kses_post( $email_heading ) . ' =\n\n';

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

printf(
	// translators: 1) auction title 2) bid value
	esc_html__( 'Congratulations! You have the won auction for %1$s. Your bid was: %2$s. Please click on this link to pay for your auction', 'auctions-for-woocommerce' ),
	esc_html( $product_data->get_title() ),
	wp_kses_post( wc_price( $current_bid ) )
);
echo "\n\n";
echo esc_attr( add_query_arg( 'pay-auction', $product_id, $checkout_url ) );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
