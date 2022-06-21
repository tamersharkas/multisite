<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/includes
 */
class Auctions_For_Woocommerce_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'auctions_for_woocommerce_send_reminders_email' );
	}

}
