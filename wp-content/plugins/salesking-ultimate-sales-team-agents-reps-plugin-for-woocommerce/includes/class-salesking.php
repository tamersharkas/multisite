<?php

class Salesking {

	function __construct() {

		// Handle Ajax Requests
		if ( wp_doing_ajax() ){

			// Mark announcement read
			add_action( 'wp_ajax_saleskingmarkread', array($this, 'saleskingmarkread') );
    		add_action( 'wp_ajax_nopriv_saleskingmarkread', array($this, 'saleskingmarkread') );

			// Mark all announcement read
			add_action( 'wp_ajax_saleskingmarkallread', array($this, 'saleskingmarkallread') );
    		add_action( 'wp_ajax_nopriv_saleskingmarkallread', array($this, 'saleskingmarkallread') );

    		// Mark message read
			add_action( 'wp_ajax_saleskingmarkreadmessage', array($this, 'saleskingmarkreadmessage') );
    		add_action( 'wp_ajax_nopriv_saleskingmarkreadmessage', array($this, 'saleskingmarkreadmessage') );
    		// Mark message closed
			add_action( 'wp_ajax_saleskingmarkclosedmessage', array($this, 'saleskingmarkclosedmessage') );
    		add_action( 'wp_ajax_nopriv_saleskingmarkclosedmessage', array($this, 'saleskingmarkclosedmessage') );
    		
    		// Reply message
    		add_action( 'wp_ajax_saleskingreplymessage', array($this, 'saleskingreplymessage') );
    		add_action( 'wp_ajax_nopriv_saleskingreplymessage', array($this, 'saleskingreplymessage') );
    		// Compose message
    		add_action( 'wp_ajax_saleskingcomposemessage', array($this, 'saleskingcomposemessage') );
    		add_action( 'wp_ajax_nopriv_saleskingcomposemessage', array($this, 'saleskingcomposemessage') );

    		// Save coupon
    		add_action( 'wp_ajax_saleskingsavecoupon', array($this, 'saleskingsavecoupon') );
    		add_action( 'wp_ajax_nopriv_saleskingsavecoupon', array($this, 'saleskingsavecoupon') );
    		// Delete coupon
    		add_action( 'wp_ajax_saleskingdeletecoupon', array($this, 'saleskingdeletecoupon') );
    		add_action( 'wp_ajax_nopriv_saleskingdeletecoupon', array($this, 'saleskingdeletecoupon') );

    		// Create / Save Cart
    		add_action( 'wp_ajax_saleskingcreatecart', array($this, 'saleskingcreatecart') );
    		add_action( 'wp_ajax_nopriv_saleskingcreatecart', array($this, 'saleskingcreatecart') );
    		// Delete Cart
    		add_action( 'wp_ajax_saleskingdeletecart', array($this, 'saleskingdeletecart') );
    		add_action( 'wp_ajax_nopriv_saleskingdeletecart', array($this, 'saleskingdeletecart') );

    		// Add Customer
    		add_action( 'wp_ajax_saleskingaddcustomer', array($this, 'saleskingaddcustomer') );
    		add_action( 'wp_ajax_nopriv_saleskingaddcustomer', array($this, 'saleskingaddcustomer') );

    		// Add Subagent
    		add_action( 'wp_ajax_saleskingaddsubagent', array($this, 'saleskingaddsubagent') );
    		add_action( 'wp_ajax_nopriv_saleskingaddsubagent', array($this, 'saleskingaddsubagent') );

    		// Shop As customer
    		add_action( 'wp_ajax_saleskingshopascustomer', array($this, 'saleskingshopascustomer') );
    		add_action( 'wp_ajax_nopriv_saleskingshopascustomer', array($this, 'saleskingshopascustomer') );

    		// Switch back to agent
    		add_action( 'wp_ajax_saleskingswitchtoagent', array($this, 'saleskingswitchtoagent') );
    		add_action( 'wp_ajax_nopriv_saleskingswitchtoagent', array($this, 'saleskingswitchtoagent') );

    		// Save Payout Info
    		add_action( 'wp_ajax_saleskingsaveinfo', array($this, 'saleskingsaveinfo') );
    		add_action( 'wp_ajax_nopriv_saleskingsaveinfo', array($this, 'saleskingsaveinfo') );

    		// Save Payment
    		add_action( 'wp_ajax_saleskingsavepayment', array($this, 'saleskingsavepayment') );
    		add_action( 'wp_ajax_nopriv_saleskingsavepayment', array($this, 'saleskingsavepayment') );

    		// Tools
    		add_action( 'wp_ajax_nopriv_saleskingbulksetsubaccounts', array($this, 'saleskingbulksetsubaccounts') );
    		add_action( 'wp_ajax_saleskingbulksetsubaccounts', array($this, 'saleskingbulksetsubaccounts') );
    		add_action( 'wp_ajax_nopriv_saleskingbulksetsubaccountsregular', array($this, 'saleskingbulksetsubaccountsregular') );
    		add_action( 'wp_ajax_saleskingbulksetsubaccountsregular', array($this, 'saleskingbulksetsubaccountsregular') );

    		add_action( 'wp_ajax_nopriv_salesking_setagentall', array($this, 'salesking_setagentall') );
    		add_action( 'wp_ajax_salesking_setagentall', array($this, 'salesking_setagentall') );


    		add_action( 'wp_ajax_saleskingactivatelicense', array($this, 'saleskingactivatelicense') );
    		add_action( 'wp_ajax_nopriv_saleskingactivatelicense', array($this, 'saleskingactivatelicense') );

    		// vendor balance history
    		add_action( 'wp_ajax_saleskingsaveadjustment', array($this, 'saleskingsaveadjustment') );
    		add_action( 'wp_ajax_nopriv_saleskingsaveadjustment', array($this, 'saleskingsaveadjustment') );

    		// Download vendor balance history
			add_action( 'wp_ajax_salesking_download_vendor_balance_history', array($this, 'salesking_download_vendor_balance_history') );
    		add_action( 'wp_ajax_nopriv_salesking_download_vendor_balance_history', array($this, 'salesking_download_vendor_balance_history') );

    		// Save User Profile Settings
    		add_action( 'wp_ajax_salesking_save_profile_settings', array($this, 'salesking_save_profile_settings') );
    		add_action( 'wp_ajax_nopriv_salesking_save_profile_settings', array($this, 'salesking_save_profile_settings') );

    		// Save User Profile Info
    		add_action( 'wp_ajax_salesking_save_profile_info', array($this, 'salesking_save_profile_info') );
    		add_action( 'wp_ajax_nopriv_salesking_save_profile_info', array($this, 'salesking_save_profile_info') );

    		add_action( 'wp_ajax_salesking_customers_table_ajax', array($this, 'salesking_customers_table_ajax') );
    		add_action( 'wp_ajax_nopriv_salesking_customers_table_ajax', array($this, 'salesking_customers_table_ajax') );	

    		// Load Orders Table AJAX Vendor Dashboard
    		add_action( 'wp_ajax_salesking_orders_table_ajax', array($this, 'salesking_orders_table_ajax') );
    		add_action( 'wp_ajax_nopriv_salesking_orders_table_ajax', array($this, 'salesking_orders_table_ajax') );

    		// Dismiss "activate woocommerce" admin notice permanently
    		add_action( 'wp_ajax_salesking_dismiss_activate_woocommerce_admin_notice', array($this, 'salesking_dismiss_activate_woocommerce_admin_notice') );
    		add_action( 'wp_ajax_salesking_dismiss_groups_howto_admin_notice', array($this, 'salesking_dismiss_groups_howto_admin_notice') );
    		add_action( 'wp_ajax_salesking_dismiss_groupsrules_howto_admin_notice', array($this, 'salesking_dismiss_groupsrules_howto_admin_notice') );

    		add_action( 'wp_ajax_salesking_dismiss_announcements_howto_admin_notice', array($this, 'salesking_dismiss_announcements_howto_admin_notice') );
    		add_action( 'wp_ajax_salesking_dismiss_messages_howto_admin_notice', array($this, 'salesking_dismiss_messages_howto_admin_notice') );
    		add_action( 'wp_ajax_salesking_dismiss_payouts_howto_admin_notice', array($this, 'salesking_dismiss_payouts_howto_admin_notice') );
    		add_action( 'wp_ajax_salesking_dismiss_earnings_howto_admin_notice', array($this, 'salesking_dismiss_earnings_howto_admin_notice') );
    		add_action( 'wp_ajax_salesking_dismiss_rules_howto_admin_notice', array($this, 'salesking_dismiss_rules_howto_admin_notice') );

    		// Reports get data
    		add_action( 'wp_ajax_salesking_reports_get_data', array($this, 'salesking_reports_get_data') );
    		add_action( 'wp_ajax_nopriv_salesking_reports_get_data', array($this, 'salesking_reports_get_data') );
    		
		}

		require_once SALESKING_DIR . '/public/class-salesking-public.php';

		// Run Admin/Public code 
		if ( is_admin() ) { 
			require_once SALESKING_DIR . '/admin/class-salesking-admin.php';
			$admin = new Salesking_Admin();
		} else if ( !$this->salesking_is_login_page() ) {
			global $salesking_public;
			$salesking_public = new Salesking_Public();
		}

		// Prevent the agent from being sent to wp-admin on wrong login
		add_action('login_redirect', array($this, 'prevent_wp_login'), 10, 3);
		// Redirect to sales agent page, if sales agent
		// also remove cookie
		add_action('wp_logout',array($this, 'auto_redirect_after_logout'), 10, 1);

		// Add email classes
		add_filter( 'woocommerce_email_classes', array($this, 'salesking_add_email_classes'));
		// Add extra email actions (account approved finish)
		add_filter( 'woocommerce_email_actions', array($this, 'salesking_add_email_actions'));

		// Add invoice gateway
		add_filter( 'woocommerce_payment_gateways',  array( $this, 'salesking_pending_gateway' ) );

		// Allow resending the new order email (for agents assigned to orders)
		add_filter('woocommerce_new_order_email_allows_resend', '__return_true' );
		// Send new order to agent
		add_filter( 'woocommerce_email_recipient_new_order', array($this, 'send_email_to_agent'), 10, 2 );

		add_action('plugins_loaded', function(){

			// Compatibility for adding customers with Dokan
			add_filter('dokan_register_nonce_check', function($val){
				return false;
			}, 100, 1);

			
			// only if user is an agent and not admin
			$agentgroup = get_user_meta( get_current_user_id(), 'salesking_group', true );
			if (!empty($agentgroup) && $agentgroup !== 'none' && !current_user_can( 'manage_woocommerce' )){
				if (intval(get_option( 'salesking_agents_can_manage_orders_setting', 1 )) === 1){
					if (apply_filters('salesking_allow_agent_manage_orders', true)){
						// Agents capability to manage orders
						add_filter( 'user_has_cap', array($this, 'agents_caps_manage_orders'), 1000, 3 );

						// Agents restrict backend interface
						add_action( 'wp_before_admin_bar_render', array($this, 'mytheme_admin_bar_render' ));
						if ( ! wp_doing_ajax() ){
							// add_action( 'admin_init', array($this, 'stop_access_profile' ));
						}
						add_action( 'admin_menu', array($this, 'remove_menus'));
						add_action( 'admin_head', array($this, 'restrict_screens'));
					}
				}
		    } 
		    
		});

	    // When order status changes, change earning status
	    add_action('woocommerce_order_status_changed', array($this,'change_earning_status'), 10, 3);


	    // Add the duplicate link to action list for post_row_actions
	    // for "post" and custom post types
	    add_filter( 'post_row_actions', array($this, 'rd_duplicate_post_link'), 10, 2 );
	    // for "page" post type
	    add_filter( 'page_row_actions', array($this,'rd_duplicate_post_link'), 10, 2 );

	    add_action( 'admin_action_salesking_duplicate_posts_draft', array($this, 'salesking_duplicate_posts_draft') );

	    add_action( 'admin_notices', array($this, 'salesking_duplication_admin_notice') );

	    // salesking templates overwrite
	    add_filter('salesking_dashboard_template', array($this,'template_file_overwrite_theme_dashboard'), 10, 1);

	    // calculate commissions automatically on backend manual orders
	    add_action( 'woocommerce_process_shop_order_meta', array($this,'salesking_manual_order_commission_calc'), 10, 2);


	}

	function salesking_manual_order_commission_calc($order_id, $order = array()){
		$public = new Salesking_Public();
		$public->salesking_register_order_calculate_earnings($order_id);
	}

	function template_file_overwrite_theme_dashboard($templatefile){

		$theme_directory = get_stylesheet_directory();

		if ( file_exists( $theme_directory . '/salesking/' . $templatefile ) ) {
			return $theme_directory . '/salesking/' . $templatefile ;
		} else {
			// check salesking pro file
			$templatefilearray = explode('/', $templatefile);

			// we are in a salesking pro file
			if ( file_exists( $theme_directory . '/salesking/' . end($templatefilearray) ) ) {
				return $theme_directory . '/salesking/' . end($templatefilearray) ;
			}
		}

		return $templatefile;

	}

	function rd_duplicate_post_link( $actions, $post ) {

		if( ! current_user_can( 'edit_posts' ) ) {
			return $actions;
		}

		if (get_post_type($post->ID) !== 'salesking_rule'){
			return $actions;
		}

		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'salesking_duplicate_posts_draft',
					'post' => $post->ID,
				),
				'admin.php'
			),
			basename(__FILE__),
			'duplicate_nonce'
		);

		$actions[ 'duplicate' ] = '<a href="' . $url . '" title="Duplicate this item" rel="permalink">'.esc_html__('Duplicate','salesking').'</a>';

		return $actions;
	}

	function salesking_duplicate_posts_draft(){

		// check if post ID has been provided and action
		if ( empty( $_GET[ 'post' ] ) ) {
			wp_die( 'No post to duplicate has been provided!' );
		}

		// Nonce verification
		if ( ! isset( $_GET[ 'duplicate_nonce' ] ) || ! wp_verify_nonce( $_GET[ 'duplicate_nonce' ], basename( __FILE__ ) ) ) {
			return;
		}

		// Get the original post id
		$post_id = absint( $_GET[ 'post' ] );

		// And all the original post data then
		$post = get_post( $post_id );

		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		if ( $post ) {

			// new post data array
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'publish',
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);

			// insert the post by wp_insert_post() function
			$new_post_id = wp_insert_post( $args );

			$taxonomies = get_object_taxonomies( get_post_type( $post ) ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			if( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
				}
			}

			// duplicate all post meta
			$post_meta = get_post_meta( $post_id );
			if( $post_meta ) {

				foreach ( $post_meta as $meta_key => $meta_values ) {

					if( '_wp_old_slug' == $meta_key ) { // do nothing for this meta key
						continue;
					}

					foreach ( $meta_values as $meta_value ) {
						add_post_meta( $new_post_id, $meta_key, $meta_value );
					}
				}
			}

			wp_safe_redirect(
				add_query_arg(
					array(
						'post_type' => ( 'post' !== get_post_type( $post ) ? get_post_type( $post ) : false ),
						'saved' => 'post_duplication_created' // just a custom slug here
					),
					admin_url( 'edit.php' )
				)
			);
			exit;

		} else {
			wp_die( 'Post creation failed, could not find original post.' );
		}

	}

	/*
	 * In case we decided to add admin notices
	 */

	function salesking_duplication_admin_notice() {

		// Get the current screen
		$screen = get_current_screen();

		if ( 'edit' !== $screen->base ) {
			return;
		}

	    //Checks if settings updated
	    if ( isset( $_GET[ 'saved' ] ) && 'post_duplication_created' == $_GET[ 'saved' ] ) {

			 echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('Duplicate has been created','salesking').'</p></div>';
			 
	    }
	}


	function saleskingbulksetsubaccounts(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$option_first = trim(sanitize_text_field($_POST['option_first']));
		$option_second = trim(sanitize_text_field($_POST['option_second']));

		$subaccount_ids = explode(',',$option_first);
		$parent_id = trim($option_second);

		foreach ($subaccount_ids as $subaccount_id){
			$subaccount_id_trimmed = trim($subaccount_id);

			// set assigned agent
			update_user_meta($subaccount_id_trimmed, 'salesking_parent_agent', $parent_id);
			$parentaggroup = get_user_meta( $parent_id, 'salesking_group', true );

			if (apply_filters('salesking_set_subagents_tool_change_group', true)){
				update_user_meta($subaccount_id_trimmed, 'salesking_group', $parentaggroup);
			}
			update_user_meta( $subaccount_id_trimmed, 'salesking_user_choice', 'agent');	
			update_user_meta( $subaccount_id_trimmed, 'salesking_assigned_agent', 'none');

		}

		echo 'success';
		exit();

	}

	function salesking_setagentall(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$option_first = trim(sanitize_text_field($_POST['option_first']));

		// get users
		$users = get_users(array(
			'role'  => 'customer',
			'fields'=> 'ids',
		));
		
		if (!empty($users)) {
		    // loop trough each author
		    foreach ($users as $user){
		       // move all users to the group
		       update_user_meta($user,'salesking_assigned_agent', $option_first);

		       $choice = get_user_meta($user,'salesking_user_choice', true);
		       if ($choice !== 'agent'){
		       		update_user_meta($user,'salesking_user_choice', 'customer');
		       }
		    }
		}

		// delete all b2bking transients
		
		b2bking()->clear_caches_transients();

		echo 'success';
		exit();
	}

	function saleskingbulksetsubaccountsregular(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$option_first = trim(sanitize_text_field($_POST['option_first']));

		$subaccount_ids = explode(',',$option_first);

		foreach ($subaccount_ids as $subaccount_id){
			$subaccount_id_trimmed = trim($subaccount_id);
			update_user_meta($subaccount_id_trimmed,'salesking_parent_agent', '');


		}

		echo 'success';
		exit();

	}

	function change_earning_status($order_id, $status_from, $status_to){
		// get earning id, if any
		$earning_id = get_post_meta($order_id, 'salesking_earning_id', true);
		if (!empty($earning_id)){
			update_post_meta($earning_id,'order_status', $status_to);
		}

		$agent_id = get_post_meta($earning_id,'agent_id', true);
		$outstanding_balance = get_user_meta($agent_id,'salesking_outstanding_earnings', true);
		if (empty($outstanding_balance)){
			$outstanding_balance = 0;
		}
		$total_earnings_on_order = get_post_meta($earning_id, 'salesking_commission_total', true);
		$agents_of_earning = get_post_meta($earning_id, 'agents_of_earning', true);
		if (empty($agents_of_earning)){
			$agents_of_earning = array();
		}
		// add or remove balance for payouts

		if (in_array($status_to,apply_filters('salesking_earning_completed_statuses', array('completed'))) && !in_array($status_from,apply_filters('salesking_earning_completed_statuses', array('completed')))){


			if (apply_filters('salesking_balance_calculation_method', 'standard') === 'standard'){
				// add balance for payout
				$new_balance = $outstanding_balance + $total_earnings_on_order;

				// user balance history start
				$old_balance = get_user_meta($agent_id,'salesking_outstanding_earnings', true);
				$amount = '+ '.$total_earnings_on_order;
				$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
				$note = 'Order #'.$order_id.' status changed to completed';
				$user_balance_history = sanitize_text_field(get_user_meta($agent_id,'salesking_user_balance_history', true));
				$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
				update_user_meta($agent_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
				// user balance history end

				update_user_meta($agent_id, 'salesking_outstanding_earnings', $new_balance);
			} else if (apply_filters('salesking_balance_calculation_method', 'standard') === 'recalculate'){
				require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
				$helper = new Salesking_Helper();
				$helper->recalculate_agent_earnings($agent_id);
			}
			


			// handle parent agent earnings
			foreach ($agents_of_earning as $ag_id){
				$outstanding_balance = get_user_meta($ag_id,'salesking_outstanding_earnings', true);
				if (empty($outstanding_balance)){
					$outstanding_balance = 0;
				}
				$ag_earnings_on_order = get_post_meta($earning_id, 'parent_agent_id_'.$ag_id.'_earnings', true);

				if (apply_filters('salesking_balance_calculation_method', 'standard') === 'standard'){
					$new_balance = $outstanding_balance + $ag_earnings_on_order;

					// user balance history start
					$old_balance = get_user_meta($ag_id,'salesking_outstanding_earnings', true);
					$amount = '+ '.$ag_earnings_on_order;
					$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
					$note = 'Order #'.$order_id.' status changed to completed';
					$user_balance_history = sanitize_text_field(get_user_meta($ag_id,'salesking_user_balance_history', true));
					$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
					update_user_meta($ag_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
					// user balance history end

					update_user_meta($ag_id, 'salesking_outstanding_earnings', $new_balance);
				} else if (apply_filters('salesking_balance_calculation_method', 'standard') === 'recalculate'){
					require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
					$helper = new Salesking_Helper();
					$helper->recalculate_agent_earnings($ag_id);
				}
			}

		}

		if (! in_array($status_to,apply_filters('salesking_earning_completed_statuses', array('completed'))) && in_array($status_from,apply_filters('salesking_earning_completed_statuses', array('completed')))){

			if (apply_filters('salesking_balance_calculation_method', 'standard') === 'standard'){
				// remove balance for payout
				$new_balance = $outstanding_balance - $total_earnings_on_order;

				// user balance history start
				$old_balance = get_user_meta($agent_id,'salesking_outstanding_earnings', true);
				$amount = '- '.$total_earnings_on_order;
				$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
				$note = 'Order #'.$order_id.' status changed away from completed';
				$user_balance_history = sanitize_text_field(get_user_meta($agent_id,'salesking_user_balance_history', true));
				$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
				update_user_meta($agent_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
				// user balance history end

				update_user_meta($agent_id, 'salesking_outstanding_earnings', $new_balance);
			} else if (apply_filters('salesking_balance_calculation_method', 'standard') === 'recalculate'){
				require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
				$helper = new Salesking_Helper();
				$helper->recalculate_agent_earnings($agent_id);
			}

			// handle parent agent earnings
			foreach ($agents_of_earning as $ag_id){
				$outstanding_balance = get_user_meta($ag_id,'salesking_outstanding_earnings', true);
				if (empty($outstanding_balance)){
					$outstanding_balance = 0;
				}
				$ag_earnings_on_order = get_post_meta($earning_id, 'parent_agent_id_'.$ag_id.'_earnings', true);

				if (apply_filters('salesking_balance_calculation_method', 'standard') === 'standard'){

					$new_balance = $outstanding_balance - $ag_earnings_on_order;

					// user balance history start
					$old_balance = get_user_meta($ag_id,'salesking_outstanding_earnings', true);
					$amount = '- '.$ag_earnings_on_order;
					$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
					$note = 'Order #'.$order_id.' status changed away from completed';
					$user_balance_history = sanitize_text_field(get_user_meta($ag_id,'salesking_user_balance_history', true));
					$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
					update_user_meta($ag_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
					// user balance history end

					update_user_meta($ag_id, 'salesking_outstanding_earnings', $new_balance);
				} else if (apply_filters('salesking_balance_calculation_method', 'standard') === 'recalculate'){
					require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
					$helper = new Salesking_Helper();
					$helper->recalculate_agent_earnings($ag_id);
				}
			}
		}
	}

	function salesking_customers_table_ajax(){
			// Check security nonce. 
			if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
			  	wp_send_json_error( 'Invalid security token sent.' );
			    wp_die();
			}

			$user_id = get_current_user_id();

			$start = sanitize_text_field($_POST['start']);
			$length = sanitize_text_field($_POST['length']);
			$search = sanitize_text_field($_POST['search']['value']);
			$pagenr = ($start/$length)+1;


			$args = array( 
			     'role' => 'customer',
		         'fields' => 'ids',
		         'search' => '*'.esc_attr( $search ).'*',
			);
			$total_items = get_users( $args );
			$itemnr = count($total_items);
			

			$data = array(
				'length'=> $length,
				'data' => array(),
				'recordsTotal' => $itemnr,
				'recordsFiltered' => $itemnr
			);
			

			// get all customers of the user

			// if all agents can shop for all customers
			if(intval(get_option( 'salesking_all_agents_shop_all_customers_setting', 0 ))=== 1){
			    // first get all customers that have this assigned agent individually
			    $user_ids_assigned = get_users(array(
			        'role' => 'customer',
			        'fields' => 'ids',
			        'search'         => '*'.esc_attr( $search ).'*',
			        'paged'   => floatval($pagenr),
			        'number' => $length,
	        	    'orderby' => 'user_registered',
	                'order' => 'DESC',
			    ));
			    $customers = $user_ids_assigned;

			} else {
			    // first get all customers that have this assigned agent individually
			    $user_ids_assigned = get_users(array(
	                'meta_key'     => 'salesking_assigned_agent',
	                'meta_value'   => $user_id,
	                'meta_compare' => '=',
	                'fields' => 'ids',
    		        'search'         => '*'.esc_attr( $search ).'*',
    		        'paged'   => floatval($pagenr),
    		        'number' => $length,
            	    'orderby' => 'user_registered',
                    'order' => 'DESC',
	            ));


			    if (defined('B2BKING_DIR')){
			        // now get all b2bking groups that have this assigned agent
			        $groups_with_agent = get_posts(array( 'post_type' => 'b2bking_group',
			                  'post_status'=>'publish',
			                  'numberposts' => -1,
			                  'fields' => 'ids',
			                  'meta_query'=> array(
			                        'relation' => 'OR',
			                        array(
			                            'key' => 'salesking_assigned_agent',
			                            'value' => $user_id,
			                            'compare' => '=',
			                        ),
			                    )));

			    } else {
			        $groups_with_agent = array();
			    }

			    if (!empty($groups_with_agent)){
			        // get all customers in the above groups with agent
			        $user_ids_in_groups_with_agent = get_users(array(
	                    'meta_key'     => 'b2bking_customergroup',
	                    'meta_value'   => $groups_with_agent,
	                    'meta_compare' => 'IN',
	                    'fields' => 'ids',
        		        'search'         => '*'.esc_attr( $search ).'*',
        		        'paged'   => floatval($pagenr),
        		        'number' => $length,
                	    'orderby' => 'user_registered',
                        'order' => 'DESC',
	                ));

			        // for all customers with this agent as group, make sure they don't have a different agent individually
			        foreach ($user_ids_in_groups_with_agent as $array_key => $user_id){
			            // check that a different agent is not assigned
			            $assigned_agent = get_user_meta($user_id,'salesking_assigned_agent', true);

			            if (!empty($assigned_agent) && $assigned_agent !== $user_id && $assigned_agent !== 'none'){
			                unset($user_ids_in_groups_with_agent[$array_key]);
			            }
			        }


			        $customers = array_merge($user_ids_assigned, $user_ids_in_groups_with_agent);
			    } else {
			        $customers = $user_ids_assigned;
			    }
			}

			
			foreach ($customers as $customer_id){
			    $customerobj = new WC_Customer($customer_id);
			    $user_info = get_userdata($customer_id);
			    $company_name = get_user_meta($customer_id,'billing_company', true);
			    if (empty($company_name)){
			        $company_name = '';
			    }

			    if (empty($user_info->first_name) && empty($user_info->last_name)){
			        $name = $user_info->user_login;
			    } else {
			        $name = $user_info->first_name.' '.$user_info->last_name;
			    }
			    $name = apply_filters('salesking_customers_page_name_display', $name, $customer_id);

			    ?>

			    <?php ob_start(); ?>
	            <td class="nk-tb-col">

	                <div>
	                    <div class="user-card">
	                        <div class="user-avatar bg-primary">
	                            <span><?php echo esc_html(substr($name, 0, 2));?></span>
	                        </div>
	                        <div class="user-info">
	                            <span class="tb-lead"><?php echo esc_html($name);?> <span class="dot dot-success d-md-none ml-1"></span></span>
	                        </div>
	                    </div>
	                </div>

	            </td>
	            <?php $col1 = ob_get_clean(); ?>
	            <?php ob_start(); ?>
	            <?php
	                if (apply_filters('b2bking_show_customers_page_company_column', true)){
	                    ?>
	                    <td class="nk-tb-col tb-col-md">
	                        <div>
	                            <span><?php echo esc_html($company_name);?></span>
	                        </div>
	                    </td>
	                    <?php
	                }
	            ?>
	            <?php $col2 = ob_get_clean(); ?>
	            <?php ob_start(); ?>
	            <?php

	            do_action('salesking_customers_custom_columns_content', $customerobj);

	                if (apply_filters('b2bking_show_customers_page_total_spent_column', true)){
	                    ?>
	                    <td class="nk-tb-col tb-col-md" data-order="<?php echo esc_attr($customerobj->get_total_spent());?>">
	                        <div>
	                            <span class="tb-amount"><?php echo wc_price($customerobj->get_total_spent());?></span>
	                        </div>
	                    </td>
	                    <?php
	                }
	            ?>

	            <?php $col3 = ob_get_clean(); ?>
	            <?php ob_start(); ?>
	            <?php
	                if (apply_filters('b2bking_show_customers_page_order_count_column', true)){
	                    ?>
	                    <td class="nk-tb-col tb-col-lg">
	                        <div>
	                            <?php
	                            if (apply_filters('salesking_customers_show_orders_link', false)){
	                                ?>
	                                <a class="salesking_clickable_highlight" href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true)))) .'orders/?search='.$user_info->user_email; ?>">
	                                <?php
	                            }
	                            ?>

	                            <span class="tb-amount"><?php echo $customerobj->get_order_count();?></span>
	                            <?php

	                            if (apply_filters('salesking_customers_show_orders_link', false)){
	                                ?>
	                                </a>
	                                <?php
	                            }
	                            ?>
	                        </div>
	                    </td>
	                    <?php
	                }
	            ?>
	            <?php $col4 = ob_get_clean(); ?>
	            <?php ob_start(); ?>
	            
			            
			            <?php /*
			            <td class="nk-tb-col tb-col-lg" data-order="<?php 
			            $last_order = $customerobj->get_last_order();
			            if (is_a($last_order, 'WC_Order')){
			                $date = explode('T',$last_order->get_date_created())[0];
			                echo esc_attr(strtotime($date));
			            }
			            ?>"> 
			                <div>
			                    <span><?php 
			                    if (is_a($last_order, 'WC_Order')){
			                        $date = ucfirst(strftime("%B %e, %G", strtotime($date)));
			                        echo $date;
			                    }?></span>
			                </div>
			            </td>
			            */?>
			            <?php
			                if (apply_filters('b2bking_show_customers_page_email_column', true)){
			                    ?>
			                    <td class="nk-tb-col tb-col-lg">
			                        <div>
			                            <span><?php echo esc_html($user_info->user_email);?></span>
			                        </div>
			                    </td>
			                    <?php
			                }
			            ?>
			            <?php $col5 = ob_get_clean(); ?>
			            <?php ob_start(); ?>
			            <?php
			                if (apply_filters('b2bking_show_customers_page_phone_column', true)){
			                    ?>
			                    <td class="nk-tb-col tb-col-lg"> 
			                        <div >
			                            <span><?php echo esc_html(get_user_meta($customer_id,'billing_phone', true));?></span>
			                        </div>
			                    </td>
			                    <?php
			                }
			            ?>
			            <?php $col6 = ob_get_clean(); ?>
			            <?php ob_start(); ?>
			            <?php
			                if (apply_filters('salesking_show_customers_page_actions_column', true)){
			                    ?>  
			                    <td class="nk-tb-col">
			                        <div class="tb-odr-btns d-md-inline">
			                            <button class="btn btn-sm btn-primary salesking_shop_as_customer" value="<?php echo esc_attr($customer_id);?>"><em class="icon ni ni-cart-fill"></em><span><?php esc_html_e('Shop as Customer','salesking');?></span></button>
			                        </div>
			                        <?php 
			                        if (intval(get_option( 'salesking_agents_can_edit_customers_setting', 1 )) === 1){
			                            ?>
			                            <div class="tb-odr-btns d-none d-md-inline">
			                                <button class="btn btn-sm btn-secondary salesking_shop_as_customer_edit" value="<?php echo esc_attr($customer_id);?>"><em class="icon ni ni-pen-alt-fill"></em><span><?php echo apply_filters('salesking_shop_customer_edit_button_text', esc_html__('Edit','salesking'));?></span></button>
			                            </div>
			                            <?php
			                        }
			                        ?>
			                    </td>
			                    <?php
			                }
			            ?>
			            <?php $col7 = ob_get_clean(); 

			            array_push($data['data'],array($col1, $col2, $col3, $col4, $col5, $col6, $col7));


			            ?>


			    <?php
			}


			
			echo json_encode($data);

			exit();
	}

	function salesking_download_vendor_balance_history(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		
		$vendorid = sanitize_text_field($_GET['userid']);

		$list_name = 'agent_balance_history';
		$list_name = apply_filters('salesking_balance_history_file_name', $list_name);

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".$list_name."_".$vendorid.".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		$output = fopen("php://output", "wb");
		// build header

		$headerrow = apply_filters('salesking_balance_history_columns_header',array(esc_html__('Date','salesking'), esc_html__('Amount','salesking'), esc_html__('Old balance','salesking'), esc_html__('New balance','salesking'), esc_html__('Note', 'salesking')));

		fputcsv($output, $headerrow);


		$user_balance_history = sanitize_text_field(get_user_meta($vendorid,'salesking_user_balance_history', true));

		if ($user_balance_history){
		    $transactions = explode(';', $user_balance_history);
		    $transactions = array_filter($transactions);
		} else {
		    // empty, no transactions
		    $transactions = array();
		}
		$transactions = array_reverse($transactions);
		foreach ($transactions as $transaction){
		    $elements = explode(':', $transaction);
		    $date = $elements[0];
		    $amount = $elements[1];
		    $old_balance = $elements[2];
		    $new_balance = $elements[3];
		    $note = $elements[4];

		    $csv_array = apply_filters('salesking_balance_history_download_columns_items', array($date, $amount, $old_balance, $new_balance, $note), $transaction);

		    fputcsv($output, $csv_array); 
		}    

		fclose($output);
		exit();
	}

	function saleskingactivatelicense(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		// If nonce verification didn't fail, run further

		$email = sanitize_text_field($_POST['email']);
		$key = sanitize_text_field($_POST['key']);

		$info = parse_url(get_site_url());
		$host = $info['host'];
		$host_names = explode(".", $host);
		$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];

		if (strlen($host_names[count($host_names)-2]) <= 3){    // likely .com.au, .co.uk, .org.uk etc
		    $bottom_host_name = $host_names[count($host_names)-3] . "." . $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
		}

		// send activation request
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://kingsplugins.com/wp-json/licensing/v1/request?email=".$email."&license=".$key."&requesttype=siteactivation&plugin=SK&website=".$bottom_host_name,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => [
			"Content-Type: application/json"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  $response = $err;
		} else {
		   $response = json_decode($response);
		}

		if ($response === 'success'){
			echo 'success';
			// activate
			update_option('pluginactivation_'.$email.'_'.$key.'_'.$bottom_host_name, 'active');
			update_option('salesking_use_legacy_activation', 'no');

		} else {
			echo 'Failed to activate: '.$response;
		}

		exit();	
	}

	function saleskingsaveadjustment(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$amount = sanitize_text_field($_POST['pamount']);
		$note = '(MANUAL ADJUSTMENT) '.sanitize_text_field($_POST['pnote']);
		$user_id = sanitize_text_field($_POST['userid']);


		$user_balance_history = sanitize_text_field(get_user_meta($user_id,'salesking_user_balance_history', true));

		// create transaction
		$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
		$old_balance = get_user_meta($user_id,'salesking_outstanding_earnings', true);
		$new_balance = floatval($old_balance) + floatval($amount);

		$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
		update_user_meta($user_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
		// user balance history end


		// update user consumed balance
		update_user_meta($user_id,'salesking_outstanding_earnings',$new_balance);


		echo 'success';
		exit();
	}


	function send_email_to_agent( $recipient, $order ) {

		// Bail on WC settings pages since the order object isn't yet set yet
		$page = $_GET['page'] = isset( $_GET['page'] ) ? $_GET['page'] : '';
		if ( 'wc-settings' === $page ) {
			return $recipient; 
		}
		
		// just in case
		if ( ! $order instanceof WC_Order ) {
			return $recipient; 
		}

		// if setting for agents to receive emails is enabled
		if (intval(get_option( 'salesking_agents_receive_order_emails_setting', 1 )) === 1){
			// if the customer of this order, or his group has an assigned agent
			$customer_id = $order->get_customer_id();

			// first, check if the customer has an agent assigned directly.
			$customer_agent = get_user_meta($customer_id,'salesking_assigned_agent', true);
			if (!empty($customer_agent) && $customer_agent !== 'none'){
				// found agent
			} else {
				// keep searching in the group
				$customer_is_b2b = get_user_meta($customer_id,'b2bking_b2buser', true);
				if ($customer_is_b2b === 'yes'){
					$customergroup = get_user_meta($customer_id, 'b2bking_customergroup', true);
					$customer_agent = get_post_meta($customergroup, 'salesking_assigned_agent', true);
				}
			}

			if (!empty($customer_agent) && $customer_agent !== 'none'){
				// send email to agent as well
				$agent_info = get_userdata($customer_agent);
				$agent_email = $agent_info->user_email;


				if (apply_filters('salesking_only_agent_gets_order_emails', false, $order, $customer_agent)){
					$recipient = $agent_email;

				} else {
					// also send it to admin
					// only once

					$recipient .= ', '.$agent_email;

					$recipient = apply_filters('salesking_agent_new_order_email_recipient', $recipient, $customer_agent);
				}
				
			}
		}

		return $recipient;
	}


	// Add email classes to the list of email classes that WooCommerce loads
	function salesking_add_email_classes( $email_classes ) {

	    $email_classes['Salesking_New_Announcement_Email'] = include SALESKING_DIR .'/includes/emails/class-salesking-new-announcement-email.php';
	    $email_classes['Salesking_New_Payout_Email'] = include SALESKING_DIR .'/includes/emails/class-salesking-new-payout-email.php';
	    $email_classes['Salesking_New_Message_Email'] = include SALESKING_DIR .'/includes/emails/class-salesking-new-message-email.php';
	    $email_classes['Salesking_Order_Pending_Email'] = include SALESKING_DIR .'/includes/emails/class-salesking-order-pending-email.php';

	    return $email_classes;
	}

	// Add email actions
	function salesking_add_email_actions( $actions ) {
	    $actions[] = 'salesking_new_announcement';
	    $actions[] = 'salesking_new_message';
	    $actions[] = 'salesking_new_payout';
	    $actions[] = 'salesking_new_order_pending';

	    return $actions;
	}

	// Add pending payment gateway
	function salesking_pending_gateway ( $methods ){
		if ( ! class_exists( 'Salesking_Pending_Gateway' ) ) {
			include_once('class-salesking-pending-gateway.php');
			$methods[] = 'Salesking_Pending_Gateway';
		}
    	return $methods;
	}

	function auto_redirect_after_logout($user_id){

		// if sales agent, redirect to sales agent page
		$is_sales_agent = get_user_meta($user_id,'salesking_group', true);
		if ($is_sales_agent === 'none' || empty($is_sales_agent)){

		} else {
		    wp_redirect( apply_filters('salesking_sales_agent_redirect', trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true)))) );
		    exit();
		}

		
	}


	function prevent_wp_login($redirect_to, $requested_redirect_to, $user) {
	    // WP tracks the current page - global the variable to access it
	    global $pagenow;
	    if( $pagenow === 'wp-login.php' && isset($_POST['salesking_dashboard_login'])) {

	    	if (is_wp_error($user)) {

    	        //Login failed, find out why...
    	        $error_types = array_keys($user->errors);
    	        //Error type seems to be empty if none of the fields are filled out
    	        $error_type = 'both_empty';

    	        if (is_array($error_types) && !empty($error_types)) {
    	            $error_type = $error_types[0];
    	        }

    	        wp_redirect(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true))). "?login=failed&reason=" . $error_type);
    	        // Stop execution to prevent the page loading for any reason
    	        exit();
    	    } else {
    	    	wp_redirect(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true))));
    	    }
	    }
	    return $redirect_to;
	}

	// Helps prevent public code from running on login / register pages, where is_admin() returns false
	function salesking_is_login_page() {
		if(isset($GLOBALS['pagenow'])){
	    	return in_array( $GLOBALS['pagenow'],array( 'wp-login.php', 'wp-register.php', 'admin.php' ),  true  );
	    }
	}

	function agents_caps_manage_orders( $allcaps, $caps, $args ){

		$caps = array('edit_shop_order', 'edit_shop_orders', 'edit_published_shop_orders', 'edit_private_shop_orders', 'edit_others_shop_orders', 'read_shop_order', 'shop_order', 'view_admin_dashboard', 'woocommerce_order_itemmeta', 'woocommerce_order_items', 'woocommerce_view_order', 'read_product');

		foreach ($caps as $cap){
			$allcaps[$cap] = 1;
		}

		return $allcaps;
	}

	function mytheme_admin_bar_render() {
	    global $wp_admin_bar;
	    $wp_admin_bar->remove_menu('edit-profile', 'user-actions');
	}

	function stop_access_profile() {
	    if(IS_PROFILE_PAGE === true) {
	        wp_die( esc_html__('Please contact your administrator to have your profile information changed.','salesking') );
	    }
	    remove_menu_page( 'profile.php' );
	    remove_submenu_page( 'users.php', 'profile.php' );
	}

	function remove_menus() {
	    global $menu;
	    $restricted = apply_filters('salesking_restricted_admin_menu_items',array('Dashboard','Subscriptions','Jetpack','Avada','Posts','Media','Pages','Comments','Portfolio','FAQs','Contact','Products','Profile','Tools','Yoast','Elastic'));
	    end($menu);
	    while(prev($menu)){
	        $value = explode(' ',$menu[key($menu)][0]);
	        if(in_array($value[0]!= NULL?$value[0]:'',$restricted)){unset($menu[key($menu)]);}
	    }
	}
	function restrict_screens(){
		$restricted = apply_filters('salesking_restricted_admin_menu_screens',array('edit-shop_subscription','toplevel_page_jetpack'));

		$current_screen = get_current_screen();
		if (in_array($current_screen->id,$restricted)){
			wp_die( esc_html__('Sorry, you are not allowed to access this page.','salesking') );
		}

		$restricted = apply_filters('salesking_restricted_admin_menu_elements',array('.toplevel_page_avada_sliders'));
		?>
		<style type="text/css">
			.toplevel_page_avada_sliders{
				display:none;
			}
		</style>
		<?php

	}

	function salesking_orders_table_ajax(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}


		$start = sanitize_text_field($_POST['start']);
		$length = sanitize_text_field($_POST['length']);
		$search = sanitize_text_field($_POST['search']['value']);
		$pagenr = ($start/$length)+1;

		$current_id = get_current_user_id();

		// get total nr of records
		$args = array( 
		    'posts_per_page' => -1,
		    'post_status'    => 'any',
		    'post_type'		=> 'shop_order',
		    'meta_key'   => 'salesking_assigned_agent',
		    'meta_value' => get_current_user_id(),
		    'fields' => 'ids',
		    's' => $search,
		);

		$total_orders = get_posts( $args );
		$itemnr = count($total_orders);

		$data = array(
			'length'=> $length,
			'data' => array(),
			'recordsTotal' => $itemnr,
			'recordsFiltered' => $itemnr,
		);

		$args = array( 
		    'posts_per_page' => $length,
		    'post_status'    => 'any',
		    'post_type'		=> 'shop_order',
		    'meta_key'   => 'salesking_assigned_agent',
		    'meta_value' => get_current_user_id(),
		    'paged'   => floatval($pagenr),
		    's' => $search,
		);

		$agent_orders = get_posts( $args );


		foreach ($agent_orders as $order){
			$orderobj = wc_get_order($order);

			if ($orderobj !== false){
			    ?>	
		    	<?php ob_start(); ?>
		    	<td class="nk-tb-col">

		    	    <div>
		    	        <span class="tb-lead">#<?php echo esc_html($orderobj->get_order_number());?></span>
		    	    </div>

		    	</td>
		        <?php $col1 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md" data-order="<?php
                    $date = explode('T',$orderobj->get_date_created())[0];
                    echo apply_filters('salesking_dashboard_date_display',strtotime($date), $orderobj->get_date_created());

                ?>">
                    <div>
                        <span class="tb-sub"><?php 
                        
                        echo apply_filters('salesking_dashboard_date_display',ucfirst(strftime("%B %e, %G", strtotime($date))), $orderobj->get_date_created());

                        ?></span>
                    </div>
                </td>
		        <?php $col2 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col"> 
                    <div >
                        <span class="dot bg-warning d-mb-none"></span>
                        <?php
                        $status = $orderobj->get_status();
                        $statustext = $badge = '';
                        if ($status === 'processing'){
                            $badge = 'badge-success';
                            $statustext = esc_html__('Processing','salesking');
                        } else if ($status === 'on-hold'){
                            $badge = 'badge-warning';
                            $statustext = esc_html__('On Hold','salesking');
                        } else if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
                            $badge = 'badge-info';
                            $statustext = esc_html__('Completed','salesking');
                        } else if ($status === 'refunded'){
                            $badge = 'badge-gray';
                            $statustext = esc_html__('Refunded','salesking');
                        } else if ($status === 'cancelled'){
                            $badge = 'badge-gray';
                            $statustext = esc_html__('Cancelled','salesking');
                        } else if ($status === 'pending'){
                            $badge = 'badge-dark';
                            $statustext = esc_html__('Pending Payment','salesking');
                        } else if ($status === 'failed'){
                            $badge = 'badge-danger';
                            $statustext = esc_html__('Failed','salesking');
                        } else {
                            // custom status
                            $badge = 'badge-gray';
                            $wcstatuses = wc_get_order_statuses();
                            if (isset($wcstatuses['wc-'.$status])){
                                $statustext = $wcstatuses['wc-'.$status];
                            } else {
                                $statustext = '';
                            }
                        }
                        ?>
                        <span class="badge badge-sm badge-dot has-bg <?php echo esc_attr($badge);?> d-none d-mb-inline-flex"><?php
                        echo esc_html($statustext);
                        ?></span>
                    </div>
                </td>
		        <?php $col3 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-sm">
                    <div>
                         <span class="tb-sub"><?php
                         $customer_id = $orderobj -> get_customer_id();
                         $data2 = get_userdata($customer_id);
                         $name = $orderobj->get_billing_first_name().' '.$orderobj->get_billing_last_name();

                         // if guest user, show name by order
                         if ($data2 === false){
                            $name = $orderobj -> get_formatted_billing_full_name() . ' '.esc_html__('(guest user)','salesking');
                         }
                         $name = apply_filters('salesking_customers_page_name_display', $name, $customer_id);

                         echo $name;
                         ?></span>
                    </div>
                </td>
		        <?php $col4 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md"> 
                    <div>
                        <span class="tb-sub text-primary"><?php
                        $items = $orderobj->get_items();
                        $items_count = count( $items );
                        if ($items_count > apply_filters('salesking_dashboard_item_count_limit', 4)){
                            echo $items_count.' '.esc_html__('Items', 'salesking');
                        } else {
                            // show the items
                            foreach ($items as $item){
                                echo apply_filters('salesking_item_display_dashboard', $item->get_name().' x '.$item->get_quantity().'<br>', $item);
                            }
                        }
                        ?></span>
                    </div>
                </td>
		        <?php $col5 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col" data-order="<?php echo esc_attr(apply_filters('salesking_orders_order_total', $orderobj->get_total(), $orderobj));?>"> 
	                <div>
	                    <span class="tb-lead"><?php echo wc_price(apply_filters('salesking_orders_order_total', $orderobj->get_total(), $orderobj), array('currency' => $orderobj->get_currency()));?></span>
	                </div>
	            </td>
		        <?php $col6 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		       <td class="nk-tb-col">
                    <div class="salesking_manage_order_container"> 
                        <a href="<?php echo esc_attr(get_edit_post_link($order->ID));?>"><button class="btn btn-sm btn-primary salesking_manage_order" value="<?php echo esc_attr($order->ID);?>"><em class="icon ni ni-bag-fill"></em><span><?php esc_html_e('Manage Order','salesking');?></span></button></a>
                    </div>
                </td>
		        <?php $col7 = ob_get_clean(); ?>
		        <?php

	        	array_push($data['data'],array($col1, $col2, $col3, $col4, $col5, $col6, $col7));

		    }

		}
		
		echo json_encode($data);

		exit();
	}


	function salesking_dismiss_activate_woocommerce_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_activate_woocommerce_notice', 1);

		echo 'success';
		exit();
	}

	function salesking_dismiss_groups_howto_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_groups_howto_notice', 1);

		echo 'success';
		exit();
	}

	function salesking_dismiss_groupsrules_howto_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_groupsrules_howto_notice', 1);

		echo 'success';
		exit();
	}

	function salesking_dismiss_announcements_howto_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_announcements_howto_notice', 1);

		echo 'success';
		exit();
	}

	function salesking_dismiss_messages_howto_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_messages_howto_notice', 1);

		echo 'success';
		exit();
	}

	function salesking_dismiss_payouts_howto_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_payouts_howto_notice', 1);

		echo 'success';
		exit();
	}

	function salesking_dismiss_earnings_howto_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_earnings_howto_notice', 1);

		echo 'success';
		exit();
	}

	function salesking_dismiss_rules_howto_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'salesking_dismiss_rules_howto_notice', 1);

		echo 'success';
		exit();
	}

	function saleskingmarkallread(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$user_id = get_current_user_id();
		
		$announcements_ids = sanitize_text_field($_POST['announcementsid']);

		$announcements_ids = explode(':', $announcements_ids);


		foreach ($announcements_ids as $announcement_id){
			update_user_meta($user_id, 'salesking_announce_read_'.$announcement_id, 'read');

		}
		

		echo 'success';
		exit();
	}

	function salesking_reports_get_data(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
		$helper = new Salesking_Helper();

		$vendor = sanitize_text_field($_POST['vendor']);
		$firstday = sanitize_text_field($_POST['firstday']);
		$lastday = sanitize_text_field($_POST['lastday']);


		global $wpdb;

		$timezone = get_option('timezone_string');
		if (empty($timezone) || $timezone === null){
			$timezone = 'UTC';
		}
		date_default_timezone_set($timezone);

		$date_to = $lastday.' 23:59:59';
		$date_from = $firstday;

		$post_status = implode("','", array('wc-on-hold','wc-pending','wc-processing', 'wc-completed') );

		if ($vendor === 'all'){
			// all orders = general marketplace report
			$orders = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
	            WHERE post_type = 'shop_order'
	            AND post_status IN ('{$post_status}')
	            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
	        ");
		} else {
			// report for specific vendor $vendor is vendor_id
			$orders = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
	            WHERE post_type = 'shop_order'
	            AND post_status IN ('{$post_status}')
	            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
	        ");

	        foreach ($orders as $index => $order){

	        	$earning_id = get_post_meta($order->ID, 'salesking_earning_id', true);
	        	if (!empty($earning_id)){
	        		$agent_id = get_post_meta($earning_id,'agent_id', true);	
	        		if ($vendor !== $agent_id){
	        			unset($orders[$index]);
	        		}
	        	} else {
	        		unset($orders[$index]);

	        	}
	        }
		}
		

        //calculate sales total and order numbers
        $sales_total = 0;
        $order_number = 0;
        $timestamps_sales = array();
        $timestamps_orders = array();

        foreach ($orders as $order){

        	$sales_total += get_post_meta($order->ID,'_order_total', true);
        	$order_number++;

        	$orderobj = wc_get_order($order->ID);
    		$date = $orderobj->get_date_created()->getTimestamp()+(get_option('gmt_offset')*3600);
   			$timestamps_sales[$date] = get_post_meta($order->ID,'_order_total', true);
    		$timestamps_orders[$date] = 1;
        	
        	

        }

        $sales_total_wc = wc_price($sales_total);

        // calculate new vendors if "all option"
        if ($vendor === 'all'){
			$vendors = get_users(array(
			    'meta_query'=> array(
	    	  		'relation' => 'AND',
	                array(
	                    'key' => 'salesking_user_choice',
	                    'value' => 'customer',
	                    'compare' => '!=',
	                ),
	                array(
	                    'key' => 'salesking_group',
	                    'value' => 'none',
	                    'compare' => '!=',
	                ),
	        	),
			    'date_query'    => array(
		            array(
		            	'before'     => $date_to,
		                'after'     => $date_from,
		                'inclusive' => true,
		            ),
		         )
			));
			$new_vendors = count($vendors);

			// get admin commission
			$commission_data = $helper->get_earnings('allagents', 'fromto', false, false, false, false, $date_from, $date_to, true);
			$commission = explode('***',$commission_data)[0];
			$timestamps_commissions = unserialize(explode('***',$commission_data)[1]);

        } else {
        	$new_vendors = '-';

        	// get admin commission
        	$commission_data = $helper->get_earnings($vendor, 'fromto', false, false, false, false, $date_from, $date_to, true);
        	$commission = explode('***',$commission_data)[0];
        	$timestamps_commissions = unserialize(explode('***',$commission_data)[1]);
        }

        $commission_wc = wc_price($commission);

        // 1. Establish draw labels in chart
        /*
		if user chooses < 32 days, show by day ; if they choose > 31 < 366 show by month; > 366 show by year
	    */
		$timedifference = strtotime($lastday) - strtotime($firstday);
		$nrdays = intval(ceil($timedifference/86400));
		if ($nrdays < 32) { // 32 days
			// show days
			$firstdaynumber = date('d',strtotime($firstday));

			$days_array = array();
			$sales_array = array();
			$ordernr_array = array();
			$commissions_array = array();

			$i = 0;
			while ($i <= $nrdays){
				// build label
				array_push($days_array, date('d',(strtotime($firstday)+86400*$i)));

				// for each day, get sales, ordernr, commission
				$sales_of_the_day = 0;
				$ordernr_of_the_day = 0;
				$sales_of_the_day = 0;
				$commissions_of_the_day = 0;

				foreach ($timestamps_sales as $timestamp => $sales){
					if (date("m.d.y", $timestamp) === date("m.d.y",strtotime($firstday)+86400*$i)){
						$sales_of_the_day += $sales;
						$ordernr_of_the_day++;
					}
				}

				foreach ($timestamps_commissions as $timestamp => $commissions){
					if (date("m.d.y", $timestamp) === date("m.d.y",strtotime($firstday)+86400*$i)){
						$commissions_of_the_day += $commissions;
					}
				}
				array_push($sales_array, $sales_of_the_day);
				array_push($ordernr_array, $ordernr_of_the_day);
				array_push($commissions_array, $commissions_of_the_day);


				$i++;

			}

			$labels = json_encode($days_array);

		} else if ($nrdays >= 32){

			// show months
			$firstmonthnumber = date('m.y',strtotime($firstday));
			$lastmonthnumber = date('m.y',strtotime($lastday));

			$months_array = array();
			$sales_array = array();
			$ordernr_array = array();
			$commissions_array = array();
			$i = 1;
			while ($i !== 'stop'){
				
				// for each month, get sales, ordernr, commission
				$sales_of_the_month = 0;
				$ordernr_of_the_month = 0;
				$sales_of_the_month = 0;
				$commissions_of_the_month = 0;

				foreach ($timestamps_sales as $timestamp => $sales){
					if (date("m.y", $timestamp) === $firstmonthnumber){
						$sales_of_the_month += $sales;
						$ordernr_of_the_month++;
					}
				}

				foreach ($timestamps_commissions as $timestamp => $commissions){
					if (date("m.y", $timestamp) === $firstmonthnumber){
						$commissions_of_the_month += $commissions;
					}
				}
				array_push($sales_array, $sales_of_the_month);
				array_push($ordernr_array, $ordernr_of_the_month);
				array_push($commissions_array, $commissions_of_the_month);


				// build label
				array_push($months_array, date("M y", strtotime("+".($i-1)." month", strtotime($firstday))));

				if($firstmonthnumber === $lastmonthnumber){
					$i = 'stop';
				} else {
					$firstmonthnumber = date("m.y", strtotime("+".$i." month", strtotime($firstday)));
					$i++;
				}

				
			}

			$labels = json_encode($months_array);

		} 

		// round values to 2 decimals
		foreach ($sales_array as $index => $value){
			$sales_array[$index] = round($value, 2);
		}
		foreach ($commissions_array as $index => $value){
			$commissions_array[$index] = round($value, 2);
		}


		$salestotal = json_encode($sales_array);
		$ordernumbers = json_encode($ordernr_array);
		$commissiontotal = json_encode($commissions_array);

		
		echo $sales_total.'*'.$sales_total_wc.'*'.$order_number.'*'.$new_vendors.'*'.$commission.'*'.$commission_wc.'*'.$labels.'*'.$salestotal.'*'.$ordernumbers.'*'.$commissiontotal;
	
		exit();
	}

	function saleskingmarkread(){

		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$announcement_id = sanitize_text_field($_POST['announcementid']);
		$user_id = get_current_user_id();
	
		update_user_meta($user_id, 'salesking_announce_read_'.$announcement_id, 'read');

		echo 'success';
		exit();

	}

	function saleskingmarkreadmessage(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$messageid = sanitize_text_field($_POST['messageid']);
		$user_id = get_current_user_id();
		
		update_user_meta($user_id, 'salesking_message_last_read_'.$messageid, time());

		echo 'success';
		exit();	
	}

	function saleskingmarkclosedmessage(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$messageid = sanitize_text_field($_POST['messageid']);
		$user_id = get_current_user_id();

		$is_closed = 'no';
		// get currently selected message
		if (!empty($messageid)){
		    $nr_messages = get_post_meta ($messageid, 'salesking_message_messages_number', true);
		    $last_message = get_post_meta ($messageid, 'salesking_message_message_'.$nr_messages, true);

		    // check if message is closed
		    $last_closed_time = get_user_meta($user_id,'salesking_message_last_closed_'.$messageid, true);
		    if (!empty($last_closed_time)){
		        $last_message_time = get_post_meta ($messageid, 'salesking_message_message_'.$nr_messages.'_time', true);
		        if (floatval($last_closed_time) > floatval($last_message_time)){
		             $is_closed = 'yes';
		        }
		    }
		}

		if ($is_closed === 'yes'){
			update_user_meta($user_id, 'salesking_message_last_closed_'.$messageid, 1);	
		} else {
			update_user_meta($user_id, 'salesking_message_last_closed_'.$messageid, time());
		}
		
		

		echo 'success';
		exit();	
	}

	function saleskingreplymessage(){

		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$message_content = sanitize_textarea_field($_POST['messagecontent']);
		$conversationid = $message_id = sanitize_text_field($_POST['messageid']);

		$currentuser = wp_get_current_user()->user_login;
		$conversationuser = get_post_meta ($conversationid, 'salesking_message_user', true);

		// Check message not empty
		if ($message_content !== NULL && trim($message_content) !== ''){

			$nr_messages = get_post_meta ($conversationid, 'salesking_message_messages_number', true);
			$current_message_nr = $nr_messages+1;
			update_post_meta( $conversationid, 'salesking_message_message_'.$current_message_nr, $message_content);
			update_post_meta( $conversationid, 'salesking_message_messages_number', $current_message_nr);
			update_post_meta( $conversationid, 'salesking_message_message_'.$current_message_nr.'_author', $currentuser );
			update_post_meta( $conversationid, 'salesking_message_message_'.$current_message_nr.'_time', time() );
			// send email notification
			$recipient = get_option( 'admin_email' );
			$recipient = apply_filters('salesking_recipient_new_message', $recipient, $conversationid);
			do_action( 'salesking_new_message', $recipient, $message_content, get_current_user_id(), $conversationid );
		}
		
		echo 'success';
		exit();

	}

	function saleskingcomposemessage(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$message_content = sanitize_textarea_field($_POST['messagecontent']);
		$recipient = sanitize_text_field($_POST['recipient']);
		$title = sanitize_text_field($_POST['title']);

		$currentuser = wp_get_current_user()->user_login;
		$conversationuser = get_post_meta ($conversationid, 'salesking_message_user', true);

		// Check message not empty
		if ($message_content !== NULL && trim($message_content) !== ''){

			// Insert post
			$args = array(
				'post_title' => $title, 
				'post_type' => 'salesking_message',
				'post_status' => 'publish', 
			);
			$conversationid = wp_insert_post( $args);


			update_post_meta( $conversationid, 'salesking_message_user', $currentuser);
			update_post_meta( $conversationid, 'salesking_message_message_1', $message_content);
			update_post_meta( $conversationid, 'salesking_message_messages_number', 1);
			update_post_meta( $conversationid, 'salesking_message_message_1_author', $currentuser );
			update_post_meta( $conversationid, 'salesking_message_message_1_time', time() );
			update_post_meta( $conversationid, 'salesking_message_user', $recipient );

			
			$recipient = get_option( 'admin_email' );
			$recipient = apply_filters('salesking_recipient_new_message', $recipient, $conversationid);

			// send email notification
			do_action( 'salesking_new_message', $recipient, $message_content, get_current_user_id(), $conversationid );
			

		}

		
		// return conversation id URL
		echo trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true))).'messages?id='.esc_attr($conversationid);
		exit();

	}

	function saleskingshopascustomer(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$customer_id = sanitize_text_field($_POST['customer']);
		$agent_id = get_current_user_id();
		// first check that customer is indeed assigned to this agent
		$assigned_agent = get_user_meta($customer_id, 'salesking_assigned_agent', true);

		if (intval($agent_id) !== intval($assigned_agent)){
			// check group agent
			$customer_group = get_user_meta($customer_id,'b2bking_customergroup', true);
			$assigned_agent = get_post_meta($customer_group, 'salesking_assigned_agent', true);
		}

		// if assigned OR if all customers setting enabled
		if (intval($agent_id) === intval($assigned_agent) || (intval(get_option( 'salesking_all_agents_shop_all_customers_setting', 0 ))=== 1)){
			// checks out, continue
			wp_set_current_user( $customer_id );
			wp_set_auth_cookie( $customer_id );

			// get the agent's registration date as a secure info point
			$udata = get_userdata( $agent_id );
            $registered_date = $udata->user_registered;

			setcookie("salesking_switch_cookie", $customer_id.'_'.$agent_id.'_'.$registered_date, time()+86400, "/");

			WC()->cart->empty_cart( apply_filters( 'salesking_empty_cart_on_switch', true ) );

		} 

		echo 'success';
		exit();
	}

	function saleskingswitchtoagent(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$agent_id = sanitize_text_field($_POST['agent']);
		$date_registered = sanitize_text_field($_POST['agentdate']);

		$customer_id = get_current_user_id();

		// first check that agent is indeed assigned to this customer
		$assigned_agent = get_user_meta($customer_id, 'salesking_assigned_agent', true);

		if (intval($agent_id) !== intval($assigned_agent)){
			// check group agent
			$customer_group = get_user_meta($customer_id,'b2bking_customergroup', true);
			$assigned_agent = get_post_meta($customer_group, 'salesking_assigned_agent', true);
		}

		if (intval($agent_id) === intval($assigned_agent) || (intval(get_option( 'salesking_all_agents_shop_all_customers_setting', 0 ))=== 1)){

			// get the agent's registration date as a secure info point
			$udata = get_userdata( $agent_id );
            $registered_date = $udata->user_registered;

            if ($registered_date === $date_registered){
	            // checks out, continue
	            wp_set_current_user( $agent_id );
	            wp_set_auth_cookie( $agent_id );

				setcookie("salesking_switch_cookie", "", time()-3600, "/");

				WC()->cart->empty_cart( apply_filters( 'salesking_empty_cart_on_switch', true ) );

            }

		} 

		echo 'success';
		exit();
	}

	function saleskingsavepayment(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$amount = sanitize_text_field($_POST['pamount']);
		$method = sanitize_text_field($_POST['pmethod']);
		$note = sanitize_text_field($_POST['pnote']);
		$user_id = sanitize_text_field($_POST['userid']);
		$havebonus = sanitize_text_field($_POST['bonus']); //bool
		$havebonus = filter_var($havebonus,FILTER_VALIDATE_BOOLEAN);


		// get user history
		$user_payout_history = sanitize_text_field(get_user_meta($user_id,'salesking_user_payout_history', true));

		// create transaction
		$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
		$outstanding_balance = get_user_meta($user_id,'salesking_outstanding_earnings', true);
		$new_outstanding_balance = floatval($outstanding_balance) - floatval($amount);
		if ($havebonus === true){
			$new_outstanding_balance = $outstanding_balance; // is bonus, so does not count
		}	
		$transaction_new = $date.':'.$amount.':'.$new_outstanding_balance.':'.$note.':'.$method;

		// update credit history
		update_user_meta($user_id,'salesking_user_payout_history',$user_payout_history.';'.$transaction_new);


		// user balance history start
		$old_balance = get_user_meta($user_id,'salesking_outstanding_earnings', true);
		$new_balance = $new_outstanding_balance;
		$amount = '- '.$amount;
		$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
		$note = 'Payout was sent to user.';
		$user_balance_history = sanitize_text_field(get_user_meta($user_id,'salesking_user_balance_history', true));
		$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
		update_user_meta($user_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
		// user balance history end


		// update user consumed balance
		update_user_meta($user_id,'salesking_outstanding_earnings',$new_outstanding_balance);
		// send email to user
		$userdata = get_userdata($user_id);
		$recipient = $userdata->user_email;
		do_action( 'salesking_new_payout', $recipient, $amount, $method, $note );

		echo 'success';
		exit();
	}

	function salesking_save_profile_info(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		$user_id = get_current_user_id();

		$fn = sanitize_text_field($_POST['firstname']);
		$ln = sanitize_text_field($_POST['lastname']);
		$dn = sanitize_text_field($_POST['displayname']);
		$em = sanitize_text_field($_POST['emailad']);

		update_user_meta($user_id, 'first_name', $fn);
		update_user_meta($user_id, 'last_name', $ln);
		wp_update_user( array( 'ID' => $user_id, 'display_name' => $dn, 'user_email' => $em ) );

		echo 'success';
		exit();
	}


	function salesking_save_profile_settings(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		$user_id = sanitize_text_field($_POST['userid']);

		$ann = sanitize_text_field($_POST['announcementsemails']);
		$msg = sanitize_text_field($_POST['messagesemails']);
		$ann = filter_var($ann,FILTER_VALIDATE_BOOLEAN);
		$msg = filter_var($msg,FILTER_VALIDATE_BOOLEAN);

		if ($ann === true){
			update_user_meta($user_id,'salesking_receive_new_announcements_emails', 'yes');
		} else {
			update_user_meta($user_id,'salesking_receive_new_announcements_emails', 'no');
		}

		if ($msg === true){
			update_user_meta($user_id,'salesking_receive_new_messages_emails', 'yes');
		} else {
			update_user_meta($user_id,'salesking_receive_new_messages_emails', 'no');
		}

		echo 'success';
		exit();
	}

	function saleskingsaveinfo(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		$user_id = get_current_user_id();

		$method = sanitize_text_field($_POST['chosenmethod']);
		update_user_meta($user_id,'salesking_agent_selected_payout_method', $method);

		$paypalemail = sanitize_text_field($_POST['paypal']);
		$custominfo = sanitize_text_field($_POST['custom']);

		$fullname = sanitize_text_field($_POST['fullname']);
		$billingaddress1 = sanitize_text_field($_POST['billingaddress1']);
		$billingaddress2 = sanitize_text_field($_POST['billingaddress2']);
		$city = sanitize_text_field($_POST['city']);
		$state = sanitize_text_field($_POST['state']);
		$postcode = sanitize_text_field($_POST['postcode']);
		$country = sanitize_text_field($_POST['country']);
		$bank_account_holder_name = sanitize_text_field($_POST['bankholdername']);
		$bank_account_number = sanitize_text_field($_POST['bankaccountnumber']);
		$branchcity = sanitize_text_field($_POST['branchcity']);
		$branchcountry = sanitize_text_field($_POST['branchcountry']);
		$intermediarycode = sanitize_text_field($_POST['intermediarycode']);
		$intermediaryname = sanitize_text_field($_POST['intermediaryname']);
		$intermediarycity = sanitize_text_field($_POST['intermediarycity']);
		$intermediarycountry = sanitize_text_field($_POST['intermediarycountry']);

		$linkedinfo = $paypalemail.'**&&'.$custominfo.'**&&'.$fullname.'**&&'.$billingaddress1.'**&&'.$billingaddress2.'**&&'.$city.'**&&'.$state.'**&&'.$postcode.'**&&'.$country.'**&&'.$bank_account_holder_name.'**&&'.$bank_account_number.'**&&'.$branchcity.'**&&'.$branchcountry.'**&&'.$intermediarycode.'**&&'.$intermediaryname.'**&&'.$intermediarycity.'**&&'.$intermediarycountry;

		update_user_meta($user_id,'salesking_payout_info', base64_encode($linkedinfo));

		echo 'success';
		exit();

	}

	function saleskingaddsubagent(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$firstname = sanitize_text_field($_POST['firstname']);
		$lastname = sanitize_text_field($_POST['lastname']);
		$phoneno = sanitize_text_field($_POST['phoneno']);
		$username = sanitize_text_field($_POST['username']);
		$emailaddress = sanitize_text_field($_POST['emailaddress']);
		$password = sanitize_text_field($_POST['password']);
		$agent_id = get_current_user_id();

		$user_id = wp_create_user( $username, $password, $emailaddress);


		if ( ! (is_wp_error($user_id))){
			// no errors, proceed
			// set user meta
			update_user_meta($user_id, 'billing_first_name', $firstname);
			update_user_meta($user_id, 'shipping_first_name', $firstname);
			update_user_meta($user_id, 'first_name', $firstname);

			update_user_meta($user_id, 'billing_last_name', $lastname);
			update_user_meta($user_id, 'shipping_last_name', $lastname);
			update_user_meta($user_id, 'last_name', $lastname);

			update_user_meta($user_id, 'billing_phone', $phoneno);
			update_user_meta($user_id, 'shipping_phone', $phoneno);

			// set assigned agent
			update_user_meta($user_id, 'salesking_parent_agent', $agent_id);
			$parentaggroup = get_user_meta( $agent_id, 'salesking_group', true );
			update_user_meta($user_id, 'salesking_group', $parentaggroup);
			update_user_meta( $user_id, 'salesking_user_choice', 'agent');	
			update_user_meta( $user_id, 'salesking_assigned_agent', 'none');		

			$userobj = new WP_User($user_id);
			$userobj->set_role('customer');

			$parent_agent_id = $agent_id;
			do_action('salesking_after_subagent_created', $user_id, $parent_agent_id, $parentaggroup);

			add_filter( 'wp_new_user_notification_email' , 'edit_user_notification_email', 10, 3 );

			function edit_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {

			    $message = sprintf(esc_html__( "Your sales agent account for %s has been created! Here are your login details:",'salesking' ), $blogname ) . "\r\n\r\n";
			    $message .= trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true))) . "\r\n";
			    $message .= sprintf(esc_html__( 'Username: %s','salesking' ), $user->user_login ) . "\r\n";
			    $message .= sprintf(esc_html__( 'Password: %s','salesking' ), sanitize_text_field($_POST['password']) ) . "\r\n\r\n";
			   

			    $key = get_password_reset_key( $user );
		        if ( is_wp_error( $key ) ) {
		            return $wp_new_user_notification_email;
		        }
		     
		        $switched_locale = switch_to_locale( get_user_locale( $user ) );
		     
		        /* translators: %s: User login. */
		        if (apply_filters('salesking_allow_team_new_password', true)){
		        	$message .= esc_html__( 'To set a new password, visit the following address:','salesking' ) . "\r\n\r\n";
		        	$message .= apply_filters('salesking_email_new_password_link', network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ) . "\r\n\r\n", $key, $user->user_login);
		        }       
        

			    $wp_new_user_notification_email['message'] = $message;

			    $wp_new_user_notification_email = array(
			            'to'      => $user->user_email,
			            /* translators: Login details notification email subject. %s: Site title. */
			            'subject' => __( 'Your %s sales agent account details' ),
			            'message' => $message,
			            'headers' => '',
			        );

			    return $wp_new_user_notification_email;

			}

			// Sent email
			wp_new_user_notification( $user_id, null, 'user');


			echo $user_id;
			echo 'success';

		} else {
			echo 'error'.$user_id->get_error_message();
		}


		exit();
	}

	function saleskingaddcustomer(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$firstname = sanitize_text_field($_POST['firstname']);
		$lastname = sanitize_text_field($_POST['lastname']);
		$companyname = sanitize_text_field($_POST['companyname']);
		$country = sanitize_text_field($_POST['country']);
		$state = sanitize_text_field($_POST['state']);
		$streetaddress = sanitize_text_field($_POST['streetaddress']);
		$towncity = sanitize_text_field($_POST['towncity']);
		$postcodezip = sanitize_text_field($_POST['postcodezip']);
		$phoneno = sanitize_text_field($_POST['phoneno']);
		$username = sanitize_text_field($_POST['username']);
		$emailaddress = sanitize_text_field($_POST['emailaddress']);
		$password = sanitize_text_field($_POST['password']);
		$agent_id = get_current_user_id();

		if (apply_filters('salesking_agent_new_customer_remove_email', false)){
			add_filter('woocommerce_email_enabled_customer_new_account', '__return_false', 1000);
		}

		$user_id = wc_create_new_customer($emailaddress, $username, $password);

		if (apply_filters('salesking_agent_new_customer_remove_email', false)){
			remove_filter('woocommerce_email_enabled_customer_new_account', '__return_false', 1000);
		}

		if ( ! (is_wp_error($user_id))){
			// no errors, proceed

			if (apply_filters('salesking_show_b2bking_groups_new_customer',true)){
				if (isset($_POST['b2bkinggroup'])){
					$b2bkinggroup = sanitize_text_field($_POST['b2bkinggroup']);
					update_user_meta($user_id,'b2bking_b2buser', 'yes');
					update_user_meta($user_id,'b2bking_customergroup', $b2bkinggroup);
				}

			}

			if (apply_filters('salesking_show_b2bwhs_groups_new_customer',true)){
				if (isset($_POST['b2bwhsgroup'])){
					$b2bkinggroup = sanitize_text_field($_POST['b2bwhsgroup']);
					update_user_meta($user_id,'b2bwhs_b2buser', 'yes');
					update_user_meta($user_id,'b2bwhs_customergroup', $b2bkinggroup);
				}

			}

			// set user meta
			update_user_meta($user_id, 'billing_first_name', $firstname);
			update_user_meta($user_id, 'first_name', $firstname);
			update_user_meta($user_id, 'shipping_first_name', $firstname);

			update_user_meta($user_id, 'billing_last_name', $lastname);
			update_user_meta($user_id, 'last_name', $lastname);
			update_user_meta($user_id, 'shipping_last_name', $lastname);

			update_user_meta($user_id, 'billing_company', $companyname);
			update_user_meta($user_id, 'shipping_company', $companyname);

			update_user_meta($user_id, 'billing_country', $country);
			update_user_meta($user_id, 'shipping_country', $country);

			update_user_meta($user_id, 'billing_state', $state);
			update_user_meta($user_id, 'shipping_state', $state);

			update_user_meta($user_id, 'billing_address_1', $streetaddress);
			update_user_meta($user_id, 'shipping_address_1', $streetaddress);

			update_user_meta($user_id, 'billing_city', $towncity);
			update_user_meta($user_id, 'shipping_city', $towncity);
 
			update_user_meta($user_id, 'billing_postcode', $postcodezip);
			update_user_meta($user_id, 'shipping_postcode', $postcodezip);

			update_user_meta($user_id, 'billing_phone', $phoneno);
			update_user_meta($user_id, 'shipping_phone', $phoneno);

			// custom fields from b2bking
			if (isset($_POST['customfields'])){
				$customfields = sanitize_text_field($_POST['customfields']);
				$fieldsarray = explode(',', $customfields);
				foreach ($fieldsarray as $field_id){
					if (!empty($field_id)){
						$field_value = sanitize_text_field($_POST[$field_id]);
						update_user_meta($user_id, 'b2bking_custom_field_'.$field_id, $field_value);
						update_user_meta($user_id, 'b2bwhs_custom_field_'.$field_id, $field_value);

						$afield_billing_connection = get_post_meta($field_id, 'b2bking_custom_field_billing_connection', true);
						$bfield_billing_connection = get_post_meta($field_id, 'b2bwhs_custom_field_billing_connection', true);
						if ($afield_billing_connection === 'custom_mapping'){
							$mapping_key = get_post_meta($field_id, 'b2bking_custom_field_mapping', true);
							update_user_meta($user_id, $mapping_key, $field_value);
						}
						if ($bfield_billing_connection === 'custom_mapping'){
							$mapping_key = get_post_meta($field_id, 'b2bwhs_custom_field_mapping', true);
							update_user_meta($user_id, $mapping_key, $field_value);
						}

					}
				}
			}

			// set assigned agent
			update_user_meta($user_id, 'salesking_assigned_agent', $agent_id);

			$userobj = new WP_User($user_id);
			$userobj->set_role('customer');

			do_action('salesking_after_customer_created', $user_id);

			// Sent email
			if (apply_filters('salesking_allow_customer_new_password', true)){
				wp_new_user_notification( $user_id, null, 'user');
			}


			echo $user_id;
		} else {
			echo 'error'.$user_id->get_error_message();
		}


		echo 'success';
		exit();
	}

	function saleskingcreatecart(){

		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}


		$cartname = sanitize_text_field($_POST['name']);
		// replace whitespaces with dashes
		$cartname = preg_replace('/\s+/', '-', $cartname);
		// if cartname exists, add characters until it does not exist
		$confirmed_unique = 'no';
		while ($confirmed_unique === 'no'){
			$confirmed_unique = 'yes';
			// check current cart names
			$current_carts = get_user_meta(get_current_user_id(),'salesking_agent_carts', true);
			$carts_array = explode('AAAENDAAA', $current_carts);
			foreach ($carts_array as $cart){
				$cartnametemp = explode('AAANAMEAAA', $cart)[0];
				if ($cartname === $cartnametemp){

					// already exists, change cart name
					$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
					$cartname .= $characters[mt_rand(0, 61)];

					$confirmed_unique = 'no';
					break;
				}
			}
		}

		// Get new cart content
		$cart_session = array();
		$cart_session['cart']            = WC()->session->cart;
		$cart_session['cart_totals']     = WC()->session->cart_totals;
		$cart_session['applied_coupons'] = WC()->session->applied_coupons;
		update_option('salesking_'.$cartname, $cart_session);
		// get new finish
	

		// Get Cart Contents
		$items = WC()->cart->get_cart();
		$cart_string = $cartname.'AAANAMEAAA';

		foreach($items as $item => $values) { 
		    $id = $values['data']->get_id();
		    $quantity = $values['quantity'];

		    if(!empty($values['variation'])){
		    	$variationdata = serialize($values['variation']);
		    	$quantity = $quantity.'+++'.$variationdata;
		    }

		    $cart_string .= $id.':::'.$quantity.';;;';

		}

		$cart_string .= 'AAAENDAAA';

		if (count($items) > 0){
			// Save Cart in User Meta
			$current_value = get_user_meta(get_current_user_id(),'salesking_agent_carts', true);
			update_user_meta(get_current_user_id(), 'salesking_agent_carts', $current_value.$cart_string);
		}


		echo 'success';
		exit();

	}

	function saleskingdeletecart(){

		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}


		$cartname = sanitize_text_field($_POST['name']);
		
		$current_value = get_user_meta(get_current_user_id(),'salesking_agent_carts', true);
		$carts = explode('AAAENDAAA',$current_value);
		foreach ($carts as $index => $cart){
			$cart_string = explode('AAANAMEAAA', $cart);
			$cartnametemp = $cart_string[0];
			if ($cartnametemp === $cartname){
				// found it, must delete it
				unset($carts[$index]);
				break;
			}
		}
		// rebuild string
		$new_string = '';
		foreach ($carts as $cart){
			$new_string .=$cart.'AAAENDAAA';
		}

		update_user_meta(get_current_user_id(), 'salesking_agent_carts', $new_string);


		echo 'success';
		exit();

	}

	function saleskingsavecoupon(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}


		$couponcode = sanitize_text_field($_POST['couponcode']);
		$expirydate = sanitize_text_field($_POST['expirydate']);
		$minspend = sanitize_text_field($_POST['minspend']);
		$maxspend = sanitize_text_field($_POST['maxspend']);
		$discount = sanitize_text_field($_POST['discount']);
		$limit = sanitize_text_field($_POST['limit']);	

		// check if user didn't cheat discount amount
		$user_id = get_current_user_id();
		$agent_group = get_user_meta($user_id,'salesking_group', true);
		$allowed_discount = get_user_meta($user_id, 'salesking_group_max_discount', true);
		if (empty($allowed_discount) || !($allowed_discount)){
		    $group_discount = get_post_meta($agent_group,'salesking_group_max_discount', true);
		    $allowed_discount = $group_discount;
		}
		if (empty($allowed_discount) || !($allowed_discount)){
		    $allowed_discount = 1;
		}
		if ($discount > $allowed_discount){
			$discount = $allowed_discount;
		}


		// if coupon exists (is valid), add characters until is not valid
		$coupon_exists = get_posts(
		    array( 
		        'post_type' => 'shop_coupon', // only conversations
		        'post_status' => 'publish',
		        'numberposts' => -1,
		        'fields' => 'ids',
		        'title' => $couponcode,
		    )
		);

		while (!empty($coupon_exists)){
			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$couponcode .= $characters[mt_rand(0, 61)];
			$coupon_exists = get_posts(
			    array( 
			        'post_type' => 'shop_coupon', // only conversations
			        'post_status' => 'publish',
			        'numberposts' => -1,
			        'fields' => 'ids',
			        'title' => $couponcode,
			    )
			);
		};


	    $coupon = array(
			'post_title' => $couponcode,
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type'  => 'shop_coupon'
		);

		$new_coupon_id = wp_insert_post( $coupon );

		// Add meta
		update_post_meta( $new_coupon_id, 'discount_type', 'percent' );
		update_post_meta( $new_coupon_id, 'coupon_amount', $discount );
		update_post_meta( $new_coupon_id, 'individual_use', 'no' );
		update_post_meta( $new_coupon_id, 'product_ids', '' );
		update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
		if (!empty($limit)){
			update_post_meta( $new_coupon_id, 'usage_limit', $limit );
		}
		
		update_post_meta( $new_coupon_id, 'date_expires', strtotime($expirydate) );
		update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
		update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
		if (!empty($minspend)){
			update_post_meta( $new_coupon_id, 'minimum_amount', $minspend );
		}
		if (!empty($maxspend)){
			update_post_meta( $new_coupon_id, 'maximum_amount', $maxspend );
		}

		$exclude = sanitize_text_field($_POST['exclude']);
		$exclude = filter_var($exclude,FILTER_VALIDATE_BOOLEAN);


		if ($exclude === true){
			update_post_meta( $new_coupon_id, 'exclude_sale_items', 'yes');
		} else {
			update_post_meta( $new_coupon_id, 'exclude_sale_items', 'no');
		}


		update_post_meta( $new_coupon_id, 'salesking_agent', get_current_user_id() );	
		return $coupon_code;



		echo 'success';
		exit();

	}

	function saleskingdeletecoupon(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'salesking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}


		$coupon_id = sanitize_text_field($_POST['couponpostid']);

		// verify that author has this coupon
		$coupon_author = get_post_meta($coupon_id,'salesking_agent', true);
		if (intval($coupon_author) === get_current_user_id()){
			wp_delete_post($coupon_id);
		}

		echo 'success';
		exit();

	}


}

