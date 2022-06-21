<?php
/**
 * Auction history tab
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post, $product;

$datetimeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

?>

<h2><?php echo esc_html( apply_filters( 'woocommerce_auction_history_heading', __( 'Auction History', 'auctions-for-woocommerce' ) ) ); ?></h2>

<?php if ( ( $product->is_closed() === true ) && ( $product->is_started() === true ) ) : ?>

	<p><?php esc_html_e( 'Auction has finished', 'auctions-for-woocommerce' ); ?></p>
	<?php
	if ( $product->get_auction_fail_reason() === 1 ) {
		esc_html_e( 'Auction failed because there were no bids', 'auctions-for-woocommerce' );
	} elseif ( $product->get_auction_fail_reason() === 2 ) {
		esc_html_e( 'Auction failed because item did not make it to reserve price', 'auctions-for-woocommerce' );
	}

	if ( $product->get_auction_closed() === 3 ) {
		?>
		<p><?php esc_html_e( 'Product sold for buy now price', 'auctions-for-woocommerce' ); ?>: <span><?php echo wp_kses_post( wc_price( $product->get_regular_price() ) ); ?></span></p>
	<?php } elseif ( $product->get_auction_current_bider() ) { ?>
		<p><?php esc_html_e( 'Highest bidder was', 'auctions-for-woocommerce' ); ?>: <span><?php echo esc_html( apply_filters( 'auctions_for_woocommerce_displayname', $product->get_auction_current_bider_displayname() ) ); ?></span></p>
	<?php } ?>
<?php endif; ?>	

<table id="auction-history-table-<?php echo intval( $product->get_id() ); ?>" class="auction-history-table">
	<?php

		$auction_history = apply_filters( 'woocommerce__auction_history_data', $product->auction_history() );

	if ( ! empty( $auction_history ) ) :
		?>

		<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'auctions-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Bid', 'auctions-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'User', 'auctions-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Auto', 'auctions-for-woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ( $product->is_sealed() ) {

				echo '<tr>';
				echo "<td colspan='4'  class='sealed'>" . esc_html__( 'This auction is sealed. Upon auction finish auction history and winner will be available to the public.', 'auctions-for-woocommerce' ) . '</td>';
				echo '</tr>';

		} else {
			foreach ( $auction_history as $history_value ) {
				echo '<tr id="logid-' . intval( $history_value->id ) . '">';
				echo "<td class='date'>" . esc_html( mysql2date( $datetimeformat, $history_value->date ) ) . '</td>';
				echo "<td class='bid'>" . wp_kses_post( wc_price( $history_value->bid ) ) . '</td>';
				echo "<td class='username'>" . esc_html( apply_filters( 'auctions_for_woocommerce_displayname', get_userdata( $history_value->userid )->display_name ) ) . '</td>';
				if ( '1' === $history_value->proxy ) {
					echo " <td class='proxy'>" . esc_html__( 'Auto', 'auctions-for-woocommerce' ) . '</td>';
				} else {
					echo " <td class='proxy'></td>";
				}
				echo '</tr>';
			}
		}
		?>
		</tbody>

	<?php endif; ?>
	<tr class="start">
		<?php
		if ( $product->is_started() === true ) {
			echo '<td class="date">' . esc_html( mysql2date( $datetimeformat, $product->get_auction_start_time() ) ) . '</td>';
			echo '<td colspan="3" class="started">';
			echo esc_html( apply_filters( 'auction_history_started_text', __( 'Auction started', 'auctions-for-woocommerce' ), $product ) );
			echo '</td>';

		} else {
				echo '<td  class="date">' . esc_html( mysql2date( $datetimeformat, $product->get_auction_start_time() ) ) . '</td>';
				echo '<td colspan="3"  class="starting">';
				echo esc_html( apply_filters( 'auction_history_starting_text', __( 'Auction starting', 'auctions-for-woocommerce' ), $product ) );
				echo '</td>';
		}
		?>
	</tr>
</table>
