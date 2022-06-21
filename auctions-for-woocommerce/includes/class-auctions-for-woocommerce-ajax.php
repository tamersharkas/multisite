<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * AJAX Event Handler.
 *
 * @class    AFW_AJAX
 */
class Auctions_For_Woocommerce_Ajax {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'wp_loaded', array( __CLASS__, 'do_wc_ajax' ), 10 );
		self::add_ajax_events();

	}

	/**
	 * Set WC AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET['afw-ajax'] ) ) {
			wc_maybe_define_constant( 'DOING_AJAX', true );
			wc_maybe_define_constant( 'WC_DOING_AJAX', true );
			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Send headers for WC Ajax Requests.
	 *
	 * @since 2.5.0
	 */
	private static function wc_ajax_headers() {
		send_origin_headers();
		send_nosniff_header();
		wc_nocache_headers();
		status_header( 200 );
	}

	/**
	 * Check for WC Ajax request and fire action.
	 */
	public static function do_wc_ajax() {
		if ( ! empty( $_GET['afw-ajax'] ) ) {
			self::wc_ajax_headers();
			do_action( 'wsa_ajax_' . sanitize_text_field( $_GET['afw-ajax'] ) );
			wp_die();
		}
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'finish_auction'         => true,
			'get_price_for_auctions' => true,
			'watchlist'              => true,
			'delete_bid'             => false,
			'remove_reserve_price'   => false,
			'resend_winning_email'   => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );

				// AFW AJAX can be used for frontend ajax requests.
				add_action( 'wsa_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Ajax finish auction
	 *
	 * Function for finishing auction with ajax when countdown is down to zero
	 *
	 * @param  array
	 * @return string
	 *
	 */
	public static function finish_auction() {

		check_ajax_referer( 'woocommerce_auction_nonce', 'security' );

		if ( isset( $_POST['post_id'] ) ) {

			$response = array();

			$post_id      = absint( $_POST['post_id'] );
			$ret          = ! empty( $_POST['ret'] ) ? true : false;
			$future       = ! empty( $_POST['future'] ) ? true : false;
			$product_data = wc_get_product( $post_id );

			if ( $product_data->is_closed() ) {
				$response['status'] = 'closed';
				if ( false !== $ret ) {

					if ( $product_data->is_reserved() && ! $product_data->is_reserve_met()) {
							$response['message'] = esc_html__( 'Reserve price has not been met', 'auctions-for-woocommerce' );
							wp_send_json( $response );
							die();
					}
					if ( $product_data->get_auction_current_bider() ) {
						/* translators: 1) current bid 2) bidder name */
						$response['message'] .= sprintf( esc_html__( 'Winning bid is %1$s by %2$s.', 'auctions-for-woocommerce' ), wc_price( $product_data->get_curent_bid() ), apply_filters( 'auctions_for_woocommerce_displayname', get_userdata( $product_data->get_auction_current_bider() )->display_name, $product_data ) );
						if ( get_current_user_id() === intval( $product_data->get_auction_current_bider() ) ) {
							$response['message'] .= '<a href="' . apply_filters( 'auctions_for_woocommerce_pay_now_button', esc_attr( add_query_arg( 'pay-auction', $product_data->get_id(), auctions_for_woocommerce_get_checkout_url() ) ) ) . '" class="button">' . esc_html__( 'Pay Now', 'auctions-for-woocommerce' ) . '</a>';
						}
						wp_send_json( $response );
						die();
					} else {
						$response['message'] = esc_html__( 'There were no bids for this auction.', 'auctions-for-woocommerce' );
						wp_send_json( $response );
						die();
					}
				}
			} else {
				if ( $product_data->is_started() ) {
					if ( true === $future ) {
						$response['status']  = 'started';
						$response['message'] = esc_html__( 'Auction has started please refresh page.', 'auctions-for-woocommerce' );
					} elseif ( false === $future ) {
						$response['status']  = 'running';
						$response['message'] = esc_html__( 'Please refresh page.', 'auctions-for-woocommerce' );
					}
				} else {
					$response['status'] = 'future';
				}
			}
			wp_send_json( apply_filters( 'auctions_for_woocommerce_ajax_finish_auction', $response ) );
			die();
		}
		die();
	}

				/**
				 * Ajax watch list auction
				 *
				 * Function for adding or removing auctions to wishlist
				 *
				 * @param  array
				 * @return string
				 *
				 */
	public static function watchlist() {

		if ( 'yes' !== get_option( 'auctions_for_woocommerce_watchlists', 'yes' ) ) {
			exit;
		}
		check_ajax_referer( 'woocommerce_auction_nonce', 'security' );
		if ( is_user_logged_in() ) {
			$post_id = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : 0;
			$user_ID = get_current_user_id();
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$post_id = $product->get_main_wpml_product_id();
				if ( $product->is_user_watching() ) {
						delete_post_meta( $post_id, '_auction_watch', $user_ID );
						delete_user_meta( $user_ID, '_auction_watch', $post_id );

						do_action( 'auctions_for_woocommerce_after_delete_fom_watchlist', $post_id, $user_ID );
				} else {
						add_post_meta( $post_id, '_auction_watch', $user_ID );
						add_user_meta( $user_ID, '_auction_watch', $post_id );
						do_action( 'auctions_for_woocommerce_after_add_to_watchlist', $post_id, $user_ID );
				}
				wc_get_template( 'single-product/watchlist-link.php', array( 'product_id' => $post_id ) );
			}
		} else {

			echo '<p>';
			/* translators: 1) link to my account page */
			echo wp_kses_post( sprintf( __( 'Sorry, you must be logged in to add auction to watchlist. <a href="%s" class="button">Login &rarr;</a>', 'auctions-for-woocommerce' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) );
			echo '</p>';
		}

		exit;
	}

	/**
	 * Ajax get price for auctions
	 * Function for getiing pices changes for auctions
	 *
	 * @param  array
	 * @return json
	 *
	 */
	public static function get_price_for_auctions() {
		$return = null;
		check_ajax_referer( 'woocommerce_auction_nonce', 'security' );
		if ( isset( $_POST['last_activity'] ) ) {

			$last_activity = get_option( 'auctions_for_woocommerce_last_activity', '0' );

			if ( intval( $_POST['last_activity'] ) === intval( $last_activity ) ) {

				wp_send_json( apply_filters( 'auctions_for_woocommerce_get_price_for_auctions', $return ) );
				die();

			} else {

				$return['last_activity'] = $last_activity;

			}

			$args = array(
				'post_type'              => 'product',
				'posts_per_page'         => '-1',
				'meta_query'             => array(
					array(
						'key'     => '_auction_last_activity',
						'compare' => '>',
						'value'   => intval( $_POST['last_activity'] ),
						'type'    => 'NUMERIC',
					),
				),
				'auction_arhive'         => true,
				'show_past_auctions'     => true,
				'show_future_auctions'   => true,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'suppress_filters'       => true,

			);
			$the_query = new WP_Query( $args );
			$posts_ids = $the_query->posts;
			if ( is_array( $posts_ids ) ) {

				foreach ( $posts_ids as $posts_id ) {

					$product_data = wc_get_product( $posts_id );

					if ( 'yes' !== $product_data->get_auction_sealed() ) {

						if ( $product_data->is_closed() ) {

							$return[ $posts_id ]['curent_bid']       = $product_data->get_price_html();
							$return[ $posts_id ]['curent_bider']     = $product_data->get_auction_current_bider();
							$return[ $posts_id ]['add_to_cart_text'] = $product_data->add_to_cart_text();
							/* translators: 1) auction link bid 2) auction name */
							$return[ $posts_id ]['notify_text']      = apply_filters( 'auctions_for_woocommerce_notify_text', sprintf( wp_kses_post( __( 'Auctions closed <a href="%1$s">%2$s</a>', 'auctions-for-woocommerce' ) ), get_permalink( $posts_id ), get_the_title( $posts_id ) ), $product_data );
							$return[ $posts_id ]['notify_text_type'] = 'warning';

							if ( $product_data->is_reserved() === true ) {
								if ( $product_data->is_reserve_met() === false ) {
									$return[ $posts_id ]['reserve'] = apply_filters( 'reserve_bid_text', __( 'Reserve price has not been met', 'auctions-for-woocommerce' ) );
								} elseif ( $product_data->is_reserve_met() === true ) {
									$return[ $posts_id ]['reserve'] = apply_filters( 'reserve_met_bid_text', __( 'Reserve price has been met', 'auctions-for-woocommerce' ) );
								}
							}
						} elseif ( $product_data->is_started() ) {

								$return[ $posts_id ]['curent_bid']       = $product_data->get_price_html();
								$return[ $posts_id ]['curent_bider']     = $product_data->get_auction_current_bider();
								$return[ $posts_id ]['bid_value']        = $product_data->bid_value();
								$return[ $posts_id ]['timer']            = $product_data->get_seconds_remaining();
								$return[ $posts_id ]['activity']         = $product_data->auction_history_last( $posts_id );
								$return[ $posts_id ]['add_to_cart_text'] = $product_data->add_to_cart_text();
							if ( $product_data->get_auction_bid_count() === 0 ) {
								/* translators: 1) auction link bid 2) auction name */
								$return[ $posts_id ]['notify_text']      = apply_filters( 'auctions_for_woocommerce_notify_text', sprintf( wp_kses_post( __( 'Auction started <a href="%1$s">%2$s</a>', 'auctions-for-woocommerce' ) ), get_permalink( $posts_id ), get_the_title( $posts_id ) ), $product_data );
								$return[ $posts_id ]['notify_text_type'] = 'info';
							} else {
								/* translators: 1) auction link bid 2) auction name */
								$return[ $posts_id ]['notify_text']      = apply_filters( 'auctions_for_woocommerce_notify_text', sprintf( wp_kses_post( __( 'New bid for <a href="%1$s">%2$s</a>', 'auctions-for-woocommerce' ) ), get_permalink( $posts_id ), get_the_title( $posts_id ) ), $product_data );
								$return[ $posts_id ]['notify_text_type'] = 'success';
							}
							if ( $product_data->is_reserved() === true ) {
								if ( $product_data->is_reserve_met() === false ) {
									$return[ $posts_id ]['reserve'] = apply_filters( 'reserve_bid_text', __( 'Reserve price has not been met', 'auctions-for-woocommerce' ) );
								} elseif ( $product_data->is_reserve_met() === true ) {
									$return[ $posts_id ]['reserve'] = apply_filters( 'reserve_met_bid_text', __( 'Reserve price has been met', 'auctions-for-woocommerce' ) );
								}
							}
						}
					}
				}
			}
		}
		wp_send_json( apply_filters( 'auctions_for_woocommerce_get_price_for_auctions', $return ) );
		die();
	}

	/**
	 * Ajax delete bid
	 *
	 * Function for deleting bid in wp admin
	 *
	 * @param  array
	 * @return string
	 *
	 */
	public static function delete_bid() {

		global $wpdb;
		check_ajax_referer( 'AFWajax-nonce', 'security' );
		$post_id = isset( $_POST['postid'] ) ? absint( $_POST['postid'] ) : 0;
		$log_id  = isset( $_POST['logid'] ) ? absint( $_POST['logid'] ) : 0;

		if ( ! current_user_can( 'edit_product', $post_id ) ) {
				die();
		}

		if ( $post_id > 0 ) {
				$product_data = wc_get_product( $post_id );
				$log          = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE id=%d', $log_id ) );
				$last_log     = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE auction_id =%d ORDER BY `id` desc', $post_id ) );

			if ( ! is_null( $log ) ) {
				if ( 'normal' === $product_data->get_auction_type() ) {
					if ( $log->id === $last_log->id ) {
						if ( $product_data->get_auction_relisted() ) {
							$newbid = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE auction_id =%d AND `date` > %s ORDER BY `date` desc , `bid`  desc LIMIT 1, 1 ', $post_id, $product_data->get_auction_relisted() ) );
						} else {
							$newbid = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE auction_id =%d ORDER BY `date` desc , `bid`  desc LIMIT 1, 1 ', $post_id ) );
						}
						if ( ! is_null( $newbid ) ) {
								update_post_meta( $post_id, '_auction_current_bid', $newbid->bid );
								update_post_meta( $post_id, '_auction_current_bider', $newbid->userid );
								delete_post_meta( $post_id, '_auction_max_bid' );
								delete_post_meta( $post_id, '_auction_max_current_bider' );
								do_action(
									'auctions_for_woocommerce_delete_bid',
									array(
										'product_id'     => $post_id,
										'delete_user_id' => $log->userid,
										'new_max_bider_id ' => $newbid->userid,
									)
								);
						} else {
								delete_post_meta( $post_id, '_auction_current_bid' );
								delete_post_meta( $post_id, '_auction_current_bider' );
								delete_post_meta( $post_id, '_auction_max_bid' );
								delete_post_meta( $post_id, '_auction_max_current_bider' );
								do_action(
									'auctions_for_woocommerce_delete_bid',
									array(
										'product_id'     => $post_id,
										'delete_user_id' => $log->userid,
										'new_max_bider_id ' => false,
									)
								);
						}
							$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE id= %d', $log_id ) );
							update_post_meta( $post_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() - 1 ) );
							$return['action'] = 'deleted';

					} else {
							$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE id= %d', $log_id ) );
							update_post_meta( $post_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() - 1 ) );
							$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE id= %d', $log_id ) );
							do_action(
								'auctions_for_woocommerce_delete_bid',
								array(
									'product_id'     => $post_id,
									'delete_user_id' => $log->userid,
								)
							);
							$return['action'] = 'deleted';

					}
				} elseif ( 'reverse' === $product_data->get_auction_type() ) {

					if ( $log->id === $last_log->id ) {

						if ( $product_data->get_auction_relisted() ) {
							$newbid = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE auction_id =%d AND `date` > %s ORDER BY `date` desc , `bid`  asc LIMIT 1, 1 ', $post_id, $product_data->get_auction_relisted() ) );
						} else {
							$newbid = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE auction_id =%d ORDER BY  `date` desc , `bid`  asc LIMIT 1, 1 ', $post_id ) );
						}

						if ( ! is_null( $newbid ) ) {
								update_post_meta( $post_id, '_auction_current_bid', $newbid->bid );
								update_post_meta( $post_id, '_auction_current_bider', $newbid->userid );
								delete_post_meta( $post_id, '_auction_max_bid' );
								delete_post_meta( $post_id, '_auction_max_current_bider' );
								do_action(
									'auctions_for_woocommerce_delete_bid',
									array(
										'product_id'     => $post_id,
										'delete_user_id' => $log->userid,
										'new_max_bider_id ' => $newbid->userid,
									)
								);
						} else {
								delete_post_meta( $post_id, '_auction_current_bid' );
								delete_post_meta( $post_id, '_auction_current_bider' );
								do_action(
									'auctions_for_woocommerce_delete_bid',
									array(
										'product_id'     => $post_id,
										'delete_user_id' => $log->userid,
										'new_max_bider_id ' => false,
									)
								);
						}
								$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE id= %d', $log_id ) );
								update_post_meta( $post_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() - 1 ) );
								$return['action'] = 'deleted';

					} else {
							$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log  WHERE id= %d', $log_id ) );
							update_post_meta( $post_id, '_auction_bid_count', absint( $product_data->get_auction_bid_count() - 1 ) );
							do_action(
								'auctions_for_woocommerce_delete_bid',
								array(
									'product_id'     => $post_id,
									'delete_user_id' => $log->userid,
								)
							);
							$return['action'] = 'deleted';

					}
				}

				$product = wc_get_product( $post_id );

				if ( $product ) {
						$return['auction_current_bid'] = wc_price( $product->get_curent_bid() );
					if ( $product->get_auction_current_bider() ) {
						$return['auction_current_bider'] = '<a href="' . get_edit_user_link( $product->get_auction_current_bider() ) . '">' . get_userdata( $product->get_auction_current_bider() )->display_name . '</a>';
					}
				}

				if ( isset( $return ) ) {
					wp_send_json( $return );
				}

					exit;

			}
		}

		$return['action'] = 'failed';

		if ( isset( $return ) ) {
			wp_send_json( $return );
		}

		exit;

	}

	/**
	 * Ajax remove reserved price
	 *
	 * Function for removing reserved price
	 *
	 * @param  array
	 * @return string
	 *
	 */
	public static function remove_reserve_price() {
		$return = array();
		check_ajax_referer( 'AFWajax-nonce', 'security' );

		$post_id = isset( $_POST['postid'] ) ? absint( $_POST['postid'] ) : 0;
		if ( ! current_user_can( 'edit_product', $post_id ) ) {
			die();
		}

		$product_data = wc_get_product( $post_id );

		if ( $product_data ) {

			if ( $product_data->is_closed() ) {
				if ( 1 === $product_data->get_auction_closed() ) {
					if ( 2 === $product_data->get_auction_fail_reason() ) {

							delete_post_meta( $post_id, '_auction_reserved_price' );
							wp_remove_object_terms( $post_id, array( 'finished', 'sold', 'buy-now' ), 'auction_visibility' );
							delete_post_meta( $post_id, '_auction_fail_reason' );
							$product_data->is_closed();
							$return['succes'] = esc_html__( 'Reserve price removed! Please refresh page.', 'auctions-for-woocommerce' );
						if ( ! empty( $return ) ) {
								wp_send_json( $return );
						}

						exit;
					}
				}
			} else {
						$return['error'] = esc_html__( 'Auction is still active!', 'auctions-for-woocommerce' );
			}
		}
			$return['error'] = esc_html__( 'Reserve price not removed', 'auctions-for-woocommerce' );
		if ( ! empty( $return ) ) {
				wp_send_json( $return );
		}

		exit;

	}

	/**
	 * Ajax  resend winning emails
	 *
	 * Function for sending winning emails
	 *
	 * @param  array
	 * @return json
	 *
	 */
	public static function resend_winning_email() {

		check_ajax_referer( 'AFWajax-nonce', 'security' );
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

		if ( ! current_user_can( 'edit_product', $product_id ) ) {
			die();
		}

		$product_data = wc_get_product( $product_id );

		if ( $product_data ) {

			if ( $product_data->is_closed() ) {
				if ( 2 === $product_data->get_auction_closed() ) {
					WC()->mailer();
					include_once AFW_ABSPATH . 'includes/emails/class-wc-email-auction-wining.php';
					$emails->emails['WC_Email_Auction_Wining'] = new WC_Email_Auction_Wining();
					$emails->emails['WC_Email_Auction_Wining']->trigger( $product_id, true );
					$return['succes'] = esc_html__( 'Email has been sent to winner!.', 'auctions-for-woocommerce' );
					if ( isset( $return ) ) {
						wp_send_json( $return );
					}
				}
			} else {
				$return['error'] = esc_html__( 'Auction is not sold!', 'auctions-for-woocommerce' );
			}
		}
		$return['error'] = esc_html__( 'Email is not sent!', 'auctions-for-woocommerce' );
		if ( isset( $return ) ) {
				wp_send_json( $return );
		}

		exit;

	}

}
Auctions_For_Woocommerce_Ajax::init();

