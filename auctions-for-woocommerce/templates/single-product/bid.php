<?php
/**
 * Auction bid
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $woocommerce, $product, $post;
if ( ! ( $product && $product->is_type( 'auction' ) ) ) {
	return;
}
$product_id       = $product->get_id();
$user_max_bid     = $product->get_user_max_bid( $product_id, get_current_user_id() );
$max_min_bid_text = $product->get_auction_type() === 'reverse' ? esc_html__( 'Your min bid is', 'auctions-for-woocommerce' ) : esc_html__( 'Your max bid is', 'auctions-for-woocommerce' );
$gmt_offset       = get_option( 'gmt_offset' ) > 0 ? '+' . get_option( 'gmt_offset' ) : get_option( 'gmt_offset' );
?>

<p class="auction-condition"><?php echo wp_kses_post( apply_filters( 'conditiond_text', esc_html__( 'Item condition:', 'auctions-for-woocommerce' ), $product ) ); ?><span class="curent-bid"> <?php echo esc_html( $product->get_condition() ); ?></span></p>

<?php if ( ( false === $product->is_closed ) && ( true === $product->is_started ) ) : ?>

	<div class="auction-time" id="countdown"><?php echo wp_kses_post( apply_filters( 'time_text', esc_html__( 'Time left:', 'auctions-for-woocommerce' ), $product_id ) ); ?> 
		<div class="main-auction auction-time-countdown" data-time="<?php echo esc_attr( $product->get_seconds_remaining() ); ?>" data-auctionid="<?php echo intval( $product_id ); ?>" data-format="<?php echo esc_attr( get_option( 'auctions_for_woocommerce_countdown_format' ) ); ?>"></div>
	</div>

	<div class='auction-ajax-change' >

		<p class="auction-end"><?php echo wp_kses_post( apply_filters( 'time_left_text', esc_html__( 'Auction ends:', 'auctions-for-woocommerce' ), $product ) ); ?> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $product->get_auction_end_time() ) ) ); ?>  <?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( $product->get_auction_end_time() ) ) ); ?> <br />
			<?php
			printf(
				// translators: 1) timezone
				esc_html__( 'Timezone: %s', 'auctions-for-woocommerce' ),
				get_option( 'timezone_string' ) ? esc_html( get_option( 'timezone_string' ) ) : esc_html__( 'UTC+', 'auctions-for-woocommerce' ) . esc_html( $gmt_offset )
			);
			?>
		</p>

		<?php if ( 'yes' !== $product->get_auction_sealed() ) { ?>
			<p class="auction-bid"><?php echo wp_kses_post( $product->get_price_html() ); ?> </p>

			<?php if ( ( $product->is_reserved() === true ) && ( $product->is_reserve_met() === false ) ) : ?>
				<p class="reserve hold"  data-auction-id="<?php echo intval( $product_id ); ?>" ><?php echo wp_kses_post( apply_filters( 'reserve_bid_text', esc_html__( 'Reserve price has not been met', 'auctions-for-woocommerce' ) ) ); ?></p>
			<?php endif; ?>
			<?php if ( ( $product->is_reserved() === true ) && ( $product->is_reserve_met() === true ) ) : ?>
				<p class="reserve free"  data-auction-id="<?php echo intval( $product_id ); ?>"><?php echo wp_kses_post( apply_filters( 'reserve_met_bid_text', esc_html__( 'Reserve price has been met', 'auctions-for-woocommerce' ) ) ); ?></p>
			<?php endif; ?>
		<?php } elseif ( 'yes' === $product->get_auction_sealed() ) { ?>
				<p class="sealed-text"><?php echo wp_kses_post( apply_filters( 'sealed_bid_text', __( "This auction is <a href='#'>sealed</a>.", 'auctions-for-woocommerce' ) ) ); ?>
					<span class='sealed-bid-desc' style="display:none;"><?php esc_html_e( 'In this type of auction all bidders simultaneously submit sealed bids so that no bidder knows the bid of any other participant. The highest bidder pays the price they submitted. If two bids with same value are placed for auction the one which was placed first wins the auction.', 'auctions-for-woocommerce' ); ?></span>
				</p>
				<?php
				if ( ! empty( $product->get_auction_start_price() ) ) {
					?>
					<?php if ( 'reverse' === $product->get_auction_type() ) : ?>
							<p class="sealed-min-text">
								<?php
									echo wp_kses_post(
										apply_filters(
											'sealed_min_text',
											sprintf(
												// translators: 1) bid value
												esc_html__( 'Maximum bid for this auction is %s.', 'auctions-for-woocommerce' ),
												wc_price( $product->get_auction_start_price() )
											)
										)
									);
								?>
									</p>
					<?php else : ?>
							<p class="sealed-min-text">
								<?php
								echo wp_kses_post(
									apply_filters(
										'sealed_min_text',
										sprintf(
											// translators: 1) bid value
											esc_html__( 'Minimum bid for this auction is %s.', 'auctions-for-woocommerce' ),
											wc_price( $product->get_auction_start_price() )
										)
									)
								);
								?>
								</p>
					<?php endif; ?>			
				<?php } ?>	
		<?php } ?>	

		<?php if ( 'reverse' === $product->get_auction_type() ) : ?>
			<p class="reverse"><?php echo wp_kses_post( apply_filters( 'reverse_auction_text', esc_html__( 'This is reverse auction.', 'auctions-for-woocommerce' ) ) ); ?></p>
		<?php endif; ?>	
		<?php if ( 'yes' !== $product->get_auction_sealed() ) { ?>
			<?php if ( $product->get_auction_proxy() && $product->get_auction_max_current_bider() && get_current_user_id() === $product->get_auction_max_current_bider() ) { ?>
				<p class="max-bid"><?php echo esc_html( $max_min_bid_text ); ?> <?php echo wp_kses_post( wc_price( $product->get_auction_max_bid() ) ); ?>
			<?php } ?>
		<?php } elseif ( $user_max_bid > 0 ) { ?>
			<p class="max-bid"><?php echo esc_html( $max_min_bid_text ); ?> <?php echo wp_kses_post( wc_price( $user_max_bid ) ); ?>
		<?php } ?>

		<?php do_action( 'woocommerce_before_bid_form' ); ?>

		<form class="auction_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo intval( $product_id ); ?>">

			<?php do_action( 'woocommerce_before_bid_button' ); ?>

			<input type="hidden" name="bid" value="<?php echo esc_attr( $product_id ); ?>" />	
				<div class="quantity buttons_added">
					<input type="button" value="+" class="plus" />
					<input type="text" name="bid_value" data-auction-id="<?php echo intval( $product_id ); ?>"
							<?php
							if ( 'yes' !== $product->get_auction_sealed() ) {
								?>
								value="<?php echo esc_attr( $product->bid_value() ); ?>" 
								<?php if ( 'reverse' === $product->get_auction_type() ) : ?>
									max="<?php echo esc_attr( $product->bid_value() ); ?>"
								<?php else : ?>
									min="<?php echo esc_attr( $product->bid_value() ); ?>"
								<?php endif; ?>
							<?php } ?>
							step="any" size="<?php echo intval( strlen( $product->get_curent_bid() ) ) + 6; ?>" title="bid"  class="input-text qty  bid text left">
					<input type="button" value="-" class="minus" />
				</div>
			<button type="submit" class="bid_button button alt"><?php echo wp_kses_post( apply_filters( 'bid_text', esc_html__( 'Bid', 'auctions-for-woocommerce' ), $product ) ); ?></button>
			<input type="hidden" name="place-bid" value="<?php echo intval( $product_id ); ?>" />
			<input type="hidden" name="product_id" value="<?php echo intval( $product_id ); ?>" />
			<?php if ( is_user_logged_in() ) { ?>
				<input type="hidden" name="user_id" value="<?php echo intval( get_current_user_id() ); ?>" />
			<?php } ?> 

			<?php do_action( 'woocommerce_after_bid_button' ); ?>

		</form>

		<?php do_action( 'woocommerce_after_bid_form' ); ?>

	</div>

<?php elseif ( ( false === $product->is_closed ) && ( false === $product->is_started ) ) : ?>

	<div class="auction-time future" id="countdown"><?php echo wp_kses_post( apply_filters( 'auction_starts_text', esc_html__( 'Auction starts in:', 'auctions-for-woocommerce' ), $product ) ); ?> 
		<div class="auction-time-countdown future" data-time="<?php echo esc_attr( $product->get_seconds_to_auction() ); ?>" data-format="<?php echo esc_attr( get_option( 'auctions_for_woocommerce_countdown_format' ) ); ?>"></div>
	</div>

	<p class="auction-starts"><?php echo wp_kses_post( apply_filters( 'time_text', esc_html__( 'Auction starts:', 'auctions-for-woocommerce' ), $product_id ) ); ?> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $product->get_auction_start_time() ) ) ); ?>  <?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( $product->get_auction_start_time() ) ) ); ?></p>
	<p class="auction-end"><?php echo wp_kses_post( apply_filters( 'time_text', esc_html__( 'Auction ends:', 'auctions-for-woocommerce' ), $product_id ) ); ?> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $product->get_auction_end_time() ) ) ); ?>  <?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( $product->get_auction_end_time() ) ) ); ?> </p>

<?php endif; ?>
