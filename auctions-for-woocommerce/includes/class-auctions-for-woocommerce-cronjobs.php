<?php

/**
 *  *
 * This class defines all code necessary to run cronjobs.
 *
 * @since      1.0.0
 * @package    Auctions_For_Woocommerce
 * @subpackage Auctions_For_Woocommerce/includes
 */
class Auctions_For_Woocommerce_Cronjobs {

	/**
	 * Hook in cronjobs handlers.
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'cron_job_handler' ), 99);
	}

	public static function cron_job_handler() {

		if ( empty( $_REQUEST['auction-cron'] ) ) {
			return;
		}

		self::cronjob_headers();

		if ( 'check' === $_REQUEST['auction-cron'] ) {

			self::check_auction_for_closing();

		} elseif ( 'mails' === $_REQUEST['auction-cron'] ) {

			self::send_mails();

		} elseif ( 'relist' === $_REQUEST['auction-cron'] ) {

			self::relist();

		} elseif ( 'closing-soon-emails' === $_REQUEST['auction-cron'] ) {

			self::closing_soon_emails();

		} elseif ( 'remind-to-pay' === $_REQUEST['auction-cron'] ) {

			self::send_reminders_email();

		}

		die();

	}

	/**
	 * Send headers for cronjob requests.
	 *
	 * @since 2.0.0
	 */
	private static function cronjob_headers() {
		send_origin_headers();
		send_nosniff_header();
		wc_nocache_headers();
		status_header( 200 );
	}

	public static function check_auction_for_closing() {

		update_option( 'auctions_for_woocommerce_cron_check', 'yes' );
		set_time_limit( 0 );
		ignore_user_abort( 1 );

		$args = array(
			'post_type'            => 'product',
			'posts_per_page'       => '-1',
			'meta_key'             => '_auction_dates_to',
			'orderby'              => 'meta_value',
			'order'                => 'ASC',
			'tax_query'            => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now' ) ),

			),
			'auction_arhive'       => true,
			'show_past_auctions'   => true,
			'show_future_auctions' => true,
			'fields'               => 'ids',

		);
		for ( $i = 0; $i < 3; $i++ ) {
			$time      = microtime( 1 );
			$the_query = new WP_Query( $args );
			$posts_ids = $the_query->posts;

			if ( is_array( $posts_ids ) ) {

				foreach ( $posts_ids as $posts_id ) {

					$product_data = wc_get_product( $posts_id );
					$product_data->is_closed();

				}
			}

			$time = microtime( 1 ) - $time;
			if ( $i < 3 ) {
				sleep( 20 - $time );
			}
		}
	}

	public static function send_mails() {

		update_option( 'auctions_for_woocommerce_cron_mail', 'yes' );
		set_time_limit( 0 );
		ignore_user_abort( 1 );

		$remind_to_pay_settings = get_option( 'woocommerce_remind_to_pay_settings' );
		if ( 'yes' !== $remind_to_pay_settings['enabled'] ) {
			exit();
		}

		$interval    = ( ! empty( $remind_to_pay_settings['interval'] ) ) ? (int) $remind_to_pay_settings['interval'] : 7;
		$stopsending = ( ! empty( $remind_to_pay_settings['stopsending'] ) ) ? (int) $remind_to_pay_settings['stopsending'] : 5;
		$args        = array(
			'post_type'          => 'product',
			'posts_per_page'     => '100',
			'show_past_auctions' => true,
			'tax_query'          => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->show_only_tax_query( 'sold' ),
			),
			'meta_query'         => array(
				'relation' => 'AND',
				array(
					'key'     => '_auction_payed',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_stop_mails',
					'compare' => 'NOT EXISTS',
				),
			),
			'auction_arhive'     => true,
			'show_past_auctions' => true,
			'fields'             => 'ids',
		);

		$the_query = new WP_Query( $args );
		$posts_ids = $the_query->posts;

		if ( is_array( $posts_ids ) ) {

			foreach ( $posts_ids as $posts_id ) {

				$product_data        = wc_get_product( $posts_id );
				$number_of_sent_mail = get_post_meta( $posts_id, '_number_of_sent_mails', true );
				$dates_of_sent_mail  = get_post_meta( $posts_id, '_dates_of_sent_mails', false );
				$n_days              = (int) $remind_to_pay_settings['interval'];

				if ( (int) $number_of_sent_mail >= $stopsending ) {

						update_post_meta( $posts_id, '_stop_mails', '1' );

				} elseif ( ( ! $dates_of_sent_mail || ( (int) end( $dates_of_sent_mail ) < strtotime( '-' . $interval . ' days' ) ) ) && ( strtotime( $product_data->get_auction_dates_to() ) < strtotime( '-' . $interval . ' days' ) ) ) {

					update_post_meta( $posts_id, '_number_of_sent_mails', $number_of_sent_mail + 1 );
					add_post_meta( $posts_id, '_dates_of_sent_mails', time(), false );

					do_action( 'auctions_for_woocommerce_pay_reminder', $posts_id );
				}
			}
		}

	}

	public static function relist() {
		update_option( 'auctions_for_woocommerce_cron_relist', 'yes' );
		set_time_limit( 0 );
		ignore_user_abort( 1 );

		$args = array(
			'post_type'          => 'product',
			'posts_per_page'     => '200',
			'tax_query'          => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->show_only_tax_query( 'finished', 'sold', 'buy-now' ),
			),
			'meta_query'         => array(
				'relation' => 'AND',
				array(
					'key'     => '_auction_payed',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'   => '_auction_automatic_relist',
					'value' => 'yes',
				),
			),
			'auction_arhive'     => true,
			'show_past_auctions' => true,
			'fields'             => 'ids',
		);

		$the_query = new WP_Query( $args );
		$posts_ids = $the_query->posts;

		if ( is_array( $posts_ids ) ) {

			require_once AFW_ABSPATH . 'admin/class-auctions-for-woocommerce-admin.php';
			$plugin_admin = new Auctions_For_Woocommerce_Admin( 'auctions-for-woocommerce', AFW_PLUGIN_VERSION );

			foreach ( $posts_ids as $post_id ) {

				$plugin_admin->relist_auction( $post_id );

			}
		}

	}

	public static function closing_soon_emails() {

		update_option( 'auctions_for_woocommerce_cron_closing_soon_emails', 'yes' );
		set_time_limit( 0 );
		ignore_user_abort( 1 );

		$auction_closing_soon_settings = get_option( 'woocommerce_auction_closing_soon_settings' );
		$interval                      = ( ! empty( $auction_closing_soon_settings['interval'] ) ) ? floatval( $auction_closing_soon_settings['interval'] ) : 1;
		$interval2                     = ( ! empty( $auction_closing_soon_settings['interval2'] ) && ! empty( $auction_closing_soon_settings['interval2'] ) ) ? floatval( $auction_closing_soon_settings['interval2'] ) : false;

		if ( false !== $interval2 ) {
			if ( $interval > $interval2 ) {
				$tmp       = $interval2;
				$interval2 = $interval;
				$interval  = $tmp;
			}
		}

		$maxtime = gmdate( 'Y-m-d H:i', current_time( 'timestamp' ) + ( $interval * HOUR_IN_SECONDS ) );

		$maxtime2 = ( false !== $interval2 ) ? gmdate( 'Y-m-d H:i', current_time( 'timestamp' ) + ( $interval2 * HOUR_IN_SECONDS ) ) : false;

		if ( false !== $maxtime2 ) {

			$args = array(
				'post_type'          => 'product',
				'posts_per_page'     => '100',
				'show_past_auctions' => true,
				'tax_query'          => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'auction',
					),
					AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now' ) ),
				),
				'meta_query'         => array(
					'relation' => 'AND',
					array(
						'key'     => '_auction_sent_closing_soon2',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_auction_sent_closing_soon',
						'compare' => 'NOT EXISTS',
					),
					array(
						array(
							'key'     => '_auction_dates_to',
							'compare' => '<=',
							'value'   => $maxtime2,
							'type '   => 'DATETIME',
						),
						array(
							'key'     => '_auction_dates_to',
							'compare' => '>',
							'value'   => $maxtime,
							'type '   => 'DATETIME',
						),
					),
				),
				'auction_arhive'     => true,
				'fields'             => 'ids',
			);

			$the_query = new WP_Query( $args );
			$posts_ids = $the_query->posts;

			if ( is_array( $posts_ids ) ) {

				foreach ( $posts_ids as $posts_id ) {

					add_post_meta( $posts_id, '_auction_sent_closing_soon2', time(), true );
					do_action( 'auctions_for_woocommerce_closing_soon', $posts_id );

				}
			}
		}

		$args = array(
			'post_type'          => 'product',
			'posts_per_page'     => '100',
			'show_past_auctions' => true,
			'tax_query'          => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now' ) ),
			),
			'meta_query'         => array(
				'relation' => 'AND',
				array(
					'key'     => '_auction_sent_closing_soon',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_auction_dates_to',
					'compare' => '<',
					'value'   => $maxtime,
					'type '   => 'DATETIME',
				),

			),
			'auction_arhive'     => true,
			'fields'             => 'ids',

		);

		$the_query = new WP_Query( $args );
		$posts_ids = $the_query->posts;

		if ( is_array( $posts_ids ) ) {

			foreach ( $posts_ids as $posts_id ) {

				add_post_meta( $posts_id, '_auction_sent_closing_soon', time(), true );
				do_action( 'auctions_for_woocommerce_closing_soon', $posts_id );

			}
		}
	}

	public function send_reminders_email() {

		update_option( 'auctions_for_woocommerce_cron_reminders_email', 'yes' );

		$remind_to_pay_settings = get_option( 'woocommerce_remind_to_pay_settings' );

		if ( 'yes' !== $remind_to_pay_settings['enabled'] ) {
			exit();
		}

		$interval    = ( isset( $remind_to_pay_settings['interval'] ) && ! empty( $remind_to_pay_settings['interval'] ) ) ? (int) $remind_to_pay_settings['interval'] : 7;
		$stopsending = ( isset( $remind_to_pay_settings['stopsending'] ) && ! empty( $remind_to_pay_settings['stopsending'] ) ) ? (int) $remind_to_pay_settings['stopsending'] : 5;
		$args = array(
			'post_type'            => 'product',
			'posts_per_page'       => '-1',
			'tax_query'            => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->show_only_tax_query( array( 'sold' ) ),

			),
			'meta_query'             => array(
				'relation' => 'AND',
				array(
					'key'     => '_auction_payed',
					'compare' => 'NOT EXISTS',
				),

				array(
					'key'     => '_stop_mails',
					'compare' => 'NOT EXISTS',
				),

			),
			'auction_arhive'       => true,
			'show_past_auctions'   => true,
			'show_future_auctions' => true,
			'fields'               => 'ids',

		);

		$the_query = new WP_Query( $args );
		$posts_ids = $the_query->posts;

		if ( is_array( $posts_ids ) ) {

			foreach ( $posts_ids as $posts_id ) {

				$number_of_sent_mail = (int) get_post_meta( $posts_id, '_number_of_sent_mails', true );
				$dates_of_sent_mail  = get_post_meta( $posts_id, '_dates_of_sent_mails', false );
				$n_days              = (int) $remind_to_pay_settings['interval'];

				if ( (int) $number_of_sent_mail >= $stopsending ) {

					update_post_meta( $posts_id, '_stop_mails', '1' );

				} elseif ( ! $dates_of_sent_mail || ( (int) end( $dates_of_sent_mail ) < strtotime( '-' . $interval . ' days' ) ) ) {

					update_post_meta( $posts_id, '_number_of_sent_mails', $number_of_sent_mail + 1 );
					add_post_meta( $posts_id, '_dates_of_sent_mails', time(), false );
					do_action( 'auctions_for_woocommerce_pay_reminder', $posts_id );

				}
			}
		}
	}

}

Auctions_For_Woocommerce_Cronjobs::init();

