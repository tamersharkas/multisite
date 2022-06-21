<?php
/**
 * The template for displaying auction product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-auction-widget-product.php.
 *
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<li>
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
		<?php echo wp_kses_post( $product->get_image() ); ?>
		<span class="product-title"><?php echo esc_html( $product->get_name() ); ?></span>
	</a>

	<?php echo wp_kses_post( $product->get_price_html() ); ?>
	<?php wc_get_template( 'global/counter.php', array( 'hide_time' => $hide_time ) ); ?>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
