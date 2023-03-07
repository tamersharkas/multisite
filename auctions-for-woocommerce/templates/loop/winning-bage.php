<?php
/**
* Wining badge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( $product && $product->is_type( 'auction' ) ) :

	$user_id = get_current_user_id();

	if ( $user_id === $product->get_auction_current_bider() && ! $product->get_auction_closed() && ! $product->is_sealed() ) :

		echo wp_kses_post( apply_filters( 'auctions_for_woocommerce_winning_bage', '<span class="winning" data-auction_id="' . $product->get_id() . '" data-user_id="' . get_current_user_id() . '">' . __( 'Winning!', 'auctions-for-woocommerce' ) . '</span>', $product ) );

	endif;
endif;


