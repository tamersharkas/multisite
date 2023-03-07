<?php
/**
* Loop counter
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;

$time = '';

if ( ! isset( $product ) || ! $product->is_type( 'auction' ) ) {
	return;
}

$time       = '';
$timetext   = esc_html__( 'Time left', 'auctions-for-woocommerce' );
$datatime   = $product->get_seconds_remaining();
$product_id = $product->get_id();
$hide_time  = isset( $hide_time ) ? $hide_time : 0;

if ( ! $product->is_started() ) {
	$timetext = esc_html__( 'Starting in', 'auctions-for-woocommerce' );
	$datatime = $product->get_seconds_to_auction();
}
if ( 1 !== $hide_time && ! $product->is_closed() ) {
	$time = '<span class="time-left">' . apply_filters( 'time_text', $timetext, $product_id ) . '</span> <div class="auction-time-countdown" data-time="' . $datatime . '" data-auctionid="' . $product_id . '" data-format="' . get_option( 'auctions_for_woocommerce_countdown_format' ) . '"></div>';
}
if ( $product->is_closed() ) {
	$time = '<span class="has-finished"> ' . apply_filters( 'time_text', esc_html__( 'Auction finished', 'auctions-for-woocommerce' ), $product_id ) . '</span>';
}
echo wp_kses_post( $time );
