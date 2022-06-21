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
	add_query_arg( 'do_update_woocommerce', 'true', admin_url( 'admin.php?page=wc-settings' ) ),
	'wc_db_update',
	'wc_db_update_nonce'
);

?>

	<p>
		<?php esc_html_e( 'Auctions for WooCommerce data update complete. Thank you for updating to the latest version!', 'auctions-for-woocommerce' ); ?></strong> 
	</p>
