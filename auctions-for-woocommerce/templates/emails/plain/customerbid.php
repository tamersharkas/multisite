<?php
/**
 * Customer outbid email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$product_data = wc_get_product( $product_id );

echo '= ' . wp_kses_post( $email_heading ) . ' =\n\n';

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

printf(
	// translators: 1) auction title
	esc_html__( 'Hi there. You have placed bid for item  %s ', 'auctions-for-woocommerce' ),
	esc_html( $product_data->get_title() )
);
echo "\n\n";
echo esc_url( get_permalink( $product_id ) );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
