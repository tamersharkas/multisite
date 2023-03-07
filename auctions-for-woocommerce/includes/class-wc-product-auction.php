<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Auction Product Class
 *
 * @class WC_Product_Auction
 *
 */
if ( ! class_exists( 'WC_Product_Auction' ) ) :
	class WC_Product_Auction extends WC_Product {

		public $post_type    = 'product';
		public $product_type = 'auction';
		public $is_closed;
		public $is_started;

		/**
		 * __construct function.
		 *
		 * @param mixed $product
		 *
		 */
		public function __construct( $product ) {

			$this->auction_item_condition_array = apply_filters(
				'auctions_for_woocommerce_item_condition',
				array(
					'new'  => esc_html__( 'New', 'auctions-for-woocommerce' ),
					'used' => esc_html__( 'Used', 'auctions-for-woocommerce' ),
				)
			);

			parent::__construct( $product );
			$this->is_closed  = $this->is_closed();
			$this->is_started = $this->is_started();
			$this->check_bid_count();

		}
		/**
	 * Returns the unique ID for this object.
	 * 
	 * @return int
	 */
		public function get_id() {
			return $this->id;
		}

		/**
	 * Get internal type.
	 *
	 * @return string
	 */
		public function get_type() {
			return 'auction';
		}

		/**
		 * Checks if a product is auction
		 *
		 * @return bool
		 *
		 */
		public function is_auction() {

			return $this->get_type() === 'auction' ? true : false;
		}

		/**
		* Get current bid
		*
		* @return int
		*
		*/
		public function get_current_bid() {

			if ( ! empty( $this->get_auction_current_bid() ) ) {
				return floatval( apply_filters( 'auctions_for_woocommerce_get_current_bid', $this->get_auction_current_bid(), $this ) );
			}
			return floatval( apply_filters( 'auctions_for_woocommerce_get_current_bid', $this->get_auction_start_price(), $this ) );

		}

		/**
		* Get curent bid
		* It is here because of misspeling in previous versions.
		*
		* @return int
		*
		*/
		public function get_curent_bid() {
			return $this->get_current_bid();
		}

		/**
	 * Get bid increment
	 *
	 * @return mixed
	 *
	 */
		public function get_increase_bid_value() {

			if ( $this->get_auction_bid_increment() ) {
				return floatval( apply_filters( 'auctions_for_woocommerce_get_increase_bid_value', $this->get_auction_bid_increment(), $this ) );
			} else {
				return false;
			}

		}

		/**
	 * Get auction condition
	 *
	 * @return mixed
	 *
	 */
		public function get_condition() {

			if ( $this->get_auction_item_condition() ) {
				return apply_filters( 'auctions_for_woocommerce_get_condition', $this->auction_item_condition_array[ $this->get_auction_item_condition() ], $this );
			} else {
				return false;
			}

		}

		/**
	 * Get auction end time
	 *
	 * @return mixed
	 *
	 */
		public function get_auction_end_time() {

			if ( $this->get_auction_dates_to() ) {
				return apply_filters( 'auctions_for_woocommerce_get_auction_end_time', $this->get_auction_dates_to(), $this );
			} else {
				return false;
			}

		}

		/**
	 * Get auction start time
	 *
	 * @return mixed
	 *
	 */
		public function get_auction_start_time() {

			if ( $this->get_auction_dates_from() ) {
				return apply_filters( 'auctions_for_woocommerce_get_auction_start_time', $this->get_auction_dates_from(), $this );
			} else {
				return false;
			}

		}

		/**
	 * Get remaining seconds till auction end
	 *
	 * @return mixed
	 *
	 */
		public function get_seconds_remaining() {

			if ( $this->get_auction_dates_to() ) {
				if ( is_user_logged_in() ) {
					return apply_filters( 'auctions_for_woocommerce_get_seconds_remaining', strtotime( $this->get_auction_dates_to() ) - ( get_option( 'gmt_offset' ) * 3600 ) - time(), $this );
				} else {
					return apply_filters( 'auctions_for_woocommerce_get_seconds_remaining', strtotime( $this->get_auction_dates_to() ) - ( get_option( 'gmt_offset' ) * 3600 ), $this );
				}
			} else {
				return false;
			}

		}

		/**
	 * Get seconds till auction starts
	 *
	 * @return mixed
	 *
	 */
		public function get_seconds_to_auction() {

			if ( $this->get_auction_dates_to() ) {
				if ( is_user_logged_in() ) {
					return apply_filters( 'auctions_for_woocommerce_get_seconds_to_auction', strtotime( $this->get_auction_dates_from() ) - ( get_option( 'gmt_offset' ) * 3600 ) - time(), $this );
				} else {
					return apply_filters( 'auctions_for_woocommerce_get_seconds_to_auction', strtotime( $this->get_auction_dates_from() ) - ( get_option( 'gmt_offset' ) * 3600 ), $this );
				}
			} else {
				return false;
			}

		}

		/**
	 * Has auction started
	 *
	 * @return mixed
	 *
	 */
		public function is_started() {

			$id = $this->get_main_wpml_product_id();

			if ( $this->get_auction_has_started() === true ) {
				return true;
			}
			if ( $this->get_auction_dates_from() != false ) {

				$date1 = new DateTime( $this->get_auction_dates_from() );
				$date2 = new DateTime( current_time( 'mysql' ) );
				if ( $date1 < $date2 ) {
					wp_remove_object_terms( $id, array( 'future' ), 'auction_visibility' );
					wp_set_post_terms( $id, array( 'started' ), 'auction_visibility', true );
					do_action( 'auctions_for_woocommerce_started', $id );
				} else {
					if ( false === array_search( 'future', $this->get_auction_visibility(), true ) ) {
						wp_set_post_terms( $id, array( 'future' ), 'auction_visibility', true );
					}
				}
				return ( $date1 < $date2 );
			} else {
				wp_set_post_terms( $id, array( 'future' ), 'auction_visibility', true );
				return false;
			}
		}

		/**
	 * Does auction have reserve price
	 *
	 * @return bool
	 *
	 */
		public function is_reserved() {

			if ( $this->get_auction_reserved_price() ) {
				return true;
			} else {
				return false;
			}
		}

		/**
	 * Has auction met reserve price
	 *
	 * @return mixed
	 *
	 */
		public function is_reserve_met() {

			if ( ! empty( $this->get_auction_reserved_price() ) ) {
				if ( $this->get_auction_type() === 'reverse' ) {
					return ( $this->get_auction_reserved_price() >= $this->get_auction_current_bid() );
				} else {
					return ( $this->get_auction_reserved_price() <= $this->get_auction_current_bid() );
				}
			}
			return true;
		}

		/**
	 * Has auction finished
	 *
	 * @return mixed
	 *
	 */
		public function is_finished() {
			if ( ! empty( $this->get_auction_dates_to() ) ) {
				$date1 = new DateTime( $this->get_auction_dates_to() );
				$date2 = new DateTime( current_time( 'mysql' ) );

				if ( $date1 < $date2 ) {
					do_action( 'auctions_for_woocommerce_finished', $this->get_id() );
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		/**
	 * Is auction closed
	 *
	 * @return bool
	 *
	 */
		public function is_closed() {

			$id = $this->get_main_wpml_product_id();

			if ( ! empty( $this->get_auction_closed() ) ) {

				return true;

			} else {

				if ( $this->is_finished() && $this->is_started() ) {

					if ( ! $this->get_auction_current_bider() && ! $this->get_auction_current_bid() ) {
						wp_set_post_terms( $id, array( 'finished' ), 'auction_visibility', true );
						update_post_meta( $id, '_auction_fail_reason', '1' );
						$order_id = false;
						do_action( 'auctions_for_woocommerce_close', $id );
						do_action(
							'auctions_for_woocommerce_fail',
							array(
								'auction_id' => $id,
								'reason'     => __( 'There was no bid', 'auctions-for-woocommerce' ),
							)
						);
						return false;
					}
					if ( $this->is_reserve_met() === false ) {
						wp_set_post_terms( $id, array( 'finished' ), 'auction_visibility', true );
						update_post_meta( $id, '_auction_fail_reason', '2' );
						$order_id = false;
						do_action( 'auctions_for_woocommerce_close', $id );
						do_action(
							'auctions_for_woocommerce_reserve_fail',
							array(
								'user_id'    => $this->get_auction_current_bider(),
								'product_id' => $id,
							)
						);
						do_action(
							'auctions_for_woocommerce_fail',
							array(
								'auction_id' => $id,
								'reason'     => __( 'The item didn\'t make it to reserve price', 'auctions-for-woocommerce' ),
							)
						);
						return false;
					}

					wp_set_post_terms( $id, array( 'sold', 'finished' ), 'auction_visibility', true );
					add_user_meta( $this->get_auction_current_bider(), '_auction_win', $id );
					do_action( 'auctions_for_woocommerce_close', $id );
					do_action( 'auctions_for_woocommerce_won', $id );

					return true;

				} else {

					return false;

				}
			}
		}

		/**
	 * Get auction history
	 *
	 * @return object
	 *
	 */
		public function auction_history( $datefrom = false ) {
			global $wpdb;

			$id = $this->get_main_wpml_product_id();

			$relisteddate = get_post_meta( $id, '_auction_relisted', true );
			if ( ! is_admin() && ! empty( $relisteddate ) ) {
				$datefrom = $relisteddate;
			}

			if ( $this->get_auction_type() === 'reverse' ) {
				if ( $datefrom ) {
					$history = $wpdb->get_results( $wpdb->prepare( 'SELECT * 	FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE auction_id = %d AND CAST(date AS DATETIME) > %s ORDER BY  `date` desc , `bid`  asc, `id`  desc   ', $id, $datefrom ) );
				} else {
					$history = $wpdb->get_results( $wpdb->prepare( 'SELECT * 	FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE auction_id = %d ORDER BY  `date` desc , `bid`  asc, `id`  desc   ', $id ) );
				}
			} else {
				if ( $datefrom ) {
					$history = $wpdb->get_results( $wpdb->prepare( 'SELECT * 	FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE auction_id = %d AND CAST(date AS DATETIME) > %s ORDER BY  `date` desc , `bid`  desc ,`id`  desc  ', $id, $datefrom ) );
				} else {
					$history = $wpdb->get_results( $wpdb->prepare( 'SELECT * 	FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE auction_id = %d  ORDER BY  `date` desc , `bid`  desc ,`id`  desc  ', $id ) );
				}
			}
			return $history;
		}


		/**
	 * Get auction history line
	 *
	 * @return object
	 *
	 */
		public function auction_history_last( $id ) {
			global $wpdb;

			$datetimeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

			$data  = array();
			$proxy = false;

			$history_value = $wpdb->get_results( 'SELECT * 	FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE auction_id =' . intval( $id ) . ' ORDER BY  `date` desc  limit 2' );

			if ( $history_value ) {
				foreach ( $history_value as $key => $value ) {
					$data[ $value->id ]  = '<tr>';
					$data[ $value->id ] .= '<td class="date">' . esc_html( mysql2date( $datetimeformat, $value->date ) ) . '</td>';
					$data[ $value->id ] .= "<td class='bid'>" . wc_price( $value->bid ) . '</td>';
					$data[ $value->id ] .= "<td class='username'>" . esc_html( apply_filters( 'auctions_for_woocommerce_displayname', get_userdata( $value->userid )->display_name ) ) . '</td>';
					if ( '1' === $value->proxy ) {
						$proxy               = true;
						$data[ $value->id ] .= " <td class='proxy'>" . __( 'Auto', 'auctions-for-woocommerce' ) . '</td>';
					} else {
						$data[ $value->id ] .= " <td class='proxy'></td>";
					}
					$data[  $value->id ] .= '</tr>';
				}
			}
			return $data;
		}

		/**
	 * Returns price in html format.
	 *
	 * @param string $price (default: '')
	 * @return string
	 *
	 */
		public function get_price_html( $price = '' ) {
			$id = $this->get_main_wpml_product_id();

			if ( $this->is_closed && $this->is_started ) {
				if ( $this->get_auction_closed() === 3 ) {
					$price = __( '<span class="sold-for auction">Sold for</span>: ', 'auctions-for-woocommerce' ) . wc_price( $this->get_price() );
				} else {
					if ( $this->get_auction_current_bid() ) {
						if ( $this->is_reserve_met() === false ) {
							$price = __( '<span class="winned-for auction">Auction item did not make it to reserve price</span> ', 'auctions-for-woocommerce' );
						} else {
							$price = __( '<span class="winned-for auction">Winning Bid:</span> ', 'auctions-for-woocommerce' ) . wc_price( $this->get_auction_current_bid() );
						}
					} else {
						$price = __( '<span class="winned-for auction">Auction Ended</span> ', 'auctions-for-woocommerce' );
					}
				}
			} elseif ( ! $this->is_started ) {
				$price = '<span class="auction-price starting-bid" data-auction-id="' . $id . '" data-bid="' . $this->get_auction_current_bid() . '" data-status="future">' . __( '<span class="starting auction">Starting bid:</span> ', 'auctions-for-woocommerce' ) . wc_price( $this->get_curent_bid() ) . '</span>';
			} else {
				if ( $this->get_auction_sealed() === 'yes' ) {
					$price = '<span class="auction-price" data-auction-id="' . $id . '"  data-status="running">' . __( '<span class="current auction">This is sealed bid auction.</span> ', 'auctions-for-woocommerce' ) . '</span>';
				} else {
					if ( ! $this->get_auction_current_bid() ) {
						$price = '<span class="auction-price starting-bid" data-auction-id="' . $id . '" data-bid="' . $this->get_auction_current_bid() . '" data-status="running">' . __( '<span class="current auction">Starting bid:</span> ', 'auctions-for-woocommerce' ) . wc_price( $this->get_curent_bid() ) . '</span>';
					} else {
						$price = '<span class="auction-price current-bid" data-auction-id="' . $id . '" data-bid="' . $this->get_auction_current_bid() . '" data-status="running">' . __( '<span class="current auction">Current bid:</span> ', 'auctions-for-woocommerce' ) . wc_price( $this->get_curent_bid() ) . '</span>';
					}
				}
			}
			return apply_filters( 'woocommerce_get_price_html', $price, $this );
		}

		/**
	 * Returns product's price.
	 *
	 * @return string
	 *
	 */
		public function get_price( $context = 'view' ) {
			if ( $this->is_closed ) {

				if ( empty( $this->get_prop( 'price', $context ) ) || $this->get_auction_closed() !== 3 ) {

					$price = null;
					if ( $this->is_reserve_met() ) {
						$price = get_post_meta( $this->get_main_wpml_product_id(), '_auction_current_bid', true );
					}

					$this->set_price( $price );
				}
				return $this->get_prop( 'price', $context );
			}

			return apply_filters( 'woocommerce_product_get_price', get_post_meta( $this->get_main_wpml_product_id(), '_price', true ), $this );

		}


		/**
		* Get the add to url used mainly in loops.
		*
		* @return string
		*/
		public function add_to_cart_url() {
			$id = $this->get_main_wpml_product_id();
			return apply_filters( 'woocommerce_product_add_to_cart_url', get_permalink( $id ), $this );
		}

		/**
		 * Wrapper for get_permalink
		 * 
		 * @return string
		 */
		public function get_permalink() {
			$id = $this->get_main_wpml_product_id();
			return get_permalink( $id );
		}

		/**
		 * Get the add to cart button text
		 *
		 * @return string
		 */
		public function add_to_cart_text() {
			if ( ! $this->is_closed && $this->is_started ) {
				$text = __( 'Bid now', 'auctions-for-woocommerce' );
			} elseif ( $this->is_closed ) {
				$text = __( 'Auction finished', 'auctions-for-woocommerce' );
			} elseif ( ! $this->is_closed && ! $this->is_started ) {
				$text = __( 'Auction not started', 'auctions-for-woocommerce' );
			}

			return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
		}

		/**
	 * Get the bid value
	 *
	 * @return string
	 */
		public function bid_value() {
			$auction_bid_increment = ( $this->get_increase_bid_value() ) ? $this->get_increase_bid_value() : 1;

			if ( $this->get_auction_bid_count() === 0 ) {
				return $this->get_curent_bid();
			} else {
				if ( $this->get_auction_type() === 'reverse' ) {

					return floatval( apply_filters( 'auctions_for_woocommerce_bid_value', round( wc_format_decimal( $this->get_curent_bid() ) - wc_format_decimal( $auction_bid_increment ), wc_get_price_decimals() ), $this ) );
				} else {

					return floatval( apply_filters( 'auctions_for_woocommerce_bid_value', round( wc_format_decimal( $this->get_curent_bid() ) + wc_format_decimal( $auction_bid_increment ), wc_get_price_decimals() ), $this ) );
				}
			}

			return false;
		}


		/**
	 * Get the title of the post.
	 *
	 * @return string
	 */
		public function get_title() {
			$id = $this->get_main_wpml_product_id();

			return apply_filters( 'woocommerce_product_title', get_the_title( $id ), $this );
		}

		/**
	 * Check if auctions is on user watchlist
	 *
	 * @return string
	 */
		public function is_user_watching( $user_ID = false ) {

			$id = $this->get_main_wpml_product_id();

			if ( ! $user_ID ) {
				$user_ID = get_current_user_id();
			}

			$users_watching_auction = get_post_meta( $id, '_auction_watch', false );
			if ( is_array( $users_watching_auction ) ) {
				return in_array( $user_ID, $users_watching_auction );
			}

			return false;

		}



		/**
	 * Get main product id for multilanguage purpose
	 *
	 * @return int
	 *
	 */

		public function get_main_wpml_product_id() {
			global $wpml_post_translations;

			if ( $wpml_post_translations ) {
				$original_product_id = $wpml_post_translations->get_original_element( $this->id );
				return intval( apply_filters( 'auctions_for_woocommerce_auction_id', $original_product_id ? $original_product_id : $this->id ) );
			}
			return intval( apply_filters( 'wpml_object_id', $this->id, 'product', false, apply_filters( 'wpml_default_language', null ) ) );
		}

		/**
	 * Get if user is biding on auction
	 *
	 * @return int
	 */
		public function is_user_biding( $auction_id, $user_ID = false ) {

			global $wpdb;

			if ( ! $user_ID ) {
				$user_ID = get_current_user_id();
			}

			$bid_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) 	FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE auction_id = %d AND userid =%d ' , $auction_id , $user_ID ) );

			return apply_filters( 'auctions_for_woocommerce_is_user_biding', absint( $bid_count ), $this );

		}

		/**
		 * Get user max bid
		 *
		 * @return float
		 */
		public function get_user_max_bid( $auction_id, $user_ID = false ) {

			global $wpdb;

			if ( ! $user_ID ) {
				$user_ID = get_current_user_id();
			}

			$maxbid = $wpdb->get_var(  $wpdb->prepare( 'SELECT bid FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE auction_id = %d AND userid = %d  ORDER BY  `bid` desc', $auction_id , $user_ID) );

			return apply_filters( 'auctions_for_woocommerce_get_user_max_bid', $maxbid, $this );

		}

		/**
		 * Get is auction is sealed
		 *
		 * @return bolean
		 */
		public function is_sealed() {
			if ( $this->is_closed ) {
				return false;
			}
			return apply_filters( 'auctions_for_woocommerce_is_sealed', $this->get_auction_sealed() === 'yes', $this );
		}

		public function check_bid_count() {
			$id = $this->get_main_wpml_product_id();

			if ( ! $this->get_auction_bid_count() ) {

				update_post_meta( $id, '_auction_bid_count', '0' );
			}

		}


		/**
		 * Get get_auction_current_bid
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_current_bid( $context = 'view' ) {

			$auction_current_bid = floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_current_bid', true ) );

			if ( ! empty( $auction_current_bid ) ) {
				$auction_current_bid = wc_format_decimal( $auction_current_bid, wc_get_price_decimals() );
			}

			return floatval( apply_filters( 'auctions_for_woocommerce_get_auction_current_bid', $auction_current_bid, $this ) );

		}

		/**
		 * Get get_auction_current_bider
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_current_bider( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_current_bider', true ) );

		}

		/**
		 * Get get_auction_current_bider
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_current_bider_displayname( $context = 'view' ) {

			return apply_filters( 'auctions_for_woocommerce_display_curent_bidder_name', get_userdata( $this->get_auction_current_bider() )->display_name );

		}

		/**
		 * Get get_auction_bid_increment
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return float
		 */
		public function get_auction_bid_increment( $context = 'view' ) {

			return floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_bid_increment', true ) );

		}

		/**
		 * Get get_auction_item_condition
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_item_condition( $context = 'view' ) {
			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_item_condition', true );
		}
		/**
		 * Get get_auction_dates_from
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_dates_from( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_dates_from', true );

		}
		/**
		 * Get get_auction_dates_to
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_dates_to( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_dates_to', true );

		}
		/**
		 * Get get_auction_reserved_price
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return float
		 */
		public function get_auction_reserved_price( $context = 'view' ) {

			return floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_reserved_price', true ) );

		}

		/**
		 * Get get_auction_type
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_type( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_type', true );

		}
		/**
		 * Get get_auction_closed
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_auction_closed( $context = 'view' ) {

			$term_names = $this->get_auction_visibility();
			$finished   = in_array( 'finished', $term_names, true );
			$buy_now    = in_array( 'buy-now', $term_names, true );
			$sold       = in_array( 'sold', $term_names, true );

			if ( $buy_now ) {
				return 3;
			} elseif ( $sold ) {
				return 2;
			} elseif ( $finished ) {
				return 1;
			} else {
				return false;
			}

		}

		/**
		 * Get get_auction_started
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_auction_started( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_started', true ) );

		}

		/**
		 * Get get_has_auction_started
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_auction_has_started( $context = 'view' ) {

			$started = in_array( 'started', $this->get_auction_visibility(), true );

			return $started;

		}

		/**
		 * Get get_auction_sealed
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_sealed( $context = 'view' ) {

			$term_names = $this->get_auction_visibility();

			$sealed = in_array( 'sealed', $term_names, true );

			return $sealed ? 'yes' : false;
		}

		/**
		 * Get get_auction_bid_count
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_auction_bid_count( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_bid_count', true ) );

		}

		/**
		 * Get get_auction_max_bid
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return float
		 */
		public function get_auction_max_bid( $context = 'view' ) {

			return floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_max_bid', true ) );
		}

		/**
		 * Get get_auction_max_current_bider
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_auction_max_current_bider( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_max_current_bider', true ) );

		}

		/**
		 * Get get_auction_fail_reason
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_fail_reason( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_fail_reason', true ) );
		}

		/**
		 * Get get_order_id
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_order_id( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_order_id', true ) );

		}

		/**
	 * Get get_stop_mails
	 *
	 * @since 1.2.8
	 * @param  string $context
	 * @return string
	 */
		public function get_stop_mails( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_stop_mails', true ) );

		}

		/**
		 * Get get_auction_proxy
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_proxy( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_proxy', true );

		}

		/**
		 * Get get_auction_start_price
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return float
		 */
		public function get_auction_start_price( $context = 'view' ) {

			return wc_format_decimal( floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_start_price', true ) ), wc_get_price_decimals() );

		}

		/**
		 * Get get_auction_wpml_language
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		*/
		public function get_auction_wpml_language( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_wpml_language', true );

		}

		/**
		 * Get get_auction_relist_fail_time
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return float
		 */
		public function get_auction_relist_fail_time( $context = 'view' ) {

			return floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_relist_fail_time', true ) );

		}

		/**
		 * Get get_auction_relist_not_paid_time
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return float
		 */
		public function get_auction_relist_not_paid_time( $context = 'view' ) {

			return floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_relist_not_paid_time', true ) );

		}

		/**
		 * Get get_auction_automatic_relist
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_automatic_relist( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_automatic_relist', true );

		}

		/**
		 * Get get_auction_relist_duration
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return float
		 */
		public function get_auction_relist_duration( $context = 'view' ) {

			return floatval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_relist_duration', true ) );

		}

		/**
		 * Get get_auction_payed
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_auction_payed( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_payed', true ) );
		}

		/**
		 * Get get_number_of_sent_mails
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return int
		 */
		public function get_number_of_sent_mails( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_number_of_sent_mails', true ) );

		}

		/**
		 * Get get_auction_relisted
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return string
		 */
		public function get_auction_relisted( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_relisted', true );

		}

		/**
		 * Returns the product's regular price.
		 *
		 * @param  string $context
		 * @return float price
		 */
		public function get_regular_price( $context = 'view' ) {

			return floatval( get_post_meta( $this->get_main_wpml_product_id(), '_regular_price', true ) );
		}


		/**
		 * Get _auction_delete_log_on_relist
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return bolean
		 */
		public function get_auction_delete_log_on_relist( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_delete_log_on_relist', true ) === 'yes' ? true : false;

		}

		/**
		 * Get __auction_delete_log_on_auto_relist
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return bolean
		 */
		public function get_auction_delete_log_on_auto_relist( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_delete_log_on_auto_relist', true ) === 'yes' ? true : false;

		}

		/**
		 * Get __auction_delete_log_on_auto_relist
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return bolean
		 */
		public function get_auction_visibility( $context = 'view' ) {

			$terms = get_the_terms( $this->get_main_wpml_product_id(), 'auction_visibility' );

			$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();

			return $term_names;

		}

		/**
		 * Get _auction_extend_enable
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return bolean
		 */
		public function get_auction_extend_enable( $context = 'view' ) {

			return get_post_meta( $this->get_main_wpml_product_id(), '_auction_extend_enable', true ) === 'yes' ? true : false;

		}
		/**
		 * Get _auction_extend_in_time
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return bolean
		 */
		public function get_auction_extend_in_time( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_extend_in_time', true ) );

		}
		/**
		 * Get _auction_extend_for_time
		 *
		 * @since 1.2.8
		 * @param  string $context
		 * @return bolean
		 */
		public function get_auction_extend_for_time( $context = 'view' ) {

			return intval( get_post_meta( $this->get_main_wpml_product_id(), '_auction_extend_for_time', true ) );

		}

		public function auction_update_lookup_table() {
			global $wpdb;

			$id            = absint( $this->get_main_wpml_product_id() );
			$table         = 'wc_product_meta_lookup';
			$existing_data = wp_cache_get( 'lookup_table', 'object_' . $id );
			$update_data   = $this->auction_get_data_for_lookup_table( $id );

			if ( ! empty( $update_data ) && $update_data !== $existing_data ) {
				$wpdb->replace(
					$wpdb->$table,
					$update_data
				);
				wp_cache_set( 'lookup_table', $update_data, 'object_' . $id );
			}
		}

		public function auction_get_data_for_lookup_table( $id ) {

			$price_meta   = (array) get_post_meta( $id, '_price', false );
			$manage_stock = get_post_meta( $id, '_manage_stock', true );
			$stock        = 'yes' === $manage_stock ? wc_stock_amount( get_post_meta( $id, '_stock', true ) ) : null;
			$price        = wc_format_decimal( get_post_meta( $id, '_price', true ) );
			$sale_price   = wc_format_decimal( get_post_meta( $id, '_sale_price', true ) );
			return array(
				'product_id'     => absint( $id ),
				'sku'            => get_post_meta( $id, '_sku', true ),
				'virtual'        => 'yes' === get_post_meta( $id, '_virtual', true ) ? 1 : 0,
				'downloadable'   => 'yes' === get_post_meta( $id, '_downloadable', true ) ? 1 : 0,
				'min_price'      => reset( $price_meta ),
				'max_price'      => end( $price_meta ),
				'onsale'         => $sale_price && $price === $sale_price ? 1 : 0,
				'stock_quantity' => $stock,
				'stock_status'   => get_post_meta( $id, '_stock_status', true ),
				'rating_count'   => array_sum( (array) get_post_meta( $id, '_wc_rating_count', true ) ),
				'average_rating' => get_post_meta( $id, '_wc_average_rating', true ),
				'total_sales'    => get_post_meta( $id, 'total_sales', true ),
			);
		}


	}
endif;
