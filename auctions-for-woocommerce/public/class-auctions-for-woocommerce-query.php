<?php
/**
 * Contains the query functions for Auctions for WooCommerce which alter the front-end post queries and loops
 *
 * @version 2.0.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Query Class.
 *
 */
class Auctions_For_Woocommerce_Query {

	/**
	 * Stores coption for out of stock item
	 *
	 * @var string
	 */
	private $hide_out_of_stock_items;

	/**
	 * Constructor for the query class. Hooks in methods.
	 *
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_action( 'woocommerce_product_query', array( $this, 'remove_auctions_from_woocommerce_product_query' ), 2 );
			add_action( 'woocommerce_product_query', array( $this, 'pre_get_posts' ), 99, 2 );
			add_filter( 'pre_get_posts', array( $this, 'auction_arhive_pre_get_posts' ) );
			add_action( 'pre_get_posts', array( $this, 'query_auction_archive' ), 1 );
			$this->hide_out_of_stock_items = get_option( 'woocommerce_hide_out_of_stock_items' );
		}
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 * @return array
	 *
	 */
	public function add_query_vars( $vars ) {
		$qvars[] = 'search_auctions';
		return $vars;
	}

	/**
	 * Modify product query based on settings
	 *
	 * @param object
	 * @return object
	 *
	 */
	public function remove_auctions_from_woocommerce_product_query( $q ) {

		// We only want to affect the main query
		if ( ! $q->is_main_query() ) {
			return;
		}

		if ( apply_filters( 'remove_auctions_from_woocommerce_product_query', false, $q ) === true ) {
			return;
		}

		if ( ! $q->is_post_type_archive( 'product' ) && ! $q->is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$auctions_for_woocommerce_dont_mix_shop = get_option( 'auctions_for_woocommerce_dont_mix_shop' );
		$auctions_for_woocommerce_dont_mix_cat  = get_option( 'auctions_for_woocommerce_dont_mix_cat' );

		if ( 'yes' !== $auctions_for_woocommerce_dont_mix_cat && is_product_category() ) {
			return;
		}

		$auctions_for_woocommerce_dont_mix_tag = get_option( 'auctions_for_woocommerce_dont_mix_tag' );
		if ( 'yes' !== $auctions_for_woocommerce_dont_mix_tag && is_product_tag() ) {
			return;
		}

		$auctions_for_woocommerce_dont_mix_search = get_option( 'auctions_for_woocommerce_dont_mix_search' );

		if ( $q->is_main_query() && $q->is_search() && ! is_admin() ) {

			if ( isset( $q->query['search_auctions'] ) && true === $q->query['search_auctions'] ) {
				$taxquery = $this->add_outofstock_items( $q->get( 'tax_query' ) );
				if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
				}

				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
				);

				$q->set( 'tax_query', $taxquery );
				$q->query['auction_arhive'] = true;

			} elseif ( 'yes' === $auctions_for_woocommerce_dont_mix_search ) {

				$taxquery = $this->add_outofstock_items( $q->get( 'tax_query' ) );
				if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
					'operator' => 'NOT IN',
				);

				$q->set( 'tax_query', $taxquery );
			}

			return;

		}

		if ( 'yes' === $auctions_for_woocommerce_dont_mix_shop && ( ! isset( $q->query_vars['is_auction_archive'] ) || 'true' !== $q->query_vars['is_auction_archive'] ) ) {
			$taxquery = $this->add_outofstock_items( $q->get( 'tax_query' ) );
			if ( ! is_array( $taxquery ) ) {
				$taxquery = array();
			}
			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'auction',
				'operator' => 'NOT IN',
			);
			$q->set( 'tax_query', $taxquery );
		}

	}

	/**
	 * Modify query based on settings
	 *
	 * @param object
	 * @return object
	 *
	 */
	public function pre_get_posts( $q ) {

		$auction_visibility_not_in                 = array();
		$auction_visibility_terms                  = wc_get_auction_visibility_term_ids();
		$auctions_for_woocommerce_finished_enabled = get_option( 'auctions_for_woocommerce_finished_enabled' );
		$auctions_for_woocommerce_future_enabled   = get_option( 'auctions_for_woocommerce_future_enabled' );
		$auctions_for_woocommerce_dont_mix_shop    = get_option( 'auctions_for_woocommerce_dont_mix_shop' );
		$auctions_for_woocommerce_dont_mix_cat     = get_option( 'auctions_for_woocommerce_dont_mix_cat' );
		$auctions_for_woocommerce_dont_mix_tag     = get_option( 'auctions_for_woocommerce_dont_mix_tag' );
		$auctions_for_woocommerce_sealed_on        = get_option( 'auctions_for_woocommerce_sealed_on', 'no' );

		if ( isset( $q->query_vars['is_auction_archive'] ) && 'true' === $q->query_vars['is_auction_archive'] ) {

			$taxquery = $this->add_outofstock_items( $q->get( 'tax_query' ) );
			if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
			}
			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'auction',
			);

			$q->set( 'tax_query', $taxquery );
			add_filter( 'woocommerce_is_filtered', array( $this, 'add_is_filtered' ), 99 ); // hack for displaying auctions when Shop Page Display is set to show categories
		}

		if ( isset( $q->query_vars['is_auction_archive'] ) && 'true' == $q->query_vars['is_auction_archive'] ) {
			$orderby_value = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : apply_filters( 'auctions_for_woocommerce_default_orderby', get_option( 'auctions_for_woocommerce_default_orderby', 'menu_order' ) );
			switch ( $orderby_value ) {
				case 'price':
					$meta_query = array(
						array(
							'key'     => '_regular_price',
							'value'   => 0,
							'type'    => 'numeric',
							'compare' => '>',
						),
					);
					$q->set( 'meta_query', $meta_query );
					break;
			}
		} else {
			$orderby_value = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : false;
		}
		switch ( $orderby_value ) {
			case 'bid_desc':
				$q->set( 'post_type', 'product' );
				$q->set( 'ignore_sticky_posts', 1 );
				if ( ! is_array( $tax_query ) ) {
					$tax_query = array();
				}
				$tax_query[] = array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'auction',
					),
				);

				

				if ( 'yes' === $auctions_for_woocommerce_sealed_on ) {
					$this->remove_sealed_auction( $q, $auction_visibility_terms );
				}
				$meta_query = $q->get( 'meta_query' );
				if ( ! is_array( $meta_query ) ) {
					$meta_query = array();
				}
				$meta_query[] = array(
					array(
						'relation'            => 'OR',
						'auction_current_bid' => array(
							'key'  => '_auction_current_bid',
							'type' => 'DECIMAL(32,4)',
						),
						// 'auction_start_price' => array(
						// 	'key'  => '_auction_start_price',
						// 	'type' => 'DECIMAL(32,4',
						// ),
					),
				);
				$q->set( 'meta_query', $meta_query );
				$q->set(
					'orderby',
					array(
						'auction_start_price' => 'desc',
						'auction_current_bid' => 'desc',
					)
				);

				break;

			case 'bid_asc':
				$q->set( 'post_type', 'product' );
				$q->set( 'ignore_sticky_posts', 1 );
				$q->set(
					'tax_query',
					array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'auction',
						),
					)
				);
				if ( 'yes' === $auctions_for_woocommerce_sealed_on ) {
					$this->remove_sealed_auction( $q, $auction_visibility_terms );
				}
				$meta_query = $q->get( 'meta_query' );
				if ( ! is_array( $meta_query ) ) {
					$meta_query = array();
				}
				$meta_query[] = array(
					array(
						'relation'            => 'OR',
						'auction_current_bid' => array(
							'key'  => '_auction_current_bid',
							'type' => 'DECIMAL(32,4)',
						),
						// 'auction_start_price' => array(
						// 	'key'  => '_auction_start_price',
						// 	'type' => 'DECIMAL(32,4)',
						// ),
					),
				);
				$q->set( 'meta_query', $meta_query );
				$q->set(
					'orderby',
					array(
						'auction_current_bid' => 'asc',
						'auction_start_price' => 'asc',
					)
				);
				break;

			case 'auction_end':
				$q->set( 'post_type', 'product' );
				$q->set( 'ignore_sticky_posts', 1 );
				$q->set(
					'tax_query',
					array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'auction',
						),
					)
				);
				$this->remove_finished_and_future_auction( $q, $auction_visibility_terms );
				$time       = current_time( 'Y-m-d H:i' );
				$meta_query = $q->get( 'meta_query' );
				if ( ! is_array( $meta_query ) ) {
					$meta_query = array();
				}
				$meta_query[] = array(
					'auction_end_date' => array(
						'key'     => '_auction_dates_to',
						'value'   => $time,
						'type'    => 'DATETIME',
						'compare' => '>=',
					),
				);
				$q->set( 'meta_query', $meta_query );
				$q->set( 'orderby', array( 'auction_end_date' => 'Asc' ) );
				break;

			case 'auction_started':
				$q->set( 'post_type', 'product' );
				$q->set( 'ignore_sticky_posts', 1 );
				$q->set(
					'tax_query',
					array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'auction',
						),
					)
				);
				$this->remove_finished_and_future_auction( $q, $auction_visibility_terms );
				$time       = current_time( 'Y-m-d H:i' );
				$meta_query = $q->get( 'meta_query' );
				if ( ! is_array( $meta_query ) ) {
					$meta_query = array();
				}
				$meta_query[] = array(
					'auction_start_date' => array(
						'key'     => '_auction_dates_from',
						'value'   => $time,
						'type'    => 'DATETIME',
						'compare' => '<=',
					),

				);
				$q->set( 'meta_query', $meta_query );
				$q->set( 'orderby', array( 'auction_start_date' => 'desc' ) );

				break;

			case 'auction_activity':
				$q->set( 'post_type', 'product' );
				$q->set( 'ignore_sticky_posts', 1 );
				$q->set(
					'tax_query',
					array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'auction',
						),
					)
				);
				$meta_query = array(
					array(
						'relation'         => 'OR',
						'auction_activity' =>
							array(
								'key'  => '_auction_bid_count',
								'type' => 'numeric',
							),
					),
				);
				$q->set( 'meta_query', $meta_query );
				$q->set( 'orderby', array( 'auction_activity' => 'desc' ) );

				break;
			case 'id':
				$q->set( 'orderby', 'ID' );
				break;
			case 'menu_order':
				$q->set( 'orderby', 'menu_order title' );
				break;
			case 'rand':
				$q->set( 'orderby', 'rand' ); // @codingStandardsIgnoreLine
				break;
			case 'date':
				$q->set( 'orderby', 'date ID' );
				$q->set( 'order', 'DESC' );
				break;
		}

		if (
			(
				( 'yes' !== $auctions_for_woocommerce_future_enabled && ( ! isset( $q->query['show_future_auctions'] ) || ! $q->query['show_future_auctions'] ) )
				|| ( isset( $q->query['show_future_auctions'] ) && false === $q->query['show_future_auctions'] )

			)

		) {

			$auction_visibility_not_in[] = $auction_visibility_terms['future'];
		}

		if (

			( 'yes' !== $auctions_for_woocommerce_finished_enabled && ( ! isset( $q->query['show_past_auctions'] ) || ! $q->query['show_past_auctions'] )
				|| ( isset( $q->query['show_past_auctions'] ) && false === $q->query['show_past_auctions'] )
			)
		) {

			$auction_visibility_not_in[] = $auction_visibility_terms['finished'];
			$auction_visibility_not_in[] = $auction_visibility_terms['buy-now'];
			$auction_visibility_not_in[] = $auction_visibility_terms['sold'];

		}

		if ( ! empty( $auction_visibility_not_in ) ) {
			$taxquery = $this->add_outofstock_items( $q->get( 'tax_query' ) );
			if ( ! is_array( $taxquery ) ) {
				$taxquery = array(
					'relation' => 'AND',
				);
			}
			$taxquery[] = array(
				'taxonomy' => 'auction_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $auction_visibility_not_in,
				'operator' => 'NOT IN',
			);
			$q->set( 'tax_query', $taxquery );
		}

		if ( 'yes' !== $auctions_for_woocommerce_dont_mix_cat && is_product_category() ) {
			return $q;
		}

		if ( 'yes' !== $auctions_for_woocommerce_dont_mix_tag && is_product_tag() ) {
			return $q;
		}

		if ( ! isset( $q->query_vars['auction_arhive'] ) && ! $q->is_main_query() ) {

			if ( 'yes' === $auctions_for_woocommerce_dont_mix_shop ) {

				$taxquery = $this->add_outofstock_items( $q->get( 'tax_query' ) );
				if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'auction',
					'operator' => 'NOT IN',
				);

				$q->set( 'tax_query', $taxquery );
				return $q;
			}

			return $q;
		}

	}
	/**
	 * Pre_get_post for auction product archive
	 *
	 * @param object
	 * @return void
	 *
	 */
	public function auction_arhive_pre_get_posts( $q ) {

		if ( isset( $q->query['auction_arhive'] ) || ( ! isset( $q->query['auction_arhive'] ) && ( isset( $q->query['post_type'] ) && 'product' === $q->query['post_type'] && ! $q->is_main_query() ) ) ) {
			$this->pre_get_posts( $q );
		}
		return $q;
	}

	/**
	 * Query for auction product archive
	 *
	 * @param object
	 * @return void
	 *
	 */
	public function query_auction_archive( $q ) {

		if ( ! $q->is_main_query() ) {
			return;
		}

		$auction_base_page_id = wc_get_page_id( 'auction' );

		if ( ( isset( $q->queried_object->ID ) && $auction_base_page_id === $q->queried_object->ID ) || get_query_var( 'page_id' ) === $auction_base_page_id ) {

			$q->set( 'post_type', 'product' );
			$q->set( 'page', '' );
			$q->set( 'page_id', '' );
			$q->set( 'pagename', '' );
			$q->set( 'auction_arhive', 'true' );
			$q->set( 'is_auction_archive', 'true' );

			// Fix conditional Functions
			$q->is_archive           = true;
			$q->is_post_type_archive = true;
			$q->is_singular          = false;
			$q->is_page              = false;
		}

		// When orderby is set, WordPress shows posts. Get around that here.
		if ( ( $q->is_home() && 'page' === get_option( 'show_on_front' ) ) && ( absint( get_option( 'page_on_front' ) ) === absint( wc_get_page_id( 'auction' ) ) ) ) {
			$_query = wp_parse_args( $q->query );
			if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
				$q->is_page = true;
				$q->is_home = false;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
				$q->set( 'post_type', 'product' );
			}
		}

		if ( $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === wc_get_page_id( 'auction' ) ) {

			$q->set( 'post_type', 'product' );

			// This is a front-page shop
			$q->set( 'post_type', 'product' );
			$q->set( 'page_id', '' );
			$q->set( 'auction_arhive', 'true' );
			$q->set( 'is_auction_archive', 'true' );

			if ( isset( $q->query['paged'] ) ) {
				$q->set( 'paged', $q->query['paged'] );
			}

			// Define a variable so we know this is the front page shop later on
			define( 'AUCTIONS_IS_ON_FRONT', true );

			// Get the actual WP page to avoid errors and let us use is_front_page()
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$auction_page = get_post( wc_get_page_id( 'auction' ) );

			$wp_post_types['product']->ID         = $auction_page->ID;
			$wp_post_types['product']->post_title = $auction_page->post_title;
			$wp_post_types['product']->post_name  = $auction_page->post_name;
			$wp_post_types['product']->post_type  = $auction_page->post_type;
			$wp_post_types['product']->ancestors  = get_ancestors( $auction_page->ID, $auction_page->post_type );

			// Fix conditional Functions like is_front_page
			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;
			$q->is_page              = true;

			// Remove post type archive name from front page title tag
			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

			// Fix WP SEO
			if ( class_exists( 'WPSEO_Meta' ) ) {
				add_filter( 'wpseo_metadesc', array( $this, 'wpseo_metadesc' ) );
				add_filter( 'wpseo_metakey', array( $this, 'wpseo_metakey' ) );
				add_filter( 'wpseo_title', array( $this, 'wpseo_title' ) );
			}
		}

	}

	/**
	 * WP SEO meta description.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @return string
	 *
	 */
	public function wpseo_metadesc() {
		return WPSEO_Meta::get_value( 'metadesc', wc_get_page_id( 'auction' ) );
	}

	/**
	 * WP SEO meta key.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @return string
	 *
	 */
	public function wpseo_metakey() {
		return WPSEO_Meta::get_value( 'metakey', wc_get_page_id( 'auction' ) );
	}

	/**
	 * WP SEO title.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @return string
	 *
	 */
	public function wpseo_title() {
		return WPSEO_Meta::get_value( 'title', wc_get_page_id( 'auction' ) );
	}

	/**
	 * Set is filtered is true to skip displaying categories only on page
	 *
	 * @return bolean
	 *
	 */
	public function add_is_filtered( $id ) {

		return true;

	}

	/**
	 * Appends tax queries to an array.
	 *
	 * @param  bool  $main_query If is main query.
	 * @param  array auction visibility terms
	 * @return void
	 *
	 */
	public function remove_finished_and_future_auction( $q, $auction_visibility_terms ) {

		$product_visibility_not_in = array();

		$tax_query = $q->get( 'tax_query' );
		if ( ! is_array( $tax_query ) ) {
			$tax_query = array(
				'relation' => 'AND',
			);
		}

		$auction_visibility_terms    = wc_get_auction_visibility_term_ids();
		$product_visibility_not_in[] = $auction_visibility_terms['finished'];
		$product_visibility_not_in[] = $auction_visibility_terms['sold'];
		$product_visibility_not_in[] = $auction_visibility_terms['buy-now'];
		$product_visibility_not_in[] = $auction_visibility_terms['future'];

		if ( ! empty( $product_visibility_not_in ) ) {
			$tax_query[] = array(
				'taxonomy' => 'auction_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_not_in,
				'operator' => 'NOT IN',
			);
		}

		$q->set( 'tax_query', $tax_query );

	}

	/**
	 * Appends tax queries to an array.
	 *
	 * @param  bool  $main_query If is main query.
	 * @param  array auction visibility terms
	 * @return void
	 */
	public function remove_sealed_auction( $q, $auction_visibility_terms ) {
		$product_visibility_not_in = array();

		$tax_query = $q->get( 'tax_query' );
		if ( ! is_array( $tax_query ) ) {
			$tax_query = array(
				'relation' => 'AND',
			);
		}
		$product_visibility_not_in[] = $auction_visibility_terms['sealed'];
		if ( ! empty( $product_visibility_not_in ) ) {
			$tax_query[] = array(
				'taxonomy' => 'auction_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_not_in,
				'operator' => 'NOT IN',
			);
		}

		$q->set( 'tax_query', $tax_query );

	}

	public function add_outofstock_items( $taxquery ) {
		
		if ( 'yes' !== $this->hide_out_of_stock_items ) {
			return $taxquery;
		}

		if ( is_array( $taxquery ) ) {

			$product_visibility_terms = wc_get_product_visibility_term_ids();

			foreach ( $taxquery as $key => $value ) {

				if ( isset( $value['taxonomy'] ) && 'product_visibility' === $value['taxonomy'] ) {

					if ( is_array( $value['terms'] ) ) {

						$key2 = array_search( intval( $product_visibility_terms['outofstock'] ), $value['terms'], true );

						if ( false !== $key2 ) {
							unset( $taxquery[ $key ]['terms'][ $key2 ] );
							break;
						}
					}				
				}
			}
		}
		return $taxquery;
	}
}

new Auctions_For_Woocommerce_Query();
