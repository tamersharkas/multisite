<?php
/**
 * Auction watchlist link
 *
 */
global $product;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( is_null( $product ) ) {
	$product = wc_get_product( $product_id );
}
$user_id = get_current_user_id();
?>

<p class="wsawl-link">
	<?php if ( $product->is_user_watching() ) { ?>
		<a href="#remove from watchlist" data-auction-id="<?php echo intval( $product->get_id() ); ?>" class="remove-wsawl sa-watchlist-action"><?php esc_html_e( 'Remove from watchlist!', 'auctions-for-woocommerce' ); ?></a>
	<?php } else { ?>
		<a href="#add_to_watchlist" 
		data-auction-id="<?php echo esc_attr( $product->get_id() ); ?>" 
		class="add-wsawl sa-watchlist-action 
		<?php
		if ( 0 === $user_id ) {
			echo ' no-action ';}
		?>
		" 
		title="
		<?php
		if ( 0 === $user_id ) {
			esc_html_e( 'You must be logged in to use watchlist feature', 'auctions-for-woocommerce' );}
		?>
		">
		<?php esc_html_e( 'Add to watchlist!', 'auctions-for-woocommerce' ); ?></a>
	<?php } ?>
</p>
