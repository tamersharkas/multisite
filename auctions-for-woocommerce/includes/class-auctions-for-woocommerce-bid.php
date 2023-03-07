<?php
/**
 * Auctions_For_Woocommerce_Bid
 *
 * The Auctions_For_Woocommerce Bid class stores bid data and handles bidding process. *
 *
 * @class       Auctions_For_Woocommerce_Bid
 * @version     1.0.0
 *
 */
class Auctions_For_Woocommerce_Bid {

	public $bid;

	/**
	 * Constructor for the bid class. Loads options and hooks in the init method.
	 *
	 * @return void
	 *
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 5 );
	}

	/**
	 * Loads the bid data from the PHP session during WordPress init and hooks in other methods.
	 *
	 * @return void
	 *
	 */
	public function init() {

	}

	/**
	 * Place bid
	 *
	 * @param string $product_id contains the id of the product to add to the cart
	 * @return bool
	 *
	 */
	public function placebid( $product_id, $bid ) {

		global $product_data;

		$is_proxy_bid = false;

		$product_id = intval( apply_filters( 'wpml_object_id', $product_id, 'product', false, apply_filters( 'wpml_default_language', null ) ) );

		$this->bid = floatval( apply_filters( 'auctions_for_woocommerce_place_bid_value', wc_format_decimal( $bid ), $product_id ) );

		$product_data = wc_get_product( $product_id );

		$maximum_bid_amount = get_option( 'auctions_for_woocommerce_max_bid_amount', '999999999999.99' );

		$maximum_bid_amount = $maximum_bid_amount > 0 ? $maximum_bid_amount : '999999999999.99';

		do_action( 'auctions_for_woocommerce_before_place_bid', $product_id, $bid, $product_data );

		if ( ( apply_filters( 'auctions_for_woocommerce_before_place_bid_filter', $product_data ) === false ) || ! is_object( $product_data ) ) {
			return false;
		}

		if ( ! is_user_logged_in() ) {
			/* translators: 1) login link */
			wc_add_notice( sprintf( __( 'Sorry, you must be logged in to place a bid. <a href="%s" class="button">Login &rarr;</a>', 'auctions-for-woocommerce' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ), 'error' );
			return false;
		}

		if ( $this->bid <= 0 ) {
			wc_add_notice( sprintf( esc_html__( 'Bid must be greater than 0!', 'auctions-for-woocommerce' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ), 'error' );
			return false;
		}

		if ( $this->bid >= $maximum_bid_amount ) {
			/* translators: 1) bid value */
			wc_add_notice( sprintf( esc_html__( 'Bid must be lower than %s !', 'auctions-for-woocommerce' ), wc_price( $maximum_bid_amount ) ), 'error' );
			return false;
		}

		// Check if product is_finished
		if ( $product_data->is_closed() ) {
			/* translators: 1) auction title */
			wc_add_notice( sprintf( esc_html__( 'Sorry, auction for &quot;%s&quot; is finished', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );
			return false;
		}

		// Check if product is_started
		if ( ! $product_data->is_started() ) {
			/* translators: 1) auction title */
			wc_add_notice( sprintf( esc_html__( 'Sorry, the auction for &quot;%s&quot; has not started yet', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );
			return false;
		}

		// Stock check - only check if we're managing stock and backorders are not allowed
		if ( ! $product_data->is_in_stock() ) {
			/* translators: 1) auction title */
			wc_add_notice( sprintf( esc_html__( 'You cannot place a bid for &quot;%s&quot; because the product is out of stock.', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );
			return false;
		}

		if ( 'yes' === $product_data->get_auction_sealed() ) {
			return $this->auction_sealed_placebid( $product_data, $bid );
		}

		$current_user = wp_get_current_user();
		$auction_type = $product_data->get_auction_type();

		// Check if bid is needed
		if ( intval( $product_data->get_auction_current_bider() ) === $current_user->ID && 'yes' !== get_option( 'auctions_for_woocommerce_curent_bidder_can_bid' ) ) {
			wc_add_notice( apply_filters( 'auctions_for_woocommerce_winning_bid_message', esc_html__( 'No need to bid. Your bid is winning! ', 'auctions-for-woocommerce' ) ) );
			return false;
		}

		if ( intval( $product_data->get_auction_current_bider() ) === $current_user->ID && 'yes' === get_option( 'auctions_for_woocommerce_curent_bidder_can_bid' ) ) {

			if ( $product_data->get_auction_proxy() && true === $product_data->is_reserve_met() ) {
				if ( 'normal' === $auction_type ) {
					if ( $this->bid <= $product_data->get_auction_max_bid() ) {
						wc_add_notice( esc_html__( 'New max bid cannot be smaller than old max bid!', 'auctions-for-woocommerce' ) );
						return false;
					}
					update_post_meta( $product_id, '_auction_max_bid', $this->bid );
					wc_add_notice( esc_html__( 'You have changed your maximum bid successfully', 'auctions-for-woocommerce' ) );
					do_action( 'auctions_for_woocommerce_changed_max_bid',
						array(
							'product_id'                => $product_id,
							'auction_max_bid'           => $this->bid,
							'auction_max_current_bider' => $current_user->ID,
						)
					);
					return true;
				} elseif ( 'reverse' === $auction_type ) {
					if ( $this->bid >= $product_data->get_auction_max_bid() ) {
						wc_add_notice( esc_html__( 'New min bid cannot be bigger than old min bid!', 'auctions-for-woocommerce' ) );
						return false;
					}
					update_post_meta( $product_id, '_auction_max_bid', $this->bid );
					wc_add_notice( sprintf( 'You have changed your minimum bid successfully', 'auctions-for-woocommerce' ) );
					do_action( 'auctions_for_woocommerce_changed_min_bid',
						array(
							'product_id'                => $product_id,
							'auction_max_bid'           => $this->bid,
							'auction_max_current_bider' => $current_user->ID,
						)
					);
					return true;
				}
			}
		}

		if ( 'normal' === $auction_type ) {

			if ( apply_filters( 'auctions_for_woocommerce_minimal_bid_value', $product_data->bid_value(), $product_data, $this->bid ) <= $this->bid ) {

				// Check for proxy bidding
				if ( $product_data->get_auction_proxy() ) {

					if ( $this->bid > $product_data->get_auction_max_bid() ) {

						if ( $product_data->get_auction_reserved_price() && $product_data->is_reserve_met() === false ) {

							if ( $this->bid > $product_data->get_auction_reserved_price() ) {

								$curent_bid = $product_data->get_auction_reserved_price();

							} else {

								$curent_bid = $this->bid;

							}
						} else {

							if ( $product_data->get_auction_max_bid() ) {

								$temp_bid   = $product_data->get_auction_max_bid() + $product_data->get_auction_bid_increment();
								$curent_bid = ( $this->bid < $temp_bid ) ? $this->bid : $temp_bid;

							} else {

								$curent_bid = ( $this->bid < $product_data->bid_value() ) ? $this->bid : $product_data->bid_value();

							}
						}
						if ( $product_data->get_auction_max_bid() > $product_data->get_auction_current_bid() ) {

							$this->log_bid( $product_id, $product_data->get_auction_max_bid(), get_userdata( $product_data->get_auction_max_current_bider() ), 1 );

						}
						$curent_bid    = apply_filters( 'auctions_for_woocommerce_proxy_curent_bid_value', $curent_bid, $product_data, $this->bid );
						$outbiddeduser = $product_data->get_auction_current_bider();
						update_post_meta( $product_id, '_auction_max_bid', $this->bid );
						update_post_meta( $product_id, '_auction_max_current_bider', $current_user->ID );
						update_post_meta( $product_id, '_auction_current_bid', $curent_bid );
						update_post_meta( $product_id, '_auction_current_bider', $current_user->ID );
						update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 1 ) );
						delete_post_meta( $product_id, '_auction_current_bid_proxy' );
						$log_id = $this->log_bid( $product_id, $curent_bid, $current_user, 0 );
						do_action(
							'auctions_for_woocommerce_outbid', array(
								'product_id'       => $product_id,
								'outbiddeduser_id' => $outbiddeduser,
								'log_id'           => $log_id,
								'auction_max_bid'  => $this->bid,
								'auction_max_current_bider' => $current_user->ID,
							)
						);

					} else {

						$is_proxy_bid = true;
						$this->log_bid( $product_id, $this->bid, $current_user, 0 );
						if ( $this->bid === $product_data->get_auction_max_bid() ) {

							$proxy_bid = $product_data->get_auction_max_bid();

						} else {
							$proxy_bid = apply_filters( 'auctions_for_woocommerce_proxy_bid_value', $this->bid + $product_data->get_auction_bid_increment(), $product_data, $this->bid );

							if ( $proxy_bid > $product_data->get_auction_max_bid() ) {

								$proxy_bid = $product_data->get_auction_max_bid();

							}
						}

						update_post_meta( $product_id, '_auction_current_bid', $proxy_bid );
						update_post_meta( $product_id, '_auction_current_bid_proxy', 'yes' );
						update_post_meta( $product_id, '_auction_current_bider', $product_data->get_auction_max_current_bider() );
						update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 2 ) );
						$log_id = $this->log_bid( $product_id, $proxy_bid, get_userdata( $product_data->get_auction_max_current_bider() ), 1 );
						do_action(
							'auctions_for_woocommerce_proxy_outbid', array(
								'product_id'       => $product_id,
								'outbiddeduser_id' => $product_data->get_auction_max_current_bider(),
								'log_id'           => $log_id,
							)
						);
						wc_add_notice( sprintf( esc_html__( 'You were outbid. Try again!', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );

					}
				} else {

					$outbiddeduser = $product_data->get_auction_current_bider();
					$curent_bid    = $product_data->get_curent_bid();
					update_post_meta( $product_id, '_auction_current_bid', $this->bid );
					update_post_meta( $product_id, '_auction_current_bider', $current_user->ID );
					update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 1 ) );
					delete_post_meta( $product_id, '_auction_current_bid_proxy' );
					$log_id = $this->log_bid( $product_id, $this->bid, $current_user );
					do_action(
						'auctions_for_woocommerce_outbid', array(
							'product_id'       => $product_id,
							'outbiddeduser_id' => $outbiddeduser,
							'log_id'           => $log_id,
						)
					);

				}
			} else {
				/* translators: 1) auction title 2) bid value*/
				wc_add_notice( sprintf( esc_html__( 'Your bid for &quot;%1$s&quot; is smaller than the current bid. Your bid must be at least %2$s ', 'auctions-for-woocommerce' ), $product_data->get_title(), wc_price( $product_data->bid_value() ) ), 'error' );
				return false;

			}
		} elseif ( 'reverse' === $auction_type ) {

			if ( apply_filters( 'auctions_for_woocommerce_minimal_bid_value', $product_data->bid_value(), $product_data, $this->bid ) >= $bid ) {

				// Check for proxy bidding
				if ( $product_data->get_auction_proxy() ) {

					if ( $this->bid < $product_data->get_auction_max_bid() || ! $product_data->get_auction_max_bid() ) {

						if ( $product_data->get_auction_reserved_price() && $product_data->is_reserve_met() === false ) {

							if ( $this->bid < $product_data->get_auction_reserved_price() ) {

								$curent_bid = $product_data->get_auction_reserved_price();

							} else {
								$curent_bid = $this->bid;
							}
						} else {

							if ( $product_data->get_auction_max_bid() ) {

								$temp_bid   = $product_data->get_auction_max_bid() - $product_data->get_auction_bid_increment();
								$curent_bid = $this->bid > $temp_bid ? $this->bid : $temp_bid;

							} else {

								$curent_bid = $this->bid > $product_data->bid_value() ? $this->bid : $product_data->bid_value();

							}
						}
						if ( $product_data->get_auction_max_bid() < $product_data->get_auction_current_bid() ) {
							$this->log_bid( $product_id, $product_data->get_auction_max_bid(), get_userdata( $product_data->get_auction_max_current_bider() ), 1 );
						}

						$outbiddeduser = $product_data->get_auction_current_bider();
						update_post_meta( $product_id, '_auction_max_bid', $this->bid );
						update_post_meta( $product_id, '_auction_max_current_bider', $current_user->ID );
						update_post_meta( $product_id, '_auction_current_bid', $curent_bid );
						update_post_meta( $product_id, '_auction_current_bider', $current_user->ID );
						update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 1 ) );
						delete_post_meta( $product_id, '_auction_current_bid_proxy' );
						$log_id = $this->log_bid( $product_id, $curent_bid, $current_user, 0 );
						do_action(
							'auctions_for_woocommerce_outbid', array(
								'product_id'       => $product_id,
								'outbiddeduser_id' => $outbiddeduser,
								'log_id'           => $log_id,
								'auction_max_bid'  => $this->bid,
								'auction_max_current_bider' => $current_user->ID,
							)
						);

					} else {

						$is_proxy_bid = true;
						$this->log_bid( $product_id, $this->bid, $current_user, 0 );
						if ( $this->bid === $product_data->get_auction_max_bid() ) {

							$proxy_bid = $product_data->get_auction_max_bid();

						} else {

							$proxy_bid = apply_filters( 'auctions_for_woocommerce_proxy_bid_value', $this->bid - $product_data->get_auction_bid_increment(), $product_data, $this->bid );
							if ( $proxy_bid < $product_data->get_auction_max_bid() ) {
								$proxy_bid = $product_data->get_auction_max_bid();
							}
						}
						update_post_meta( $product_id, '_auction_current_bid', $proxy_bid );
						update_post_meta( $product_id, '_auction_current_bid_proxy', 'yes' );
						update_post_meta( $product_id, '_auction_current_bider', $product_data->get_auction_max_current_bider() );
						update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 2 ) );
						$log_id = $this->log_bid( $product_id, $proxy_bid, get_userdata( $product_data->get_auction_max_current_bider() ), 1 );

						wc_add_notice( sprintf( esc_html__( 'You were outbid. Try again!', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );

					}
				} else {

					$outbiddeduser = $product_data->get_auction_current_bider();
					$curent_bid    = $product_data->get_curent_bid();
					update_post_meta( $product_id, '_auction_current_bid', $this->bid );
					update_post_meta( $product_id, '_auction_current_bider', $current_user->ID );
					update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 1 ) );
					delete_post_meta( $product_id, '_auction_current_bid_proxy' );
					$log_id = $this->log_bid( $product_id, $this->bid, $current_user );
					do_action(
						'auctions_for_woocommerce_outbid', array(
							'product_id'       => $product_id,
							'outbiddeduser_id' => $outbiddeduser,
							'log_id'           => $log_id,
						)
					);

				}
			} else {
				/* translators: 1) auction title 2) bid value*/
				wc_add_notice( sprintf( esc_html__( 'Your bid for &quot;%s&quot; is larger than the current bid', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );
				return false;
			}
		} else {
			wc_add_notice( sprintf( esc_html__( 'There was no bid', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );
			return false;
		}

		do_action(
			'auctions_for_woocommerce_place_bid', array(
				'product_id'   => $product_id,
				'is_proxy_bid' => $is_proxy_bid,
			)
		);

		return true;
	}

	/**
	 * Log bid
	 *
	 * @param string, int, int, int
	 * @return void
	 *
	 */
	public function log_bid( $product_id, $bid, $current_user, $proxy = 0 ) {

		global $wpdb;
		$log_bid_id = false;

		$log_bid = $wpdb->insert(
			$wpdb->prefix . 'auctions_for_woocommerce_log', array(
				'userid'     => $current_user->ID,
				'auction_id' => $product_id,
				'bid'        => $bid,
				'proxy'      => $proxy,
				'date'       => current_time( 'mysql' ),
			), array( '%d', '%d', '%f', '%d', '%s' )
		);
		if ( $log_bid ) {
			$log_bid_id = $wpdb->insert_id;
		}
		do_action( 'auctions_for_woocommerce_log_bid', $log_bid_id, $product_id, $bid, $current_user );
		return $log_bid_id;
	}


	/**
	 * Process auction with sealed bid
	 *
	 * @param object, float
	 * @return bolean
	 *
	 */
	public function auction_sealed_placebid( $product_data, $bid ) {

		$current_user = wp_get_current_user();
		$product_id   = $product_data->get_id();
		$auction_type = $product_data->get_auction_type();

		// Check if bid is needed
		if ( ( $product_data->is_user_biding( $current_user->ID ) > 0 ) && get_option( 'auctions_for_woocommerce_curent_bidder_can_bid' ) !== 'yes' ) {

			wc_add_notice( sprintf( esc_html__( 'You already placed bid for this auction! ', 'auctions-for-woocommerce' ), $product_data->get_title() ) );
			return false;

		}

		if ( 'normal' === $auction_type ) {

			if ( ! empty( $product_data->get_auction_start_price() ) ) {

				if ( $product_data->get_auction_start_price() > $bid ) {
					/* translators: 1) auction title 2) bid value*/
					wc_add_notice( sprintf( esc_html__( 'Your bid for &quot;%1$s&quot; is smaller than the minimum bid. Your bid must be at least %2$s ', 'auctions-for-woocommerce' ), $product_data->get_title(), wc_price( $product_data->get_auction_start_price() ) ), 'error' );
					return false;
				}
			}

			if ( $this->bid > (float) $product_data->get_curent_bid() ) {
				update_post_meta( $product_id, '_auction_current_bid', $this->bid );
				update_post_meta( $product_id, '_auction_current_bider', $current_user->ID );
			}

			update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 1 ) );

			$log_id = $this->log_bid( $product_id, $bid, $current_user );
			do_action(
				'auctions_for_woocommerce_place_sealedbid', array(
					'product_id'   => $product_id,
					'bid'          => $bid,
					'current_user' => $current_user,
					'log_id'       => $log_id,
				)
			);

		} elseif ( 'reverse' === $auction_type ) {

			if ( ! empty( $product_data->get_auction_start_price() ) ) {

				if ( $product_data->get_auction_start_price() < $bid ) {
					/* translators: 1) auction title 2) bid value*/
					wc_add_notice( sprintf( esc_html__( 'Your bid for &quot;%1$s&quot; is bigger than the maximum bid. Your bid must be at least %2$s ', 'auctions-for-woocommerce' ), $product_data->get_title(), wc_price( $product_data->get_auction_start_price() ) ), 'error' );
					return false;
				}
			}

			if ( $this->bid < (float) $product_data->get_curent_bid() ) {
				update_post_meta( $product_id, '_auction_current_bid', $this->bid );
				update_post_meta( $product_id, '_auction_current_bider', $current_user->ID );
			}

			update_post_meta( $product_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() + 1 ) );

			$log_id = $this->log_bid( $product_id, $bid, $current_user );
			do_action(
				'auctions_for_woocommerce_place_sealedbid', array(
					'product_id'   => $product_id,
					'bid'          => $bid,
					'current_user' => $current_user,
					'log_id'       => $log_id,
				)
			);

		} else {
			wc_add_notice( sprintf( esc_html__( 'There was no bid', 'auctions-for-woocommerce' ), $product_data->get_title() ), 'error' );
			return false;

		}

		do_action( 'auctions_for_woocommerce_place_bid', array( 'product_id' => $product_id ) );
		return true;

	}

}
new Auctions_For_Woocommerce_Bid();
