<?php
/**
* Loop Add to Cart
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;
?>

<?php if ( $product && $product->is_type( 'auction' ) ) : ?>
	<?php echo wp_kses_post( apply_filters( 'auctions_for_woocommerce_auction_bage', '<span class="auction-bage"  ></span>', $product ) ); ?>
	<?php
endif;
