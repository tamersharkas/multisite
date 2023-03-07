<?php
/**
 * Admin auction fail email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$product_data = wc_get_product( $product_id );

echo '= ' . wp_kses_post( $email_heading ) . ' =\n\n';

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";



printf(
	// translators: 1) auction title 2) winning bid value
	esc_html__( 'The auction for %1$s finished. Winning bid is %2$s ', 'auctions-for-woocommerce' ),
	esc_html( $product_data->get_title() ),
	wp_kses_post( wc_price( $product_data->get_curent_bid() ) )
);
echo "\n\n";
echo esc_url( get_permalink( $product_id ) );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
