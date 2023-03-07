<?php

class Salesking_Admin{

	function __construct() {

		// How to use notices
		add_action( 'admin_notices', array($this, 'salesking_groups_howto') );
		add_action( 'admin_notices', array($this, 'salesking_groupsrules_howto') );
		add_action( 'admin_notices', array($this, 'salesking_announcements_howto') );
		add_action( 'admin_notices', array($this, 'salesking_messages_howto') );
		add_action( 'admin_notices', array($this, 'salesking_payouts_howto') );
		add_action( 'admin_notices', array($this, 'salesking_earnings_howto') );
		add_action( 'admin_notices', array($this, 'salesking_rules_howto') );

		// Require WooCommerce notification
		add_action( 'admin_notices', array($this, 'salesking_plugin_dependencies') );
		// Load admin notice resources (enables notification dismissal)
		add_action( 'admin_enqueue_scripts', array($this, 'load_global_admin_notice_resource') ); 
		// Allow shop manager to set plugin options
		add_filter( 'option_page_capability_salesking', array($this, 'salesking_options_capability' ) );

		// Add  header bar in SALESKING post types
		add_action('in_admin_header', array($this,'salesking_show_header_bar_salesking_posts'));

		add_action( 'plugins_loaded', function(){
			if ( class_exists( 'woocommerce' ) ) {

				
				// Disable Guternberg Editor on Post Type
				add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg'), 10, 2);

				/* Announcements */
				if (intval(get_option( 'salesking_enable_announcements_setting', 1 )) === 1){
					add_action( 'init', array($this, 'salesking_register_post_type_announcement'), 0 );
					add_action( 'add_meta_boxes', array($this, 'salesking_announcement_metaboxes') );
					// Save post and send emails
					add_action( 'save_post', array($this, 'salesking_save_announcement_metaboxes'), 10, 1);

					add_filter( 'manage_salesking_announce_posts_columns', array($this, 'salesking_add_columns_group_menu_announcement') );
					add_action( 'manage_salesking_announce_posts_custom_column' , array($this, 'salesking_columns_group_data_announcement'), 10, 2 );
				}

				/* Commission Rules */
				// Register new post type
				add_action( 'init', array($this, 'salesking_register_post_type_commission_rules'), 0 );
				// Add metaboxes to rules
				add_action( 'add_meta_boxes', array($this, 'salesking_rules_metaboxes') );
				// Save metaboxes
				add_action('save_post', array($this, 'salesking_save_rules_metaboxes'), 10, 1);
				add_filter( 'manage_salesking_rule_posts_columns', array($this, 'salesking_add_columns_group_menu_rules') );
				add_action( 'manage_salesking_rule_posts_custom_column' , array($this, 'salesking_columns_group_data_rules'), 10, 2 );

				/* Agent Groups */
				add_action( 'init', array($this, 'salesking_register_post_type_agent_groups'), 0 );
				add_action( 'add_meta_boxes', array($this, 'salesking_groups_metaboxes') );
				// save groups + save order / order assigned
				add_action( 'save_post', array($this, 'salesking_save_groups_metaboxes'), 10, 1);
				add_filter( 'manage_salesking_group_posts_columns', array($this, 'salesking_add_columns_group_menu') );
				add_action( 'manage_salesking_group_posts_custom_column' , array($this, 'salesking_columns_group_data'), 10, 2 );


				/* Group Rules */
				// Register new post type
				add_action( 'init', array($this, 'salesking_register_post_type_group_rules'), 0 );
				// Add metaboxes to rules
				add_action( 'add_meta_boxes', array($this, 'salesking_group_rules_metaboxes') );
				// Save metaboxes
				add_action('save_post', array($this, 'salesking_save_group_rules_metaboxes'), 10, 1);
				add_filter( 'manage_salesking_grule_posts_columns', array($this, 'salesking_add_columns_grule_menu') );
				add_action( 'manage_salesking_grule_posts_custom_column' , array($this, 'salesking_columns_grule_data'), 10, 2 );

				// Integrate with B2BKing Manual Approval 
				add_action('b2bking_before_registration_approval', array($this, 'assign_agent_registration_approval'), 5);


				/* Messages */
				if (intval(get_option( 'salesking_enable_messages_setting', 1 )) === 1){
					// Messages Count
					add_action( 'admin_head', array( $this, 'salesking_messages_menu_order_count' ) );
					add_action( 'init', array($this, 'salesking_register_post_type_message'), 0 );
					add_action( 'add_meta_boxes', array($this, 'salesking_message_metaboxes') );
					add_action( 'save_post', array($this, 'salesking_save_message_metaboxes'), 10, 1);
					
					//add_action( 'transition_post_status', array($this, 'salesking_first_publish_announcement_email'), 10, 3 );
					add_filter( 'manage_salesking_message_posts_columns', array($this, 'salesking_add_columns_group_menu_message') );
					add_action( 'manage_salesking_message_posts_custom_column' , array($this, 'salesking_columns_group_data_message'), 10, 2 );
				}


				/* Earning Post Type */
				add_action( 'init', array($this, 'salesking_register_post_type_earning'), 0 );

				if (current_user_can( 'manage_woocommerce' )){ 
					/* Custom User Meta */
					// Show the new user meta in New User, User Profile and Edit
					add_action( 'user_new_form', array($this, 'salesking_show_user_meta_profile'), 999, 1 );
					add_action( 'show_user_profile', array($this, 'salesking_show_user_meta_profile'), 999, 1 );
					add_action( 'edit_user_profile', array($this, 'salesking_show_user_meta_profile'), 999, 1 );
					// Save the new user meta (Update or Create)
					add_action( 'personal_options_update', array($this, 'salesking_save_user_meta_agent_group') );
					add_action( 'edit_user_profile_update', array($this, 'salesking_save_user_meta_agent_group') );
					add_action( 'user_register', array($this, 'salesking_save_user_meta_agent_group') );
					// Add columns to Users Table
					add_filter( 'manage_users_columns',  array($this, 'salesking_add_columns_user_table') );
					add_filter( 'manage_users_custom_column', array($this, 'salesking_retrieve_group_column_contents_users_table'), 10, 3 );
					/* Filters by agent in users backend */
					add_action( 'restrict_manage_users', array($this, 'add_filter_by_agent_filter' ));
					add_filter( 'pre_get_users', array($this, 'filter_users_by_filter_by_agent' ));
				}


				// Add group agent metabox
				add_action( 'add_meta_boxes', array($this, 'salesking_b2bking_groups_metaboxes') );

				// only if user is an agent and not admin
				$agentgroup = get_user_meta( get_current_user_id(), 'salesking_group', true );
				if ( ! wp_doing_ajax() ){ 
					if (!empty($agentgroup) && $agentgroup !== 'none' && !current_user_can( 'manage_woocommerce' )){
						// In backend, only show agents their own orders
						add_filter('parse_query', array($this, 'salesking_show_agents_only_own_orders'));
						// only show own customers
						add_action( 'pre_get_users', array($this, 'salesking_show_own_customers' ));
						// hide numbers
						add_filter( 'woocommerce_include_processing_order_count_in_menu', '__return_false' );
						add_filter('wp_count_posts', array($this, 'salesking_hide_numbers'), 10, 3);
						// forbid access to orders that are not own
						add_action('admin_init', array($this, 'salesking_forbid_other_orders'), 100);
						add_action( 'wp_before_admin_bar_render', array($this, 'customize_admin_bar' ));
						
					}
				}

				if(intval(get_option( 'salesking_agents_can_manage_orders_setting', 1 )) === 1){
					// Add agent column to orders
					add_filter( 'manage_edit-shop_order_columns', array($this, 'salesking_add_columns_shop_order') );
					add_action( 'manage_shop_order_posts_custom_column' , array($this, 'salesking_add_columns_shop_order_content'), 10, 2 );

					/* Filters by agent in orders backend */
					if (current_user_can( 'manage_woocommerce' )){ 
						// Add a dropdown to filter orders by meta
						add_action( 'restrict_manage_posts', [$this, 'display_admin_shop_order_by_meta_filter'] );
						// Process the filter dropdown for orders by Marketing optin
						add_filter( 'request', [$this, 'process_admin_shop_order_marketing_by_meta'], 99 );
						// (Optional) Make a custom meta field searchable from the admin order list search field
						add_filter( 'woocommerce_shop_order_search_fields', [$this, 'shop_order_meta_search_fields'], 10, 1 );
					}
				}
				// Add agent to orders
				add_action( 'woocommerce_admin_order_data_after_order_details', array($this, 'salesking_agent_for_orders'));
				// Add order commissions metabox to orders
				add_action( 'add_meta_boxes', array($this, 'salesking_order_metaboxes') );
				// Save order commissions edits
				add_action( 'save_post', array($this, 'salesking_order_metaboxes_save'), 10, 1 );
				// Add agent to order in quick order view
				add_filter( 'woocommerce_admin_order_preview_get_order_details', array($this,'salesking_agent_for_orders_quick_view'), 10, 2);

				/* Load resources */
				// Load global admin styles
				add_action( 'admin_enqueue_scripts', array($this, 'load_global_admin_resources') ); 
				// Only load scripts and styles in this specific admin page
				add_action( 'admin_enqueue_scripts', array($this, 'load_admin_resources') );
 

				/* Settings */
				// Registers settings
				add_action( 'admin_init', array( $this, 'salesking_settings_init' ) );
				// Renders settings 
				add_action( 'admin_menu', array( $this, 'salesking_settings_page' ) ); 


			}
		});
	}

	
	function add_filter_by_agent_filter() {
		if (!isset( $_GET[ 'filter_by_agent' ] )){
		    if ( isset( $_GET[ 'filter_by_agent' ]) ) {
		        $section = $_GET[ 'filter_by_agent' ];
		        $section = !empty( $section[ 0 ] ) ? $section[ 0 ] : $section[ 1 ];
		    } else {
		        $section = -1;
		    }
		    echo ' <select name="filter_by_agent[]" style="float:none;"><option value="">'.esc_html__('Filter by agent','salesking').'...</option>';

	        $agents = get_users(array(
	    			    'meta_key'     => 'salesking_group',
	    			    'meta_value'   => 'none',
	    			    'meta_compare' => '!=',
	    			));

	        $array_agents = array();
	     	foreach ($agents as $agent){
	     		$selected = $agent->ID == $section ? ' selected="selected"' : '';
	     		echo '<option value="' . $agent->ID . '"' . $selected . '>' . apply_filters('salesking_agent_display_name_filter', $agent->user_login, $agent) . '</option>';
	     	}

		    echo '</select>';
		    echo '<input type="submit" class="button" value="Filter">';
		}
	}

	function filter_users_by_filter_by_agent( $query ) {
	    global $pagenow;

	    if ( is_admin() && 
	         'users.php' == $pagenow && 
	         isset( $_GET[ 'filter_by_agent' ] ) && 
	         is_array( $_GET[ 'filter_by_agent' ] )
	        ) {

	    	$empty = 'yes';
	    	if (!empty($_GET[ 'filter_by_agent' ])){
	    		foreach ($_GET[ 'filter_by_agent' ] as $item){
	    			if (!empty($item)){
	    				$empty = 'no';
	    			}
	    		}
	    	}

	    	if ($empty === 'no'){
		        $section = $_GET[ 'filter_by_agent' ];
		        $section = !empty( $section[ 0 ] ) ? $section[ 0 ] : $section[ 1 ];
		        $meta_query = array(
		            array(
		                'key' => 'salesking_assigned_agent',
		                'value' => $section
		            )
		        );
		        $query->set( 'meta_key', 'salesking_assigned_agent' );
		        $query->set( 'meta_query', $meta_query );
		    }
	    }

	    return $query;
	}

	// Custom function where metakeys / labels pairs are defined
	function get_filter_shop_order_meta( $domain = 'woocommerce' ){
	    // Add below the metakey / label pairs to filter orders
	    $agents = get_users(array(
				    'meta_key'     => 'salesking_group',
				    'meta_value'   => 'none',
				    'meta_compare' => '!=',
				));

	    $array_agents = array();
	 	foreach ($agents as $agent){
	 		$array_agents[$agent->ID] = $agent->display_name;
	 	}
	    return $array_agents;
	}

	function shop_order_meta_search_fields( $meta_keys ){
	    foreach ( $this->get_filter_shop_order_meta() as $meta_key => $label ) {
	        $meta_keys[] = $meta_key;
	    }
	    return $meta_keys;
	}
	function process_admin_shop_order_marketing_by_meta( $vars ) {
	    global $pagenow, $typenow;
	    
	    $filter_id = 'filter_shop_order_by_meta';

	    if ( $pagenow == 'edit.php' && 'shop_order' === $typenow 
	    && isset( $_GET[$filter_id] ) && ! empty($_GET[$filter_id]) ) {
	        $vars['meta_key']   = 'salesking_assigned_agent';
	    	$vars['meta_value']   = sanitize_text_field($_GET[$filter_id]);
	    }
	    return $vars;
	}

	function display_admin_shop_order_by_meta_filter(){
	    global $pagenow, $typenow;

	    if( 'shop_order' === $typenow && 'edit.php' === $pagenow ) {
	        $domain    = 'woocommerce';
	        $filter_id = 'filter_shop_order_by_meta';
	        $current   = isset($_GET[$filter_id])? sanitize_text_field($_GET[$filter_id]) : '';

	        echo '<select name="'.$filter_id.'">
	        <option value="">' . esc_html__('Filter by agent...', $domain) . '</option>';

	        $options = $this->get_filter_shop_order_meta( $domain );

	        foreach ( $options as $key => $label ) {
	            printf( '<option value="%s"%s>%s</option>', $key, 
	                intval($key) === intval($current) ? '" selected="selected"' : '', $label );
	        }
	        echo '</select>';
	    }
	}

	function customize_admin_bar(){
	    global $wp_admin_bar;

	    $wp_admin_bar->remove_menu('my-account');
	    $wp_admin_bar->remove_menu('edit-profile');
	    $wp_admin_bar->add_menu( array(
	        'id' => 'mys-account',
	        'parent' => 'top-secondary',
	        'title' => esc_html__('Click to RETURN to the Sales Agent Dashboard', 'salesking'),
	        'href' => trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true))).'orders',
	    ) );
	}

	function salesking_forbid_other_orders(){
		// Global object containing current admin page
	   global $pagenow;

	   if ( 'post.php' === $pagenow && isset($_GET['post']) && 'shop_order' === get_post_type( $_GET['post'] ) ){

	   		$order_id = sanitize_text_field($_GET['post']);
	   		// check if order is own, else disallow
	   		$assigned_agent = intval(get_post_meta($order_id,'salesking_assigned_agent', true));
	   		if (get_current_user_id() !== $assigned_agent){
	   			// disallow
	   			exit();
	   		}
	   }	
	   
	}

	function salesking_agent_for_orders_quick_view($data, $order){
		$agentassigned = $order->get_meta('salesking_assigned_agent');
		if (!empty($agentassigned)){
			$agent = new WP_User($agentassigned);

			$data['payment_via'] .= '<br><strong>Sales agent</strong>'.$agent->user_login.' ('.$agent->display_name.')';
		}
		return $data;
	}

	function salesking_agent_for_orders($order){
		$agentgroup = get_user_meta( get_current_user_id(), 'salesking_group', true );
		if ((current_user_can( 'manage_woocommerce' )  || (current_user_can( apply_filters('salesking_choose_agent_manage_order_permission', 'set_agent_manage_order')   )) ) && intval(get_option( 'salesking_agents_can_manage_orders_setting', 1 )) === 1){
			?>
			<p class="form-field form-field-wide">
				<label for="salesking_agent_order">
					<?php
					esc_html_e( 'Sales agent managing this order:', 'salesking' );
					$tip = esc_html__('This does NOT assign commission. This only controls which agent can view / edit / handle this order. For commissions, scroll down to the "Sales Agent Commissions" box.','salesking');
					echo ' '.wc_help_tip($tip, false);
					?>
				</label>
				<select id="salesking_agent_order" name="salesking_agent_order" class="wc-enhanced-select">
					<?php
					$agentassigned = $order->get_meta('salesking_assigned_agent');
					
				 	echo '<option value="none" '.selected('none', $agentassigned, false).'>'.esc_html__('- None -', 'salesking').'</option>'; 

				 	$agents = get_users(array(
							    'meta_key'     => 'salesking_group',
							    'meta_value'   => 'none',
							    'meta_compare' => '!=',
							));
				 	?>
					<optgroup label="<?php esc_html_e('Agents', 'salesking'); ?>">
						
					<?php
					foreach ($agents as $agent){
						echo '<option value="'.esc_attr($agent->ID).'" '.selected($agent->ID, $agentassigned, false).'>'.esc_html($agent->user_login).'</option>';
					}
					?>
					</optgroup>
				</select>
			</p>

			<?php
		}
	}

	function salesking_hide_numbers( $counts, $type, $perm ) {
	    global $wpdb;

	    // We only want to modify the counts shown in admin and depending on $perm being 'readable' 
	    if ( ! is_admin() || 'readable' !== $perm ) {
	        return $counts;
	    }

	    // Only modify the counts if the user is not allowed to edit the posts of others
	    $post_type_object = get_post_type_object($type);
	

	    $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND (post_author = %d) GROUP BY post_status";
	    $results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type, get_current_user_id() ), ARRAY_A );
	    $counts = array_fill_keys( get_post_stati(), 0 );

	    foreach ( $results as $row ) {
	        $counts[ $row['post_status'] ] = $row['num_posts'];
	    }

	    return (object) $counts;
	}

	function disable_gutenberg ($current_status, $post_type){
	    if ($post_type === 'salesking_announce') {
	    	return false;
	    }
	    return $current_status;
	}

	function salesking_show_own_customers($query){
	    if( ! is_admin() ){
	        return;
	    }

	    if (isset($query->query_vars['post_type'])){
	    	if ($query->query_vars['post_type'] === 'b2bking_group'){
	    		return;
	    	}
	    }

	    if (isset($query->query_vars['meta_key'])){
	    	if ($query->query_vars['meta_key'] === 'salesking_agent_order'){
	    		return;
	    	}
	    	if ($query->query_vars['meta_key'] === 'salesking_assigned_agent'){
	    		return;
	    	}
	    	if ($query->query_vars['meta_key'] === 'b2bking_customergroup'){
	    		return;
	    	}
	    	if ($query->query_vars['meta_key'] === 'salesking_group'){
	    		return;
	    	}

	    }


	    // get all customers of the user
	    $user_id = get_current_user_id();

	    // first get all customers that have this assigned agent individually
	    $user_ids_assigned = get_users(array(
	                'meta_key'     => 'salesking_assigned_agent',
	                'meta_value'   => $user_id,
	                'meta_compare' => '=',
	                'fields' => 'ids',
	            ));

	    // now get all b2bking groups that have this assigned agent
	    $groups_with_agent = get_posts(
	    	array( 'post_type' => 'b2bking_group',
	                'post_status'=>'publish',
	                'numberposts' => -1,
	                'fields' => 'ids',
	                'meta_query'=> array(
	                    'relation' => 'OR',
	                    array(
	                        'key' => 'salesking_assigned_agent',
	                        'value' => $user_id,
	                        'compare' => '=',
	                    )
	                )
	          )
	    );

	    if (!empty($groups_with_agent)){
	        // get all customers in the above groups with agent
	        $user_ids_in_groups_with_agent = get_users(array(
	                    'meta_key'     => 'b2bking_customergroup',
	                    'meta_value'   => $groups_with_agent,
	                    'meta_compare' => 'IN',
	                    'fields' => 'ids',
	                ));

	        // for all customers with this agent as group, make sure they don't have a different agent individually
	        foreach ($user_ids_in_groups_with_agent as $array_key => $user_id){
	            // check that a different agent is not assigned
	            $assigned_agent = get_user_meta($user_id,'salesking_assigned_agent', true);
	            if (!empty($assigned_agent) && $assigned_agent !== $user_id ){
	                unset($user_ids_in_groups_with_agent[$array_key]);
	            }
	        }
	        $customers = array_merge($user_ids_assigned, $user_ids_in_groups_with_agent);
	    } else {
	        $customers = $user_ids_assigned;
	    }
	 
	    $query->set( 'include', $customers );

	    
	}

	function salesking_show_agents_only_own_orders($query){

		if (isset($query->query_vars['post_type'])){
			if ($query->query_vars['post_type'] === 'b2bking_group'){
				return $query;
			}
		}

		if (isset($query->query_vars['meta_key'])){
			if ($query->query_vars['meta_key'] === 'salesking_agent_order'){
				return $query;
			}
			if ($query->query_vars['meta_key'] === 'salesking_assigned_agent'){
				return $query;
			}
			if ($query->query_vars['meta_key'] === 'b2bking_customergroup'){
				return $query;
			}

		}


		if (isset($query->query_vars['post_type'])){

			if ($query->query_vars['post_type'] === 'shop_order'){
				$agent_orders = get_posts( array( 
					'post_type' => 'shop_order',
					'numberposts' => -1,
					'post_status'    => 'any',
					'fields' => 'ids',
					'meta_key'   => 'salesking_assigned_agent',
					'meta_value' => get_current_user_id(),
				));

				if (empty($agent_orders)){
					$agent_orders = array('invalid');
				}

				$query->query_vars['post__in'] = $agent_orders;
			}
		}


		return $query;

	}

	function assign_agent_registration_approval(){
		$user_id = 0;
		if (isset($_GET['user_id'])){
			$user_id = sanitize_text_field($_GET['user_id']);
		}

		// skip this function if user signed up to be an agent
		if (get_user_meta($user_id,'registration_role_agent', true) !== 'yes'){

			?>
			<div class="b2bking_user_registration_user_data_container_element_label">
			    <?php esc_html_e('Assign sales agent','salesking'); ?>
			</div>
			<select id="salesking_assign_sales_agent" name="salesking_assign_sales_agent" class="salesking_user_settings_select_admin b2bking_user_registration_user_data_container_element_text">
				<?php
				
				$agentassigned = get_user_meta( $user_id, 'salesking_assigned_agent', true );
			 	echo '<option value="none" '.selected('none', $agentassigned, false).'>'.esc_html__('- None -', 'salesking').'</option>'; 

			 	$agents = get_users(array(
						    'meta_key'     => 'salesking_group',
						    'meta_value'   => 'none',
						    'meta_compare' => '!=',
						));
			 	?>
					<optgroup label="<?php esc_html_e('Agents', 'salesking'); ?>">
					
					<?php
				foreach ($agents as $agent){
					echo '<option value="'.esc_attr($agent->ID).'" '.selected($agent->ID, $agentassigned, false).'>'.esc_html($agent->user_login).'</option>';
				}
				?>
				</optgroup>
			</select>
			<br /><br />
			<?php 
			if (!defined('b2bkingcredit_DIR')){
				?>
				<div class="b2bking_user_registration_user_data_container_element_label">
				    <?php esc_html_e('Choose group','salesking'); ?>
				</div>
				<?php
			}
		}
		?>
		

		<?php
	}

	function salesking_b2bking_groups_metaboxes($post_type){
		$post_types = array('b2bking_group');     //limit meta box to certain post types
		if ( in_array( $post_type, $post_types ) ) {

		   add_meta_box(
		       'salesking_group_assigned_agent_metabox'
		       ,esc_html__( 'Assign this group to a sales agent (optional, is overwritten by agent assigned in user profile)', 'salesking' )
		       ,array( $this, 'salesking_group_assigned_agent_metabox_content' )
		       ,$post_type
		       ,'advanced'
		       ,'high'
		   );

		}
	}

	function salesking_group_assigned_agent_metabox_content(){
		global $post;
		?>

    	<div class="salesking_user_shipping_payment_methods_container">
    		<div class="salesking_user_shipping_payment_methods_container_top">
    			<div class="salesking_user_shipping_payment_methods_container_top_title">
    				<?php esc_html_e('Agent Settings','salesking'); ?>
    			</div>		
    		</div>

    		<div class="salesking_user_settings_container">
    			<div class="salesking_user_settings_container_column">
    				<div class="salesking_user_settings_container_column_title">
    					<svg class="salesking_user_settings_container_column_title_icon_right" xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="none" viewBox="0 0 45 45">
    					  <path fill="#C4C4C4" d="M22.382 7.068c-3.876 0-7.017 3.668-7.017 8.193 0 3.138 1.51 5.863 3.73 7.239l-2.573 1.192-6.848 3.176c-.661.331-.991.892-.991 1.686v7.541c.054.943.62 1.822 1.537 1.837h24.36c1.048-.091 1.578-.935 1.588-1.837v-7.541c0-.794-.33-1.355-.992-1.686l-6.6-3.175-2.742-1.3c2.128-1.407 3.565-4.073 3.565-7.132 0-4.525-3.142-8.193-7.017-8.193zM11.063 9.95c-1.667.063-2.99.785-3.993 1.935a7.498 7.498 0 00-1.663 4.663c.068 2.418 1.15 4.707 3.076 5.905l-7.69 3.573c-.529.198-.793.661-.793 1.389v6.053c.041.802.458 1.477 1.24 1.488h5.11v-6.401c.085-1.712.888-3.095 2.333-3.77l5.109-2.43a4.943 4.943 0 001.141-.944c-2.107-3.25-2.4-7.143-1.041-10.567-.883-.54-1.876-.888-2.829-.894zm22.822 0c-1.09.023-2.098.425-2.926.992 1.32 3.455.956 7.35-.993 10.37.43.495.877.876 1.34 1.14l4.912 2.333c1.496.82 2.267 2.216 2.282 3.77v6.401h5.259c.865-.074 1.233-.764 1.241-1.488v-6.053c0-.662-.264-1.124-.794-1.39l-7.59-3.622c1.968-1.452 2.956-3.627 2.976-5.855-.053-1.763-.591-3.4-1.663-4.663-1.12-1.215-2.51-1.922-4.044-1.935z"/>
    					</svg>
    					<?php esc_html_e('Agent assigned to this group','salesking'); ?>
    				</div>

    				<select name="salesking_group_agent" id="salesking_group_agent" class="salesking_user_settings_select">
    					<?php
    						$agentassigned = get_post_meta( $post->ID, 'salesking_assigned_agent', true );
    					 	echo '<option value="none" '.selected('none', $agentassigned, false).'>'.esc_html__('- None -', 'salesking').'</option>'; 

    					 	$agents = get_users(array(
 	    						    'meta_key'     => 'salesking_group',
 	    						    'meta_value'   => 'none',
 	    						    'meta_compare' => '!=',
 	    						));
    					 	?>
  	    					<optgroup label="<?php esc_html_e('Agents', 'salesking'); ?>">
  	    					
  	    					<?php
	    					foreach ($agents as $agent){
	    						echo '<option value="'.esc_attr($agent->ID).'" '.selected($agent->ID, $agentassigned, false).'>'.esc_html($agent->user_login).'</option>';
	    					}
		    				?>
    					</optgroup>
    				</select>
				</div>
	    	</div>

			<!-- Information panel -->
			<div class="salesking_user_settings_information_box">
				<svg class="salesking_group_payment_shipping_information_box_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 36 36">
				  <path fill="#358BBB" d="M18 0C8.06 0 0 8.06 0 18s8.06 18 18 18 18-8.06 18-18S27.94 0 18 0zm0 28.446a1.607 1.607 0 110-3.213 1.607 1.607 0 010 3.213zm2.527-8.819a1.941 1.941 0 00-1.241 1.8v.912a.322.322 0 01-.322.322h-1.928a.322.322 0 01-.322-.322v-.864c0-.928.27-1.844.8-2.607a4.49 4.49 0 012.093-1.643c1.366-.527 2.25-1.672 2.25-2.921 0-1.772-1.732-3.215-3.857-3.215s-3.857 1.443-3.857 3.215v.305a.322.322 0 01-.322.321h-1.928a.322.322 0 01-.322-.321v-.305c0-1.58.691-3.054 1.945-4.15C14.721 9.095 16.312 8.517 18 8.517c1.688 0 3.279.582 4.484 1.635 1.253 1.097 1.945 2.572 1.945 4.15 0 2.323-1.531 4.412-3.902 5.324z"/>
				</svg>
				<?php esc_html_e('All users in this group will be assigned to this agent, unless differently assigned in their user profiile page.','salesking'); ?>
			</div>
		</div>

		<?php
	}

	// Register post type earning

	public static function salesking_register_post_type_earning(){
			// Build labels and arguments
		    $labels = array(
		        'name'                  => esc_html__( 'Earning', 'salesking' ),
		        'singular_name'         => esc_html__( 'Earning', 'salesking' ),
		        'all_items'             => esc_html__( 'Earnings', 'salesking' ),
		        'menu_name'             => esc_html__( 'Earnings', 'salesking' ),
		        'add_new'               => esc_html__( 'Create new earning', 'salesking' ),
		        'add_new_item'          => esc_html__( 'Create new customer earning', 'salesking' ),
		        'edit'                  => esc_html__( 'Edit', 'salesking' ),
		        'edit_item'             => esc_html__( 'Edit earning', 'salesking' ),
		        'new_item'              => esc_html__( 'New earning', 'salesking' ),
		        'view_item'             => esc_html__( 'View earning', 'salesking' ),
		        'view_items'            => esc_html__( 'View earnings', 'salesking' ),
		        'search_items'          => esc_html__( 'Search earnings', 'salesking' ),
		        'not_found'             => esc_html__( 'No earnings found', 'salesking' ),
		        'not_found_in_trash'    => esc_html__( 'No earnings found in trash', 'salesking' ),
		        'parent'                => esc_html__( 'Parent earning', 'salesking' ),
		        'featured_image'        => esc_html__( 'Earning image', 'salesking' ),
		        'set_featured_image'    => esc_html__( 'Set earning image', 'salesking' ),
		        'remove_featured_image' => esc_html__( 'Remove earning image', 'salesking' ),
		        'use_featured_image'    => esc_html__( 'Use as earning image', 'salesking' ),
		        'insert_into_item'      => esc_html__( 'Insert into earning', 'salesking' ),
		        'uploaded_to_this_item' => esc_html__( 'Uploaded to this earning', 'salesking' ),
		        'filter_items_list'     => esc_html__( 'Filter earnings', 'salesking' ),
		        'items_list_navigation' => esc_html__( 'Earnings navigation', 'salesking' ),
		        'items_list'            => esc_html__( 'Earnings list', 'salesking' )
		    );
		    $args = array(
		        'label'                 => esc_html__( 'Earning', 'salesking' ),
		        'description'           => esc_html__( 'Agent earnings', 'salesking' ),
		        'labels'                => $labels,
		        'supports'              => array( 'title' ),
		        'hierarchical'          => false,
		        'public'                => false,
		        'show_ui'               => true,
		        'show_in_menu'          => false,
		        'menu_position'         => 105,
		        'show_in_admin_bar'     => true,
		        'show_in_nav_menus'     => false,
		        'can_export'            => true,
		        'has_archive'           => false,
		        'exclude_from_search'   => true,
		        'publicly_queryable'    => false,
		        'capability_type'       => 'product',
		        'map_meta_cap'          => true,
		        'show_in_rest'          => true,
		        'rest_base'             => 'salesking_earning',
		        'rest_controller_class' => 'WP_REST_Posts_Controller',
		    );

			// Actually register the post type
			register_post_type( 'salesking_earning', $args );
	}

	// Register messages
	public static function salesking_register_post_type_message() {
		// Build labels and arguments
	    $labels = array(
	        'name'                  => esc_html__( 'Messages', 'salesking' ),
	        'singular_name'         => esc_html__( 'Message', 'salesking' ),
	        'all_items'             => esc_html__( 'Messages', 'salesking' ),
	        'menu_name'             => esc_html__( 'Messages', 'salesking' ),
	        'add_new'               => esc_html__( 'New message', 'salesking' ),
	        'add_new_item'          => esc_html__( 'New message', 'salesking' ),
	        'edit'                  => esc_html__( 'Edit', 'salesking' ),
	        'edit_item'             => esc_html__( 'Edit message', 'salesking' ),
	        'new_item'              => esc_html__( 'New message', 'salesking' ),
	        'view_item'             => esc_html__( 'View message', 'salesking' ),
	        'view_items'            => esc_html__( 'View messages', 'salesking' ),
	        'search_items'          => esc_html__( 'Search messages', 'salesking' ),
	        'not_found'             => esc_html__( 'No messages found', 'salesking' ),
	        'not_found_in_trash'    => esc_html__( 'No messages found in trash', 'salesking' ),
	        'parent'                => esc_html__( 'Parent message', 'salesking' ),
	        'featured_image'        => esc_html__( 'Message image', 'salesking' ),
	        'set_featured_image'    => esc_html__( 'Set message image', 'salesking' ),
	        'remove_featured_image' => esc_html__( 'Remove message image', 'salesking' ),
	        'use_featured_image'    => esc_html__( 'Use as message image', 'salesking' ),
	        'insert_into_item'      => esc_html__( 'Insert into message', 'salesking' ),
	        'uploaded_to_this_item' => esc_html__( 'Uploaded to this message', 'salesking' ),
	        'filter_items_list'     => esc_html__( 'Filter messages', 'salesking' ),
	        'items_list_navigation' => esc_html__( 'Message navigation', 'salesking' ),
	        'items_list'            => esc_html__( 'Messages list', 'salesking' )
	    );
	    $args = array(
	        'label'                 => esc_html__( 'Message', 'salesking' ),
	        'description'           => esc_html__( 'This is where you can send new messages', 'salesking' ),
	        'labels'                => $labels,
	        'supports'              => array('title'),
	        'hierarchical'          => false,
	        'public'                => false,
	        'publicly_queryable' 	=> false,
	        'show_ui'               => true,
	        'show_in_menu'          => 'salesking',
	        'menu_position'         => 100,
	        'show_in_admin_bar'     => true,
	        'show_in_nav_menus'     => false,
	        'can_export'            => false,
	        'has_archive'           => false,
	        'exclude_from_search'   =>  true,
	        'rewrite'               => false,
	        'capability_type'       => 'product',
	        'show_in_rest'          => true,
	        'rest_base'             => 'salesking_message',
	        'rest_controller_class' => 'WP_REST_Posts_Controller',
	    );

		// Actually register the post type
		register_post_type( 'salesking_message', $args );
	}

	// Add Metaboxes to orders
	function salesking_order_metaboxes($post_type) {
		if (current_user_can( 'manage_woocommerce' )){
		    $post_types = array('shop_order');     //limit meta box to certain post types
	       	if ( in_array( $post_type, $post_types ) ) {
	       		global $post;
	       		$order_id = $post->ID;
	           add_meta_box(
	               'salesking_order_commission_metabox'
	               ,esc_html__( 'Sales Agents Commissions', 'salesking' )
	               ,array( $this, 'salesking_order_commission_metabox_content' )
	               ,$post_type
	               ,'advanced'
	               ,'high'
	           );
	        }
	    }
	}

	function salesking_order_metaboxes_save($post_id){
		if (isset($_POST['_inline_edit'])){
			if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')){
			    return;
			}
		}
		if (isset($_REQUEST['bulk_edit'])){
		    return;
		}
		if (get_post_type($post_id) === 'shop_order'){

			$order_id = $post_id;
			$order = wc_get_order($order_id);
			$earning_id = get_post_meta($order_id,'salesking_earning_id', true);
			$agent_id = get_post_meta($earning_id,'agent_id', true);	
			$agent_data = get_userdata($agent_id);
			$earnings_total = get_post_meta($earning_id, 'salesking_commission_total', true);
			$earnings_status = get_post_meta($earning_id, 'order_status', true);
			$delete_earning = false;

			if (isset($_POST['salesking_main_commission_order_value_edited'])){
				$edited_earnings = sanitize_text_field($_POST['salesking_main_commission_order_value_edited']);
				if (!empty($edited_earnings) || intval($edited_earnings) === 0){

					// update commission
					update_post_meta($earning_id,'salesking_commission_total', $edited_earnings);

					if (in_array($earnings_status,apply_filters('salesking_earning_completed_statuses', array('completed')))){

						// update agent outstanding balance
						$old_balance = get_user_meta($agent_id,'salesking_outstanding_earnings', true);
						$modification = floatval($earnings_total) - floatval($edited_earnings);
						$new_balance = floatval($old_balance)-$modification;


						// user balance history start
						$amount = 'MANUAL MODIFICATION';
						$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
						$note = 'MANUAL MODIFICATION';
						$user_balance_history = sanitize_text_field(get_user_meta($agent_id,'salesking_user_balance_history', true));
						$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
						update_user_meta($agent_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
						// user balance history end


						update_user_meta($agent_id,'salesking_outstanding_earnings', $new_balance);

					}
				}
				if (floatval($edited_earnings) < 0.01){
					// delete earning
					$delete_earning = true;
				}
			}

			// check all other agents in the order
			if (isset($_POST['salesking_agents_ids_string'])){
				$agent_ids = sanitize_text_field($_POST['salesking_agents_ids_string']);
				$agent_ids = explode(':', $agent_ids);
				$agent_ids = array_unique(array_filter($agent_ids));
				foreach ($agent_ids as $ag_id){
					$edited_earnings = sanitize_text_field($_POST['salesking_commission_order_value_edited_'.$ag_id]);

					// if delete main, delete all
					if (isset($_POST['salesking_main_commission_order_value_edited'])){
						if (floatval(sanitize_text_field($_POST['salesking_main_commission_order_value_edited'])) < 0.01){
							// remove earnings
							$agearn = get_post_meta($earning_id,'parent_agent_id_'.$ag_id.'_earnings', true);
							if (in_array($earnings_status,apply_filters('salesking_earning_completed_statuses', array('completed')))){

								// update agent outstanding balance
								$old_balance = get_user_meta($ag_id,'salesking_outstanding_earnings', true);
								$new_balance = floatval($old_balance)-floatval($agearn);

								// user balance history start
								$amount = 'MANUAL MODIFICATION';
								$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
								$note = 'MANUAL MODIFICATION';
								$user_balance_history = sanitize_text_field(get_user_meta($ag_id,'salesking_user_balance_history', true));
								$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
								update_user_meta($ag_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
								// user balance history end


								update_user_meta($ag_id,'salesking_outstanding_earnings', $new_balance);
								continue;
							}
						}
					}

					if (!empty($edited_earnings)){
						// update commission
						$old_earnings = get_post_meta($earning_id,'parent_agent_id_'.$ag_id.'_earnings', true);
						update_post_meta($earning_id,'parent_agent_id_'.$ag_id.'_earnings', $edited_earnings);

						// update agent outstanding balance
						if (in_array($earnings_status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
							$old_balance = get_user_meta($ag_id,'salesking_outstanding_earnings', true);
							$modification = floatval($old_earnings) - floatval($edited_earnings);
							$new_balance = floatval($old_balance)-$modification;

							// user balance history start
							$amount = 'MANUAL MODIFICATION';
							$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
							$note = 'MANUAL MODIFICATION';
							$user_balance_history = sanitize_text_field(get_user_meta($ag_id,'salesking_user_balance_history', true));
							$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
							update_user_meta($ag_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
							// user balance history end


							update_user_meta($ag_id,'salesking_outstanding_earnings', $new_balance);
						}
						
					}
				}
			}

			if ($delete_earning === true){
				wp_delete_post($earning_id);
				update_post_meta($order_id, 'salesking_earning_id', '');
			} else {
				// check new commission
				if (isset($_POST['salesking_group_agent'])){
					$new_commission_agent = sanitize_text_field($_POST['salesking_group_agent']);
					$commission_value = sanitize_text_field($_POST['salesking_commission_value_new']);

					// set agent
					if ($new_commission_agent !== 'none' && !empty($commission_value)){
						$agent_id = $new_commission_agent;
						// if first commission
						if (empty($earning_id) ){
							// first commission for order
							// Create transaction
							$earning = array(
							    'post_title' => sanitize_text_field(esc_html__('Earning','salesking')),
							    'post_status' => 'publish',
							    'post_type' => 'salesking_earning',
							    'post_author' => 1,
							);
							$earning_post_id = wp_insert_post($earning);

							// set meta
							update_post_meta($earning_post_id, 'time', time());
							update_post_meta($earning_post_id, 'order_id', $order_id);
							update_post_meta($earning_post_id, 'customer_id', $order->get_customer_id());
							update_post_meta($earning_post_id, 'order_status', $order->get_status());
							update_post_meta($earning_post_id, 'created_in', 'admin_backend');

							if ($agent_id !== 0){
								update_post_meta($earning_post_id, 'agent_id', $agent_id);
							}

							if ($commission_value > 0){
								update_post_meta($earning_post_id, 'commission_rules_total', $commission_value);
							}

							update_post_meta($order_id, 'salesking_earning_id', $earning_post_id);
							update_post_meta($earning_post_id, 'salesking_commission_total', $commission_value);

							// update agent outstanding balance
							if (in_array($order->get_status(),apply_filters('salesking_earning_completed_statuses', array('completed')))){

								$old_balance = get_user_meta($agent_id,'salesking_outstanding_earnings', true);
								$new_balance = floatval($old_balance)+$commission_value;

								// user balance history start
								$amount = 'MANUAL MODIFICATION';
								$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
								$note = 'MANUAL MODIFICATION';
								$user_balance_history = sanitize_text_field(get_user_meta($ag_id,'salesking_user_balance_history', true));
								$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
								update_user_meta($ag_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
								// user balance history end

								update_user_meta($agent_id,'salesking_outstanding_earnings', $new_balance);
							}
						}
					}

					// set new parent agent
					$commission_value = sanitize_text_field($_POST['salesking_commission_value_new_parent']);
					if ($new_commission_agent !== 'none' && !empty($commission_value)){
						$agent_id = $new_commission_agent;
						$earning_post_id = $earning_id;
						$agents_of_earning = get_post_meta($earning_post_id, 'agents_of_earning', true);
						if (empty($agents_of_earning)){
							$agents_of_earning = array();
						}
						array_push($agents_of_earning, $agent_id);

						update_post_meta($earning_post_id, 'parent_agent_id_'.$agent_id, $agent_id);
						update_post_meta($earning_post_id, 'parent_agent_id_'.$agent_id.'_earnings', $commission_value);
						update_post_meta($earning_post_id, 'agents_of_earning', $agents_of_earning);

						// update agent outstanding balance
						if (in_array($earnings_status,apply_filters('salesking_earning_completed_statuses', array('completed')))){

							$old_balance = get_user_meta($agent_id,'salesking_outstanding_earnings', true);
							$new_balance = floatval($old_balance)+$commission_value;

							// user balance history start
							$amount = 'MANUAL MODIFICATION';
							$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
							$note = 'MANUAL MODIFICATION';
							$user_balance_history = sanitize_text_field(get_user_meta($ag_id,'salesking_user_balance_history', true));
							$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
							update_user_meta($ag_id,'salesking_user_balance_history', $user_balance_history.';'.$new_entry);
							// user balance history end

							update_user_meta($agent_id,'salesking_outstanding_earnings', $new_balance);
						}
					
					}
				}
			}

			
	
		}
	}

	function salesking_order_commission_metabox_content(){
		global $post;
		$order_id = $post->ID;
		$earning_id = get_post_meta($order_id,'salesking_earning_id', true);

		if (!empty($earning_id)){

			$agent_id = get_post_meta($earning_id,'agent_id', true);	
			$agent_data = get_userdata($agent_id);
			$agent_name = $agent_data->user_login;
			$earnings_total = get_post_meta($earning_id, 'salesking_commission_total', true);
			$agents_of_earning = get_post_meta($earning_id, 'agents_of_earning', true);
			if (empty($agents_of_earning)){
				$agents_of_earning = array();
			}
			?>
			<table class="wc-order-totals">
				<tbody>
					<tr>
						<td class="label"><?php esc_html_e('Agent:','salesking');?></td>
						<td width="1%"></td>
						<td class="total">
							<span class="woocommerce-Price-amount amount"><bdi><?php echo esc_html($agent_name);?></bdi>
						</td>
						<td><span class="dashicons dashicons-edit salesking_main_edit_icon" data-edit="commissions"></span></td>
					</tr>
						
				
					<tr>
						<td class="label"><?php esc_html_e('Commission:','salesking');?></td>
						<td width="1%"></td>
						<td class="total">
							<span class="woocommerce-Price-amount amount salesking_main_commission_order"><?php echo wc_price($earnings_total);?></span>
							<input type="hidden" id="salesking_main_commission_order_value" value="<?php echo esc_attr($earnings_total);?>">	
						</td>
					</tr>
					<?php
					// for all other agents of earning
					$agents_string = '';
					foreach ($agents_of_earning as $ag_id){
						$agents_string.=$ag_id.':';
						$agent_data = get_userdata($ag_id);
						$agent_name = $agent_data->user_login;
						$earnings_total = get_post_meta($earning_id, 'parent_agent_id_'.$ag_id.'_earnings', true);

						?>
						<tr></tr>
						<tr>
							<td class="label"><?php esc_html_e('Agent (parent):','salesking');?></td>
							<td width="1%"></td>
							<td class="total">
								<span class="woocommerce-Price-amount amount"><bdi><?php echo esc_html($agent_name);?></bdi>
							</td>
							<td><span class="dashicons dashicons-edit salesking_edit_icon" data-edit="commissions"></span>
								<input type="hidden" class="salesking_edit_icon_agent" value="<?php echo esc_attr($ag_id);?>"></td>
						</tr>
							
						
						<tr>
							<td class="label"><?php esc_html_e('Commission:','salesking');?></td>
							<td width="1%"></td>
							<td class="total">
								<span class="woocommerce-Price-amount amount salesking_commission_order_<?php echo esc_attr($ag_id);?>"><?php echo wc_price($earnings_total);?></span>
								<input type="hidden" class="salesking_commission_order_value_<?php echo esc_attr($ag_id);?>" value="<?php echo esc_attr($earnings_total);?>">	
							</td>
						</tr>
						<?php
					}

					?>
				</tbody>
				<input type="hidden" name="salesking_agents_ids_string" value="<?php echo esc_attr($agents_string);?>">
			</table>
			<?php

			// Add new parent agent
			?>
			<br><div id="salesking_add_new_parent_commission"><?php esc_html_e('Add New Parent Agent Commission','salesking');?></div>
			<?php

			?>
			<select name="salesking_group_agent" id="salesking_group_agent" class="salesking_user_settings_select">
			<?php
			 	echo '<option value="none">'.esc_html__('- Choose Agent -', 'salesking').'</option>'; 

			 	$agents = get_users(array(
				    'meta_key'     => 'salesking_group',
				    'meta_value'   => 'none',
				    'meta_compare' => '!=',
				));
			 	?>
				<optgroup label="<?php esc_html_e('Agents', 'salesking'); ?>">
				
				<?php
					foreach ($agents as $agent){
						if(!in_array($agent->ID, $agents_of_earning) && intval($agent->ID) !== intval($agent_id)){
							// if self is agent, dont show self
							echo '<option value="'.esc_attr($agent->ID).'">'.esc_html($agent->user_login).'</option>';
						}
						
					}
				?>
				</optgroup>
			</select>
			<input type="number" step="0.01" name="salesking_commission_value_new_parent" class="salesking_custom_field_settings_metabox_top_column_sort_text salesking_975" placeholder="<?php esc_html_e('Enter commission value...', 'salesking'); ?>">

			<?php

		} else {
			?>
			
			<div id="salesking_add_new_commission"><?php esc_html_e('Add New Commission','salesking');?></div>
			<?php

			?>
			<select name="salesking_group_agent" id="salesking_group_agent" class="salesking_user_settings_select">
			<?php
			 	echo '<option value="none">'.esc_html__('- Choose Agent -', 'salesking').'</option>'; 

			 	$agents = get_users(array(
				    'meta_key'     => 'salesking_group',
				    'meta_value'   => 'none',
				    'meta_compare' => '!=',
				));
			 	?>
				<optgroup label="<?php esc_html_e('Agents', 'salesking'); ?>">
				
				<?php
					foreach ($agents as $agent){
						// if self is agent, dont show self
						echo '<option value="'.esc_attr($agent->ID).'">'.esc_html($agent->user_login).'</option>';
					}
				?>
				</optgroup>
			</select>
			<input type="number" step="0.01" name="salesking_commission_value_new" class="salesking_custom_field_settings_metabox_top_column_sort_text salesking_975" placeholder="<?php esc_html_e('Enter commission value...', 'salesking'); ?>">
			<?php
		}

		
	}

	// Add Metaboxes to message
	function salesking_message_metaboxes($post_type) {
	    $post_types = array('salesking_message');     //limit meta box to certain post types
       	if ( in_array( $post_type, $post_types ) ) {
           add_meta_box(
               'salesking_message_details_metabox'
               ,esc_html__( 'Thread Details', 'salesking' )
               ,array( $this, 'salesking_message_details_metabox_content' )
               ,$post_type
               ,'advanced'
               ,'high'
           );
           add_meta_box(
               'salesking_message_messaging_metabox'
               ,esc_html__( 'Messages', 'salesking' )
               ,array( $this, 'salesking_message_messaging_metabox_content' )
               ,$post_type
               ,'advanced'
               ,'high'
           );
       }
	}


	


	// Add custom columns to Groups menu
	function salesking_add_columns_group_menu($columns) {

		$columns_initial = $columns;
		
		// rename title
		$columns = array(
			'title' => esc_html__( 'Group name', 'salesking' ),
			'salesking_user_number' => esc_html__( 'Number of agents', 'salesking' ),
			'salesking_max_discount' => esc_html__( 'Maximum discount allowed', 'salesking' ),

		);

		$columns = array_slice($columns_initial, 0, 1, true) + $columns;

	    return $columns;
	}

	// Add groups custom columns data
	function salesking_columns_group_data( $column, $post_id ) {
	    switch ( $column ) {

	        case 'salesking_user_number' :
	        	$users = get_users(array(
				    'meta_key'     => 'salesking_group',
				    'meta_value'   => $post_id,
				    'fields' => 'ids',
				));	

	            echo '<strong>'.esc_html(count($users)).'</strong>';
	            break;

	            case 'salesking_max_discount' :
	            	$discount = get_post_meta($post_id, 'salesking_group_max_discount', true);

                echo '<strong>'.esc_html($discount).'%</strong>';
                break;

	    }
	}

	// Add custom columns to RULES menu
	function salesking_add_columns_group_menu_rules($columns) {

		$columns_initial = $columns;
		
		// rename title
		$columns = array(
			'title' => esc_html__( 'Rule name', 'salesking' ),
			'salesking_commission' => esc_html__( 'Commission', 'salesking' ),

		);

		$columns = array_slice($columns_initial, 0, 1, true) + $columns;

	    return $columns;
	}

	// Add groups custom columns data
	function salesking_columns_group_data_rules( $column, $post_id ) {
	    switch ( $column ) {

	        case 'salesking_commission' :
	        	$rule_type = get_post_meta($post_id,'salesking_rule_what', true);
	        	$howmuch = get_post_meta($post_id,'salesking_rule_howmuch', true);
	        	if ($rule_type === 'percentage'){
	        		$text = $howmuch.'%';
	        	} else if ($rule_type === 'fixed'){
	        		$text = wc_price($howmuch);
	        	} else {
	        		$text = '-';
	        	}

	            echo '<strong>'.wp_kses( $text, array( 'span' => true, 'bdi' => true ) ).'</strong>';
	            break;


	    }
	}

	// Conversation Details Metabox Content
	function salesking_message_details_metabox_content(){

		// If current page is ADD New Conversation
		if(get_current_screen()->action === 'add'){
			?>
			<div id="salesking_message_details_wrapper">
				<div id="salesking_message_user_container">
					<?php esc_html_e('Agent: ','salesking'); ?>
					<?php 
					$included_ids = get_users(array(
							    'meta_key'     => 'salesking_group',
							    'meta_value'   => 'none',
							    'meta_compare' => '!=',
							    'fields' => 'ids',
							));

					wp_dropdown_users($args = array('id' => 'salesking_message_user_input', 'name'=>'salesking_message_user_input', 'show' => 'user_login', 'include' => $included_ids)); 

					?>
				</div>
			</div>
			<?php
		} else {
			// just display user
			global $post;
			$user = get_post_meta( $post->ID, 'salesking_message_user', true );
			if ($user === 'shop'){
				$user = get_post_meta ($post->ID, 'salesking_message_message_1_author', true);
			}
			echo '
			<div id="salesking_message_details_wrapper">
			<div id="salesking_message_user_container">'.esc_html__('Agent: ', 'salesking').'&nbsp;<strong>'.esc_html($user).'</strong></div></div>';
		}
	}

	// Conversation Details Metabox Content
	function salesking_message_messaging_metabox_content(){

		// If current page is ADD New Conversation
		if(get_current_screen()->action === 'add'){
			?>
			<textarea name="salesking_message_start_message" id="salesking_message_start_message" placeholder="<?php esc_html_e('Enter your message here...','salesking');?>" required></textarea>
			<?php
		} else {
			// Display Conversation
			// get number of messages
			global $post;
			$nr_messages = get_post_meta ($post->ID, 'salesking_message_messages_number', true);
			
			?>
			<div id="salesking_message_messages_container">
				<?php	
				// loop through and display messages
				for ($i = 1; $i <= $nr_messages; $i++) {
				    // get message details
				    $message = get_post_meta ($post->ID, 'salesking_message_message_'.$i, true);
				    $author = get_post_meta ($post->ID, 'salesking_message_message_'.$i.'_author', true);
				    $time = get_post_meta ($post->ID, 'salesking_message_message_'.$i.'_time', true);
				    // check if message author is self
				    if (wp_get_current_user()->user_login === $author){
				    	$self = ' salesking_message_message_self';
				    } else {
				    	$self = '';
				    }
				    // build time string
					    // if today
					    if((time()-$time) < 86400){
					    	// show time
					    	$timestring = date_i18n( 'h:i A', $time+(get_option('gmt_offset')*3600) );
					    } else if ((time()-$time) < 172800){
					    // if yesterday
					    	$timestring = 'Yesterday at '.date_i18n( 'h:i A', $time+(get_option('gmt_offset')*3600) );
					    } else {
					    // date
					    	$timestring = date_i18n( get_option('date_format'), $time+(get_option('gmt_offset')*3600) ); 
					    }
				    ?>
				    <div class="salesking_message_message <?php echo esc_attr($self); ?>">
				    	<?php echo wp_kses( nl2br($message), array( 'br' => true ) ); ?>
				    	<div class="salesking_message_message_time">
				    		<?php echo esc_html($author).' - '; ?>
				    		<?php echo esc_html($timestring); ?>
				    	</div>
				    </div>
				    <?php
				}
				?>
			</div>
			<textarea name="salesking_message_admin_new_message" id="salesking_message_admin_new_message" placeholder="<?php esc_html_e('Enter your message here...','salesking');?>" ></textarea><br /><br />
			<button type="submit" class="button button-primary button-large"><?php esc_html_e('Send message'); ?></button>

			<?php
		}
		
	}


	public function salesking_messages_menu_order_count() {
		global $submenu;

		// New messages are: How many conversations are not "resolved" AND do not have a response from admin.

		// first get all conversations that are new or open
		$new_open_conversations = get_posts( array( 
			'post_type' => 'salesking_message',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields' => 'ids',
		));

		// go through all of them to find which ones have the latest response from someone who is a sales rep
		$message_nr = 0;
		foreach ($new_open_conversations as $conversation){
			// check latest response and role
			$conversation_msg_nr = get_post_meta($conversation, 'salesking_message_messages_number', true);
			$latest_message_author = get_post_meta($conversation, 'salesking_message_message_'.$conversation_msg_nr.'_author', true);
			// Get the user object.
			$user = get_user_by('login', $latest_message_author);
			if (is_object($user)){
				$agent_group = get_user_meta($user->ID, 'salesking_group', true);
				if ($agent_group !== 'none' && !empty($agent_group)){
					$message_nr++;	
				}
			}
		}

		if ( $message_nr ) {
			foreach ( $submenu['salesking'] as $key => $menu_item ) {
				if ( 0 === strpos( $menu_item[0], esc_html_x( 'Messages', 'Admin menu name', 'salesking' ) ) ) {
					$submenu['salesking'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $message_nr ) . '"><span class="processing-count">' . number_format_i18n( $message_nr ) . '</span></span>'; 
					break;
				}
			}
		}
	}

	// Save message Metabox Content
	function salesking_save_message_metaboxes($post_id){
		if (isset($_POST['_inline_edit'])){
			if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')){
			    return;
			}
		}
		if (isset($_REQUEST['bulk_edit'])){
		    return;
		}
		if (get_post_type($post_id) === 'salesking_message'){
			$meta_user = sanitize_text_field(filter_input(INPUT_POST, 'salesking_message_user_input'));
			if ($meta_user !== NULL && trim($meta_user) !== ''){
				// meta user is user ID . Get user login
				$user_login = get_user_by('id', $meta_user)->user_login;
				update_post_meta( $post_id, 'salesking_message_user', sanitize_text_field($user_login));
			}

			$meta_conversation_start_message = sanitize_textarea_field(filter_input(INPUT_POST, 'salesking_message_start_message'));
			if ($meta_conversation_start_message !== NULL && trim($meta_conversation_start_message) !== ''){
				update_post_meta( $post_id, 'salesking_message_message_1', sanitize_textarea_field($meta_conversation_start_message));
				update_post_meta( $post_id, 'salesking_message_message_1_author', wp_get_current_user()->user_login );
				update_post_meta( $post_id, 'salesking_message_message_1_time', time() );
				update_post_meta( $post_id, 'salesking_message_messages_number', 1);
				update_post_meta( $post_id, 'salesking_message_type', 'message');

				// send email notification
				do_action( 'salesking_new_message', get_user_by('id', $meta_user)->user_email, $meta_conversation_start_message, get_current_user_id(), $post_id );
			}

			$meta_admin_new_message = sanitize_textarea_field(filter_input(INPUT_POST, 'salesking_message_admin_new_message'));
			if ($meta_admin_new_message !== NULL && trim($meta_admin_new_message) !== ''){
				$nr_messages = get_post_meta ($post_id, 'salesking_message_messages_number', true);
				$current_message_nr = $nr_messages+1;

				update_post_meta( $post_id, 'salesking_message_message_'.$current_message_nr, sanitize_textarea_field($meta_admin_new_message));
				update_post_meta( $post_id, 'salesking_message_messages_number', $current_message_nr);
				update_post_meta( $post_id, 'salesking_message_message_'.$current_message_nr.'_author', wp_get_current_user()->user_login );
				update_post_meta( $post_id, 'salesking_message_message_'.$current_message_nr.'_time', time() );

				$currentuser = wp_get_current_user();
				$other_party = get_post_meta($post_id, 'salesking_message_user', true);
				if ($other_party === $currentuser->user_login){
					$other_party = get_post_meta($post_id, 'salesking_message_message_1_author', true);
				}
				if ($other_party === 'shop'){
					$other_party = get_post_meta($post_id, 'salesking_message_message_1_author', true);
				}

				do_action( 'salesking_new_message', get_user_by('login', $other_party)->user_email, $meta_admin_new_message , get_current_user_id(), $post_id );
				
			}
		}
	}

	// Add custom columns to message menu
	function salesking_add_columns_shop_order($columns) {

		$columns_initial = $columns;
		
		// rename title
		$columns = array(
			'salesking_agent' => esc_html__( 'Agent Assigned', 'salesking' ),
		);
		$columns = $columns_initial + $columns;

	    return $columns;
	}

	function salesking_add_columns_shop_order_content($column, $post_id ){
		 switch ( $column ) {

	        case 'salesking_agent' :

            	$user_id = get_post_meta($post_id, 'salesking_assigned_agent', true);
            	if (!empty($user_id)){
            		$user = new WP_User($user_id);
            		echo '<a href="'.esc_attr(get_edit_user_link($user->ID)).'">'.esc_html($user->display_name).'</a>';
            	} else {
            		echo '—';
            	}
	           
	           
	            break;
	    }
	}

	// Add custom columns to message menu
	function salesking_add_columns_group_menu_message($columns) {

		$columns_initial = $columns;
		
		// rename title
		$columns = array(
			'salesking_agent' => esc_html__( 'Agent', 'salesking' ),
			'salesking_lastreplydate' => esc_html__( 'Date of last reply', 'salesking' ),
		);
		$columns = array_slice($columns_initial, 0, 2, true) + $columns;

	    return $columns;
	}

	// Add message custom columns data
	function salesking_columns_group_data_message( $column, $post_id ) {
	    switch ( $column ) {

	        case 'salesking_agent' :

            	$user = get_post_meta($post_id, 'salesking_message_user', true);
            	if ($user === 'shop'){
            		$user = get_post_meta ($post_id, 'salesking_message_message_1_author', true);
            	}
	            echo '<strong>'.esc_html($user).'</strong>';
	            // check if have new message, and add
	            // check latest response and role
	            $conversation_msg_nr = get_post_meta($post_id, 'salesking_message_messages_number', true);
	            $latest_message_author = get_post_meta($post_id, 'salesking_message_message_'.$conversation_msg_nr.'_author', true);
	            // Get the user object.
	            $user = get_user_by('login', $latest_message_author);
	            if (is_object($user)){
	            	$agent_group = get_user_meta($user->ID, 'salesking_group', true);
	            	if ($agent_group !== 'none' && !empty($agent_group)){
	            		esc_html_e(' (New message!)','salesking');
	            	}
	            }
	            break;

	        case 'salesking_lastreplydate' :
	        	$lastmessagenumber = get_post_meta ($post_id, 'salesking_message_messages_number', true);
	            $time_last_message = get_post_meta( $post_id , 'salesking_message_message_'.$lastmessagenumber.'_time' , true );

	            // In case of empty start message, prevent error
	            if ($time_last_message === '' || $time_last_message === null){
	            	$time_last_message = 1;
	            }

	            // if today
	            if((time()-$time_last_message) < 86400){
	            	// show time
	            	echo date_i18n( 'h:i A', $time_last_message+(get_option('gmt_offset')*3600) );
	            } else if ((time()-$time_last_message) < 172800){
	            // if yesterday
	            	echo esc_html__('Yesterday at ','salesking').date_i18n( 'h:i A', $time_last_message+(get_option('gmt_offset')*3600) );
	            } else {
	            // date
	            	echo date_i18n( get_option('date_format'), $time_last_message+(get_option('gmt_offset')*3600) ); 
	            }

	            break;

	    }
	}

	// Register announcements
	public static function salesking_register_post_type_announcement() {
		// Build labels and arguments
	    $labels = array(
	        'name'                  => esc_html__( 'Announcements', 'salesking' ),
	        'singular_name'         => esc_html__( 'Announcement', 'salesking' ),
	        'all_items'             => esc_html__( 'Announcements', 'salesking' ),
	        'menu_name'             => esc_html__( 'Announcements', 'salesking' ),
	        'add_new'               => esc_html__( 'New announcement', 'salesking' ),
	        'add_new_item'          => esc_html__( 'New announcement', 'salesking' ),
	        'edit'                  => esc_html__( 'Edit', 'salesking' ),
	        'edit_item'             => esc_html__( 'Edit announcement', 'salesking' ),
	        'new_item'              => esc_html__( 'New announcement', 'salesking' ),
	        'view_item'             => esc_html__( 'View announcement', 'salesking' ),
	        'view_items'            => esc_html__( 'View announcements', 'salesking' ),
	        'search_items'          => esc_html__( 'Search announcements', 'salesking' ),
	        'not_found'             => esc_html__( 'No announcements found', 'salesking' ),
	        'not_found_in_trash'    => esc_html__( 'No announcements found in trash', 'salesking' ),
	        'parent'                => esc_html__( 'Parent announcement', 'salesking' ),
	        'featured_image'        => esc_html__( 'Announcement image', 'salesking' ),
	        'set_featured_image'    => esc_html__( 'Set announcement image', 'salesking' ),
	        'remove_featured_image' => esc_html__( 'Remove announcement image', 'salesking' ),
	        'use_featured_image'    => esc_html__( 'Use as announcement image', 'salesking' ),
	        'insert_into_item'      => esc_html__( 'Insert into announcement', 'salesking' ),
	        'uploaded_to_this_item' => esc_html__( 'Uploaded to this announcement', 'salesking' ),
	        'filter_items_list'     => esc_html__( 'Filter announcements', 'salesking' ),
	        'items_list_navigation' => esc_html__( 'Announcement navigation', 'salesking' ),
	        'items_list'            => esc_html__( 'Announcements list', 'salesking' )
	    );
	    $args = array(
	        'label'                 => esc_html__( 'Announcement', 'salesking' ),
	        'description'           => esc_html__( 'This is where you can create new announcements', 'salesking' ),
	        'labels'                => $labels,
	        'supports'              => array('title', 'editor'),
	        'hierarchical'          => false,
	        'public'                => false,
	        'publicly_queryable' 	=> false,
	        'show_ui'               => true,
	        'show_in_menu'          => 'salesking',
	        'menu_position'         => 100,
	        'show_in_admin_bar'     => true,
	        'show_in_nav_menus'     => false,
	        'can_export'            => false,
	        'has_archive'           => false,
	        'exclude_from_search'   =>  true,
	        'rewrite'               => false,
	        'capability_type'       => 'product',
	        'show_in_rest'          => true,
	        'rest_base'             => 'salesking_announce',
	        'rest_controller_class' => 'WP_REST_Posts_Controller',
	    );

		// Actually register the post type
		register_post_type( 'salesking_announce', $args );
	}

	// Add Metaboxes to Announcements
	function salesking_announcement_metaboxes($post_type) {
	    $post_types = array('salesking_announce');     //limit meta box to certain post types
       	if ( in_array( $post_type, $post_types ) ) {
	           add_meta_box(
	               'salesking_announcement_visibility_metabox'
	               ,esc_html__( 'Announcement Visibility', 'salesking' )
	               ,array( $this, 'salesking_announcement_visibility_metabox_content' )
	               ,$post_type
	               ,'advanced'
	               ,'high'
	           );
	       }
	}

	function salesking_save_groups_metaboxes($post_id){

		if (isset($_POST['_inline_edit'])){
			if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')){
			    return;
			}
		}
		if (isset($_REQUEST['bulk_edit'])){
		    return;
		}

		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX)) { 
			return;
		}

		if (get_post_type($post_id) === 'salesking_group'){

			$group_max_discount = sanitize_text_field(filter_input(INPUT_POST, 'salesking_group_max_discount'));

			if ($group_max_discount !== NULL){
				update_post_meta( $post_id, 'salesking_group_max_discount', $group_max_discount);
			}
		}

		if (get_post_type($post_id) === 'b2bking_group'){
			$salesking_assigned_agent = sanitize_text_field(filter_input(INPUT_POST, 'salesking_group_agent'));

			if ($salesking_assigned_agent !== NULL){
				update_post_meta( $post_id, 'salesking_assigned_agent', $salesking_assigned_agent);
			}
		}

		if (get_post_type($post_id) === 'shop_order'){
			$salesking_assigned_agent = sanitize_text_field(filter_input(INPUT_POST, 'salesking_agent_order'));

			if ($salesking_assigned_agent !== NULL && !empty($salesking_assigned_agent)){
				$old_agent_value = get_post_meta($post_id,'salesking_assigned_agent', true);
				update_post_meta( $post_id, 'salesking_assigned_agent', $salesking_assigned_agent);

				// also send new order email to agent
				// here send only to agent, not to admin as it is not a truly new order, just intended as a notification
				// only if agent value has changed
				if ($old_agent_value !== $salesking_assigned_agent){
					if (intval(get_option( 'salesking_agents_receive_order_emails_setting', 1 )) === 1){
						$email_new_order = WC()->mailer()->get_emails()['WC_Email_New_Order'];
						// Sending the new Order email notification for an $order_id (order ID)
						// get agent email
						$agent_info = get_userdata($salesking_assigned_agent);
						$email_new_order->recipient = $agent_info->user_email;
						$email_new_order->trigger( $post_id );
					}
				}
			}
			
		}
	}

	// Save Announcements Metabox Content
	function salesking_save_announcement_metaboxes($post_id){
		if (isset($_POST['_inline_edit'])){
			if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')){
			    return;
			}
		}
		if (isset($_REQUEST['bulk_edit'])){
		    return;
		}
		if (get_post_type($post_id) === 'salesking_announce'){

			// Get all groups
			$groups = get_posts([
			  'post_type' => 'salesking_group',
			  'post_status' => 'publish',
			  'numberposts' => -1
			]);

			// For each group option, save user's choice as post meta
			foreach ($groups as $group){
				$meta_input = sanitize_text_field(filter_input(INPUT_POST, 'salesking_group_'.$group->ID));
				if($meta_input !== NULL){
					update_post_meta($post_id, 'salesking_group_'.$group->ID, sanitize_text_field($meta_input));
				}
			}

			// Save user visibility
			$meta_user_visibility = sanitize_text_field(filter_input(INPUT_POST, 'salesking_category_users_textarea'));
			if ($meta_user_visibility !== NULL){
				// get current users list
				$currentuserstextarea = esc_html(get_post_meta($post_id, 'salesking_category_users_textarea', true));
				$currentusersarray = explode(',', $currentuserstextarea);
				// delete all individual user meta
				foreach ($currentusersarray as $user){
					delete_post_meta( $post_id, 'salesking_user_'.trim($user));
				}
				// get new users list
				$newusertextarea = $meta_user_visibility;
				$newusersarray = explode(',', $newusertextarea);
				// set new user meta
				foreach ($newusersarray as $newuser){
					update_post_meta( $post_id, 'salesking_user_'.sanitize_text_field(trim($newuser)), 1);
				}
				// Update users textarea
				update_post_meta($post_id, 'salesking_category_users_textarea', sanitize_text_field($meta_user_visibility));
			}


		    if ( 'publish' !== get_post_status($post_id) ){
		        return;
		    }
		    $post = get_post($post_id);

		    $content = $post->post_content;
		    // get all agents
		    $agents = get_users(array(
			    'meta_key'     => 'salesking_group',
			    'meta_value'   => 'none',
			    'meta_compare' => '!=',
			    'fields' => 'ids',
			));
			
			foreach ($agents as $agent){
				// check if announcement visible, and if so, send it.
				$agent_group = get_user_meta($agent, 'salesking_group', true);
				$group_visible = intval(get_post_meta($post->ID, 'salesking_group_'.$agent_group, true));
				$user_info = get_userdata($agent);

				$login = $user_info->user_login;
				$user_visible = intval(get_post_meta($post->ID, 'salesking_user_'.$login, true));
				
				if (($group_visible === 1) || ($user_visible === 1)){
					// send it
					$mailadress = $user_info->user_email;
					do_action( 'salesking_new_announcement', $mailadress, $content, get_current_user_id(), $post->ID );
				}
			}
	
		}
	}

	// Add custom columns to announcements menu
	function salesking_add_columns_group_menu_announcement($columns) {

		$columns_initial = $columns;
		
		// rename title
		$columns = array(
			'salesking_visible' => esc_html__( 'Visible to:', 'salesking' ),
		);
		$columns = array_slice($columns_initial, 0, 2, true) + $columns + array_slice($columns_initial, 2, 1, true);

	    return $columns;
	}

	// Add announcements custom columns data
	function salesking_columns_group_data_announcement( $column, $post_id ) {
	    switch ( $column ) {

	        case 'salesking_visible' :

            	$groups = get_posts([
            	  'post_type' => 'salesking_group',
            	  'post_status' => 'publish',
            	  'numberposts' => -1
            	]);

            	$groups_message = '';
            	foreach ($groups as $group){
            		$check = intval(get_post_meta($post_id, 'salesking_group_'.$group->ID, true));
            		if ($check === 1){
            			$groups_message .= esc_html($group->post_title).', ';
            		}        		
            	}
            	if ( ! empty($groups_message)){
            		echo '<strong>'.esc_html__('Groups: ','salesking').'</strong>'.esc_html(substr($groups_message, 0, -2));
            		echo '<br />';
            	}

            	$users = get_post_meta($post_id, 'salesking_category_users_textarea', true);
            	if (!empty($users)){
            		echo '<strong>'.esc_html__('Users: ','salesking').'</strong>'.esc_html($users);
            	}
	            break;

	    }
	}


	function salesking_announcement_visibility_metabox_content(){
		if ( ! current_user_can( 'manage_woocommerce' ) ) { return; }
	    ?>
	    <div class="salesking_group_visibility_container">
	    	<div class="salesking_group_visibility_container_top">
	    		<?php esc_html_e( 'Group Visibility', 'salesking' ); ?>
	    	</div>
	    	<div class="salesking_group_visibility_container_content">
	    		<div class="salesking_group_visibility_container_content_title">
					<svg class="salesking_group_visibility_container_content_title_icon" xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="none" viewBox="0 0 45 45">
					  <path fill="#C4C4C4" d="M22.382 7.068c-3.876 0-7.017 3.668-7.017 8.193 0 3.138 1.51 5.863 3.73 7.239l-2.573 1.192-6.848 3.176c-.661.331-.991.892-.991 1.686v7.541c.054.943.62 1.822 1.537 1.837h24.36c1.048-.091 1.578-.935 1.588-1.837v-7.541c0-.794-.33-1.355-.992-1.686l-6.6-3.175-2.742-1.3c2.128-1.407 3.565-4.073 3.565-7.132 0-4.525-3.142-8.193-7.017-8.193zM11.063 9.95c-1.667.063-2.99.785-3.993 1.935a7.498 7.498 0 00-1.663 4.663c.068 2.418 1.15 4.707 3.076 5.905l-7.69 3.573c-.529.198-.793.661-.793 1.389v6.053c.041.802.458 1.477 1.24 1.488h5.11v-6.401c.085-1.712.888-3.095 2.333-3.77l5.109-2.43a4.943 4.943 0 001.141-.944c-2.107-3.25-2.4-7.143-1.041-10.567-.883-.54-1.876-.888-2.829-.894zm22.822 0c-1.09.023-2.098.425-2.926.992 1.32 3.455.956 7.35-.993 10.37.43.495.877.876 1.34 1.14l4.912 2.333c1.496.82 2.267 2.216 2.282 3.77v6.401h5.259c.865-.074 1.233-.764 1.241-1.488v-6.053c0-.662-.264-1.124-.794-1.39l-7.59-3.622c1.968-1.452 2.956-3.627 2.976-5.855-.053-1.763-.591-3.4-1.663-4.663-1.12-1.215-2.51-1.922-4.044-1.935z"/>
					</svg>
					<?php esc_html_e( 'Groups who can see this announcement', 'salesking' ); ?>
	    		</div>
            	<?php
	            	$groups = get_posts([
	            	  'post_type' => 'salesking_group',
	            	  'post_status' => 'publish',
	            	  'numberposts' => -1
	            	]);
	            	foreach ($groups as $group){
	            		$checked = '';
		            		// If current page is not Add New 
		            		if( get_current_screen()->action !== 'add'){
			            		global $post;
			            		$check = intval(get_post_meta($post->ID, 'salesking_group_'.$group->ID, true));
			            		if ($check === 1){
			            			$checked = 'checked="checked"';
			            		}	
			            	}  
	            		?>
	            		<div class="salesking_group_visibility_container_content_checkbox">
	            			<div class="salesking_group_visibility_container_content_checkbox_name">
	            				<?php echo esc_html($group->post_title); ?>
	            			</div>
	            			<input type="hidden" name="salesking_group_<?php echo esc_attr($group->ID);?>" value="0">
	            			<input type="checkbox" value="1" class="salesking_group_visibility_container_content_checkbox_input" name="salesking_group_<?php echo esc_attr($group->ID);?>" id="salesking_group_<?php echo esc_attr($group->ID);?>" value="1" <?php echo $checked;?> />
	            		</div>
	            		<?php
	            	}
	            ?>
	    	</div>
	    </div>

	    <div class="salesking_group_visibility_container">
	    	<div class="salesking_group_visibility_container_top">
	    		<?php esc_html_e( 'Agent Visibility', 'salesking' ); ?>
	    	</div>
	    	<div class="salesking_group_visibility_container_content">
	    		<div class="salesking_group_visibility_container_content_title">
					<svg class="salesking_user_visibility_container_content_title_icon" xmlns="http://www.w3.org/2000/svg" width="31" height="23" fill="none" viewBox="0 0 31 23">
					  <path fill="#C4C4C4" d="M9.333 11.58c3.076 0 5.396-2.32 5.396-5.396C14.73 3.11 12.41.79 9.333.79c-3.075 0-5.396 2.32-5.396 5.395 0 3.076 2.32 5.396 5.396 5.396zm1.542 1.462H7.792c-4.25 0-7.709 3.458-7.709 7.708v1.542h18.5V20.75c0-4.25-3.458-7.708-7.708-7.708zm17.412-7.258l-6.63 6.616-1.991-1.992-2.18 2.18 4.171 4.17 8.806-8.791-2.176-2.183z"/>
					</svg>
					<?php esc_html_e( 'Agents who can see this announcement (comma-separated)', 'salesking' ); ?>
	    		</div>
	    		<textarea name="salesking_category_users_textarea" id="salesking_category_users_textarea"><?php 
		            		// If current page is not Add New 
		            		if( get_current_screen()->action !== 'add'){
			            		global $post;
			            		echo get_post_meta($post->ID, 'salesking_category_users_textarea', true);
			            	}  
	            			?></textarea>
            	<div class="salesking_category_users_textarea_buttons_container"><?php 
            		// get all agent ids

            		$included_ids = get_users(array(
            				    'meta_key'     => 'salesking_group',
            				    'meta_value'   => 'none',
            				    'meta_compare' => '!=',
            				    'fields' => 'ids',
            				));

            		wp_dropdown_users($args = array('id' => 'salesking_all_users_dropdown', 'show' => 'user_login', 'include' => $included_ids)); ?><button type="button" class="button" id="salesking_category_add_user"><?php esc_html_e('Add agent','salesking'); ?></button>
            	</div>

	    	</div>
	    </div>
	    <?php
	}


	// Register Agent Groups
	public static function salesking_register_post_type_agent_groups() {
		// Build labels and arguments
	    $labels = array(
	        'name'                  => esc_html__( 'Agent Groups', 'salesking' ),
	        'singular_name'         => esc_html__( 'Group', 'salesking' ),
	        'all_items'             => esc_html__( 'Agent Groups', 'salesking' ),
	        'menu_name'             => esc_html__( 'Agent Groups', 'salesking' ),
	        'add_new'               => esc_html__( 'Create new group', 'salesking' ),
	        'add_new_item'          => esc_html__( 'Create new customer group', 'salesking' ),
	        'edit'                  => esc_html__( 'Edit', 'salesking' ),
	        'edit_item'             => esc_html__( 'Edit group', 'salesking' ),
	        'new_item'              => esc_html__( 'New group', 'salesking' ),
	        'view_item'             => esc_html__( 'View group', 'salesking' ),
	        'view_items'            => esc_html__( 'View groups', 'salesking' ),
	        'search_items'          => esc_html__( 'Search groups', 'salesking' ),
	        'not_found'             => esc_html__( 'No groups found', 'salesking' ),
	        'not_found_in_trash'    => esc_html__( 'No groups found in trash', 'salesking' ),
	        'parent'                => esc_html__( 'Parent group', 'salesking' ),
	        'featured_image'        => esc_html__( 'Group image', 'salesking' ),
	        'set_featured_image'    => esc_html__( 'Set group image', 'salesking' ),
	        'remove_featured_image' => esc_html__( 'Remove group image', 'salesking' ),
	        'use_featured_image'    => esc_html__( 'Use as group image', 'salesking' ),
	        'insert_into_item'      => esc_html__( 'Insert into group', 'salesking' ),
	        'uploaded_to_this_item' => esc_html__( 'Uploaded to this group', 'salesking' ),
	        'filter_items_list'     => esc_html__( 'Filter groups', 'salesking' ),
	        'items_list_navigation' => esc_html__( 'Groups navigation', 'salesking' ),
	        'items_list'            => esc_html__( 'Groups list', 'salesking' )
	    );
	    $args = array(
	        'label'                 => esc_html__( 'Agent Group', 'salesking' ),
	        'description'           => esc_html__( 'This is where you can create new agent groups', 'salesking' ),
	        'labels'                => $labels,
	        'supports'              => array( 'title' ),
	        'hierarchical'          => false,
	        'public'                => false,
	        'show_ui'               => true,
	        'show_in_menu'          => 'salesking',
	        'menu_position'         => 105,
	        'show_in_admin_bar'     => true,
	        'show_in_nav_menus'     => false,
	        'can_export'            => true,
	        'has_archive'           => false,
	        'exclude_from_search'   => true,
	        'publicly_queryable'    => false,
	        'capability_type'       => 'product',
	        'map_meta_cap'          => true,
	        'show_in_rest'          => true,
	        'rest_base'             => 'salesking_group',
	        'rest_controller_class' => 'WP_REST_Posts_Controller',
	    );

		// Actually register the post type
		register_post_type( 'salesking_group', $args );


	}

	// Add Groups Metaboxes
	function salesking_groups_metaboxes($post_type) {
	    $post_types = array('salesking_group');     //limit meta box to certain post types
       	if ( in_array( $post_type, $post_types ) ) {
       		if( get_current_screen()->action !== 'add'){
	           add_meta_box(
	               'salesking_group_users_metabox'
	               ,esc_html__( 'Agents in this group', 'salesking' )
	               ,array( $this, 'salesking_group_users_metabox_content' )
	               ,$post_type
	               ,'advanced'
	               ,'low'
	           );
	       }
	       add_meta_box(
	           'salesking_group_settings_metabox'
	           ,esc_html__( 'Group Settings', 'salesking' )
	           ,array( $this, 'salesking_group_settings_metabox_content' )
	           ,$post_type
	           ,'advanced'
	           ,'low'
	       );
	    }
	}

	function salesking_group_settings_metabox_content(){
		global $post;
		?>
		<div class="salesking_group_payment_shipping_methods_container">
			<div class="salesking_group_payment_shipping_methods_container_element">

				<div class="salesking_custom_role_approval_sort_container_element">

					<div class="salesking_custom_field_settings_metabox_top_column_sort_title salesking_tooltip" data-tooltip="<?php esc_html_e('This is the maximum discount (wiggle room) sales agents can offer to end customers.','salesking');?>" data-inverted="">
						<svg class="salesking_custom_field_settings_metabox_top_column_sort_title_icon" xmlns="http://www.w3.org/2000/svg" width="37" height="37" fill="none" viewBox="0 0 34 34">
						  <path fill="#C4C4C4" d="M32.375 7.708H4.625A1.542 1.542 0 003.083 9.25v6.167H4.46c1.536 0 2.96 1.05 3.207 2.565a3.085 3.085 0 01-3.042 3.601H3.083v6.167a1.542 1.542 0 001.542 1.542h27.75a1.542 1.542 0 001.542-1.542v-6.167h-1.542a3.084 3.084 0 01-3.042-3.601c.247-1.515 1.671-2.565 3.207-2.565h1.377V9.25a1.542 1.542 0 00-1.542-1.542zm-18.5 6.167a1.542 1.542 0 110 3.083 1.542 1.542 0 010-3.083zm-1.233 9.867l9.25-12.334 2.466 1.85-9.25 12.334-2.466-1.85zm10.483-.617a1.542 1.542 0 110-3.083 1.542 1.542 0 010 3.083z"/>
						</svg>
						<?php esc_html_e('Max Discount Percentage (%) Allowed','salesking'); ?>
					</div>
					<input type="number" min="0" max="100" name="salesking_group_max_discount" class="salesking_custom_field_settings_metabox_top_column_sort_text" placeholder="<?php esc_html_e('Enter discount percentage...', 'salesking'); ?>" value="<?php echo esc_attr(get_post_meta($post->ID, 'salesking_group_max_discount', true)); ?>" required>
				</div>
			</div>
		</div>



		<br /><br />

		<!-- Information panel -->
		<div class="salesking_group_payment_shipping_information_box">
			<svg class="salesking_group_payment_shipping_information_box_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 36 36">
			  <path fill="#358BBB" d="M18 0C8.06 0 0 8.06 0 18s8.06 18 18 18 18-8.06 18-18S27.94 0 18 0zm0 28.446a1.607 1.607 0 110-3.213 1.607 1.607 0 010 3.213zm2.527-8.819a1.941 1.941 0 00-1.241 1.8v.912a.322.322 0 01-.322.322h-1.928a.322.322 0 01-.322-.322v-.864c0-.928.27-1.844.8-2.607a4.49 4.49 0 012.093-1.643c1.366-.527 2.25-1.672 2.25-2.921 0-1.772-1.732-3.215-3.857-3.215s-3.857 1.443-3.857 3.215v.305a.322.322 0 01-.322.321h-1.928a.322.322 0 01-.322-.321v-.305c0-1.58.691-3.054 1.945-4.15C14.721 9.095 16.312 8.517 18 8.517c1.688 0 3.279.582 4.484 1.635 1.253 1.097 1.945 2.572 1.945 4.15 0 2.323-1.531 4.412-3.902 5.324z"/>
			</svg>
			<?php esc_html_e('These values can also be set for each agent individually in their profile panel. Agent values have priority over group values.','salesking'); ?>
		</div>


		<?php
	}

	// Group Users Metabox Content
	function salesking_group_users_metabox_content(){
		?>
		<div id="salesking_metabox_product_categories_wrapper">
			<div id="salesking_metabox_product_categories_wrapper_content">
				<div class="salesking_metabox_product_categories_wrapper_content_line">
					<?php
					global $post;
					// get all users in the group
					$users = get_users(array(
							    'meta_key'     => 'salesking_group',
							    'meta_value'   => $post->ID,
							    'fields' => array('ID', 'user_login'),

							));
					foreach ($users as $user){
						echo '
						<a href="'.esc_attr(get_edit_user_link($user->ID)).'" class="salesking_metabox_product_categories_wrapper_content_category_user_link"><div class="salesking_metabox_product_categories_wrapper_content_category_user">
							'.esc_html($user->user_login).'
						</div></a>
						';
					}
					if (empty($users)){
						esc_html_e('There are no agents in this group','salesking');
					}
					?>
				</div>
			</div>
		</div>

		<?php
	}

	function salesking_show_user_meta_profile($user){
		if (isset($user->ID)){
			$user_id = $user->ID;
		} else {
			$user_id = 0;
		}
		?>
		<input type="hidden" id="salesking_admin_user_id" value="<?php echo esc_attr($user_id);?>">
	    <h3><?php esc_html_e("Agent Settings (SalesKing)", "salesking"); ?></h3>

	    <?php
	    	$customer_agent = get_user_meta($user_id,'salesking_user_choice', true);
	    	if (empty($customer_agent)){
	    		$customer_agent = 'customer';
	    	}
	    ?>
    	<h2 class="salesking_inline_header"><?php esc_html_e('This user is a','salesking');?></h2>
    	<div class="salesking_switch-field">
    		<input type="radio" id="salesking_radio-one" name="salesking_user_choice" value="customer" <?php checked('customer',$customer_agent, true);?>/>
    		<label for="salesking_radio-one"><strong><?php esc_html_e('Customer','salesking');?></strong></label>
    		<input type="radio" id="salesking_radio-two" name="salesking_user_choice" value="agent" <?php checked('agent',$customer_agent, true);?> />
    		<label for="salesking_radio-two"><strong><?php esc_html_e('Sales Agent','salesking');?></strong></label>
    	</div>


    	<div class="salesking_user_shipping_payment_methods_container">
    		<div class="salesking_user_shipping_payment_methods_container_top">
    			<div class="salesking_user_shipping_payment_methods_container_top_title">
    				<?php esc_html_e('Agent Settings','salesking'); ?>
    			</div>		
    		</div>
    		<div class="salesking_user_settings_container salesking_agent_settings_agent">
    			<div class="salesking_user_settings_container_column">
    				<div class="salesking_user_settings_container_column_title">
    					<svg class="salesking_user_settings_container_column_title_icon_right" xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="none" viewBox="0 0 45 45">
    					  <path fill="#C4C4C4" d="M22.382 7.068c-3.876 0-7.017 3.668-7.017 8.193 0 3.138 1.51 5.863 3.73 7.239l-2.573 1.192-6.848 3.176c-.661.331-.991.892-.991 1.686v7.541c.054.943.62 1.822 1.537 1.837h24.36c1.048-.091 1.578-.935 1.588-1.837v-7.541c0-.794-.33-1.355-.992-1.686l-6.6-3.175-2.742-1.3c2.128-1.407 3.565-4.073 3.565-7.132 0-4.525-3.142-8.193-7.017-8.193zM11.063 9.95c-1.667.063-2.99.785-3.993 1.935a7.498 7.498 0 00-1.663 4.663c.068 2.418 1.15 4.707 3.076 5.905l-7.69 3.573c-.529.198-.793.661-.793 1.389v6.053c.041.802.458 1.477 1.24 1.488h5.11v-6.401c.085-1.712.888-3.095 2.333-3.77l5.109-2.43a4.943 4.943 0 001.141-.944c-2.107-3.25-2.4-7.143-1.041-10.567-.883-.54-1.876-.888-2.829-.894zm22.822 0c-1.09.023-2.098.425-2.926.992 1.32 3.455.956 7.35-.993 10.37.43.495.877.876 1.34 1.14l4.912 2.333c1.496.82 2.267 2.216 2.282 3.77v6.401h5.259c.865-.074 1.233-.764 1.241-1.488v-6.053c0-.662-.264-1.124-.794-1.39l-7.59-3.622c1.968-1.452 2.956-3.627 2.976-5.855-.053-1.763-.591-3.4-1.663-4.663-1.12-1.215-2.51-1.922-4.044-1.935z"/>
    					</svg>
    					<?php esc_html_e('Agent Group','salesking'); ?>
    				</div>
    				<select name="salesking_group" id="salesking_group" class="salesking_user_settings_select">
    					<?php
    						$agentgroup = get_user_meta( $user_id, 'salesking_group', true );
    					 	echo '<option value="none" '.selected('none', $agentgroup, false).'>'.esc_html__('- Not an agent -', 'salesking').'</option>'; 
    					 	?>
  	    					<optgroup label="<?php esc_html_e('Agent Groups', 'salesking'); ?>">
  	    					
  	    					<?php
	    					$posts = get_posts([
	    					  'post_type' => 'salesking_group',
	    					  'post_status' => 'publish',
	    					  'numberposts' => -1
	    					]);
	    					foreach ($posts as $post){
	    						echo '<option value="'.esc_attr($post->ID).'" '.selected($post->ID, $agentgroup, false).'>'.esc_html($post->post_title).'</option>';
	    					}
		    				?>
    					</optgroup>
    				</select>
    			</div>
    		</div>
    		<div class="salesking_user_settings_container salesking_agent_settings_customer">
    			<div class="salesking_user_settings_container_column">
    				<div class="salesking_user_settings_container_column_title">
    					<svg class="salesking_user_settings_container_column_title_icon_right" xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="none" viewBox="0 0 45 45">
    					  <path fill="#C4C4C4" d="M22.382 7.068c-3.876 0-7.017 3.668-7.017 8.193 0 3.138 1.51 5.863 3.73 7.239l-2.573 1.192-6.848 3.176c-.661.331-.991.892-.991 1.686v7.541c.054.943.62 1.822 1.537 1.837h24.36c1.048-.091 1.578-.935 1.588-1.837v-7.541c0-.794-.33-1.355-.992-1.686l-6.6-3.175-2.742-1.3c2.128-1.407 3.565-4.073 3.565-7.132 0-4.525-3.142-8.193-7.017-8.193zM11.063 9.95c-1.667.063-2.99.785-3.993 1.935a7.498 7.498 0 00-1.663 4.663c.068 2.418 1.15 4.707 3.076 5.905l-7.69 3.573c-.529.198-.793.661-.793 1.389v6.053c.041.802.458 1.477 1.24 1.488h5.11v-6.401c.085-1.712.888-3.095 2.333-3.77l5.109-2.43a4.943 4.943 0 001.141-.944c-2.107-3.25-2.4-7.143-1.041-10.567-.883-.54-1.876-.888-2.829-.894zm22.822 0c-1.09.023-2.098.425-2.926.992 1.32 3.455.956 7.35-.993 10.37.43.495.877.876 1.34 1.14l4.912 2.333c1.496.82 2.267 2.216 2.282 3.77v6.401h5.259c.865-.074 1.233-.764 1.241-1.488v-6.053c0-.662-.264-1.124-.794-1.39l-7.59-3.622c1.968-1.452 2.956-3.627 2.976-5.855-.053-1.763-.591-3.4-1.663-4.663-1.12-1.215-2.51-1.922-4.044-1.935z"/>
    					</svg>
    					<?php esc_html_e('Agent assigned to this customer','salesking'); ?>
    				</div>

    				<select name="salesking_group_agent" id="salesking_group_agent" class="salesking_user_settings_select">
    					<?php
    						$agentassigned = get_user_meta( $user_id, 'salesking_assigned_agent', true );
    					 	echo '<option value="none" '.selected('none', $agentassigned, false).'>'.esc_html__('- None -', 'salesking').'</option>'; 

    					 	$agents = get_users(array(
 	    						    'meta_key'     => 'salesking_group',
 	    						    'meta_value'   => 'none',
 	    						    'meta_compare' => '!=',
 	    						));
    					 	?>
  	    					<optgroup label="<?php esc_html_e('Agents', 'salesking'); ?>">
  	    					
  	    					<?php
	    					foreach ($agents as $agent){
	    						// if self is agent, dont show self
	    						if ($agent->ID !== $user_id){
	    							echo '<option value="'.esc_attr($agent->ID).'" '.selected($agent->ID, $agentassigned, false).'>'.esc_html($agent->user_login).'</option>';
	    						}
	    					}
		    				?>
    					</optgroup>
    				</select>

			</div>
    	</div>

		<!-- Information panel -->
		<div class="salesking_user_settings_information_box salesking_agent_settings_customer">
			<svg class="salesking_group_payment_shipping_information_box_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 36 36">
			  <path fill="#358BBB" d="M18 0C8.06 0 0 8.06 0 18s8.06 18 18 18 18-8.06 18-18S27.94 0 18 0zm0 28.446a1.607 1.607 0 110-3.213 1.607 1.607 0 010 3.213zm2.527-8.819a1.941 1.941 0 00-1.241 1.8v.912a.322.322 0 01-.322.322h-1.928a.322.322 0 01-.322-.322v-.864c0-.928.27-1.844.8-2.607a4.49 4.49 0 012.093-1.643c1.366-.527 2.25-1.672 2.25-2.921 0-1.772-1.732-3.215-3.857-3.215s-3.857 1.443-3.857 3.215v.305a.322.322 0 01-.322.321h-1.928a.322.322 0 01-.322-.321v-.305c0-1.58.691-3.054 1.945-4.15C14.721 9.095 16.312 8.517 18 8.517c1.688 0 3.279.582 4.484 1.635 1.253 1.097 1.945 2.572 1.945 4.15 0 2.323-1.531 4.412-3.902 5.324z"/>
			</svg>
			<?php esc_html_e('Here you can assign this customer to a specific agent','salesking'); ?>
		</div>

		<!-- Information panel -->
		<div class="salesking_user_settings_information_box salesking_agent_settings_agent">
			<svg class="salesking_group_payment_shipping_information_box_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 36 36">
			  <path fill="#358BBB" d="M18 0C8.06 0 0 8.06 0 18s8.06 18 18 18 18-8.06 18-18S27.94 0 18 0zm0 28.446a1.607 1.607 0 110-3.213 1.607 1.607 0 010 3.213zm2.527-8.819a1.941 1.941 0 00-1.241 1.8v.912a.322.322 0 01-.322.322h-1.928a.322.322 0 01-.322-.322v-.864c0-.928.27-1.844.8-2.607a4.49 4.49 0 012.093-1.643c1.366-.527 2.25-1.672 2.25-2.921 0-1.772-1.732-3.215-3.857-3.215s-3.857 1.443-3.857 3.215v.305a.322.322 0 01-.322.321h-1.928a.322.322 0 01-.322-.321v-.305c0-1.58.691-3.054 1.945-4.15C14.721 9.095 16.312 8.517 18 8.517c1.688 0 3.279.582 4.484 1.635 1.253 1.097 1.945 2.572 1.945 4.15 0 2.323-1.531 4.412-3.902 5.324z"/>
			</svg>
			<?php esc_html_e('Here you must assign the agent to a group.','salesking'); ?>
		</div>

		<div class="salesking_user_settings_container salesking_agent_settings_agent">
			<div class="salesking_user_settings_container_column salesking_discount_percentage_column">
				<div class="salesking_custom_field_settings_metabox_top_column_sort_title salesking_tooltip" data-tooltip="<?php esc_html_e('This is the maximum discount (wiggle room) this particular agent can offer to end customers.','salesking');?>" data-inverted="">
					<svg class="salesking_custom_field_settings_metabox_top_column_sort_title_icon" xmlns="http://www.w3.org/2000/svg" width="37" height="37" fill="none" viewBox="0 0 34 34">
					  <path fill="#C4C4C4" d="M32.375 7.708H4.625A1.542 1.542 0 003.083 9.25v6.167H4.46c1.536 0 2.96 1.05 3.207 2.565a3.085 3.085 0 01-3.042 3.601H3.083v6.167a1.542 1.542 0 001.542 1.542h27.75a1.542 1.542 0 001.542-1.542v-6.167h-1.542a3.084 3.084 0 01-3.042-3.601c.247-1.515 1.671-2.565 3.207-2.565h1.377V9.25a1.542 1.542 0 00-1.542-1.542zm-18.5 6.167a1.542 1.542 0 110 3.083 1.542 1.542 0 010-3.083zm-1.233 9.867l9.25-12.334 2.466 1.85-9.25 12.334-2.466-1.85zm10.483-.617a1.542 1.542 0 110-3.083 1.542 1.542 0 010 3.083z"/>
					</svg>
					<?php esc_html_e('Max Discount Percentage (%) Allowed','salesking'); ?>
				</div>
				<input type="number" min="0" max="100" name="salesking_group_max_discount" class="salesking_custom_field_settings_metabox_top_column_sort_text salesking_975" placeholder="<?php esc_html_e('Enter discount percentage...', 'salesking'); ?>" value="<?php echo esc_attr(get_user_meta($user_id, 'salesking_group_max_discount', true)); ?>">
    			</div>
    		</div>
		</div>
					        	
		<br /><br />
		<?php
	}

	function salesking_save_user_meta_agent_group($user_id ){
		if ( !current_user_can( 'edit_user', $user_id ) ) { 
		    return false; 
		}

		if (isset($_POST['salesking_group_max_discount'])){
			$max_discount = sanitize_text_field($_POST['salesking_group_max_discount']);
			update_user_meta( $user_id, 'salesking_group_max_discount', $max_discount);	
		}

		if (isset($_POST['salesking_group'])){
			$agent_group = sanitize_text_field($_POST['salesking_group']);

			// if user chose customer, we must set the agent value to none
			if (isset($_POST['salesking_user_choice'])){
				$customer_or_agent = sanitize_text_field($_POST['salesking_user_choice']);
				if ($customer_or_agent === 'customer'){
					$agent_group = 'none';
				}
			}
			update_user_meta( $user_id, 'salesking_group', $agent_group);	
		}

		if (isset($_POST['salesking_group_agent'])){
			$assigned_agent = sanitize_text_field($_POST['salesking_group_agent']);
			// if user chose agent, we must set the customer value to none
			if (isset($_POST['salesking_user_choice'])){
				$customer_or_agent = sanitize_text_field($_POST['salesking_user_choice']);
				if ($customer_or_agent === 'agent'){
					$assigned_agent = 'none';
				}
			}
			update_user_meta( $user_id, 'salesking_assigned_agent', $assigned_agent);	
		}

		if (isset($_POST['salesking_user_choice'])){
			$customer_or_agent = sanitize_text_field($_POST['salesking_user_choice']);
			update_user_meta( $user_id, 'salesking_user_choice', $customer_or_agent);	
		}
		// remove existing roles of salesking, and add new role
		$groups = get_posts([
		  'post_type' => 'salesking_group',
		  'post_status' => 'publish',
		  'numberposts' => -1,
		  'fields' => 'ids',
		]);

		$user_obj = new WP_User($user_id);
		foreach ($groups as $group){
			$user_obj->remove_role('salesking_role_'.$group);
		}
		$user_obj->add_role('salesking_role_'.$agent_group);

	}
	function salesking_add_columns_user_table ($columns){

	    $columns['salesking_group'] = esc_html__('Sales Agent','salesking');


		return $columns;
	}

	function salesking_retrieve_group_column_contents_users_table( $val, $column_name, $user_id ) {
	    if ($column_name === 'salesking_group') {

        	$agentgroup = get_user_meta( $user_id, 'salesking_group', true );

        	if (!empty($agentgroup) && $agentgroup !== 'none'){
            	$val = esc_html(get_the_title($agentgroup));
            } else {
            	// check if user has an assigned agent
            	$assignedagent = get_user_meta( $user_id, 'salesking_assigned_agent', true );
            	$choice = get_user_meta($user_id,'salesking_user_choice', true);
            	if (!empty($assignedagent) && $choice !== 'agent'){
            		// get agent name and link
            		$user = new WP_User($assignedagent);

            		if ($user){
            			if (current_user_can( 'administrator')){
            				$val = '<a href="'.esc_attr(get_edit_user_link($user->ID)).'">'.esc_html($user->user_login).'</a>';
            			} else {
            				$val = esc_html($user->user_login);
            			}
            		}
            		
            	} else {
            		$val = '-';
            	}
            }

	    }
	    return $val;
	}

	
	function salesking_settings_page() {
		// Admin Menu Settings 
		$page_title = esc_html__('SalesKing','salesking');
		$menu_title = esc_html__('SalesKing','salesking');
		$capability = 'manage_woocommerce';
		$slug = 'salesking';
		$callback = array( $this, 'salesking_settings_page_content' );

		$iconurl = plugins_url('../includes/assets/images/salesking-icon2.svg', __FILE__);
		$position = 57;
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $iconurl, $position );

		// Build plugin file path relative to plugins folder
		$absolutefilepath = dirname(plugins_url('', __FILE__),1);
		$pluginsurllength = strlen(plugins_url())+1;
		$relativepath = substr($absolutefilepath, $pluginsurllength);

		// Add the action links
		add_filter('plugin_action_links_'.$relativepath.'/salesking.php', array($this, 'salesking_action_links') );
		

	    if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
		    // Add "Earnings" submenu page
	    	add_submenu_page(
	            'salesking',
	            esc_html__('Earnings','salesking'), //page title
	            esc_html__('Earnings','salesking'), //menu title
	            'manage_woocommerce', //capability,
	            'salesking_earnings',//menu slug
	            array( $this, 'salesking_earnings_page_content' ), //callback function
	        	8
	        );	
	    } else {
		    // Add "Orders" submenu page
	    	add_submenu_page(
	            'salesking',
	            esc_html__('Orders','salesking'), //page title
	            esc_html__('Orders','salesking'), //menu title
	            'manage_woocommerce', //capability,
	            'salesking_earnings',//menu slug
	            array( $this, 'salesking_earnings_page_content' ), //callback function
	        	8
	        );	
	    }

        if (intval(get_option( 'salesking_enable_payouts_setting', 1 )) === 1){
    	    // Add "Payouts" submenu page
        	add_submenu_page(
                'salesking',
                esc_html__('Payouts','salesking'), //page title
                esc_html__('Payouts','salesking'), //menu title
                'manage_woocommerce', //capability,
                'salesking_payouts',//menu slug
                array( $this, 'salesking_payouts_page_content' ), //callback function
            	9
            );	
        }

        add_submenu_page(
	        'salesking',
	        esc_html__('Reports','marketking'), //page title
	        esc_html__('Reports','marketking'), //menu title
	        'manage_woocommerce', //capability,
	        'salesking_reports',//menu slug
	        array( $this, 'salesking_reports_page_content' ), //callback function
	    	9	
	    );

         // Add "Teams" submenu page
        if (intval(get_option( 'salesking_enable_teams_setting', 1 )) === 1){
			add_submenu_page(
		        'salesking',
		        esc_html__('Teams','salesking'), //page title
		        esc_html__('Teams','salesking'), //menu title
		        'manage_woocommerce', //capability,
		        'salesking_teams',//menu slug
		        array( $this, 'salesking_teams_page_content' ), //callback function
		    	10
		    );
		}


		// Add "Settings" submenu page
		add_submenu_page(
	        'salesking',
	        esc_html__('Settings','salesking'), //page title
	        esc_html__('Settings','salesking'), //menu title
	        'manage_woocommerce', //capability,
	        'salesking',//menu slug
	        '', //callback function
	    	11
	    );




	    // Individual Payout Page
    	add_submenu_page(
            null,
            esc_html__('View Payouts','salesking'), //page title
            esc_html__('View Payouts','salesking'), //menu title
            'manage_woocommerce', //capability,
            'salesking_view_payouts', //menu slug
            array( $this, 'salesking_view_payouts_content' ), //callback function
        	1
        );

	    // Individual Earning Backend Page
    	add_submenu_page(
            null,
            esc_html__('View Earnings','salesking'), //page title
            esc_html__('View Earnings','salesking'), //menu title
            'manage_woocommerce', //capability,
            'salesking_view_earnings', //menu slug
            array( $this, 'salesking_view_earnings_content' ), //callback function
        	1
        );


	}

	public static function salesking_get_dashboard_data(){

		require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
		$helper = new Salesking_Helper();

		$data = array();

		// get all orders in past 31 days for calculations
		global $wpdb;

		$timezone = get_option('timezone_string');
		if (empty($timezone) || $timezone === null){
			$timezone = 'UTC';
		}
		date_default_timezone_set($timezone);

		$date_to = date('Y-m-d H:i:s');
		$date_from = date('Y-m-d');

		$post_status = implode("','", array('wc-processing', 'wc-completed') );
		$orders_today = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
		            WHERE post_type = 'shop_order'
		            AND post_status IN ('{$post_status}')
		            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
		        ");

		$date_from = date('Y-m-d', strtotime('-6 days'));
		$orders_seven_days = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
		            WHERE post_type = 'shop_order'
		            AND post_status IN ('{$post_status}')
		            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
		        ");

		$date_from = date('Y-m-d', strtotime('-30 days'));
		$orders_thirtyone_days = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
		            WHERE post_type = 'shop_order'
		            AND post_status IN ('{$post_status}')
		            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
		        ");

		// total b2b sales
		$total_b2b_sales_today = 0;
		$total_b2b_sales_seven_days = 0;
		$total_b2b_sales_thirtyone_days = 0;

		// total tax
		$tax_b2b_sales_today = 0;
		$tax_b2b_sales_seven_days = 0;
		$tax_b2b_sales_thirtyone_days = 0;

		// nr of orders
		$number_b2b_sales_today = 0;
		$number_b2b_sales_seven_days = 0;
		$number_b2b_sales_thirtyone_days = 0;

		// nr of vendor signups
		$signups_b2b_sales_today = 0;
		$signups_b2b_sales_seven_days = 0;
		$signups_b2b_sales_thirtyone_days = 0;

		// today signups
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
	                'after'     => date('Y-m-d H:i:s', strtotime('-1 days')),
	                'inclusive' => true,
	            ),
	         )
		));
		$signups_b2b_sales_today = count($vendors);

		// 7 day signups
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
	                'after'     => date('Y-m-d H:i:s', strtotime('-7 days')),
	                'inclusive' => true,
	            ),
	         )
		));
		$signups_b2b_sales_seven_days = count($vendors);


		// 31 day signups
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
	                'after'     => date('Y-m-d H:i:s', strtotime('-31 days')),
	                'inclusive' => true,
	            ),
	         )
		));
		$signups_b2b_sales_thirtyone_days = count($vendors);


		//calculate today
		foreach ($orders_today as $order){

			$total_b2b_sales_today += get_post_meta($order->ID,'_order_total', true);
			$tax_b2b_sales_today += get_post_meta($order->ID,'_order_tax', true)+get_post_meta($order->ID,'_order_shipping_tax', true);
			$number_b2b_sales_today++;
		}

		//calculate seven days
		foreach ($orders_seven_days as $order){

			$total_b2b_sales_seven_days += get_post_meta($order->ID,'_order_total', true);
			$tax_b2b_sales_seven_days += get_post_meta($order->ID,'_order_tax', true)+get_post_meta($order->ID,'_order_shipping_tax', true);
			$number_b2b_sales_seven_days++;
		}

		//calculate thirtyone days
		foreach ($orders_thirtyone_days as $order){

			$total_b2b_sales_thirtyone_days += get_post_meta($order->ID,'_order_total', true);
			$tax_b2b_sales_thirtyone_days += get_post_meta($order->ID,'_order_tax', true)+get_post_meta($order->ID,'_order_shipping_tax', true);
			$number_b2b_sales_thirtyone_days++;
		}


		// get each day in the past 31 days and form an array with day and total sales
		$i=1;
		$days_sales_array = array();
		$hours_sales_array = array(
			'00' => 0,
			'01' => 0,
			'02' => 0,
			'03' => 0,
			'04' => 0,
			'05' => 0,
			'06' => 0,
			'07' => 0,
			'08' => 0,
			'09' => 0,
			'10' => 0,
			'11' => 0,
			'12' => 0,
			'13' => 0,
			'14' => 0,
			'15' => 0,
			'16' => 0,
			'17' => 0,
			'18' => 0,
			'19' => 0,
			'20' => 0,
			'21' => 0,
			'22' => 0,
			'23' => 0,
		);

		while ($i<32){
			$date_from = $date_to = date('Y-m-d', strtotime('-'.($i-1).' days'));

			$post_status = implode("','", array('wc-processing', 'wc-completed') );

			if ($i===1){
				$date_to = date('Y-m-d H:i:s');
				$date_from = date('Y-m-d');
				$orders_day = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
			            WHERE post_type = 'shop_order'
			            AND post_status IN ('{$post_status}')
			            AND post_date BETWEEN '{$date_from} 00:00:00' AND '{$date_to}'
			        ");
			} else {
				$orders_day = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
			            WHERE post_type = 'shop_order'
			            AND post_status IN ('{$post_status}')
			            AND post_date BETWEEN '{$date_from} 00:00:00' AND '{$date_to} 23:59:59'
			        ");
			}
			//calculate totals
			$sales_total = 0;
			foreach ($orders_day as $order){
				$order_user_id = get_post_meta($order->ID,'_customer_user', true);

				$sales_total += get_post_meta($order->ID,'_order_total', true);
			}

			// if first day, get this by hour
			if ($i===1){
				$date_to = date('Y-m-d H:i:s');
				$date_from = date('Y-m-d');

				$post_status = implode("','", array('wc-processing', 'wc-completed') );
				$orders_day = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
				            WHERE post_type = 'shop_order'
				            AND post_status IN ('{$post_status}')
				            AND post_date BETWEEN '{$date_from} 00:00:00' AND '{$date_to}'
				        ");

				foreach ($orders_day as $order){
					// get hour of the order
					$hour = get_post_time('H', false, $order->ID);
					$hours_sales_array[$hour] += get_post_meta($order->ID,'_order_total', true);
				}
			}

			array_push ($days_sales_array, $sales_total);
			$i++;
		}

		// get admin commissions
		$earnings_today = $helper->get_earnings('allagents', 'last_days', 1, false, false, false);
		$earnings_seven_days = $helper->get_earnings('allagents', 'last_days', 7, false, false, false);
		$earnings_thirtyone_days = $helper->get_earnings('allagents', 'last_days', 31, false, false, false);

		$data['days_sales_array'] = $days_sales_array;
		$data['hours_sales_array'] = $hours_sales_array;
		$data['total_b2b_sales_today'] = $total_b2b_sales_today;
		$data['total_b2b_sales_seven_days'] = $total_b2b_sales_seven_days;
		$data['total_b2b_sales_thirtyone_days'] = $total_b2b_sales_thirtyone_days;
		$data['number_b2b_sales_today'] = $number_b2b_sales_today;
		$data['number_b2b_sales_seven_days'] = $number_b2b_sales_seven_days;
		$data['number_b2b_sales_thirtyone_days'] = $number_b2b_sales_thirtyone_days;
		$data['signups_b2b_sales_today'] = $signups_b2b_sales_today;
		$data['signups_b2b_sales_seven_days'] = $signups_b2b_sales_seven_days;
		$data['signups_b2b_sales_thirtyone_days'] = $signups_b2b_sales_thirtyone_days;

		$data['earnings_today'] = $earnings_today;
		$data['earnings_seven_days'] = $earnings_seven_days;
		$data['earnings_thirtyone_days'] = $earnings_thirtyone_days;



		return $data;
	}

	function salesking_reports_page_content(){

		echo self::get_header_bar();		

		$data = self::salesking_get_dashboard_data();

		require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
		$helper = new Salesking_Helper();
		
		// Send data to JS
		$translation_array = array(
			'days_sales_b2b' => $data['days_sales_array'],
			'hours_sales_b2b' => array_values($data['hours_sales_array']),
			'currency_symbol' => get_woocommerce_currency_symbol(),
		);

		wp_localize_script( 'salesking_global_admin_script', 'salesking_dashboard', $translation_array );

		?>
		<div id="salesking_dashboard_wrapper">
		    <div class="salesking_dashboard_page_wrapper salesking_reports_page_wrapper">
		        <div class="container-fluid">
		            <div class="row">
		                <div class="col-12">
		                    <div class="card card-hover">
		                        <div class="card-body">
		                            <div class="d-md-flex align-items-center">
		                                <div>
		                                    <h3 class="card-title"><?php esc_html_e('Sales Reports','salesking');?></h3>
		                                    <h5 class="card-subtitle"><?php esc_html_e('Total Sales Value','salesking');?></h5>
		                                </div>
		                                <div class="ml-auto d-flex no-block align-items-center">
		                                    <ul class="list-inline font-12 dl m-r-15 m-b-0">
		                                        <li class="list-inline-item text-primary"><i class="icon salesking-ni salesking-ni-circle-fill"></i> <?php esc_html_e('Commission','salesking');?></li>
		                                        <li class="list-inline-item text-cyan"><i class="icon salesking-ni salesking-ni-circle-fill"></i> <?php esc_html_e('Total Sales','salesking');?></li>
		                                        <li class="list-inline-item text-info"><i class="icon salesking-ni salesking-ni-circle-fill"></i> <?php esc_html_e('Number of Orders','salesking');?></li>
		                                        
		                                    </ul>
		                                    <div class="salesking_reports_topright_container">
			                                    <div class="dl salesking_reports_topright">
			                                        <select id="salesking_dashboard_days_select" class="custom-select">
			                                            <option value="all" selected><?php esc_html_e('All Agents (Store)','salesking');?></option>
			                                            <optgroup label="<?php esc_html_e('Agents', 'salesking'); ?>">

				                                            <?php

				                                            $vendors = get_users(array(
                                        	    			    'meta_key'     => 'salesking_group',
                                        	    			    'meta_value'   => 'none',
                                        	    			    'meta_compare' => '!=',
                                        	    			));
				                                            foreach ($vendors as $vendor){
				                                            	?>
		                                	                    <option value="<?php echo esc_attr( $vendor->ID ); ?>"><?php
		                                		                    echo apply_filters('salesking_agent_display_name_filter', $vendor->display_name. '('.$vendor->user_login.')', $vendor)
		                                	                    ?></option>
				                                            	<?php
				                                            }
				                                            ?>
				                                        </optgroup>	
			                                        </select>
			                                        <div class="salesking_reports_fromto">
				                                        <div class="salesking_reports_fromto_text"><?php esc_html_e('From:','salesking'); ?></div>
				                                        <input type="date" class="salesking_reports_date_input salesking_reports_date_input_from">
				                                    </div>
				                                    <div class="salesking_reports_fromto">
				                                        <div class="salesking_reports_fromto_text"><?php esc_html_e('To:','salesking'); ?></div>
				                                        <input type="date" class="salesking_reports_date_input salesking_reports_date_input_to">
				                                    </div>	
			                                    </div>
			                                    <div id="salesking_reports_quick_links">
			                                    	<div class="salesking_reports_linktext"><?php esc_html_e('Quick Select:','salesking'); ?></div>
			                                    	<a id="salesking_reports_link_thismonth" hreflang="thismonth" class="salesking_reports_link"><?php esc_html_e('This Month','salesking'); ?></a>
			                                    	<a hreflang="lastmonth" class="salesking_reports_link"><?php esc_html_e('Last Month','salesking'); ?></a>
			                                    	<a hreflang="thisyear" class="salesking_reports_link"><?php esc_html_e('This Year','salesking'); ?></a>
			                                    	<a hreflang="lastyear" class="salesking_reports_link"><?php esc_html_e('Last Year','salesking'); ?></a>
			                                    </div>
			                                </div>


		                                </div>
		                            </div>
		                            <div class="row">
		                                <!-- column -->
		                                <div class="col-lg-3">
		                                    <h1 class="salesking_total_b2b_sales_today m-b-0 m-t-30"><?php echo wc_price($data['total_b2b_sales_today']); ?></h1>
		                                    <h6 class="font-light text-muted"><?php esc_html_e('Sales','salesking');?></h6>
		                                    <h3 class="salesking_number_orders_today m-t-30 m-b-0"><?php echo esc_html($data['number_b2b_sales_today']); ?></h3>
		                                    <h6 class="font-light text-muted"><?php esc_html_e('Orders','salesking');?></h6>
		                                    <a id="salesking_dashboard_blue_button" class="btn btn-info m-t-20 p-15 p-l-25 p-r-25 m-b-20" href="javascript:void(0)"></a>
		                                </div>
		                                <!-- column -->
		                                <div class="col-lg-9">
		                                    <div class="campaign ct-charts"></div>
		                                </div>
		                                <div class="col-lg-3">
		                                </div>
		                                <div class="col-lg-9">
		                                    <div class="campaign2 ct-charts"></div>
		                                </div>
		                                <!-- column -->
		                            </div>
		                        </div>
		                        <!-- ============================================================== -->
		                        <!-- Info Box -->
		                        <!-- ============================================================== -->
		                        <div class="card-body border-top">
		                            <div class="row m-b-0">
		                            	<!-- col -->
		                            	<div class="col-lg-3 col-md-6">
		                            	    <div class="d-flex align-items-center">
		                            	        <div class="m-r-10"><span class="text-orange display-5"><i class="icon salesking-ni salesking-ni-user-circle-fill"></i></span></div>
		                            	        <div><span><?php esc_html_e('New Agents','salesking');?></span>
		                            	            <h3 class="salesking_number_customers_today font-medium m-b-0"><?php echo esc_html($data['signups_b2b_sales_today']); ?></h3>
		                            	        </div>
		                            	    </div>
		                            	</div>
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-cyan display-5"><i class="icon salesking-ni salesking-ni-cart-fill"></i></span></div>
		                                        <div><span><?php esc_html_e('Total Sales','salesking');?></span>
		                                            <h3 class="salesking_total_b2b_sales_today font-medium m-b-0">
		                                            	<?php echo wc_price($data['total_b2b_sales_today']); ?>
		                                           	</h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-info display-5"><i class="icon salesking-ni salesking-ni-package-fill"></i></span></div>
		                                        <div><span><?php esc_html_e('Number of Orders','salesking');?></span>
		                                            <h3 class="salesking_number_orders_today font-medium m-b-0"><?php echo esc_html($data['number_b2b_sales_today']); ?></h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-primary display-5"><i class="icon salesking-ni salesking-ni-reports"></i></span></div>
		                                        <div><span><?php esc_html_e('Commission','salesking');?></span>
		                                            <h3 class="salesking_net_earnings_today font-medium m-b-0"><?php echo wc_price($data['earnings_today']); ?></h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>

		        </div>
		    </div>
		</div>
		<?php
	}
	
	function salesking_action_links( $links ) {
		// Build and escape the URL.
		$url = esc_url( add_query_arg('page', 'salesking', get_admin_url() . 'admin.php') );

		// Create the link.
		$settings_link = '<a href='.esc_attr($url).'>' . esc_html__( 'Settings', 'salesking' ) . '</a>';
		
		// Adds the link to the end of the array.
		array_unshift($links,	$settings_link );
		return $links;
	}

	
	function salesking_settings_init(){
		require_once ( SALESKING_DIR . 'admin/class-salesking-settings.php' );
		$settings = new Salesking_Settings;
		$settings-> register_all_settings();

		// if a POST variable exists indicating the user saved settings, flush permalinks
		if (isset($_POST['salesking_plugin_status_setting'])){
			require_once ( SALESKING_DIR . 'public/class-salesking-public.php' );
			$publicobj = new Salesking_Public;
			$this->salesking_register_post_type_agent_groups();
			$this->salesking_register_post_type_conversation();
			$this->salesking_register_post_type_announcement();
			$this->salesking_register_post_type_dynamic_rules();
			$this->salesking_register_post_type_custom_role();
			$this->salesking_register_post_type_custom_field();
			$publicobj->salesking_custom_endpoints();

			flush_rewrite_rules();

		}
	}

	function salesking_teams_page_content(){

		// get all agents
	    $users = get_users(array(
		    'meta_key'     => 'salesking_group',
		    'meta_value'   => 'none',
		    'meta_compare' => '!=',
		));

		echo self::get_header_bar();		


		?>

		<h1 class="salesking_page_title"><?php esc_html_e('Teams','salesking');?></h1>
		<div id="salesking_admin_customers_table_container">
			<table id="salesking_admin_customers_table">
			        <thead>
			            <tr>
			                <th><?php esc_html_e('Name','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Group','salesking'); ?></th>
			                <th><?php esc_html_e('Account Type','salesking'); ?></th>
			                <th><?php esc_html_e('Subagent of','salesking'); ?></th>
			            </tr>
			        </thead>
			        <tbody>
			        	<?php

			        	foreach ( $users as $user ) {

			        		$user_id = $user->ID;
			        		$original_user_id = $user_id;
			        		$username = trim($user->first_name.' '.$user->last_name);
			        		if (empty($username)){
			        			$username = $user->user_login;
			        		} 

			        		$username = apply_filters('salesking_teams_username_display', $username, $user);


			        		// first check if subaccount. If subaccount, user is equivalent with parent
			        		$parent_account_id = get_user_meta($user_id, 'salesking_parent_agent', true);
			        		
			        		if (!empty($parent_account_id)){
			        			// get parent
			        			$parent_ag = get_user_by('id', $parent_account_id);
			        			$parent_name = $parent_ag->user_login;
			        			$account_type = esc_html__('Subagent','b2bking');
			        		} else {
			        			$account_type = esc_html__('Main agent account','b2bking');
			        			$parent_name = '-';
			        		}

			        		$group_name = get_the_title(get_user_meta($user_id, 'salesking_group', true));
			        		if (empty($group_name)){
			        			$group_name = '-';
			        		}

			        		?>
			        		<tr>
			        		    <td><a href="<?php echo esc_attr(get_edit_user_link($original_user_id));?>">
			        		    	<?php echo esc_html( $username ); ?></a></td>
			        		    <td><?php echo esc_html( $group_name ); ?></td>
			        		    <td><?php echo esc_html( $account_type ); ?></td>
			        		    <?php
			        		    if ($parent_name === '-'){
			        		    	?>
			        		    	<td><?php echo esc_html( $parent_name ); ?></td>
			        		    	<?php
			        		    } else {
			        		    	?>
			        		    	<td><a href="<?php echo esc_attr(get_edit_user_link($parent_account_id));?>"><?php echo esc_html( $parent_name ); ?></a></td>

			        		    	<?php
			        		    }
			        		    ?>
			        		</tr>
				           <?php
				       }
				       ?>
			        </tbody>
			        <tfoot>
			            <tr>
			                <th><?php esc_html_e('Name','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Group','salesking'); ?></th>
			                <th><?php esc_html_e('Account Type','salesking'); ?></th>
			                <th><?php esc_html_e('Subagent of','salesking'); ?></th>
			            </tr>
			        </tfoot>
			    </table>
			</div>
		<?php
	}

	function salesking_earnings_page_content(){
		// get all agents
		$users = get_users(array(
		    'meta_key'     => 'salesking_group',
		    'meta_value'   => 'none',
		    'meta_compare' => '!=',
		));

		echo self::get_header_bar();		

		?>
		<h1 class="salesking_page_title"><?php esc_html_e('Earnings','salesking');?></h1>
		<div id="salesking_admin_earnings_table_container">
			<table id="salesking_admin_earnings_table">
			        <thead>
			            <tr>
			            	<th><?php esc_html_e('Agent ID','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Name','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Group','salesking'); ?></th>
			                <th><?php esc_html_e('Total Orders Value','salesking'); ?></th>
			                <?php
			                if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
			                	?>
			                	<th><?php esc_html_e('Completed Commissions','salesking'); ?></th>
			                	<th><?php esc_html_e('Pending Commissions','salesking'); ?></th>
			                	<?php
			                }
			                ?>
			                <th><?php esc_html_e('Actions','salesking'); ?></th>

			            </tr>
			        </thead>
			        <tbody>
			        	<?php

			        	foreach ( $users as $user ) {

			        		$user_id = $user->ID;
			        		$original_user_id = $user_id;
			        		$username = $user->user_login;
			        		$name = $user->first_name.' '.$user->last_name;

			        		$group_name = get_the_title(get_user_meta($user_id, 'salesking_group', true));
			        		if (empty($group_name)){
			        			$group_name = '-';
			        		}

			        		$total_orders_amount = $total_agent_commissions = $pending_agent_commissions = 0;
			        		// get total orders amount

			        		// get total agent commissions
			        		$earnings = get_posts( array( 
			        		    'post_type' => 'salesking_earning',
			        		    'numberposts' => -1,
			        		    'post_status'    => 'any',
			        		    'fields'    => 'ids',
			        		    'meta_key'   => 'agent_id',
			        		    'meta_value' => $user_id,
			        		));

			        		foreach ($earnings as $earning_id){
			        		    $order_id = get_post_meta($earning_id,'order_id', true);
			        		    $orderobj = wc_get_order($order_id);
			        		    if ($orderobj !== false){
				        		    $earnings_total = get_post_meta($earning_id,'salesking_commission_total', true);
				        		    if (!empty($earnings_total) && floatval($earnings_total) !== 0){
				        		        $status = $orderobj->get_status();
				        		        $order_total = apply_filters('salesking_earnings_order_value_total',$orderobj->get_total(), $orderobj);
				        		        if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
				        		        	$total_agent_commissions+=$earnings_total;
				        		        	$total_orders_amount += $order_total;
				        		        } else if (!in_array($status, array('refunded','cancelled','failed'))){
				        		        	$pending_agent_commissions+=$earnings_total;
				        		        }
				        		    }
				        		}
			        		}

			        		$site_time = time()+(get_option('gmt_offset')*3600);
			        		$current_day = date_i18n( 'd', $site_time );

			        		// also get all earnings where this agent is parent
			        		$earnings = get_posts( array( 
			        		    'post_type' => 'salesking_earning',
			        		    'numberposts' => -1,
			        		    'post_status'    => 'any',
			        		    'fields'    => 'ids',
			        		    'meta_key'   => 'parent_agent_id_'.$user_id,
			        		    'meta_value' => $user_id,
			        		));

			        		foreach ($earnings as $earning_id){
			        		    $order_id = get_post_meta($earning_id,'order_id', true);
			        		    $orderobj = wc_get_order($order_id);
			        		    if ($orderobj !== false){
				        		    $status = $orderobj->get_status();
				        		    $earnings_total = get_post_meta($earning_id,'parent_agent_id_'.$user_id.'_earnings', true);
				        		    // check if approved
				        		    if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
				        		        $total_agent_commissions+=$earnings_total;
				        		    } else if (!in_array($status, array('refunded','cancelled','failed'))){
			        		        	$pending_agent_commissions+=$earnings_total;
			        		        }
				        		}
			        		}

			        		// if want to show total order value for all assigned orders instead
			        		if(apply_filters('salesking_show_total_order_value_all_assigned_orders',false)){
			        			// calculate new total orders amount
        		        		$earnings = get_posts( array( 
        		        		    'post_type' => 'shop_order',
        		        		    'numberposts' => -1,
        		        		    'post_status'    => 'any',
        		        		    'fields'    => 'ids',
        		        		    'meta_key'   => 'salesking_assigned_agent',
        		        		    'meta_value' => $user_id,
        		        		));
        		        		$total_orders_amount = 0;

        		        		foreach ($earnings as $earning_id){
        		        		    $order_id = $earning_id;
        		        		    $orderobj = wc_get_order($order_id);
        		        		    if ($orderobj !== false){
    			        		        $status = $orderobj->get_status();
    			        		        $order_total = $orderobj->get_total();
    			        		        if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
    			        		        	$total_orders_amount += $order_total;
    			        		        }
        			        		}
        		        		}

			        		}

			        		if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
			        		
				        		echo
				        		'<tr>
				        			<td><strong>'.esc_html( $original_user_id ).'</strong></td>
				        		    <td><a href="'.esc_attr(get_edit_user_link($original_user_id)).'">'.esc_html( $name ).' ('.$username.')</a></td>
				        		    <td>'.esc_html( $group_name ).'</td>
				        		    <td data-order="'.esc_attr($total_orders_amount).'">'.wc_price( $total_orders_amount ).'</td>
				        		    <td data-order="'.esc_attr($total_agent_commissions).'">'.wc_price( $total_agent_commissions ).'</td>
				        		    <td data-order="'.esc_attr($pending_agent_commissions).'">'.wc_price( $pending_agent_commissions ).'</td>
				        		    <td><a href="'.admin_url( 'admin.php?page=salesking_view_earnings').'&user='.esc_attr($original_user_id).'"><button type="button" class="salesking_manage_earnings_button">'.esc_html__('View Earnings','salesking').'</button></a></td>

				        		</tr>';
				        	} else {
				        		// calculate new total orders amount
        		        		$earnings = get_posts( array( 
        		        		    'post_type' => 'shop_order',
        		        		    'numberposts' => -1,
        		        		    'post_status'    => 'any',
        		        		    'fields'    => 'ids',
        		        		    'meta_key'   => 'salesking_assigned_agent',
        		        		    'meta_value' => $user_id,
        		        		));
        		        		$total_orders_amount = 0;

        		        		foreach ($earnings as $earning_id){
        		        		    $order_id = $earning_id;
        		        		    $orderobj = wc_get_order($order_id);
        		        		    if ($orderobj !== false){
    			        		        $status = $orderobj->get_status();
    			        		        $order_total = $orderobj->get_total();
    			        		        if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
    			        		        	$total_orders_amount += $order_total;
    			        		        }
        			        		}
        		        		}


				        		echo
				        		'<tr>
				        			<td><strong>'.esc_html( $original_user_id ).'</strong></td>
				        		    <td><a href="'.esc_attr(get_edit_user_link($original_user_id)).'">'.esc_html( $name ).' ('.$username.')</a></td>
				        		    <td>'.esc_html( $group_name ).'</td>
				        		    <td data-order="'.esc_attr($total_orders_amount).'">'.wc_price( $total_orders_amount ).'</td>
				        		    <td><a href="'.admin_url( 'admin.php?page=salesking_view_earnings').'&user='.esc_attr($original_user_id).'"><button type="button" class="salesking_manage_earnings_button">'.esc_html__('View Orders','salesking').'</button></a></td>

				        		</tr>';

				        	}
			        	}

			        	?>
			           
			        </tbody>
			        <tfoot>
			            <tr>
			            	<th><?php esc_html_e('Agent ID','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Name','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Group','salesking'); ?></th>
			                <th><?php esc_html_e('Total Orders Value','salesking'); ?></th>
			                <?php  
			                if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
			                	?>
			               	 	<th><?php esc_html_e('Total Agent Commissions','salesking'); ?></th>
			               	 	<th><?php esc_html_e('Pending Agent Commissions','salesking'); ?></th>
			               	 	<?php
			               	 }
			               	 ?>
			                <th><?php esc_html_e('Actions','salesking'); ?></th>

			            </tr>
			        </tfoot>
			    </table>
			</div>
		<?php
	}

	function salesking_show_header_bar_salesking_posts(){
		global $post;
		if (isset($post->ID)){
			$post_type = get_post_type($post->ID);
			if (substr($post_type,0,9) === 'salesking'){
				echo self::get_header_bar();
			}
		} else {
			if (isset($_GET['post_type'])){
				if (substr($_GET['post_type'],0,9) === 'salesking'){
					echo self::get_header_bar();
				}
			}
		}
	}

	public static function get_header_bar(){
		
		?>
		<div id="salesking_admin_header_bar">
			<div id="salesking_admin_header_bar_left">
				<img style="width: 127px;position: relative;top: 0.5px;" src="<?php echo plugins_url('../includes/assets/images/saleskinglogo3.png', __FILE__); ?>">
				<div id="salesking_admin_header_version2"><?php echo SALESKING_VERSION; ?></div>
			</div>
			<div id="salesking_admin_header_bar_right">
				<?php
				$supportlink = 'https://webwizards.ticksy.com';

				?>
				<a class="salesking_admin_header_right_element" href="https://woocommerce-b2b-plugin.com/sales-agents-reps/salesking-documentation/"><span class="dashicons <?php echo apply_filters('salesking_header_documentation_dashicon','dashicons-edit-page');?> salesking_header_icon"></span><?php esc_html_e('Documentation', 'salesking');?></a>
				<a class="salesking_admin_header_right_element" href="<?php echo esc_attr($supportlink);?>"><span class="dashicons dashicons-universal-access-alt salesking_header_icon"></span><?php esc_html_e('Support', 'salesking');?></a>
				
			</div>
		</div>
		<?php
	}

	function salesking_view_earnings_content(){

		echo self::get_header_bar();		

		if (isset($_GET['user'])){
			$user_id = sanitize_text_field($_GET['user']);
		} else {
			$user_id = 0;
		}
		
		$userinfo = get_userdata($user_id);
		$info = base64_decode(get_user_meta($user_id,'salesking_payout_info', true));
		$info = explode('**&&', $info);

		?>
		<!-- User-specific shipping and payment methods -->
		<div class="salesking_user_shipping_payment_methods_container salesking_special_group_container">
			<input type="hidden" name="salesking_admin_user_id" value="<?php echo esc_attr($user_id);?>">
			<div class="salesking_above_top_title_button">
				<div class="salesking_above_top_title_button_left">
					<?php 
					if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
						esc_html_e('Agent Earnings','salesking');
					} else {
						esc_html_e('Agent Orders','salesking');
					} 
					?>
				</div>
				<div class="salesking_above_top_title_button_right">
					<a href="<?php echo admin_url( 'admin.php?page=salesking_earnings'); ?>">
						<button type="button" class="salesking_above_top_title_button_right_button">
							<?php esc_html_e('←  Go Back','salesking'); ?>
						</button>
					</a>
				</div>
			</div>
			<div class="salesking_user_shipping_payment_methods_container_top">
				<div class="salesking_user_shipping_payment_methods_container_top_title">
					<?php 
					if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
						esc_html_e('Earnings of','salesking'); echo ': '.esc_html($userinfo->user_login); 
					} else {
						esc_html_e('Orders of','salesking'); echo ': '.esc_html($userinfo->user_login); 
					}
					?>
					
				</div>		
			</div>

			<!-- BEGIN CONTENT -->
			<div class="salesking_user_payouts_container">
			  <!-- 3. TRANSACTION HISTORY SECTION -->
			    <div class="salesking_user_registration_user_data_container_title">
			        <svg class="salesking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
			          <path fill="#C4C4C4" d="M29.531 0H3.281A3.29 3.29 0 000 3.281V31.72A3.29 3.29 0 003.281 35h26.25a3.29 3.29 0 003.282-3.281V3.28A3.29 3.29 0 0029.53 0zm-1.093 30.625H4.375V4.375h24.063v26.25zM8.75 15.312h15.313V17.5H8.75v-2.188zm0 4.376h15.313v2.187H8.75v-2.188zm0 4.375h15.313v2.187H8.75v-2.188zm0-13.125h15.313v2.187H8.75v-2.188z"/>
			        </svg>
			        <?php 
			        if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
			        	esc_html_e('Earnings History','salesking'); 
			        } else {
			        	esc_html_e('Order History','salesking'); 
			        }
			        ?>
			        
			    </div>
			    <?php
			    if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
			    	?>
				    <div class="salesking_user_registration_user_data_container_element">
				        <div class="salesking_user_registration_user_data_container_element_label">
				            <?php esc_html_e('current outstanding balance (unpaid earnings)','salesking'); ?>
				        </div>
				        <?php
				        $user_outstanding_earnings = get_user_meta($user_id,'salesking_outstanding_earnings', true);
				        if (empty($user_outstanding_earnings)){ // no earnings yet
				        	$user_outstanding_earnings = 0;
				        }
				        ?>
				        <input type="text" class="salesking_user_registration_user_data_container_element_text" value="<?php echo strip_tags(wc_price($user_outstanding_earnings));?>" readonly>
				    </div>
				    <?php
				}
				?>
			    <br />
			    <h3><?php 
			    if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
			    	esc_html_e('Agent direct earnings', 'salesking');
				}
				?></h3>
			    <table id="salesking_payout_history_table">
			        <thead>
			            <tr>
			                <th><?php esc_html_e('Order','salesking'); ?></th>
			                <th><?php esc_html_e('Date','salesking'); ?></th>
			                <th><?php esc_html_e('Status','salesking'); ?></th>
			                <th><?php esc_html_e('Customer','salesking'); ?></th>
			                <th><?php esc_html_e('Purchased','salesking'); ?></th>
			                <th><?php esc_html_e('Order Value','salesking'); ?></th>
			                <th><?php esc_html_e('Payment Method','salesking'); ?></th>
			                <th><?php esc_html_e('Coupon Used','salesking'); ?></th>

			                <?php
			                if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
			                	?>
			               		<th><?php esc_html_e('Agent Earnings','salesking'); ?></th>
			               		<?php
			               	}
			               	?>
			            </tr>	
			        </thead>
			        <tbody>

		        	<?php


			        	if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){

				        	$earnings = get_posts( array( 
				        	    'post_type' => 'salesking_earning',
				        	    'numberposts' => -1,
				        	    'post_status'    => 'any',
				        	    'fields'    => 'ids',
				        	    'meta_key'   => 'agent_id',
				        	    'meta_value' => $user_id,
				        	));

				        	foreach ($earnings as $earning_id){
				        	    $order_id = get_post_meta($earning_id,'order_id', true);
				        	    $orderobj = wc_get_order($order_id);
				        	    if ($orderobj !== false){
					        	    $earnings_total = get_post_meta($earning_id,'salesking_commission_total', true);
					        	    if (!empty($earnings_total) && floatval($earnings_total) !== 0){
					        	        ?>
					        	        <tr class="nk-tb-item">
					        	            <td><a href="<?php echo esc_attr(get_edit_post_link($order_id));?>">#<?php echo esc_html($order_id);?></a></td>
					        	            <td data-order="<?php 
		  		        	                    $date = explode('T',$orderobj->get_date_created())[0];
		  		        	                    echo strtotime($date);
					        	            ?>"><?php 
				        	                 //   echo date('F j, Y', strtotime($date));
				        	                    echo date_i18n( get_option('date_format'), strtotime($date) ); 

											?>
					        	            </td>
					        	            <td> 
					        	            	<?php
					        	                    $status = $orderobj->get_status();
					        	                    $statustext = $badge = '';
					        	                    if ($status === 'processing'){
					        	                        $badge = 'badge-warning';
					        	                        $statustext = esc_html__('Pending Order Completion','salesking');
					        	                    } else if ($status === 'on-hold'){
					        	                        $badge = 'badge-warning';
					        	                        $statustext = esc_html__('Pending Order Completion','salesking');
					        	                    } else if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
					        	                        $badge = 'badge-success';
					        	                        $statustext = esc_html__('Completed','salesking');
					        	                    } else if ($status === 'refunded'){
					        	                        $badge = 'badge-danger';
					        	                        $statustext = esc_html__('Order Refunded','salesking');
					        	                    } else if ($status === 'cancelled'){
					        	                        $badge = 'badge-danger';
					        	                        $statustext = esc_html__('Order Cancelled','salesking');
					        	                    } else if ($status === 'pending'){
					        	                        $badge = 'badge-warning';
					        	                        $statustext = esc_html__('Pending Order Payment','salesking');
					        	                    } else if ($status === 'failed'){
					        	                        $badge = 'badge-danger';
					        	                        $statustext = esc_html__('Order Failed','salesking');
					        	                    }
					        	                    
					        	                    echo esc_html($statustext);
					        	            ?></td>
					        	            <td><?php
					        	                     $name = $orderobj->get_billing_first_name().' '.$orderobj->get_billing_last_name();
					        	                     echo $name;
					        	                     ?>
					        	            </td>
					        	            <td><?php
					        	                    $items = $orderobj->get_items();
					        	                    $items_count = count( $items );
					        	                    if ($items_count > 4){
					        	                        echo $items_count.' '.esc_html__('Items', 'salesking');
					        	                    } else {
					        	                        // show the items
					        	                        foreach ($items as $item){
					        	                            echo $item->get_name().' x '.$item->get_quantity().'<br>';
					        	                        }
					        	                    }
					        	                    ?>
					        	            </td>
					        	            <td data-order="<?php echo esc_attr($orderobj->get_total());?>"> 
					        	               <?php echo wc_price($orderobj->get_total());?>
					        	            </td>
					        	            <td>
					        	            	<?php echo $orderobj->get_payment_method_title(); ?>
					        	            </td>
					        	            <td>
					        	            	<?php 

					        	            	$coupons = $orderobj->get_coupon_codes();
					        	            	if (empty($coupons)){
					        	            		echo '-';
					        	            	} else {
					        	            		foreach( $coupons as $coupon_code ){
					        	            			echo $coupon_code;
					        	            		}
					        	            	}
					        	            	?>
					        	            </td>


					        	            <?php
					        	            if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
					        	            	?>
						        	            <td data-order="<?php echo esc_attr($earnings_total);?>"> 
					        	                    <?php
					        	                    if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
					        	                        $text_color = 'text-success';
					        	                    } else {
					        	                        $text_color = 'text-soft';
					        	                    }
					        	                    ?>
					        	                    <span class="tb-lead <?php echo esc_attr($text_color);?>"><?php 
					        	                    
					        	                    echo wc_price($earnings_total);
					        	                    if (!in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
					        	                        esc_html_e(' (pending)', 'salesking');
					        	                    }
					        	                    ?></span>
						        	            </td>
						        	            <?php
						        	        }
						        	        ?>
					        	        </tr>
					        	    <?php
					        	    }
					        	}
				        	}

				        } else {
				        	// orders only
    			        	$earnings = get_posts( array( 
    			        	    'post_type' => 'shop_order',
    			        	    'numberposts' => -1,
    			        	    'post_status'    => 'any',
    			        	    'fields'    => 'ids',
    			        	    'meta_key'   => 'salesking_assigned_agent', // alternatively salesking_order_place_by for only orders  placed by the agent
    			        	    'meta_value' => $user_id,
    			        	));

    			        	foreach ($earnings as $earning_id){
    			        	    $order_id = $earning_id;
    			        	    $orderobj = wc_get_order($order_id);
    			        	    if ($orderobj !== false){
    				        	    ?>
    				        	        <tr class="nk-tb-item">
    				        	            <td><a href="<?php echo esc_attr(get_edit_post_link($order_id));?>">#<?php echo esc_html($order_id);?></a></td>
    				        	            <td data-order="<?php 
    	  		        	                    $date = explode('T',$orderobj->get_date_created())[0];
    	  		        	                    echo strtotime($date);
    				        	            ?>"><?php 
    			        	                    echo date('F j, Y', strtotime($date));
    										?>
    				        	            </td>
    				        	            <td> 
    				        	            	<?php
    				        	                    $status = $orderobj->get_status();
    				        	                    $statustext = $badge = '';
    				        	                    if ($status === 'processing'){
    				        	                        $badge = 'badge-warning';
    				        	                        $statustext = esc_html__('Pending Order Completion','salesking');
    				        	                    } else if ($status === 'on-hold'){
    				        	                        $badge = 'badge-warning';
    				        	                        $statustext = esc_html__('Pending Order Completion','salesking');
    				        	                    } else if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
    				        	                        $badge = 'badge-success';
    				        	                        $statustext = esc_html__('Completed','salesking');
    				        	                    } else if ($status === 'refunded'){
    				        	                        $badge = 'badge-danger';
    				        	                        $statustext = esc_html__('Order Refunded','salesking');
    				        	                    } else if ($status === 'cancelled'){
    				        	                        $badge = 'badge-danger';
    				        	                        $statustext = esc_html__('Order Cancelled','salesking');
    				        	                    } else if ($status === 'pending'){
    				        	                        $badge = 'badge-warning';
    				        	                        $statustext = esc_html__('Pending Order Payment','salesking');
    				        	                    } else if ($status === 'failed'){
    				        	                        $badge = 'badge-danger';
    				        	                        $statustext = esc_html__('Order Failed','salesking');
    				        	                    }
    				        	                    
    				        	                    echo esc_html($statustext);
    				        	            ?></td>
    				        	            <td><?php
    				        	                     $customer_id = $orderobj -> get_customer_id();
    				        	                     $data = get_userdata($customer_id);
    				        	                     $name = $data->first_name.' '.$data->last_name;
    				        	                     echo $name;
    				        	                     ?>
    				        	            </td>
    				        	            <td><?php
    				        	                    $items = $orderobj->get_items();
    				        	                    $items_count = count( $items );
    				        	                    if ($items_count > 4){
    				        	                        echo $items_count.' '.esc_html__('Items', 'salesking');
    				        	                    } else {
    				        	                        // show the items
    				        	                        foreach ($items as $item){
    				        	                            echo $item->get_name().' x '.$item->get_quantity().'<br>';
    				        	                        }
    				        	                    }
    				        	                    ?>
    				        	            </td>
    				        	            <td data-order="<?php echo esc_attr($orderobj->get_total());?>"> 
    				        	               <?php echo wc_price($orderobj->get_total());?>
    				        	            </td>
    				        	            <?php
    				        	            if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
    				        	            	?>
    					        	            <td data-order="<?php echo esc_attr($earnings_total);?>"> 
    				        	                    <?php
    				        	                    if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
    				        	                        $text_color = 'text-success';
    				        	                    } else {
    				        	                        $text_color = 'text-soft';
    				        	                    }
    				        	                    ?>
    				        	                    <span class="tb-lead <?php echo esc_attr($text_color);?>"><?php 
    				        	                    
    				        	                    echo wc_price($earnings_total);
    				        	                    if (!in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
    				        	                        esc_html_e(' (pending)', 'salesking');
    				        	                    }
    				        	                    ?></span>
    					        	            </td>
    					        	            <?php
    					        	        }
    					        	        ?>
    				        	        </tr>
    				        	    <?php
    				        	}
    			        	}

				        }
			        	?>


		       
			        </tbody>

			    </table>

			    <?php
			    if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
				    if (intval(get_option( 'salesking_enable_teams_setting', 1 )) === 1){
				        ?>
				        <br>
					    <h3><?php 
					    if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
					    	esc_html_e('Earnings of subagents', 'salesking');
					    } else {
					    	esc_html_e('Orders of subagents', 'salesking');
					    }

					    ?></h3>
					    <table id="salesking_payout_history_table2">
					        <thead>
					            <tr>
					                <th><?php esc_html_e('Order','salesking'); ?></th>
					                <th><?php esc_html_e('Date','salesking'); ?></th>
					                <th><?php esc_html_e('Status','salesking'); ?></th>
					                <th><?php esc_html_e('Subagent','salesking'); ?></th>
					                <th><?php esc_html_e('Order Value','salesking'); ?></th>
					                <?php
					                if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
					                	?>
					               		<th><?php esc_html_e('Agent Earnings','salesking'); ?></th>
					               		<?php
					               	}
					               	?>

					            </tr>
					        </thead>
					        <tbody>

				        	<?php



					        	$earnings = get_posts( array( 
					        	    'post_type' => 'salesking_earning',
					        	    'numberposts' => -1,
					        	    'post_status'    => 'any',
					        	    'fields'    => 'ids',
					        	    'meta_key'   => 'agent_id',
					        	    'meta_key'   => 'parent_agent_id_'.$user_id,
					        	    'meta_value' => $user_id,
					        	));

					        	foreach ($earnings as $earning_id){
					        	    $order_id = get_post_meta($earning_id,'order_id', true);
					        	    $orderobj = wc_get_order($order_id);
					        	    if ($orderobj !== false){
						        	    $earnings_total = get_post_meta($earning_id,'parent_agent_id_'.$user_id.'_earnings', true);

						        	    if (!empty($earnings_total) && floatval($earnings_total) !== 0){
						        	        ?>
						        	        <tr class="nk-tb-item">
						        	            <td><a href="<?php echo esc_attr(get_edit_post_link($order_id));?>">#<?php echo esc_html($order_id);?></a></td>
						        	            <td data-order="<?php 
			  		        	                    $date = explode('T',$orderobj->get_date_created())[0];
			  		        	                    echo strtotime($date);
						        	            ?>"><?php 
					        	                   // echo date('F j, Y', strtotime($date));
						        	            	echo date_i18n( get_option('date_format'), strtotime($date) ); 

												?>
						        	            </td>
						        	            <td> 
						        	            	<?php
						        	                    $status = $orderobj->get_status();
						        	                    $statustext = $badge = '';
						        	                    if ($status === 'processing'){
						        	                        $badge = 'badge-warning';
						        	                        $statustext = esc_html__('Pending Order Completion','salesking');
						        	                    } else if ($status === 'on-hold'){
						        	                        $badge = 'badge-warning';
						        	                        $statustext = esc_html__('Pending Order Completion','salesking');
						        	                    } else if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
						        	                        $badge = 'badge-success';
						        	                        $statustext = esc_html__('Completed','salesking');
						        	                    } else if ($status === 'refunded'){
						        	                        $badge = 'badge-danger';
						        	                        $statustext = esc_html__('Order Refunded','salesking');
						        	                    } else if ($status === 'cancelled'){
						        	                        $badge = 'badge-danger';
						        	                        $statustext = esc_html__('Order Cancelled','salesking');
						        	                    } else if ($status === 'pending'){
						        	                        $badge = 'badge-warning';
						        	                        $statustext = esc_html__('Pending Order Payment','salesking');
						        	                    } else if ($status === 'failed'){
						        	                        $badge = 'badge-danger';
						        	                        $statustext = esc_html__('Order Failed','salesking');
						        	                    } else {
						        	                    	$badge = '';
						        	                    	$statustext = $status;
						        	                    }
						        	                    
						        	                    echo esc_html($statustext);
						        	            ?></td>
						        	            <td><?php
		                                         // get subagent name
		                                         $subagent_id = get_post_meta($earning_id, 'agent_id', true);
		                                         $datat = get_userdata($subagent_id);
		                                         $named = $datat->first_name.' '.$datat->last_name;
		                                         echo $named;
		                                         ?>
						        	            </td>

						        	            <td data-order="<?php echo esc_attr($orderobj->get_total());?>"> 
						        	               <?php echo wc_price($orderobj->get_total());?>
						        	            </td>
						        	            <?php
						        	            if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
						        	            	?>
							        	            <td data-order="<?php echo esc_attr($earnings_total);?>"> 
						        	                    <?php
						        	                    if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
						        	                        $text_color = 'text-success';
						        	                    } else {
						        	                        $text_color = 'text-soft';
						        	                    }
						        	                    ?>
						        	                    <span class="tb-lead <?php echo esc_attr($text_color);?>"><?php 
						        	                    
						        	                    echo wc_price($earnings_total);
						        	                    if (!in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
						        	                        esc_html_e(' (pending)', 'salesking');
						        	                    }
						        	                    ?></span>
							        	            </td>
							        	            <?php
							        	        }
							        	        ?>
						        	        </tr>
						        	    <?php
						        	    }
						        	}
					        	}
					        	?>


				       
					        </tbody>

					    </table>
					    <?php
					}
				}
				?>
			</div>

			<!--- END CCONTENT -->
			
		</div>
		<?php
	}

	function salesking_view_payouts_content(){

		echo self::get_header_bar();		

		if (isset($_GET['user'])){
			$user_id = sanitize_text_field($_GET['user']);
		} else {
			$user_id = 0;
		}
		
		$userinfo = get_userdata($user_id);
		$info = base64_decode(get_user_meta($user_id,'salesking_payout_info', true));
		$info = explode('**&&', $info);

		?>
		<!-- User-specific shipping and payment methods -->
		<div class="salesking_user_shipping_payment_methods_container salesking_special_group_container">
			<input type="hidden" name="salesking_admin_user_id" value="<?php echo esc_attr($user_id);?>">
			<div class="salesking_above_top_title_button">
				<div class="salesking_above_top_title_button_left">
					<?php esc_html_e('User Payouts','salesking'); ?>
				</div>
				<div class="salesking_above_top_title_button_right">
					<a href="<?php echo admin_url( 'admin.php?page=salesking_payouts'); ?>">
						<button type="button" class="salesking_above_top_title_button_right_button">
							<?php esc_html_e('←  Go Back','salesking'); ?>
						</button>
					</a>
				</div>
			</div>
			<div class="salesking_user_shipping_payment_methods_container_top">
				<div class="salesking_user_shipping_payment_methods_container_top_title">
					<?php esc_html_e('Payouts for','salesking'); echo ': '.esc_html($userinfo->user_login); ?>
				</div>		
			</div>

			<!-- BEGIN CONTENT -->
			<div class="salesking_user_payouts_container">
				<div class="salesking_user_registration_user_data_container_title">
				    <svg class="salesking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="31" fill="none" viewBox="0 0 36 31">
				      <path fill="#C4C4C4" d="M20.243 25.252c0-.553.065-1.09.147-1.628H3.964v-9.767h26.047v1.628c1.14 0 2.23.211 3.256.57V4.088A3.245 3.245 0 0030.01.833H3.964A3.245 3.245 0 00.708 4.09v19.535a3.256 3.256 0 003.256 3.256H20.39a10.807 10.807 0 01-.147-1.628zM3.964 4.089h26.047v3.256H3.964V4.089zm24.012 26.047l-4.477-4.884 1.888-1.888 2.589 2.588 5.844-5.844 1.888 2.295-7.732 7.733z"/>
				    </svg>
				    <?php esc_html_e('Current User Payout Information','salesking'); ?>
				</div>
				<div class="salesking_user_registration_user_data_container_element">
				    <div class="salesking_user_registration_user_data_container_element_label">
				        <?php esc_html_e('Chosen payout method','salesking'); ?>
				    </div>
				    <?php 
				    $method = get_user_meta($user_id,'salesking_agent_selected_payout_method', true);
				    if ($method === 'paypal'){
				        $method = 'PayPal';
				    } else if ($method === 'bank'){
				        $method = 'Bank';
				    } else if ($method === 'custom'){
				        $method = get_option( 'salesking_enable_custom_payouts_title_setting', '' );
				    }
				    if (empty($method)){
				    	$method = esc_html__('The user has not configured a payout method yet', 'salesking');
				    }
				    ?>
				    <input type="text" class="salesking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($method);?>" readonly>
				</div>
				<?php
				if ($method === 'PayPal'){
					?>
					<div class="salesking_user_registration_user_data_container_element">
					    <div class="salesking_user_registration_user_data_container_element_label">
					        <?php esc_html_e('PayPal email address','salesking'); ?>
					    </div>
					    <input type="text" class="salesking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($info[0]);?>" readonly>
					</div>
					<?php
				}
				?>
				<?php
				if ($method === get_option( 'salesking_enable_custom_payouts_title_setting', '' )){
					?>
					<div class="salesking_user_registration_user_data_container_element">
					    <div class="salesking_user_registration_user_data_container_element_label">
					        <?php esc_html_e('Details','salesking'); ?>
					    </div>
					    <input type="text" class="salesking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($info[1]);?>" readonly>
					</div>
					<?php
				}
				?>
				<?php
				if ($method === 'Bank'){
					for ($i=2; $i<=16; $i++){
						if (!empty($info[$i])){
						?>
						<div class="salesking_user_registration_user_data_container_element">
						    <div class="salesking_user_registration_user_data_container_element_label">
						        <?php 
						        switch($i){
						        	case 2:
						        	esc_html_e('Full Name', 'salesking');
						        	break;

						        	case 3:
						        	esc_html_e('Billing Address Line 1', 'salesking');
						        	break;

						        	case 4:
						        	esc_html_e('Billing Address Line 2', 'salesking');
						        	break;

						        	case 5:
						        	esc_html_e('City', 'salesking');
						        	break;

						        	case 6:
						        	esc_html_e('State', 'salesking');
						        	break;

						        	case 7:
						        	esc_html_e('Postcode', 'salesking');
						        	break;

						        	case 8:
						        	esc_html_e('Country', 'salesking');
						        	break;

						        	case 9:
						        	esc_html_e('Bank Account Holder Name', 'salesking');
						        	break;

						        	case 10:
						        	esc_html_e('Bank Account Number/IBAN', 'salesking');
						        	break;

						        	case 11:
						        	esc_html_e('Bank Branch City', 'salesking');
						        	break;

						        	case 12:
						        	esc_html_e('Bank Branch Country', 'salesking');
						        	break;

						        	case 13:
						        	esc_html_e('Intermediary Bank - Bank Code', 'salesking');
						        	break;

						        	case 14:
						        	esc_html_e('Intermediary Bank - Name', 'salesking');
						        	break;

						        	case 15:
						        	esc_html_e('Intermediary Bank - City', 'salesking');
						        	break;

						        	case 16:
						        	esc_html_e('Intermediary Bank - Country', 'salesking');
						        	break;

						        }

						        ?>
						    </div>
						    <input type="text" class="salesking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($info[$i]);?>" readonly>
						</div>
						<?php
						}
					}
				}
				?>
				<br />
				<!-- 2. REIMBURSEMENT SECTION -->
			    <div class="salesking_user_registration_user_data_container_title">
			        <svg class="salesking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="29" fill="none" viewBox="0 0 36 29">
			          <g clip-path="url(#clip0)">
			            <path fill="#C4C4C4" d="M14.4 18.952h-.001c0-.913.075-.493-4.784-10.238-.993-1.99-3.836-1.995-4.83 0C-.115 18.543 0 18.068 0 18.952H0c0 2.492 3.224 4.512 7.2 4.512s7.2-2.02 7.2-4.512zM7.2 9.927l4.05 8.122h-8.1L7.2 9.927zm28.799 9.025c0-.913.075-.493-4.784-10.238-.993-1.99-3.836-1.995-4.83 0-4.9 9.829-4.784 9.354-4.784 10.238H21.6c0 2.492 3.224 4.512 7.2 4.512s7.2-2.02 7.2-4.512h-.001zm-11.249-.903l4.05-8.122 4.05 8.122h-8.1zm4.95 7.22h-9.9V8.644a4.513 4.513 0 002.61-3.23h7.29c.497 0 .9-.403.9-.902V2.707a.901.901 0 00-.9-.902h-8.12C20.759.715 19.468 0 18 0s-2.758.715-3.58 1.805H6.3c-.497 0-.9.404-.9.902v1.805c0 .499.403.903.9.903h7.29a4.513 4.513 0 002.61 3.229v16.625H6.3c-.497 0-.9.404-.9.903v1.805c0 .498.403.902.9.902h23.4c.497 0 .9-.404.9-.902v-1.805a.901.901 0 00-.9-.903z"/>
			          </g>
			          <defs>
			            <clipPath id="clip0">
			              <path fill="#fff" d="M0 0h36v28.879H0z"/>
			            </clipPath>
			          </defs>
			        </svg>
			        <?php esc_html_e('Manage Payments','salesking'); ?>
			    </div>
			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Payment Amount','salesking'); ?>
			        </div>
			        <input type="number" step="0.0001" id="salesking_reimbursement_value" class="salesking_user_registration_user_data_container_element_text" placeholder="<?php esc_attr_e('Enter the amount that has been sent...','salesking');?>">
			    </div>
			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Payment Method','salesking'); ?>
			        </div>
			        <input type="text" id="salesking_reimbursement_method" class="salesking_user_registration_user_data_container_element_text" placeholder="<?php esc_attr_e('Enter payment method used here...','salesking');?>">
			    </div>
			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Note / Details','salesking'); ?>
			        </div>
			        <input type="text" id="salesking_reimbursement_note" class="salesking_user_registration_user_data_container_element_text" placeholder="<?php esc_attr_e('Enter note / details here...','salesking');?>">
			    </div>
			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Bonus / Extra Payment (is not deducted from outstanding balance)','salesking'); ?>
			        </div>
			        <input type="checkbox" id="salesking_bonus_payment">
			    </div>
			    <button id="salesking_save_payment" type="button" class="button button-primary"><?php esc_html_e('Save Payment and Notify Agent','salesking'); ?></button>

			    <br /><br /><br />
			  <!-- 3. TRANSACTION HISTORY SECTION -->
			    <div class="salesking_user_registration_user_data_container_title">
			        <svg class="salesking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
			          <path fill="#C4C4C4" d="M29.531 0H3.281A3.29 3.29 0 000 3.281V31.72A3.29 3.29 0 003.281 35h26.25a3.29 3.29 0 003.282-3.281V3.28A3.29 3.29 0 0029.53 0zm-1.093 30.625H4.375V4.375h24.063v26.25zM8.75 15.312h15.313V17.5H8.75v-2.188zm0 4.376h15.313v2.187H8.75v-2.188zm0 4.375h15.313v2.187H8.75v-2.188zm0-13.125h15.313v2.187H8.75v-2.188z"/>
			        </svg>
			        <?php esc_html_e('Payouts History','salesking'); ?>
			    </div>
			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('current outstanding balance (unpaid earnings)','salesking'); ?>
			        </div>
			        <?php
			        $user_outstanding_earnings = get_user_meta($user_id,'salesking_outstanding_earnings', true);
			        if (empty($user_outstanding_earnings)){ // no earnings yet
			        	$user_outstanding_earnings = 0;
			        }
			        ?>
			        <input type="text" class="salesking_user_registration_user_data_container_element_text" value="<?php echo strip_tags(wc_price($user_outstanding_earnings));?>" readonly>
			    </div>
			    <br />

			    <table id="salesking_payout_history_table">
			        <thead>
			            <tr>
			                <th><?php esc_html_e('Date','salesking'); ?></th>
			                <th><?php esc_html_e('Amount','salesking'); ?></th>
			                <th><?php esc_html_e('Payment Method','salesking'); ?></th>
			                <th><?php esc_html_e('Outstanding (Unpaid) Balance','salesking'); ?></th>
			                <th><?php esc_html_e('Note','salesking'); ?></th>
			            </tr>
			        </thead>
			        <tbody>
			            <?php
			            $user_payout_history = sanitize_text_field(get_user_meta($user_id,'salesking_user_payout_history', true));

			            if ($user_payout_history){
			                $transactions = explode(';', $user_payout_history);
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
			                $oustanding_balance = $elements[2];
			                $note = $elements[3];
			                $method = $elements[4];
			                ?>
			                <tr>
			                    <td data-order="<?php echo esc_attr(strtotime($date));?>"><?php echo esc_html($date);?></td>
			                    <td data-order="<?php echo esc_attr($amount);?>"><?php echo wc_price($amount);?></td>
			                    <td><?php echo $method;?></td>
			                    <td data-order="<?php echo esc_attr($oustanding_balance);?>"><?php echo wc_price($oustanding_balance);?></td>
			                    <td><?php echo esc_html($note);?></td>
			                </tr>
			                <?php
			            }
			            ?>
			       
			        </tbody>

			    </table>

			    <br><br>
			    <div class="salesking_user_registration_user_data_container_title">
			        <svg class="salesking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
			          <path fill="#C4C4C4" d="M29.531 0H3.281A3.29 3.29 0 000 3.281V31.72A3.29 3.29 0 003.281 35h26.25a3.29 3.29 0 003.282-3.281V3.28A3.29 3.29 0 0029.53 0zm-1.093 30.625H4.375V4.375h24.063v26.25zM8.75 15.312h15.313V17.5H8.75v-2.188zm0 4.376h15.313v2.187H8.75v-2.188zm0 4.375h15.313v2.187H8.75v-2.188zm0-13.125h15.313v2.187H8.75v-2.188z"/>
			        </svg>
			        <?php esc_html_e('Agent Balance History & Manual Adjustments','salesking'); ?>
			    </div>
			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('current outstanding balance (unpaid earnings)','salesking'); ?>
			        </div>
			        <?php
			        $user_outstanding_earnings = get_user_meta($user_id,'salesking_outstanding_earnings', true);
			        if (empty($user_outstanding_earnings)){ // no earnings yet
			        	$user_outstanding_earnings = 0;
			        }
			        ?>
			        <input type="text" class="salesking_user_registration_user_data_container_element_text" value="<?php echo strip_tags(wc_price($user_outstanding_earnings));?>" readonly>
			    </div>

			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Manual Adjustment Amount','salesking'); ?>
			        </div>
			        <input type="number" id="salesking_adjustment_value" class="salesking_user_registration_user_data_container_element_text" placeholder="<?php esc_attr_e('Enter the adjustment amount (you can enter a positive / negative value to increase / reduce balance).','salesking');?>">
			    </div>
			    <div class="salesking_user_registration_user_data_container_element">
			        <div class="salesking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Note / Details','salesking'); ?>
			        </div>
			        <input type="text" id="salesking_adjustment_note" class="salesking_user_registration_user_data_container_element_text" placeholder="<?php esc_attr_e('Enter note / explanation here...','salesking');?>">
			    </div>
			    <br />
			    <button id="salesking_make_vendor_balance_adjustment" class="button button-primary" value="<?php echo esc_attr($user_id); ?>"><?php esc_html_e('Save Adjustment','salesking'); ?></button> &nbsp; 

			    <button id="salesking_download_vendor_balance_history" class="button button-secondary" value="<?php echo esc_attr($user_id); ?>"><?php esc_html_e('Download Agent Balance History','salesking'); ?></button>
			</div>

			<!--- END CCONTENT -->
			
		</div>
		<?php
	}


	function salesking_payouts_page_content(){

		// get all agents
		$users = get_users(array(
		    'meta_key'     => 'salesking_group',
		    'meta_value'   => 'none',
		    'meta_compare' => '!=',
		));

		echo self::get_header_bar();		


		?>
		<h1 class="salesking_page_title"><?php esc_html_e('Payouts','salesking');?></h1>
		<div id="salesking_admin_payouts_table_container">
			<table id="salesking_admin_payouts_table">
			        <thead>
			            <tr>
			            	<th><?php esc_html_e('Agent ID','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Name','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Group','salesking'); ?></th>
			                <th><?php esc_html_e('Last Payment','salesking'); ?></th>
			                <th><?php esc_html_e('Outstanding Balance','salesking'); ?></th>
			                <?php
			                do_action('salesking_admin_payouts_columns_heading');
			                ?>
			                <th><?php esc_html_e('Actions','salesking'); ?></th>
			                
			            </tr>
			        </thead>
			        <tbody>
			        	<?php

			        	foreach ( $users as $user ) {

			        		$user_id = $user->ID;
			        		$original_user_id = $user_id;
			        		$username = $user->user_login;
			        		$name = $user->first_name.' '.$user->last_name;

			        		$group_name = get_the_title(get_user_meta($user_id, 'salesking_group', true));
			        		if (empty($group_name)){
			        			$group_name = '-';
			        		}

			        		$user_outstanding_earnings = get_user_meta($user_id,'salesking_outstanding_earnings', true);
			        		if (empty($user_outstanding_earnings)){ // no earnings yet
			        			$user_outstanding_earnings = 0;
			        		}

			        		$user_payout_history = sanitize_text_field(get_user_meta($user_id,'salesking_user_payout_history', true));

			        		if ($user_payout_history){
			        		    $transactions = explode(';', $user_payout_history);
			        		    $transactions = array_filter($transactions);
		    	        		$transactions = array_reverse($transactions);
		            		    $elements = explode(':', $transactions[0]);
		            		    $last_payment = $elements[0];

		            		    $last_payment = date_i18n( get_option('date_format'), strtotime($last_payment) ); 

			        		} else {
			        		    // empty, no transactions
			        		    $transactions = array();
			        		    $last_payment = esc_html__('No payment yet', 'salesking');
			        		}
			        		
			        		
			        		echo
			        		'<tr>
			        			<td><strong>'.esc_html( $original_user_id ).'</strong></td>
			        		    <td><a href="'.esc_attr(get_edit_user_link($original_user_id)).'">'.esc_html( $name ).' ('.$username.')</a></td>
			        		    <td>'.esc_html( $group_name ).'</td>
			        		    <td data-order="'.esc_attr(strtotime($last_payment)).'">'.esc_html( $last_payment ).'</td>
			        		    <td data-order="'.esc_attr($user_outstanding_earnings).'">'.wc_price( $user_outstanding_earnings ).'</td>
			        		';

			        		do_action('salesking_admin_payouts_columns_values', $user);


			        		echo '<td><a href="'.admin_url( 'admin.php?page=salesking_view_payouts').'&user='.esc_attr($original_user_id).'"><button type="button" class="salesking_manage_payouts_button">'.esc_html__('View Payouts','salesking').'</button></a></td></tr>';
			        	}

			        	?>
			           
			        </tbody>
			        <tfoot>
			            <tr>
			            	<th><?php esc_html_e('Agent ID','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Name','salesking'); ?></th>
			                <th><?php esc_html_e('Agent Group','salesking'); ?></th>
			                <th><?php esc_html_e('Last Payment','salesking'); ?></th>
			                <th><?php esc_html_e('Outstanding Balance','salesking'); ?></th>
			                <?php
			                do_action('salesking_admin_payouts_columns_heading');
			                ?>
			                <th><?php esc_html_e('Actions','salesking'); ?></th>
			                
			            </tr>
			        </tfoot>
			    </table>
			</div>
		<?php
	}

	// Register new post type: Group Rules
	public static function salesking_register_post_type_group_rules() {
		// Build labels and arguments
	    $labels = array(
	        'name'                  => esc_html__( 'Group Rules', 'salesking' ),
	        'singular_name'         => esc_html__( 'Rule', 'salesking' ),
	        'all_items'             => esc_html__( 'Group Rules', 'salesking' ),
	        'menu_name'             => esc_html__( 'Group Rules', 'salesking' ),
	        'add_new'               => esc_html__( 'Create new rule', 'salesking' ),
	        'add_new_item'          => esc_html__( 'Create new rule', 'salesking' ),
	        'edit'                  => esc_html__( 'Edit', 'salesking' ),
	        'edit_item'             => esc_html__( 'Edit rule', 'salesking' ),
	        'new_item'              => esc_html__( 'New rule', 'salesking' ),
	        'view_item'             => esc_html__( 'View rule', 'salesking' ),
	        'view_items'            => esc_html__( 'View rules', 'salesking' ),
	        'search_items'          => esc_html__( 'Search rules', 'salesking' ),
	        'not_found'             => esc_html__( 'No rules found', 'salesking' ),
	        'not_found_in_trash'    => esc_html__( 'No rules found in trash', 'salesking' ),
	        'parent'                => esc_html__( 'Parent rule', 'salesking' ),
	        'featured_image'        => esc_html__( 'Rule image', 'salesking' ),
	        'set_featured_image'    => esc_html__( 'Set rule image', 'salesking' ),
	        'remove_featured_image' => esc_html__( 'Remove rule image', 'salesking' ),
	        'use_featured_image'    => esc_html__( 'Use as rule image', 'salesking' ),
	        'insert_into_item'      => esc_html__( 'Insert into rule', 'salesking' ),
	        'uploaded_to_this_item' => esc_html__( 'Uploaded to this rule', 'salesking' ),
	        'filter_items_list'     => esc_html__( 'Filter rules', 'salesking' ),
	        'items_list_navigation' => esc_html__( 'Rules navigation', 'salesking' ),
	        'items_list'            => esc_html__( 'Commission rules list', 'salesking' )
	    );
	    $args = array(
	        'label'                 => esc_html__( 'Group Rules', 'salesking' ),
	        'description'           => esc_html__( 'This is where you can create group rules', 'salesking' ),
	        'labels'                => $labels,
	        'supports'              => array( 'title'),
	        'hierarchical'          => false,
	        'public'                => false,
	        'show_ui'               => true,
	        'show_in_menu'          => 'salesking',
	        'menu_position'         => 123,
	        'show_in_admin_bar'     => true,
	        'show_in_nav_menus'     => false,
	        'can_export'            => true,
	        'has_archive'           => false,
	        'exclude_from_search'   => true,
	        'publicly_queryable'    => false,
	        'capability_type'       => 'product',
	        'map_meta_cap'          => true,
	        'show_in_rest'          => true,
	        'rest_base'             => 'salesking_grule',
	        'rest_controller_class' => 'WP_REST_Posts_Controller',
	    );

		// Actually register the post type
		register_post_type( 'salesking_grule', $args );
	}

	// Add Rule Details Metabox to Rules
	function salesking_group_rules_metaboxes($post_type) {
	    $post_types = array('salesking_grule');     //limit meta box to certain post types
       	if ( in_array( $post_type, $post_types ) ) {
	           add_meta_box(
	               'salesking_rule_details_metabox'
	               ,esc_html__( 'Rule Details', 'salesking' )
	               ,array( $this, 'salesking_grule_details_metabox_content' )
	               ,$post_type
	               ,'advanced'
	               ,'high'
	           );
	       }
	}

	// Save Rules Metabox Content
	function salesking_save_group_rules_metaboxes($post_id){
		if (isset($_POST['_inline_edit'])){
			if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')){
			    return;
			}
		}
		if (isset($_REQUEST['bulk_edit'])){
		    return;
		}

		if (get_post_type($post_id) === 'salesking_grule'){

			// set that rules have changed so that pricing cache can be updated
			update_option('salesking_commission_rules_have_changed', 'yes');

			// delete all salesking transients
			global $wpdb;
			$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%transient_salesking%'" );
			foreach( $plugin_options as $option ) {
			    delete_option( $option->option_name );
			}
			wp_cache_flush();

			$rule_what = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_what'));
			$rule_applies = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_applies'));
			$rule_orders = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_orders'));

			$rule_who = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_who'));
			$rule_agents_who = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_agents_who'));

			$rule_quantity_value = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_quantity_value'));
			$rule_tax_shipping = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_tax_shipping'));
			$rule_tax_shipping_rate = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_tax_shipping_rate'));
			$rule_howmuch = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_howmuch'));
			$rule_x = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_x'));

			$rule_currency = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_currency'));
			$rule_paymentmethod = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_paymentmethod'));
			$rule_paymentmethod_minmax = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_paymentmethod_minmax'));
			$rule_paymentmethod_percentamount = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_paymentmethod_percentamount'));

			$rule_taxname = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_taxname'));
			$rule_discountname = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_discountname'));
			$rule_conditions = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_conditions'));
			$rule_tags = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_tags'));
			$rule_discount_show_everywhere = sanitize_text_field(filter_input(INPUT_POST, 'salesking_commission_rule_discount_show_everywhere_checkbox_input'));
			
			if (isset($_POST['salesking_rule_select_countries'])){
				$rule_countries = $_POST['salesking_rule_select_countries'];
			} else {
				$rule_countries = NULL;
			}

			if (isset($_POST['salesking_select_multiple_product_categories_selector_select'])){
				$rule_applies_multiple_options = $_POST['salesking_select_multiple_product_categories_selector_select'];
			} else {
				$rule_applies_multiple_options = NULL;
			}

			if (isset($_POST['salesking_select_multiple_users_selector_select'])){
				$rule_who_multiple_options = $_POST['salesking_select_multiple_users_selector_select'];
			} else {
				$rule_who_multiple_options = NULL;
			}

			if (isset($_POST['salesking_select_multiple_agents_selector_select'])){
				$rule_agents_who_multiple_options = $_POST['salesking_select_multiple_agents_selector_select'];
			} else {
				$rule_agents_who_multiple_options = NULL;
			}

			$rule_requires = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_requires'));
			$rule_showtax = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_showtax'));

			if ($rule_what !== NULL){
				update_post_meta( $post_id, 'salesking_rule_what', $rule_what);
			}
			if ($rule_currency !== NULL){
				update_post_meta( $post_id, 'salesking_rule_currency', $rule_currency);
			}
			if ($rule_paymentmethod !== NULL){
				update_post_meta( $post_id, 'salesking_rule_paymentmethod', $rule_paymentmethod);
			}
			if ($rule_paymentmethod_minmax !== NULL){
				update_post_meta( $post_id, 'salesking_rule_paymentmethod_minmax', $rule_paymentmethod_minmax);
			}
			if ($rule_paymentmethod_percentamount !== NULL){
				update_post_meta( $post_id, 'salesking_rule_paymentmethod_percentamount', $rule_paymentmethod_percentamount);
			}
			if ($rule_applies !== NULL){
				update_post_meta( $post_id, 'salesking_rule_applies', $rule_applies);
			}
			if ($rule_who !== NULL){
				update_post_meta( $post_id, 'salesking_rule_who', $rule_who);
			}
			if ($rule_orders !== NULL){
				update_post_meta( $post_id, 'salesking_rule_orders', $rule_orders);
			}
			if ($rule_agents_who !== NULL){
				update_post_meta( $post_id, 'salesking_rule_agents_who', $rule_agents_who);
			}
			if ($rule_quantity_value !== NULL){
				update_post_meta( $post_id, 'salesking_rule_quantity_value', $rule_quantity_value);
			}
			if ($rule_howmuch !== NULL){
				update_post_meta( $post_id, 'salesking_rule_howmuch', $rule_howmuch);
			}
			if ($rule_x !== NULL){
				update_post_meta( $post_id, 'salesking_rule_x', $rule_x);
			}
			if ($rule_taxname !== NULL){
				update_post_meta( $post_id, 'salesking_rule_taxname', $rule_taxname);
			}
			if ($rule_tax_shipping !== NULL){
				update_post_meta( $post_id, 'salesking_rule_tax_shipping', $rule_tax_shipping);
			}
			if ($rule_tax_shipping_rate !== NULL){
				update_post_meta( $post_id, 'salesking_rule_tax_shipping_rate', $rule_tax_shipping_rate);
			}
			if ($rule_discountname !== NULL){
				update_post_meta( $post_id, 'salesking_rule_discountname', $rule_discountname);
			}
			if ($rule_conditions !== NULL){
				update_post_meta( $post_id, 'salesking_rule_conditions', $rule_conditions);
			}
			if ($rule_tags !== NULL){
				update_post_meta( $post_id, 'salesking_rule_tags', $rule_tags);
			}
			if ($rule_discount_show_everywhere !== NULL){
				update_post_meta( $post_id, 'salesking_rule_discount_show_everywhere', $rule_discount_show_everywhere);
			}

			
			if ($rule_countries !== NULL){
				$countries_string = '';
				foreach ($rule_countries as $country){
					$countries_string .= sanitize_text_field ($country).',';
				}
				// remove last comma
				$countries_string = substr($countries_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_countries', $countries_string);
			}
			if ($rule_requires !== NULL){
				update_post_meta( $post_id, 'salesking_rule_requires', $rule_requires);
			}
			if ($rule_showtax !== NULL){
				update_post_meta( $post_id, 'salesking_rule_showtax', $rule_showtax);
			}

			if ($rule_applies_multiple_options !== NULL){
				$options_string = '';
				foreach ($rule_applies_multiple_options as $option){
					$options_string .= sanitize_text_field ($option).',';
				}
				// remove last comma
				$options_string = substr($options_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_applies_multiple_options', $options_string);
			}

			if ($rule_who_multiple_options !== NULL){
				$options_string = '';
				foreach ($rule_who_multiple_options as $option){
					$options_string .= sanitize_text_field ($option).',';
				}
				// remove last comma
				$options_string = substr($options_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_who_multiple_options', $options_string);
			}

			if ($rule_agents_who_multiple_options !== NULL){
				$options_string = '';
				foreach ($rule_agents_who_multiple_options as $option){
					$options_string .= sanitize_text_field ($option).',';
				}
				// remove last comma
				$options_string = substr($options_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_agents_who_multiple_options', $options_string);
			}

			$rule_replaced =  sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_applies_replaced')); 
			$rule_replaced_array = explode(',',$rule_replaced);
			$rule_replaced_string = '';
			foreach ($rule_replaced_array as $element){
				$rule_replaced_string.= 'product_'.trim($element).',';
			}
			// remove last comma
			$rule_replaced_string = substr($rule_replaced_string, 0, -1);

			// if rule applies is product & variation IDS, set applies as salesking_rule_select_applies_replaced
			if ($rule_applies === 'replace_ids'){
				if ($rule_replaced !== NULL){
					update_post_meta( $post_id, 'salesking_rule_applies', 'multiple_options');
					update_post_meta( $post_id, 'salesking_rule_applies_multiple_options', $rule_replaced_string);
					update_post_meta( $post_id, 'salesking_rule_replaced', 'yes');
				}
			} else {
				update_post_meta( $post_id, 'salesking_rule_replaced', 'no');
			}

		}
	}

	// Add custom columns to Group Rules menu
	function salesking_add_columns_grule_menu($columns) {

		$columns_initial = $columns;
		
		// rename title
		$columns = array(
			'title' => esc_html__( 'Rule name', 'salesking' ),
			'type' => esc_html__( 'Rule type', 'salesking' ),
			'condition' => esc_html__( 'Condition', 'salesking' ),
			'value' => esc_html__( 'Value', 'salesking' ),
			'newgroup' => esc_html__( 'New Group', 'salesking' ),
		);

		$columns = array_slice($columns_initial, 0, 1, true) + $columns;

	    return $columns;
	}

	// Add groups custom columns data
	function salesking_columns_grule_data( $column, $post_id ) {

		$rule_type = get_post_meta($post_id,'salesking_rule_what', true);
		if ($rule_type === 'change_group'){
			$rule_type = esc_html__('Change group','salesking');
		}

		$condition = get_post_meta($post_id,'salesking_rule_applies', true);
		if ($condition === 'earnings_total'){
			$condition = esc_html__('Total earnings reached','salesking');
		} else if ($condition === 'order_value_total'){
			$condition = esc_html__('Total order value','salesking');
		} else if ($condition === 'earnings_monthly'){
			$condition = esc_html__('Monthly earnings reached (reset)','salesking');
		} else if ($condition === 'order_value_monthly'){
			$condition = esc_html__('Monthly orders value (reset)','salesking');
		}

		$howmuch = get_post_meta($post_id,'salesking_rule_howmuch', true);
		$howmuch = strip_tags(wc_price($howmuch));
		$newgroup = get_post_meta($post_id,'salesking_rule_who', true);
		$newgroup = get_the_title(explode('_',$newgroup)[1]);
	    switch ( $column ) {

	        case 'type' :

	            echo '<strong>'.esc_html($rule_type).'</strong>';
	            break;

	        case 'condition' :

	            echo '<strong>'.esc_html($condition).'</strong>';
	            break;

	        case 'value' :

	            echo '<strong>'.esc_html($howmuch).'</strong>';
	            break;


	        case 'newgroup' :

	            echo '<strong>'.esc_html($newgroup).'</strong>';
	            break;

	    }
	}

	// Register new post type: Commission Rules
	public static function salesking_register_post_type_commission_rules() {
		// Build labels and arguments
	    $labels = array(
	        'name'                  => esc_html__( 'Commission Rules', 'salesking' ),
	        'singular_name'         => esc_html__( 'Rule', 'salesking' ),
	        'all_items'             => esc_html__( 'Commission Rules', 'salesking' ),
	        'menu_name'             => esc_html__( 'Commission Rules', 'salesking' ),
	        'add_new'               => esc_html__( 'Create new rule', 'salesking' ),
	        'add_new_item'          => esc_html__( 'Create new rule', 'salesking' ),
	        'edit'                  => esc_html__( 'Edit', 'salesking' ),
	        'edit_item'             => esc_html__( 'Edit rule', 'salesking' ),
	        'new_item'              => esc_html__( 'New rule', 'salesking' ),
	        'view_item'             => esc_html__( 'View rule', 'salesking' ),
	        'view_items'            => esc_html__( 'View rules', 'salesking' ),
	        'search_items'          => esc_html__( 'Search rules', 'salesking' ),
	        'not_found'             => esc_html__( 'No rules found', 'salesking' ),
	        'not_found_in_trash'    => esc_html__( 'No rules found in trash', 'salesking' ),
	        'parent'                => esc_html__( 'Parent rule', 'salesking' ),
	        'featured_image'        => esc_html__( 'Rule image', 'salesking' ),
	        'set_featured_image'    => esc_html__( 'Set rule image', 'salesking' ),
	        'remove_featured_image' => esc_html__( 'Remove rule image', 'salesking' ),
	        'use_featured_image'    => esc_html__( 'Use as rule image', 'salesking' ),
	        'insert_into_item'      => esc_html__( 'Insert into rule', 'salesking' ),
	        'uploaded_to_this_item' => esc_html__( 'Uploaded to this rule', 'salesking' ),
	        'filter_items_list'     => esc_html__( 'Filter rules', 'salesking' ),
	        'items_list_navigation' => esc_html__( 'Rules navigation', 'salesking' ),
	        'items_list'            => esc_html__( 'Commission rules list', 'salesking' )
	    );
	    $args = array(
	        'label'                 => esc_html__( 'Commission Rules', 'salesking' ),
	        'description'           => esc_html__( 'This is where you can create commission rules', 'salesking' ),
	        'labels'                => $labels,
	        'supports'              => array( 'title','custom-fields' ),
	        'hierarchical'          => false,
	        'public'                => false,
	        'show_ui'               => true,
	        'show_in_menu'          => 'salesking',
	        'menu_position'         => 123,
	        'show_in_admin_bar'     => true,
	        'show_in_nav_menus'     => false,
	        'can_export'            => true,
	        'has_archive'           => false,
	        'exclude_from_search'   => true,
	        'publicly_queryable'    => false,
	        'capability_type'       => 'product',
	        'map_meta_cap'          => true,
	        'show_in_rest'          => true,
	        'rest_base'             => 'salesking_rule',
	        'rest_controller_class' => 'WP_REST_Posts_Controller',
	    );

		// Actually register the post type
		register_post_type( 'salesking_rule', $args );
	}

	// Add Rule Details Metabox to Rules
	function salesking_rules_metaboxes($post_type) {
	    $post_types = array('salesking_rule');     //limit meta box to certain post types
       	if ( in_array( $post_type, $post_types ) ) {
	           add_meta_box(
	               'salesking_rule_details_metabox'
	               ,esc_html__( 'Rule Details', 'salesking' )
	               ,array( $this, 'salesking_rule_details_metabox_content' )
	               ,$post_type
	               ,'advanced'
	               ,'high'
	           );
	       }
	}

	// Rule Details Metabox Content
	function salesking_grule_details_metabox_content(){
		global $post;
		?>
		<div class="salesking_commission_rule_metabox_content_container">
			<div class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('Rule type:','salesking'); ?></div>
				<select id="salesking_rule_select_what" name="salesking_rule_select_what">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_what', true));
			        }
					?>
					<option value="change_group" <?php selected('change_group',$selected,true); ?>><?php esc_html_e('Change group','salesking'); ?></option>
				</select>
			</div>

			<div class="salesking_rule_select_container" id="salesking_container_applies">
				<div class="salesking_rule_label"><?php esc_html_e('Condition:','salesking'); ?></div>
				
				<select id="salesking_rule_select_applies" name="salesking_rule_select_applies">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_applies', true));
			        	$rule_replaced = esc_html(get_post_meta($post->ID, 'salesking_rule_replaced', true));
			        	if ($rule_replaced === 'yes' && $selected === 'multiple_options'){
			        		$selected = 'replace_ids';
			        	}
			        }
					?>
					<option value="earnings_total" <?php selected('earnings_total',$selected,true); ?>><?php esc_html_e('Total earnings reached','salesking'); ?></option>
					<option value="order_value_total" <?php selected('order_value_total',$selected,true); ?>><?php esc_html_e('Total orders value','salesking'); ?></option>
					<option value="earnings_monthly" <?php selected('earnings_monthly',$selected,true); ?>><?php esc_html_e('Monthly earnings reached (reset)','salesking'); ?></option>
					<option value="order_value_monthly" <?php selected('order_value_monthly',$selected,true); ?>><?php esc_html_e('Monthly orders value (reset)','salesking'); ?></option>
					
				</select>
			</div>
			<div id="salesking_container_howmuch" class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('How much:','salesking'); ?></div>
				<input type="number" step="0.001" name="salesking_rule_select_howmuch" id="salesking_rule_select_howmuch" value="<?php echo esc_attr(get_post_meta($post->ID, 'salesking_rule_howmuch', true)); ?>">
			</div>
			<div class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('For who:','salesking'); ?></div>
				<select id="salesking_rule_select_agents_who" name="salesking_rule_select_agents_who">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_agents_who', true));
			        }
					?>
					<optgroup label="<?php esc_attr_e('Multiple', 'salesking'); ?>">
						<option value="multiple_options" <?php selected('multiple_options',$selected,true); ?>><?php esc_html_e('Select multiple options','salesking'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Agent Groups', 'salesking'); ?>">
						<?php
						// Get all groups
						$groups = get_posts( array( 'post_type' => 'salesking_group','post_status'=>'publish','numberposts' => -1) );
						foreach ($groups as $group){
							echo '<option value="group_'.esc_attr($group->ID).'" '.selected('group_'.$group->ID,$selected,false).'>'.esc_html($group->post_title).'</option>';
						}
						?>
					</optgroup>
				</select>
			</div>
			<div class="salesking_rule_select_container" id="salesking_container_forcustomers">
				<div class="salesking_rule_label"><?php esc_html_e('New group:','salesking'); ?></div>
				<select id="salesking_rule_select_who" name="salesking_rule_select_who">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_who', true));
			        }
					?>
					<optgroup label="<?php esc_attr_e('Agent Groups', 'salesking'); ?>">
						<?php
						// Get all groups
						$groups = get_posts( array( 'post_type' => 'salesking_group','post_status'=>'publish','numberposts' => -1) );
						foreach ($groups as $group){
							echo '<option value="group_'.esc_attr($group->ID).'" '.selected('group_'.$group->ID,$selected,false).'>'.esc_html($group->post_title).'</option>';
						}
						?>
					</optgroup>
				</select>
			</div>
			<br><br>
			<div id="salesking_select_multiple_agents_selector" >
				<div class="salesking_select_multiple_products_categories_title">
					<?php esc_html_e('Select multiple agent options','salesking'); ?>
				</div>
				<select class="salesking_select_multiple_product_categories_selector_select" name="salesking_select_multiple_agents_selector_select[]" multiple>
					<?php
					// if page not "Add new", get selected options
					$selected_options = array();
					if( get_current_screen()->action !== 'add'){
			        	$selected_options_string = get_post_meta($post->ID, 'salesking_rule_agents_who_multiple_options', true);
			        	$selected_options = explode(',', $selected_options_string);
			        }
					?>
					<optgroup label="<?php esc_attr_e('Agent Groups', 'salesking'); ?>">
						<?php
						// Get all groups
						$groups = get_posts( array( 'post_type' => 'salesking_group','post_status'=>'publish','numberposts' => -1) );
						foreach ($groups as $group){
    		            	$is_selected = 'no';
    		            	foreach ($selected_options as $selected_option){
								if ($selected_option === ('group_'.$group->ID )){
									$is_selected = 'yes';
								}
							}
							echo '<option value="group_'.esc_attr($group->ID).'" '.selected('yes',$is_selected,false).'>'.esc_html($group->post_title).'</option>';
						}
						?>
					</optgroup>
					
				</select>

			</div>


			<br /><br />
			
		</div>
		<?php
	}
	
	// Rule Details Metabox Content
	function salesking_rule_details_metabox_content(){
		global $post;
		?>
		<div class="salesking_commission_rule_metabox_content_container">
			<div class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('Rule type:','salesking'); ?></div>
				<select id="salesking_rule_select_what" name="salesking_rule_select_what">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_what', true));
			        }
					?>
					<optgroup label="<?php esc_attr_e('Commission Rules', 'salesking'); ?>"> 
						<option value="fixed" <?php selected('fixed',$selected,true); ?>><?php esc_html_e('Fixed amount','salesking'); ?></option>
						<option value="percentage" <?php selected('percentage',$selected,true); ?>><?php esc_html_e('Percentage','salesking'); ?></option>
					</optgroup>
				</select>
			</div>
			<div id="salesking_container_howmuch" class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('How much:','salesking'); ?></div>
				<input type="number" step="0.001" name="salesking_rule_select_howmuch" id="salesking_rule_select_howmuch" value="<?php echo esc_attr(get_post_meta($post->ID, 'salesking_rule_howmuch', true)); ?>">
			</div>
			<div class="salesking_rule_select_container" id="salesking_container_applies">
				<div class="salesking_rule_label"><?php esc_html_e('Applies for products:','salesking'); ?></div>
				
				<select id="salesking_rule_select_applies" name="salesking_rule_select_applies">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_applies', true));
			        	$rule_replaced = esc_html(get_post_meta($post->ID, 'salesking_rule_replaced', true));
			        	if ($rule_replaced === 'yes' && $selected === 'multiple_options'){
			        		$selected = 'replace_ids';
			        	}
			        }
					?>
					<optgroup label="<?php esc_attr_e('Multiple', 'salesking'); ?>" id="salesking_cart_total_optgroup" >
						<option value="cart_total" <?php selected('cart_total',$selected,true); ?>><?php esc_html_e('All products','salesking'); ?></option>
						<option value="multiple_options" <?php selected('multiple_options',$selected,true); ?>><?php esc_html_e('Select categories & tags','salesking'); ?></option>
						<option value="once_per_order" <?php selected('once_per_order',$selected,true); ?>><?php esc_html_e('Once per order','salesking'); ?></option>


						<?php
						if (intval(get_option( 'salesking_replace_product_selector_setting', 0 )) === 1){
							?>
							<option value="replace_ids" <?php selected('replace_ids',$selected,true); ?>><?php esc_html_e('Product or Variation ID(s)','salesking'); ?></option>
							<?php
						}
						?>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Product Categories', 'salesking'); ?>">
						<?php
						// Get all categories
						$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
						foreach ($categories as $category){
							echo '<option value="category_'.esc_attr($category->term_id).'" '.selected('category_'.$category->term_id, $selected,false).'>'.esc_html($category->name).'</option>';
						}
						?>
					</optgroup>

					<optgroup label="<?php esc_attr_e('Product Tags', 'salesking'); ?>">
						<?php
						// Get all categories
						$tags = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => false ) );
						foreach ($tags as $tag){
							echo '<option value="tag_'.esc_attr($tag->term_id).'" '.selected('tag_'.$tag->term_id, $selected,false).'>'.esc_html($tag->name).'</option>';
						}
						?>
					</optgroup>
					
				</select>
			</div>
			<div class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('For agents:','salesking'); ?></div>
				<select id="salesking_rule_select_agents_who" name="salesking_rule_select_agents_who">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_agents_who', true));
			        }
					?>
					<optgroup label="<?php esc_attr_e('Multiple', 'salesking'); ?>">

						<option value="all_agents" <?php selected('all_agents',$selected,true); ?>><?php esc_html_e('All agents','salesking'); ?></option>
						<option value="multiple_options" <?php selected('multiple_options',$selected,true); ?>><?php esc_html_e('Select multiple options','salesking'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Agent Groups', 'salesking'); ?>">
						<?php
						// Get all groups
						$groups = get_posts( array( 'post_type' => 'salesking_group','post_status'=>'publish','numberposts' => -1) );
						foreach ($groups as $group){
							echo '<option value="group_'.esc_attr($group->ID).'" '.selected('group_'.$group->ID,$selected,false).'>'.esc_html($group->post_title).'</option>';
						}
						?>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Agents (individual)', 'salesking'); ?>">
						<?php 
							// if B2B/B2C Hybrid, show only B2B users
						 	$agents = get_users(array(
									    'meta_key'     => 'salesking_group',
									    'meta_value'   => 'none',
									    'meta_compare' => '!=',
									));

							foreach ($agents as $agent){
								echo '<option value="agent_'.esc_attr($agent->ID).'" '.selected('agent_'.$agent->ID,$selected,false).'>'.esc_html($agent->user_login).'</option>';
							}
						?>
					</optgroup>
				</select>
			</div>
			<div class="salesking_rule_select_container" id="salesking_container_forcustomers">
				<div class="salesking_rule_label"><?php esc_html_e('For customers:','salesking'); ?></div>
				<select id="salesking_rule_select_who" name="salesking_rule_select_who">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_who', true));
			        }
					?>
					<optgroup label="<?php esc_attr_e('Everyone', 'salesking'); ?>">
						<option value="everyone" <?php selected('everyone',$selected,true); ?>><?php esc_html_e('All users','salesking'); ?></option>
						<option value="all_registered" <?php selected('all_registered',$selected,true); ?>><?php esc_html_e('All logged in users','salesking'); ?></option>
						<?php
						if (defined('B2BKING_DIR')){
							?>
							<option value="everyone_registered_b2b" <?php selected('everyone_registered_b2b',$selected,true); ?>><?php esc_html_e('All logged in B2B users','salesking'); ?></option>
							<option value="everyone_registered_b2c" <?php selected('everyone_registered_b2c',$selected,true); ?>><?php esc_html_e('All logged in B2C users','salesking'); ?></option>
							<?php
						}
						?>
						<option value="user_0" <?php selected('user_0',$selected,true); ?>><?php esc_html_e('All logged out users','salesking'); ?></option>
						<option value="multiple_options" <?php selected('multiple_options',$selected,true); ?>><?php esc_html_e('Select multiple options','salesking'); ?></option>

					</optgroup>
					<?php
					if (defined('B2BKING_DIR')){
						?>
						<optgroup label="<?php esc_attr_e('B2B Groups', 'salesking'); ?>">
							<?php
							// Get all groups
							$groups = get_posts( array( 'post_type' => 'b2bking_group','post_status'=>'publish','numberposts' => -1) );
							foreach ($groups as $group){
								echo '<option value="group_'.esc_attr($group->ID).'" '.selected('group_'.$group->ID,$selected,false).'>'.esc_html($group->post_title).'</option>';
							}
							?>
						</optgroup>
						<?php
					}
					?>
					<?php if (intval(get_option( 'salesking_hide_users_commission_rules_setting', 1 )) !== 1){ ?>
					<optgroup label="<?php esc_attr_e('Users (individual)', 'salesking'); ?>">
						<?php 
							// if B2B/B2C Hybrid, show only B2B users
							if(get_option( 'salesking_plugin_status_setting', 'b2b' ) === 'hybrid'){
								$users = get_users(array(
								    'meta_key'     => 'b2bking_b2buser',
								    'meta_value'   => 'yes',
								    'fields'=> array('ID', 'user_login'),
								));

							} else {
								$users = get_users(array(
								    'fields'=> array('ID', 'user_login'),
								));
							}

							foreach ($users as $user){
								// do not show subaccounts
								$account_type = get_user_meta($user->ID, 'salesking_account_type', true);
								if ($account_type !== 'subaccount'){
									echo '<option value="user_'.esc_attr($user->ID).'" '.selected('user_'.$user->ID,$selected,false).'>'.esc_html($user->user_login).'</option>';
								}
							}
						?>
					</optgroup>
					<?php } ?>
				</select>
			</div>
			<br /><br />
			<div class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('For orders:','salesking'); ?></div>
				<select id="salesking_rule_select_orders" name="salesking_rule_select_orders">
					<?php
					// if page not "Add new", get selected
					$selected = '';
					if( get_current_screen()->action !== 'add'){
			        	$selected = esc_html(get_post_meta($post->ID, 'salesking_rule_orders', true));
			        }
					?>
					<optgroup label="<?php esc_attr_e('All Orders', 'salesking'); ?>">
						<option value="all" <?php selected('all',$selected,true); ?>><?php esc_html_e('All orders','salesking'); ?></option>
						<option value="all_first_days_after_registration" <?php selected('all_first_days_after_registration',$selected,true); ?>><?php esc_html_e('All orders in the first X days after customer registration','salesking'); ?></option>
						<option value="first_x_orders_after_registration" <?php selected('first_x_orders_after_registration',$selected,true); ?>><?php esc_html_e('First X orders after customer registration','salesking'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Orders placed by Agent', 'salesking'); ?>">
						<option value="all_agent" <?php selected('all_agent',$selected,true); ?>><?php esc_html_e('All orders placed by the agent','salesking'); ?></option>
						<option value="all_agent_first_days_after_registration" <?php selected('all_agent_first_days_after_registration',$selected,true); ?>><?php esc_html_e('All orders placed by the agent in the first X days after customer registration','salesking'); ?></option>
						<option value="agent_first_x_orders_after_registration" <?php selected('agent_first_x_orders_after_registration',$selected,true); ?>><?php esc_html_e('First X orders placed by the agent after customer registration','salesking'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Orders placed by Customer', 'salesking'); ?>">
						<option value="all_customer_first_days_after_registration" <?php selected('all_customer_first_days_after_registration',$selected,true); ?>><?php esc_html_e('All orders placed by the customer in the first X days after customer registration','salesking'); ?></option>
						<option value="customer_first_x_orders_after_registration" <?php selected('customer_first_x_orders_after_registration',$selected,true); ?>><?php esc_html_e('First X orders placed by the customer after customer registration','salesking'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Orders of Subagents', 'salesking'); ?>">
						<option value="all_earnings" <?php selected('all_earnings',$selected,true); ?>><?php esc_html_e('All earnings of subagent','salesking'); ?></option>
						<option value="reach_x_number" <?php selected('reach_x_number',$selected,true); ?>><?php esc_html_e('Until subagent earnings reach x total','salesking'); ?></option>
						<option value="first_x_earnings" <?php selected('first_x_earnings',$selected,true); ?>><?php esc_html_e('First x transactions (earnings) of the subagent','salesking'); ?></option>
						<option value="first_x_days" <?php selected('first_x_days',$selected,true); ?>><?php esc_html_e('Earnings in the first x days after subagent is recruited','salesking'); ?></option>

					</optgroup>
				</select>
			</div>

			<div id="salesking_container_x" class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('X:','salesking'); ?></div>
				<input type="number" step="1" min="1" name="salesking_rule_select_x" id="salesking_rule_select_x" value="<?php echo esc_attr(get_post_meta($post->ID, 'salesking_rule_x', true)); ?>">
			</div>
			<br ><br>



			<div id="salesking_select_multiple_product_categories_selector" >
				<div class="salesking_select_multiple_products_categories_title">
					<?php esc_html_e('Select multiple categories & tags','salesking'); ?>
				</div>
				<select class="salesking_select_multiple_product_categories_selector_select" name="salesking_select_multiple_product_categories_selector_select[]" multiple>
					<?php
					// if page not "Add new", get selected options
					$selected_options = array();
					if( get_current_screen()->action !== 'add'){
			        	$selected_options_string = get_post_meta($post->ID, 'salesking_rule_applies_multiple_options', true);
			        	$selected_options = explode(',', $selected_options_string);
			        }
			        ?>
			        <optgroup label="<?php esc_attr_e('Product Categories', 'salesking'); ?>">
			        	<?php
			        	// Get all categories
			        	$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false) );
			        	foreach ($categories as $category){
    		            	$is_selected = 'no';
    		            	foreach ($selected_options as $selected_option){
								if ($selected_option === ('category_'.$category->term_id )){
									$is_selected = 'yes';
								}
							}
			        		echo '<option value="category_'.esc_attr($category->term_id).'" '.selected('yes',$is_selected, true).'>'.esc_html($category->name).'</option>';
			        	}
			        	?>
			        </optgroup>
			        <optgroup label="<?php esc_attr_e('Product Tags', 'salesking'); ?>">
			        	<?php
			        	// Get all categories
			        	$tags = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => false) );
			        	foreach ($tags as $tag){
    		            	$is_selected = 'no';
    		            	foreach ($selected_options as $selected_option){
								if ($selected_option === ('tag_'.$tag->term_id )){
									$is_selected = 'yes';
								}
							}
			        		echo '<option value="tag_'.esc_attr($tag->term_id).'" '.selected('yes',$is_selected, true).'>'.esc_html($tag->name).'</option>';
			        	}
			        	?>
			        </optgroup>
				</select>

			</div>

			<div id="salesking_container_minmax" class="salesking_rule_select_container">
				<div class="salesking_rule_label"><?php esc_html_e('Order Value Restrictions (Optional)','salesking'); ?></div>
				<input type="number" step="0.0001" min="0.0001" name="salesking_rule_min" id="salesking_rule_min" value="<?php echo esc_attr(get_post_meta($post->ID, 'salesking_rule_min', true)); ?>" placeholder="<?php esc_html_e('Minimum Order Value','salesking');?>"><br />
				<input type="number" step="0.0001" min="0.0001" name="salesking_rule_max" id="salesking_rule_max" value="<?php echo esc_attr(get_post_meta($post->ID, 'salesking_rule_max', true)); ?>"  placeholder="<?php esc_html_e('Maximum Order Value','salesking');?>">
			</div>
		

			<div id="salesking_select_multiple_users_selector" >
				<div class="salesking_select_multiple_products_categories_title">
					<?php esc_html_e('Select multiple customer options','salesking'); ?>
				</div>
				<select class="salesking_select_multiple_product_categories_selector_select" name="salesking_select_multiple_users_selector_select[]" multiple>
					<?php
					// if page not "Add new", get selected options
					$selected_options = array();
					if( get_current_screen()->action !== 'add'){
			        	$selected_options_string = get_post_meta($post->ID, 'salesking_rule_who_multiple_options', true);
			        	$selected_options = explode(',', $selected_options_string);
			        }

					?>
					<optgroup label="<?php esc_attr_e('Everyone', 'salesking'); ?>">
						<?php
		            	$is_selected_everyone_registered = 'no';
		            	$is_selected_everyone_registered_b2b = 'no';
		            	$is_selected_everyone_registered_b2c = 'no';
		            	$is_selected_guests = 'no';
		            	foreach ($selected_options as $selected_option){
							if ($selected_option === ('all_registered')){
								$is_selected_everyone_registered = 'yes';
							}
							if ($selected_option === ('everyone_registered_b2b')){
								$is_selected_everyone_registered_b2b = 'yes';
							}
							if ($selected_option === ('everyone_registered_b2c')){
								$is_selected_everyone_registered_b2c = 'yes';
							}
							if ($selected_option === ('user_0')){
								$is_selected_guests = 'yes';
							}
						}
						?>
						<option value="everyone" <?php selected('yes',$is_selected_everyone_registered,true); ?>><?php esc_html_e('All users','salesking'); ?></option>
						<option value="all_registered" <?php selected('yes',$is_selected_everyone_registered,true); ?>><?php esc_html_e('All logged in users','salesking'); ?></option>
						<?php if (defined('B2BKING_DIR')){ 
							?>
							<option value="everyone_registered_b2b" <?php selected('yes',$is_selected_everyone_registered_b2b,true); ?>><?php esc_html_e('All logged in B2B users','salesking'); ?></option>
							<option value="everyone_registered_b2c" <?php selected('yes',$is_selected_everyone_registered_b2c,true); ?>><?php esc_html_e('All logged in B2C users','salesking'); ?></option>
							<?php
						}
						?>
						<option value="user_0" <?php selected('yes',$is_selected_guests,true); ?>><?php esc_html_e('All logged out users','salesking'); ?></option>

					</optgroup>
					<?php if (defined('B2BKING_DIR')){ 
						?>
						<optgroup label="<?php esc_attr_e('B2B Groups', 'salesking'); ?>">
							<?php
							// Get all groups
							$groups = get_posts( array( 'post_type' => 'salesking_group','post_status'=>'publish','numberposts' => -1) );
							foreach ($groups as $group){
	    		            	$is_selected = 'no';
	    		            	foreach ($selected_options as $selected_option){
									if ($selected_option === ('group_'.$group->ID )){
										$is_selected = 'yes';
									}
								}
								echo '<option value="group_'.esc_attr($group->ID).'" '.selected('yes',$is_selected,false).'>'.esc_html($group->post_title).'</option>';
							}
							?>
						</optgroup>
						<?php
					}
					?>
					<?php if (intval(get_option( 'salesking_hide_users_commission_rules_setting', 1 )) !== 1){ ?>
					<optgroup label="<?php esc_attr_e('Users (individual)', 'salesking'); ?>">
						<?php 
							// if B2B/B2C Hybrid, show only B2B users
							if(get_option( 'salesking_plugin_status_setting', 'b2b' ) === 'hybrid'){
								$users = get_users(array(
								    'meta_key'     => 'salesking_b2buser',
								    'meta_value'   => 'yes',
								    'fields'=> array('ID', 'user_login'),
								));
							} else {
								$users = get_users(array(
								    'fields'=> array('ID', 'user_login'),
								));
							}
							foreach ($users as $user){
	    		            	$is_selected = 'no';
	    		            	foreach ($selected_options as $selected_option){
									if ($selected_option === ('user_'.$user->ID )){
										$is_selected = 'yes';
									}
								}
								// do not show subaccounts
								$account_type = get_user_meta($user->ID, 'salesking_account_type', true);
								if ($account_type !== 'subaccount'){
									echo '<option value="user_'.esc_attr($user->ID).'" '.selected('yes',$is_selected,false).'>'.esc_html($user->user_login).'</option>';
								}
							}
						?>
					</optgroup>
					<?php } ?>
				</select>

			</div>

			<div id="salesking_select_multiple_agents_selector" >
				<div class="salesking_select_multiple_products_categories_title">
					<?php esc_html_e('Select multiple agent options','salesking'); ?>
				</div>
				<select class="salesking_select_multiple_product_categories_selector_select" name="salesking_select_multiple_agents_selector_select[]" multiple>
					<?php
					// if page not "Add new", get selected options
					$selected_options = array();
					if( get_current_screen()->action !== 'add'){
			        	$selected_options_string = get_post_meta($post->ID, 'salesking_rule_agents_who_multiple_options', true);
			        	$selected_options = explode(',', $selected_options_string);
			        }
					?>
					<optgroup label="<?php esc_attr_e('Agent Groups', 'salesking'); ?>">
						<?php
						// Get all groups
						$groups = get_posts( array( 'post_type' => 'salesking_group','post_status'=>'publish','numberposts' => -1) );
						foreach ($groups as $group){
    		            	$is_selected = 'no';
    		            	foreach ($selected_options as $selected_option){
								if ($selected_option === ('group_'.$group->ID )){
									$is_selected = 'yes';
								}
							}
							echo '<option value="group_'.esc_attr($group->ID).'" '.selected('yes',$is_selected,false).'>'.esc_html($group->post_title).'</option>';
						}
						?>
					</optgroup>
					<optgroup label="<?php esc_attr_e('Agents (individual)', 'salesking'); ?>">
						<?php 
							// if B2B/B2C Hybrid, show only B2B users
						 	$agents = get_users(array(
									    'meta_key'     => 'salesking_group',
									    'meta_value'   => 'none',
									    'meta_compare' => '!=',
									));

							foreach ($agents as $agent){
	    		            	$is_selected = 'no';
	    		            	foreach ($selected_options as $selected_option){
									if ($selected_option === ('agent_'.$agent->ID )){
										$is_selected = 'yes';
									}
								}
								echo '<option value="agent_'.esc_attr($agent->ID).'" '.selected('yes',$is_selected,false).'>'.esc_html($agent->user_login).'</option>';
							}
						?>
					</optgroup>
				</select>

			</div>



			<br /><br />
			
		</div>
		<?php
	}

	

	// Save Rules Metabox Content
	function salesking_save_rules_metaboxes($post_id){
		if (isset($_POST['_inline_edit'])){
			if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')){
			    return;
			}
		}
		if (isset($_REQUEST['bulk_edit'])){
		    return;
		}
		if (isset($_REQUEST['duplicate_nonce']) or isset($_POST['duplicate_nonce'])){
		    return;
		}
		// clear cache when saving products
		if (get_post_type($post_id) === 'product'){
			// set that rules have changed so that pricing cache can be updated
			update_option('salesking_commission_rules_have_changed', 'yes');

			// delete all salesking transients
			global $wpdb;
			$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%transient_salesking%'" );
			foreach( $plugin_options as $option ) {
			    delete_option( $option->option_name );
			}
			wp_cache_flush();
		}
		if (get_post_type($post_id) === 'salesking_rule'){

			// set that rules have changed so that pricing cache can be updated
			update_option('salesking_commission_rules_have_changed', 'yes');

			// delete all salesking transients
			global $wpdb;
			$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%transient_salesking%'" );
			foreach( $plugin_options as $option ) {
			    delete_option( $option->option_name );
			}
			wp_cache_flush();

			$rule_what = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_what'));
			$rule_applies = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_applies'));
			$rule_orders = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_orders'));

			$rule_who = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_who'));
			$rule_agents_who = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_agents_who'));

			$rule_quantity_value = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_quantity_value'));
			$rule_tax_shipping = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_tax_shipping'));
			$rule_tax_shipping_rate = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_tax_shipping_rate'));
			$rule_howmuch = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_howmuch'));
			$rule_x = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_x'));
			$rule_min = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_min'));
			$rule_max = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_max'));

			$rule_currency = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_currency'));
			$rule_paymentmethod = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_paymentmethod'));
			$rule_paymentmethod_minmax = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_paymentmethod_minmax'));
			$rule_paymentmethod_percentamount = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_paymentmethod_percentamount'));

			$rule_taxname = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_taxname'));
			$rule_discountname = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_discountname'));
			$rule_conditions = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_conditions'));
			$rule_tags = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_tags'));
			$rule_discount_show_everywhere = sanitize_text_field(filter_input(INPUT_POST, 'salesking_commission_rule_discount_show_everywhere_checkbox_input'));
			
			if (isset($_POST['salesking_rule_select_countries'])){
				$rule_countries = $_POST['salesking_rule_select_countries'];
			} else {
				$rule_countries = NULL;
			}

			if (isset($_POST['salesking_select_multiple_product_categories_selector_select'])){
				$rule_applies_multiple_options = $_POST['salesking_select_multiple_product_categories_selector_select'];
			} else {
				$rule_applies_multiple_options = NULL;
			}

			if (isset($_POST['salesking_select_multiple_users_selector_select'])){
				$rule_who_multiple_options = $_POST['salesking_select_multiple_users_selector_select'];
			} else {
				$rule_who_multiple_options = NULL;
			}

			if (isset($_POST['salesking_select_multiple_agents_selector_select'])){
				$rule_agents_who_multiple_options = $_POST['salesking_select_multiple_agents_selector_select'];
			} else {
				$rule_agents_who_multiple_options = NULL;
			}

			$rule_requires = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_requires'));
			$rule_showtax = sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_showtax'));

			if ($rule_what !== NULL){
				update_post_meta( $post_id, 'salesking_rule_what', $rule_what);
			}
			if ($rule_currency !== NULL){
				update_post_meta( $post_id, 'salesking_rule_currency', $rule_currency);
			}
			if ($rule_paymentmethod !== NULL){
				update_post_meta( $post_id, 'salesking_rule_paymentmethod', $rule_paymentmethod);
			}
			if ($rule_paymentmethod_minmax !== NULL){
				update_post_meta( $post_id, 'salesking_rule_paymentmethod_minmax', $rule_paymentmethod_minmax);
			}
			if ($rule_paymentmethod_percentamount !== NULL){
				update_post_meta( $post_id, 'salesking_rule_paymentmethod_percentamount', $rule_paymentmethod_percentamount);
			}
			if ($rule_applies !== NULL){
				update_post_meta( $post_id, 'salesking_rule_applies', $rule_applies);
			}
			if ($rule_who !== NULL){
				update_post_meta( $post_id, 'salesking_rule_who', $rule_who);
			}
			if ($rule_orders !== NULL){
				update_post_meta( $post_id, 'salesking_rule_orders', $rule_orders);
			}
			if ($rule_agents_who !== NULL){
				update_post_meta( $post_id, 'salesking_rule_agents_who', $rule_agents_who);
			}
			if ($rule_quantity_value !== NULL){
				update_post_meta( $post_id, 'salesking_rule_quantity_value', $rule_quantity_value);
			}
			if ($rule_howmuch !== NULL){
				update_post_meta( $post_id, 'salesking_rule_howmuch', $rule_howmuch);
			}
			if ($rule_x !== NULL){
				update_post_meta( $post_id, 'salesking_rule_x', $rule_x);
			}
			if ($rule_min !== NULL){
				update_post_meta( $post_id, 'salesking_rule_min', $rule_min);
			}
			if ($rule_max !== NULL){
				update_post_meta( $post_id, 'salesking_rule_max', $rule_max);
			}
			if ($rule_taxname !== NULL){
				update_post_meta( $post_id, 'salesking_rule_taxname', $rule_taxname);
			}
			if ($rule_tax_shipping !== NULL){
				update_post_meta( $post_id, 'salesking_rule_tax_shipping', $rule_tax_shipping);
			}
			if ($rule_tax_shipping_rate !== NULL){
				update_post_meta( $post_id, 'salesking_rule_tax_shipping_rate', $rule_tax_shipping_rate);
			}
			if ($rule_discountname !== NULL){
				update_post_meta( $post_id, 'salesking_rule_discountname', $rule_discountname);
			}
			if ($rule_conditions !== NULL){
				update_post_meta( $post_id, 'salesking_rule_conditions', $rule_conditions);
			}
			if ($rule_tags !== NULL){
				update_post_meta( $post_id, 'salesking_rule_tags', $rule_tags);
			}
			if ($rule_discount_show_everywhere !== NULL){
				update_post_meta( $post_id, 'salesking_rule_discount_show_everywhere', $rule_discount_show_everywhere);
			}

			
			if ($rule_countries !== NULL){
				$countries_string = '';
				foreach ($rule_countries as $country){
					$countries_string .= sanitize_text_field ($country).',';
				}
				// remove last comma
				$countries_string = substr($countries_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_countries', $countries_string);
			}
			if ($rule_requires !== NULL){
				update_post_meta( $post_id, 'salesking_rule_requires', $rule_requires);
			}
			if ($rule_showtax !== NULL){
				update_post_meta( $post_id, 'salesking_rule_showtax', $rule_showtax);
			}

			if ($rule_applies_multiple_options !== NULL){
				$options_string = '';
				foreach ($rule_applies_multiple_options as $option){
					$options_string .= sanitize_text_field ($option).',';
				}
				// remove last comma
				$options_string = substr($options_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_applies_multiple_options', $options_string);
			}

			if ($rule_who_multiple_options !== NULL){
				$options_string = '';
				foreach ($rule_who_multiple_options as $option){
					$options_string .= sanitize_text_field ($option).',';
				}
				// remove last comma
				$options_string = substr($options_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_who_multiple_options', $options_string);
			}

			if ($rule_agents_who_multiple_options !== NULL){
				$options_string = '';
				foreach ($rule_agents_who_multiple_options as $option){
					$options_string .= sanitize_text_field ($option).',';
				}
				// remove last comma
				$options_string = substr($options_string, 0, -1);
				update_post_meta( $post_id, 'salesking_rule_agents_who_multiple_options', $options_string);
			}

			$rule_replaced =  sanitize_text_field(filter_input(INPUT_POST, 'salesking_rule_select_applies_replaced')); 
			$rule_replaced_array = explode(',',$rule_replaced);
			$rule_replaced_string = '';
			foreach ($rule_replaced_array as $element){
				$rule_replaced_string.= 'product_'.trim($element).',';
			}
			// remove last comma
			$rule_replaced_string = substr($rule_replaced_string, 0, -1);

			// if rule applies is product & variation IDS, set applies as salesking_rule_select_applies_replaced
			if ($rule_applies === 'replace_ids'){
				if ($rule_replaced !== NULL){
					update_post_meta( $post_id, 'salesking_rule_applies', 'multiple_options');
					update_post_meta( $post_id, 'salesking_rule_applies_multiple_options', $rule_replaced_string);
					update_post_meta( $post_id, 'salesking_rule_replaced', 'yes');
				}
			} else {
				update_post_meta( $post_id, 'salesking_rule_replaced', 'no');
			}

		}
	}

	
	function salesking_settings_page_content() {
		require_once ( SALESKING_DIR . 'admin/class-salesking-settings.php' );
		$settings = new Salesking_Settings;
		$settings-> render_settings_page_content();
	}


	function salesking_options_capability( $capability ) {
	    return 'manage_woocommerce';
	}

	function load_global_admin_notice_resource(){
		wp_enqueue_script( 'salesking_global_admin_notice_script', plugins_url('assets/js/adminnotice.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

		// Send data to JS
		$data_js = array(
			'security'  => wp_create_nonce( 'salesking_notice_security_nonce' ),
		);
		wp_localize_script( 'salesking_global_admin_notice_script', 'salesking_notice', $data_js );
		
	}

	function load_global_admin_resources( $hook ){
		// compatibility with welaunch single variations plugin
		if ($hook !== 'woocommerce_page_woocommerce_single_variations_options_options'){
			wp_enqueue_style('select2', plugins_url('../includes/assets/lib/select2/select2.min.css', __FILE__) );
			wp_enqueue_script('select2', plugins_url('../includes/assets/lib/select2/select2.min.js', __FILE__), array('jquery') );
		}

		wp_enqueue_style ( 'salesking_global_admin_style', plugins_url('assets/css/adminglobal.css', __FILE__));
		// Enqueue color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'salesking_global_admin_script', plugins_url('assets/js/adminglobal.js', __FILE__), $deps = array('wp-color-picker'), $ver = false, $in_footer =true);

		wp_enqueue_script('dataTables', plugins_url('../includes/assets/lib/dataTables/jquery.dataTables.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
		wp_enqueue_style( 'dataTables', plugins_url('../includes/assets/lib/dataTables/jquery.dataTables.min.css', __FILE__));

		if ($hook === 'salesking_page_salesking_earnings' or $hook === 'salesking_page_salesking_payouts' or $hook === 'admin_page_salesking_view_earnings'){

			wp_enqueue_script('dataTablesButtons', plugins_url('../includes/assets/lib/dataTables/dataTables.buttons.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_script('dataTablesButtonsHTML', plugins_url('../includes/assets/lib/dataTables/buttons.html5.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_script('dataTablesButtonsPrint', plugins_url('../includes/assets/lib/dataTables/buttons.print.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_script('dataTablesButtonsColvis', plugins_url('../includes/assets/lib/dataTables/buttons.colVis.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

			wp_enqueue_script('jszip', plugins_url('../includes/assets/lib/dataTables/jszip.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_script('pdfmake', plugins_url('../includes/assets/lib/dataTables/pdfmake.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_script('vfsfonts', plugins_url('../includes/assets/lib/dataTables/vfs_fonts.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

		}
		// Dashboard
		if ($hook === 'salesking_page_salesking_reports'){
			wp_enqueue_style( 'salesking_admin_dashboard', plugins_url('assets/dashboard/cssjs/dashboardstyle.min.css', __FILE__));

			// Dashboard
			wp_enqueue_style ('salesking_chartist', plugins_url('assets/dashboard/chartist/chartist.min.css', __FILE__));
			wp_enqueue_script('salesking_chartist', plugins_url('assets/dashboard/chartist/chartist.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_script('salesking_chartist-plugin-tooltip', plugins_url('assets/dashboard/chartist/chartist-plugin-tooltip.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

			wp_enqueue_style ( 'salesking_pages_admin_style', plugins_url('assets/css/adminmkpages.css', __FILE__));

		}

		// Dashboard end

		wp_enqueue_script('notify', plugins_url('../includes/assets/lib/notify/notify.min.js', __FILE__) );

		// Send data to JS
		$translation_array = array(
			'admin_url' => get_admin_url(),
			'security'  => wp_create_nonce( 'salesking_security_nonce' ),
		    'currency_symbol' => get_woocommerce_currency_symbol(),
		    'sure_save_payment' => esc_html__('Are you sure you want to save this payment?', 'salesking'),
		    'sure_save_adjustment' => esc_html__('Are you sure you want to make this manual adjustment?', 'salesking'),
		    'group_rules_link' => admin_url( 'edit.php?post_type=salesking_grule'),
		    'group_rules_text' => esc_html__('Set up group rules (optional)', 'salesking'),
		    'are_you_sure_set_subaccounts' => esc_html__('Are you sure you want to set these users as subagents of the parent agent? They will become a part of the main agent\'s team and they will be assigned to the Agent Group of the parent account.', 'salesking'),
		    'are_you_sure_set_subaccounts_regular' => esc_html__('Are you sure you want to make these users no longer be subagents? (they will become main / top level agents )', 'salesking'),
		    'are_you_sure_set_agent_customers' => esc_html__('Are you sure you want assign this agent for all customers?', 'salesking'),
		    'subaccounts_have_been_set' => esc_html__('All accounts have been set','salesking'),
		    'tables_language_option' => get_option('salesking_tables_language_option_setting','English'),
		    'datatables_folder' => plugins_url('../includes/assets/lib/dataTables/i18n/', __FILE__),
		    'sending_request' => esc_html__('Processing activation request...', 'salesking'),
		    'print' => esc_html__('Print', 'salesking'), 
		    'edit_columns' => esc_html__('Edit Columns', 'salesking'), 

		);
		if (isset($_GET['post'])){
			$translation_array['current_post_type'] = get_post_type(sanitize_text_field($_GET['post'] ));
		}
		if (isset($_GET['action'])){
			$translation_array['current_action'] = sanitize_text_field($_GET['action'] );
		}

		wp_localize_script( 'salesking_global_admin_script', 'salesking', $translation_array );

		if ($hook === 'salesking_page_salesking_tools'){
			wp_enqueue_script('semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_style( 'semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.css', __FILE__));
			wp_enqueue_style ( 'salesking_admin_style', plugins_url('assets/css/adminstyle.css', __FILE__));
			wp_enqueue_script( 'salesking_admin_script', plugins_url('assets/js/admin.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
		}
	}
	
	function load_admin_resources($hook) {

		// Load only on this specific plugin admin
		if($hook !== 'toplevel_page_salesking') {
			return;
		}

		// remove boostrap
		global $wp_scripts;
		foreach ($wp_scripts->queue as $index => $name){
			if ($name === 'bootstrap'){
				unset($wp_scripts->queue[$index]);
			}
		}

		wp_enqueue_media();

		wp_enqueue_script('semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
	    wp_enqueue_style( 'semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.css', __FILE__));

		wp_enqueue_script('jquery');

		wp_enqueue_style ( 'salesking_admin_style', plugins_url('assets/css/adminstyle.css', __FILE__));
		wp_enqueue_script( 'salesking_admin_script', plugins_url('assets/js/admin.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

		wp_enqueue_style( 'salesking_style', plugins_url('../includes/assets/css/style.css', __FILE__)); 

	}

	
	function salesking_plugin_dependencies() {
		if ( ! class_exists( 'woocommerce' ) ) {
			// if notice has not already been dismissed once by the current user
			if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_activate_woocommerce_notice', true)) !== 1){
	    		?>
	    	    <div class="salesking_activate_woocommerce_notice notice notice-warning is-dismissible">
	    	        <p><?php esc_html_e( 'Warning: The plugin "SalesKing" requires WooCommerce to be installed and activated.', 'salesking' ); ?></p>
	    	    </div>
    	    	<?php
    	    }
		}
	}

	function salesking_rules_howto() {
		global $current_screen;
	    if( 'salesking_rule' != $current_screen->post_type ){
		    return;
	    }

		// if notice has not already been dismissed once by the current user
		if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_rules_howto_notice', true)) !== 1){
    		?>
    	    <div class="salesking_rules_howto_notice notice notice-info is-dismissible">
    	        <p><?php esc_html_e( 'Here you can set up a complex commission structure for your sales agents. You can combine multiple types of rules for agent commission, as well as set up subagents (teams) commissions.', 'salesking' ); ?></p>
    	    </div>
	    	<?php
	    }
		
	}

	function salesking_groups_howto() {
		global $current_screen;
	    if( 'salesking_group' != $current_screen->post_type ){
		    return;
	    }

		// if notice has not already been dismissed once by the current user
		if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_groups_howto_notice', true)) !== 1){
    		?>
    	    <div class="salesking_groups_howto_notice notice notice-info is-dismissible">
    	        <p><?php esc_html_e( 'Agent groups help you organize and manage your sales agents. Create, edit, or delete groups based on your store\'s needs. To add a user to a group, go to the user\'s profile and scroll down to \'Agent Settings\'.', 'salesking' ); ?></p>
    	    </div>
	    	<?php
	    }
		
	}

	function salesking_groupsrules_howto() {
		global $current_screen;
	    if( 'salesking_grule' != $current_screen->post_type ){
		    return;
	    }

		// if notice has not already been dismissed once by the current user
		if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_groupsrules_howto_notice', true)) !== 1){
    		?>
    	    <div class="salesking_groupsrules_howto_notice notice notice-info is-dismissible">
    	        <p><?php esc_html_e( 'Through group rules, you can automatically change an agent\'s group when they hit a particular threshold such as an earnings numbers. For example, this allows you to promote agents across ranks.', 'salesking' ); ?></p>
    	    </div>
	    	<?php
	    }
		
	}

	function salesking_announcements_howto() {
		global $current_screen;
	    if( 'salesking_announce' != $current_screen->post_type ){
		    return;
	    }

		// if notice has not already been dismissed once by the current user
		if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_announcements_howto_notice', true)) !== 1){
    		?>
    	    <div class="salesking_announcements_howto_notice notice notice-info is-dismissible">
    	        <p><?php esc_html_e( 'Announcements are notifications that are broadcast to your agents and show up in each agent\'s dashboard and in email notifications. Agents cannot reply to announcements.', 'salesking' ); ?></p>
    	    </div>
	    	<?php
	    }
	}

	function salesking_messages_howto() {
		global $current_screen;
	    if( 'salesking_message' != $current_screen->post_type ){
		    return;
	    }

		// if notice has not already been dismissed once by the current user
		if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_messages_howto_notice', true)) !== 1){
    		?>
    	    <div class="salesking_messages_howto_notice notice notice-info is-dismissible">
    	        <p><?php esc_html_e( 'Messages allow you to stay in touch with your sales team, ask or receive questions, clarify matters, queries, etc. Sales agents can also initiate messages.', 'salesking' ); ?></p>
    	    </div>
	    	<?php
	    }
	}

	function salesking_payouts_howto() {
		global $current_screen;
	    if( 'salesking_page_salesking_payouts' != $current_screen->id ){
		    return;
	    }

		// if notice has not already been dismissed once by the current user
		if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_payouts_howto_notice', true)) !== 1){
    		?>
    		<br />
    	    <div class="salesking_payouts_howto_notice notice notice-info is-dismissible">
    	        <p><?php esc_html_e( 'This panel allows you to manage payouts for each of your agents, and keep track of payments sent, payments due, bonuses, etc.', 'salesking' ); ?></p>
    	    </div>
	    	<?php
	    }
	}

	function salesking_earnings_howto() {
		global $current_screen;
	    if( 'salesking_page_salesking_earnings' != $current_screen->id ){
		    return;
	    }

		// if notice has not already been dismissed once by the current user
		if (intval(get_user_meta(get_current_user_id(),'salesking_dismiss_earnings_howto_notice', true)) !== 1){
    		?>
    		<br />
    	    <div class="salesking_earnings_howto_notice notice notice-info is-dismissible">
    	        <p><?php esc_html_e( 'This panel allows you to view, and keep track of your agents\' earnings', 'salesking' ); ?></p>
    	    </div>
	    	<?php
	    }
	}


}
