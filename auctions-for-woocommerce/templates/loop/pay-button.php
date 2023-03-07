<?php
/**
* Pay button
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
if ( $product && $product->is_type( 'auction' ) ) :
	$user_id = get_current_user_id();

	if ( $user_id === $product->get_auction_current_bider() && 2 === $product->get_auction_closed() && ! $product->get_auction_payed() ) : ?>

		<a href="<?php echo wp_kses_post( apply_filters( 'auctions_for_woocommerce_pay_now_button', esc_url( add_query_arg( 'pay-auction', $product->get_id(), auctions_for_woocommerce_get_checkout_url() ) ) ) ); ?>" class="button"><?php esc_html_e( 'Pay Now', 'auctions-for-woocommerce' ); ?></a>

	<?php endif; ?>
<?php endif; ?>
