<?php
/**
 * Email notification template (plain) for auctions closing soon.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $woocommerce;

$product_data = wc_get_product( $product_id );

echo '= ' . wp_kses_post( $email_heading ) . ' =\n\n';

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

printf(
	// translators: 1) auction title 2) closing time 3) current bid value
	esc_html__( 'Auction %1$s is going to is going to be closed at %2$s. Current bid is %3$s', 'auctions-for-woocommerce' ),
	esc_html( $product_data->get_title() ),
	esc_html( date_i18n( get_option( 'date_format' ), strtotime( $product_data->get_auction_end_time() ) ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $product_data->get_auction_end_time() ) ) ),
	wp_kses_post( wc_price( $product_data->get_curent_bid() ) )
);

echo "\n\n";
echo esc_url( get_permalink( $product_id ) );
echo "\n\n";
echo "\n\n";
echo esc_html__( 'To unsubscribe from ending soon emails click on link below', 'auctions-for-woocommerce' );
echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '/auctions-endpoint/' );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
