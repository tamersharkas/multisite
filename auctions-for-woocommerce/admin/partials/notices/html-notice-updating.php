<?php
/**
 * Admin View: Notice - Updating
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$update_url = wp_nonce_url(
	add_query_arg( 'do_update_wsa', 'true', admin_url( 'admin.php?page=wc-settings' ) ),
	'wsa_db_update',
	'wsa_db_update_nonce'
);

?>

	<p>
		<strong><?php esc_html_e( 'Auctions for WooCommerce needs data update', 'auctions-for-woocommerce' ); ?></strong> <a href="<?php echo esc_url( $update_url ); ?>"><?php esc_html_e( 'Click here to run it now.', 'auctions-for-woocommerce' ); ?></a>
	</p>
