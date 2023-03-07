<?php
/**
  * Simple Auctions Shortcode
  *
  */

class Auctions_For_Woocommerce_Shortcodes extends WC_Shortcodes {

	public function __construct() {
		// Regular shortcodes

		add_shortcode( 'auctions', array( $this, 'auctions' ) );
		add_shortcode( 'recent_auctions', array( $this, 'recent_auctions' ) );
		add_shortcode( 'featured_auctions', array( $this, 'featured_auctions' ) );
		add_shortcode( 'ending_soon_auctions', array( $this, 'ending_soon_auctions' ) );
		add_shortcode( 'future_auctions', array( $this, 'future_auctions' ) );
		add_shortcode( 'past_auctions', array( $this, 'past_auctions' ) );
		add_shortcode( 'auctions_watchlist', array( $this, 'auctions_watchlist' ) );
		add_shortcode( 'my_auctions_activity', array( $this, 'my_auctions_activity' ) );
		add_shortcode( 'all_user_auctions', array( $this, 'all_user_auctions' ) );
		add_shortcode( 'auctions_for_woocommerce_my_auctions', array( $this, 'shortcode_my_auctions' ) );
		add_shortcode( 'auctions_recent_bids', array( $this, 'auctions_recent_bids' ) );

	}
		/**
		 * Output featured products
		 *
		 * @param array $atts
		 * @return string
		 */
	public function featured_auctions( $atts ) {

		global $woocommerce_loop;

		$atts = shortcode_atts(
			array(
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'date',
				'order'    => 'desc',
			),
			$atts
		);

		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts['per_page'],
			'orderby'             => $atts['orderby'],
			'order'               => $atts ['order'],
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
			),
			'auction_arhive'      => true,

		);

		$args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'featured',
		);

		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $atts ['columns'];

		if ( $products->have_posts() ) : ?>

				 <?php woocommerce_product_loop_start(); ?>

					 <?php
						while ( $products->have_posts() ) :
							  $products->the_post();
							?>

							  <?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>

				 <?php woocommerce_product_loop_end(); ?>

			 <?php
			 endif;

		wp_reset_postdata();

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

		/**
		 * Recent Auction shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
	public function recent_auctions( $atts ) {

		global $woocommerce_loop, $woocommerce;

		$atts = shortcode_atts(
			array(
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'date',
				'order'    => 'desc',
			),
			$atts
		);

		$meta_query = $woocommerce->query->get_meta_query();

		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts['per_page'],
			'orderby'             => $atts['orderby'],
			'order'               => $atts ['order'],
			'meta_query'          => $meta_query,
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
			),
			'auction_arhive'      => true,
		);

		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $atts ['columns'];

		if ( $products->have_posts() ) :
			?>

			   <?php woocommerce_product_loop_start(); ?>

				   <?php
					while ( $products->have_posts() ) :
						$products->the_post();
						?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				 <?php woocommerce_product_loop_end(); ?>
				<?php else : ?>
					<?php wc_get_template( 'loop/no-products-found.php' ); ?>

					<?php
			 endif;

				wp_reset_postdata();

				return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

	 /**
		  * List multiple auctions shortcode
		  
		  * @param array $atts
		  * @return string
		  */
	public function auctions( $atts ) {
		global $woocommerce_loop;

		if ( empty( $atts ) ) {
			return;
		}

		$atts = shortcode_atts(
			array(
				'columns' => '4',
				'orderby' => 'title',
				'order'   => 'asc',

			),
			$atts
		);

		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts ['order'],
			'posts_per_page'      => -1,
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
			),
			'auction_arhive'      => true,

		);

		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$product_visibility_not_in = $product_visibility_terms['exclude-from-catalog'];
		if ( ! empty( $product_visibility_not_in ) ) {
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_not_in,
					'operator' => 'NOT IN',
				);
		}

		if ( isset( $atts['skus'] ) ) {
			$skus                 = explode( ',', $atts['skus'] );
			$skus                 = array_map( 'trim', $skus );
			$args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => $skus,
				'compare' => 'IN',
			);
		}

		if ( isset( $atts['ids'] ) ) {
			$ids              = explode( ',', $atts['ids'] );
			$ids              = array_map( 'trim', $ids );
			$args['post__in'] = $ids;
		}
		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $atts ['columns'];

		if ( $products->have_posts() ) :
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php
				while ( $products->have_posts() ) :
					$products->the_post();
					?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>
			<?php else : ?>
				<?php wc_get_template( 'loop/no-products-found.php' ); ?>

				<?php
			endif;

			wp_reset_postdata();

			return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

		/**
		 * Recent Auction shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
	public function ending_soon_auctions( $atts ) {

		global $woocommerce_loop, $woocommerce;

		$future_tax_query = array();

		$atts = shortcode_atts(
			array(
				'per_page' => '12',
				'columns'  => '4',
				'order'    => 'desc',
				'orderby'  => 'meta_value',
				'future'   => 'no',
			),
			$atts
		);

		if ( 'yes' === $atts['future'] ) {
			$future_tax_query = AFW()->show_only_tax_query( 'future' );
		} else {
			$future_tax_query = AFW()->remove_from_tax_query( 'future' );
		}
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts['per_page'],
			'orderby'             => $atts['orderby'],
			'order'               => $atts ['order'],
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now' ) ),
				$future_tax_query,
			),
			'meta_key'            => '_auction_dates_to',
			'auction_arhive'      => true,
		);
		ob_start();

		$products                    = new WP_Query( $args );
		$woocommerce_loop['columns'] = $atts['columns'];

		if ( $products->have_posts() ) :
			?>

			   <?php woocommerce_product_loop_start(); ?>

				   <?php
					while ( $products->have_posts() ) :
						$products->the_post();
						?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>
			<?php else : ?>
				<?php wc_get_template( 'loop/no-products-found.php' ); ?>

				<?php
			 endif;

			wp_reset_postdata();

			return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}
		   /**
		 * Recent Auction shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
	public function future_auctions( $atts ) {

		global $woocommerce_loop, $woocommerce;

		$future_tax_query = array();

		$atts = shortcode_atts(
			array(
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'meta_value',
				'order'    => 'desc',
			),
			$atts
		);

		$args = array(
			'post_type'            => 'product',
			'post_status'          => 'publish',
			'ignore_sticky_posts'  => 1,
			'posts_per_page'       => $atts['per_page'],
			'orderby'              => $atts['orderby'],
			'order'                => $atts ['order'],
			'tax_query'            => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->show_only_tax_query( 'future' ),
			),

			'meta_key'             => '_auction_dates_to',
			'auction_arhive'       => true,
			'show_future_auctions' => true,
		);

		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $atts['columns'];

		if ( $products->have_posts() ) :
			?>

			   <?php woocommerce_product_loop_start(); ?>

				   <?php
					while ( $products->have_posts() ) :
						$products->the_post();
						?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>
			<?php else : ?>
				<?php wc_get_template( 'loop/no-products-found.php' ); ?>

				<?php
			 endif;

			wp_reset_postdata();

			return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

		/**
	* Recent Auction shortcode
	*
	* @param array $atts
	* @return string
	*/
	public function past_auctions( $atts ) {

		global $woocommerce_loop, $woocommerce;

		$past_tax_query = array();

		$atts = shortcode_atts(
			array(
				'per_page' => '12',
				'columns'  => '4',
				'orderby'  => 'meta_value',
				'order'    => 'desc',
			),
			$atts
		);

		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts['per_page'],
			'orderby'             => $atts['orderby'],
			'order'               => $atts ['order'],
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
				AFW()->show_only_tax_query( array( 'finished', 'sold', 'buy-now' ) ),
			),
			'meta_key'            => '_auction_dates_to',
			'auction_arhive'      => true,
			'show_past_auctions'  => true,
		);

		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $atts['columns'];

		if ( $products->have_posts() ) :
			?>

			<?php woocommerce_product_loop_start(); ?>

			   <?php
				while ( $products->have_posts() ) :
					$products->the_post();
					?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>
			<?php else : ?>
						<?php wc_get_template( 'loop/no-products-found.php' ); ?>

				<?php
	  endif;

			wp_reset_postdata();

			return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

		 /**
		 * Watchlist shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
	public function auctions_watchlist( $atts ) {

		global $woocommerce_loop, $woocommerce, $watchlist;

		$atts = shortcode_atts(
			array(
				'per_page' => '-1',
				'columns'  => '4',
				'orderby'  => 'meta_value',
				'order'    => 'desc',
			),
			$atts
		);

		$meta_query = $woocommerce->query->get_meta_query();

		$user_ID       = get_current_user_id();
		$watchlist_ids = get_user_meta( $user_ID, '_auction_watch' );

		$args = array(
			'post_type'            => 'product',
			'post_status'          => 'publish',
			'ignore_sticky_posts'  => 1,
			'posts_per_page'       => $atts['per_page'],
			'orderby'              => $atts['orderby'],
			'order'                => $atts ['order'],
			'meta_query'           => $meta_query,
			'tax_query'            => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				),
			),
			'meta_key'             => '_auction_dates_to',
			'auction_arhive'       => true,
			'show_future_auctions' => true,
			'post__in'             => $watchlist_ids,
		);

		ob_start();

		if ( is_user_logged_in() ) {

			$products = new WP_Query( $args );

			$woocommerce_loop['columns'] = $atts['columns'];

			$watchlist = true;

			if ( $products->have_posts() && ! empty( $watchlist_ids ) ) :
				?>

				   <?php woocommerce_product_loop_start(); ?>

					   <?php
						while ( $products->have_posts() ) :
							$products->the_post();
							?>

							<?php wc_get_template_part( 'content', 'product' ); ?>

						<?php endwhile; // end of the loop. ?>

					<?php woocommerce_product_loop_end(); ?>
				<?php else : ?>
					<?php wc_get_template( 'loop/no-products-found.php' ); ?>

					<?php
				 endif;
				$watchlist = false;
				wp_reset_postdata();
		} else {
			echo '<p class="woocommerce-info">' . esc_html__( 'Please log in to see your auction watchlist', 'auctions-for-woocommerce' ) . '.</p>';
		}

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

		/**
		 * My_auctions_activity shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
	public function my_auctions_activity( $atts ) {
		global $wpdb;

		if ( is_user_logged_in() ) {

			$atts = shortcode_atts(
				array(
					'limit' => 10,

				),
				$atts
			);

			$user_id = get_current_user_id();

			$useractivity = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->prefix . 'auctions_for_woocommerce_log` WHERE `userid` = %d ORDER BY date DESC limit %d ', $user_id, $atts['limit'] ) );

			if ( $useractivity ) {

				echo '<table class="my_auctions_activity">';
				echo '<tr>';
				echo '<th>' . esc_html__( 'Date', 'auctions-for-woocommerce' ) . '</th>';
				echo '<th>' . esc_html__( 'Auction', 'auctions-for-woocommerce' ) . '</th>';
				echo '<th>' . esc_html__( 'Bid', 'auctions-for-woocommerce' ) . '</th>';
				echo '<th>' . esc_html__( 'Status', 'auctions-for-woocommerce' ) . '</th>';
				echo '</tr>';

				foreach ( $useractivity as $value ) {
					if ( get_post_status( $value->auction_id ) === 'publish' ) {
						$class   = '';
						$product = wc_get_product( $value->auction_id );

						if ( $product && $product->is_type( 'auction' ) ) {
							if ( $product->is_closed() ) {
								$class .= 'closed ';
							}

							if ( $product->get_auction_current_bider() === $user_id && ! $product->is_sealed() ) {
								$class .= 'winning ';
							}

							if ( $product->get_auction_current_bider() === $user_id && ! $product->is_reserve_met() ) {
								$class .= 'reserved ';
							}

							if ( strtotime( $product->get_auction_relisted() ) > strtotime( $value->date ) ) {
								$class .= 'relisted ';
							}

							echo '<tr class="' . esc_attr( $class ) . '">';
							echo '<td>' . esc_html( $value->date ) . '</td>';
							echo '<td><a href="' . esc_url( get_permalink( $value->auction_id ) ) . '">' . esc_html( get_the_title( $value->auction_id ) ) . '</a></td>';
							echo '<td>' . wp_kses_post( wc_price( $value->bid ) ) . '</td>';
							echo '<td>' . wp_kses_post( $product->get_price_html() ) . '</td>';
							echo '</tr>';
						}
					}
				}
				echo '</table>';
			}
		} else {
			echo '<div class="woocommerce"><p class="woocommerce-info">' . esc_html__( 'Please log in to see your auctions activity.', 'auctions-for-woocommerce' ) . '</p></div>';
		}
	}

		 /**
		 * All_user_auctions shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
	public function all_user_auctions( $atts ) {
		global $wpdb;

		if ( is_user_logged_in() ) {

			$atts = shortcode_atts(
				array(
					'limit' => '500',

				),
				$atts
			);

			$user_id     = get_current_user_id();
			$postids     = array();
			$userauction = $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT auction_id FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log WHERE userid = %d ORDER BY date DESC limit %d  ', $user_id, $atts['limit'] ), ARRAY_N );
			if ( isset( $userauction ) && ! empty( $userauction ) ) {
				foreach ( $userauction as $auction ) {
					$postids [] = $auction[0];

				}
			}

			?>

			<div class="auctions-for-woocommerce active-auctions clearfix">
				<h2><?php esc_html_e( 'All user auctions', 'auctions-for-woocommerce' ); ?></h2>

				<?php

				$args = array(
					'post__in'           => $postids,
					'post_type'          => 'product',
					'posts_per_page'     => '-1',
					'order'              => 'ASC',
					'orderby'            => 'meta_value',
					'tax_query'          => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'auction',
						),
					),
					'auction_arhive'     => true,
					'show_past_auctions' => true,
				);

				$activeloop = new WP_Query( $args );

				if ( $activeloop->have_posts() && ! empty( $postids ) ) {
					woocommerce_product_loop_start();
					while ( $activeloop->have_posts() ) :
						$activeloop->the_post();
						wc_get_template_part( 'content', 'product' );
						endwhile;
					woocommerce_product_loop_end();

				} else {
					esc_html_e( 'You are not participating in auction.', 'auctions-for-woocommerce' );
				}

				wp_reset_postdata();

				?>
				</div>
				<?php

		}
	}


	public static function shortcode_my_auctions( $atts ) {
		$auction_closed_type = array();

		if ( is_user_logged_in() ) {

			$atts = shortcode_atts(
				array(
					'show_buy_it_now' => 'false',
				),
				$atts
			);

			$user_id = get_current_user_id();
			$postids = get_user_meta( $user_id, 'wsa_my_auctions', false );

			?>
			<div class="auctions-for-woocommerce active-auctions clearfix">
				<h2><?php esc_html_e( 'Active auctions', 'auctions-for-woocommerce' ); ?></h2>
					
				<?php

				$args       = array(
					'post__in'       => $postids,
					'post_type'      => 'product',
					'posts_per_page' => '-1',
					'order'          => 'ASC',
					'orderby'        => 'meta_value',
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'auction',
						),
						AFW()->remove_from_tax_query( array( 'finished', 'sold', 'buy-now' ) ),
					),
					'auction_arhive' => true,
				);
				$activeloop = new WP_Query( $args );

				if ( $activeloop->have_posts() && ! empty( $postids ) ) {
					woocommerce_product_loop_start();
					while ( $activeloop->have_posts() ) :
						$activeloop->the_post();
						wc_get_template_part( 'content', 'product' );
						endwhile;
					woocommerce_product_loop_end();

				} else {
					esc_html_e( 'You are not participating in auction.', 'auctions-for-woocommerce' );
				}

				wp_reset_postdata();

				?>
				</div>
				<div class="auctions-for-woocommerce active-auctions clearfix">
				<h2><?php esc_html_e( 'Won auctions', 'auctions-for-woocommerce' ); ?></h2>
				   <?php
					$auction_closed_type[] = '2';
					if ( 'true' === $atts['show_buy_it_now'] ) {
						$auction_closed_type[] = '3';
					}

					$args = array(
						'post_type'          => 'product',
						'posts_per_page'     => '-1',
						'order'              => 'ASC',
						'orderby'            => 'meta_value',
						'meta_key'           => '_auction_dates_to',
						'tax_query'          => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => 'auction',
							),
							AFW()->show_only_tax_query( 'sold' ),
						),
						'meta_query'         => array(
							array(
								'key'   => '_auction_current_bider',
								'value' => $user_id,
							),
						),
						'show_past_auctions' => true,
						'auction_arhive'     => true,
					);

					$winningloop = new WP_Query( $args );

					if ( $winningloop->have_posts() && ! empty( $postids ) ) {
						woocommerce_product_loop_start();
						while ( $winningloop->have_posts() ) :
							$winningloop->the_post();
							wc_get_template_part( 'content', 'product' );
							endwhile;
						woocommerce_product_loop_end();
					} else {
						esc_html_e( 'You did not win any auctions yet.', 'auctions-for-woocommerce' );
					}

					wp_reset_postdata();
					echo '</div>';

		} else {
			echo '<div class="woocommerce"><p class="woocommerce-info">' . esc_html__( 'Please log in to see your auctions.', 'auctions-for-woocommerce' ) . '</p></div>';
		}

	}

		/**
		 * My_auctions_activity shortcode
		 *
		 * @param array $atts
		 * @return string
		 */
	public function auctions_recent_bids( $atts ) {
		global $wpdb;

		$atts = shortcode_atts(
			array(
				'limit'          => 20,
				'show_usernames' => 'yes',

			),
			$atts
		);
		$user_id          = get_current_user_id();
		$auction_activity = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'auctions_for_woocommerce_log ORDER BY date DESC limit %d ', $atts['limit'] ) );

		if ( $auction_activity ) {
			echo '<table class="auctions_activity">';
			echo '<tr>';
			echo '<th>' . esc_html__( 'Date', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'Auction', 'auctions-for-woocommerce' ) . '</th>';
			if ( 'yes' === $atts['$show_usernames'] ) {
				echo '<th>' . esc_html__( 'Username', 'auctions-for-woocommerce' ) . '</th>';
			}
			echo '<th>' . esc_html__( 'Bid', 'auctions-for-woocommerce' ) . '</th>';
			echo '<th>' . esc_html__( 'Status', 'auctions-for-woocommerce' ) . '</th>';
			echo '</tr>';

			foreach ( $auction_activity as $value ) {
				if ( get_post_status( $value->auction_id ) === 'publish' ) {
					$class   = '';
					$product = wc_get_product( $value->auction_id );

					if ( $product && $product->is_type( 'auction' ) ) {
						if ( $product->is_closed() ) {
							$class .= 'closed ';
						}

						if ( $product->get_auction_current_bider() === $user_id && ! $product->is_sealed() ) {
							$class .= 'winning ';
						}

						if ( $product->get_auction_current_bider() === $user_id && ! $product->is_reserve_met() ) {
							$class .= 'reserved ';
						}

						if ( strtotime( $product->get_auction_relisted() ) > strtotime( $value->date ) ) {
							$class .= 'relisted ';
						}

						echo '<tr class="' . esc_attr( $class ) . '">';
						echo '<td>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $value->date ) ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $value->date ) ) ) . '</td>';
						echo '<td><a href="' . esc_url( get_permalink( $value->auction_id ) ) . '">' . esc_html( get_the_title( $value->auction_id ) ) . '</a></td>';
						if ( 'yes' === $atts['show_usernames'] ) {
							echo '<td>';
							$userdata = get_userdata( $value->userid );
							if ( $userdata ) {
								echo esc_attr( $userdata->user_nicename );
							} else {
								esc_html_e( 'n/a', 'auctions-for-woocommerce' );
							}
							echo '</td>';
						}
						echo '<td>' . wp_kses_post( wc_price( $value->bid ) ) . '</td>';
						echo '<td>' . wp_kses_post( $product->get_price_html() ) . '</td>';
						echo '</tr>';
					}
				}
			}
			echo '</table>';
		} else {
			echo '<div class="woocommerce"><p class="woocommerce-info">' . esc_html__( 'There is not any bids at the moment!.', 'auctions-for-woocommerce' ) . '</p></div>';
		}
	}

}
 new Auctions_For_Woocommerce_Shortcodes();

