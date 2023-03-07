<?php
/**
 * Auction pay
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

if ( ! ( $product && $product->is_type( 'auction' ) ) ) {
	return;
}

$user_id = get_current_user_id();

if ( ( $user_id === $product->get_auction_current_bider() && 2 === $product->get_auction_closed() && ! $product->get_auction_payed() ) ) :
	?>

	<p><?php esc_html_e( 'Congratulations you have won this auction!', 'auctions-for-woocommerce' ); ?></p>

	<?php if ( ! ( 'reverse' === $product->get_auction_type() && 'yes' === get_option( 'auctions_for_woocommerce_remove_pay_reverse' ) ) ) { ?>
		<p><a href="<?php echo esc_url( apply_filters( 'auctions_for_woocommerce_pay_now_button', esc_attr( add_query_arg( 'pay-auction', $product->get_id(), auctions_for_woocommerce_get_checkout_url() ) ) ) ); ?>" class="button"><?php esc_html_e( 'Pay Now', 'auctions-for-woocommerce' ); ?></a></p>
	<?php } ?>	

<?php endif; ?>
