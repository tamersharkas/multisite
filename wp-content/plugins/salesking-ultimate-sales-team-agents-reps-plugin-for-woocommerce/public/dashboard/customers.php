<?php
    if (intval(apply_filters('salesking_enable_my_customers_page', 1)) === 1){
    ?>
    <div class="nk-content salesking_customers_page">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title"><?php esc_html_e('Customers','salesking');?></h3>
                                <div class="nk-block-des text-soft">
                                    <p><?php esc_html_e('Here you can view and manage your customers. Through the "Shop as Customer" button, you can place orders on behalf of customers.', 'salesking');?><br /><?php esc_html_e('By choosing the "pending payment" option at checkout, customers will be sent a payment link by email.', 'salesking');?></p>
                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="more-options">
                                        <ul class="nk-block-tools g-3">
                                            <li>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-right">
                                                        <em class="icon ni ni-search"></em>
                                                    </div>
                                                    <input type="text" class="form-control" id="salesking_customers_search" placeholder="<?php esc_html_e('Search customers...','salesking');?>">
                                                </div>
                                            </li>
                                            
                                            <?php
                                                if (apply_filters('salesking_default_add_customer', true)){
                                                    if (apply_filters('b2bking_show_customers_page_add_button', true)){
                                                        ?>
                                                        
                                                        <?php

                                                        require_once ( SALESKING_DIR . 'includes/class-salesking-helper.php' );
                                                        $helper = new Salesking_Helper();
                                                        if($helper->agent_can_add_more_customers($user_id)){
                                                            ?>
                                                            <li class="nk-block-tools-opt">
                                                                <a href="#" class="btn btn-icon btn-primary d-md-none" data-toggle="modal" data-target="#modal_add_customer"><em class="icon ni ni-plus"></em></a>
                                                                <button class="btn btn-primary d-none d-md-inline-flex" data-toggle="modal" data-target="#modal_add_customer"><em class="icon ni ni-plus"></em><span><?php esc_html_e('Add','salesking');?></span></button>
                                                            </li>
                                                            <?php
                                                        } else {
                                                            // show some error message that they reached the max nr of products
                                                            ?>
                                                            <button class="btn btn-primary d-none d-md-inline-flex" disabled="disabled"><em class="icon ni ni-plus"></em><span><?php esc_html_e('Add (Max Limit Reached)','salesking');?></span></button>

                                                            <?php
                                                        }
                                                    }
                                                } else {
                                                    do_action('salesking_alternative_add_customer');
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->
                    <table id="salesking_dashboard_customers_table" class="nk-tb-list is-separate mb-3">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Customer','salesking'); ?></span></th>
                                <?php
                                    if (apply_filters('b2bking_show_customers_page_company_column', true)){
                                        ?>
                                        <th class="nk-tb-col tb-col-md"><span class="sub-text"><?php esc_html_e('Company','salesking'); ?></span></th>
                                        <?php
                                    }
                                ?>
                                <?php
                                do_action('salesking_customers_custom_columns_header');

                                    if (apply_filters('b2bking_show_customers_page_total_spent_column', true)){
                                        ?>
                                        <th class="nk-tb-col tb-col-md"><span class="sub-text"><?php esc_html_e('Total Spend','salesking'); ?></span></th>

                                        <?php
                                    }
                                ?>
                                <?php
                                    if (apply_filters('b2bking_show_customers_page_order_count_column', true)){
                                        ?>
                                        <th class="nk-tb-col tb-col-lg"><span class="sub-text"><?php esc_html_e('Number of Orders','salesking'); ?></span></th>

                                        <?php
                                    }
                                ?>

                                <?php
                                    if (apply_filters('b2bking_show_customers_page_email_column', true)){
                                        ?>
                                        <th class="nk-tb-col tb-col-lg"><span class="sub-text"><?php esc_html_e('Email','salesking'); ?></span></th>
                                        <?php
                                    }
                                ?>
                                <?php
                                    if (apply_filters('b2bking_show_customers_page_phone_column', true)){
                                        ?>
                                        <th class="nk-tb-col tb-col-lg"><span class="sub-text"><?php esc_html_e('Phone','salesking'); ?></span></th>
                                        <?php
                                    }
                                ?> 
                                <?php
                                    if (apply_filters('salesking_show_customers_page_actions_column', true)){
                                        ?>                          
                                        <th class="nk-tb-col"><?php esc_html_e('Actions','salesking'); ?></span></th>
                                        <?php
                                    }
                                ?> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php


                            if (!apply_filters('salesking_load_customers_table_ajax', false)){


                                // get all customers of the user

                                // if all agents can shop for all customers
                                if(intval(get_option( 'salesking_all_agents_shop_all_customers_setting', 0 ))=== 1){
                                    // first get all customers that have this assigned agent individually
                                    $user_ids_assigned = get_users(array(
                                        'role' => 'customer',
                                        'fields' => 'ids',
                                    ));
                                    $customers = $user_ids_assigned;

                                } else {
                                    // first get all customers that have this assigned agent individually
                                    $user_ids_assigned = get_users(array(
                                                'meta_key'     => 'salesking_assigned_agent',
                                                'meta_value'   => $user_id,
                                                'meta_compare' => '=',
                                                'fields' => 'ids',
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
                                    <tr class="nk-tb-item">
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
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" id="modal_add_customer">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php esc_html_e('Customer Info','salesking'); ?></h5>
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <form action="#" class="form-validate is-alter" id="salesking_add_customer_form">
                            <div class="form-group">
                                <label class="form-label" for="first-name"><?php esc_html_e('First name','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="first-name" name="first-name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="last-name"><?php esc_html_e('Last name','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="last-name" name="last-name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="company-name"><?php esc_html_e('Company name','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="company-name" name="company-name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="billing_country"><?php esc_html_e('Country','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <?php
                                    woocommerce_form_field( 'billing_country', array( 'type' => 'country', 'input_class' => array('form-control')));
                                    ?>
                                </div>
                            </div>
                            <?php do_action('salesking_add_customer_after_country'); ?>
                            <div class="form-group">
                                <label class="form-label" for="street-address"><?php esc_html_e('Street address','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="street-address" name="street-address">
                                </div>
                            </div>
                            <?php do_action('salesking_add_customer_after_street'); ?>
                            <div class="form-group">
                                <label class="form-label" for="town-city"><?php esc_html_e('Town / City','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="town-city" name="town-city">
                                </div>
                            </div>
                            <?php do_action('salesking_add_customer_after_city'); ?>
                            <div class="form-group">
                                <label class="form-label" for="postcode-zip"><?php esc_html_e('Postcode / ZIP','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="postcode-zip" name="postcode-zip">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="phone-no"><?php esc_html_e('Phone No','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="phone-no" name="phone-no">
                                </div>
                            </div>
                            <?php
                            // b2bking custom fields (optional)
                            if (defined('B2BKING_DIR') && apply_filters('salesking_show_b2bking_fields_customer', true)){
                                // add editable fields
                                $custom_fields_editable = get_posts([
                                                'post_type' => 'b2bking_custom_field',
                                                'post_status' => 'publish',
                                                'numberposts' => -1,
                                                'meta_key' => 'b2bking_custom_field_sort_number',
                                                'orderby' => 'meta_value_num',
                                                'order' => 'ASC',
                                                'fields' => 'ids',
                                                'meta_query'=> array(
                                                    'relation' => 'AND',
                                                    array(
                                                        'key' => 'b2bking_custom_field_status',
                                                        'value' => 1
                                                    ),
                                                )
                                            ]);
                                $custom_fields = '';
                                $custom_fields_array_exploded = array();

                                
                                foreach ($custom_fields_editable as $editable_field){
                                    if (!in_array($editable_field, $custom_fields_array_exploded)){

                                        // don't show files
                                        $afield_type = get_post_meta($editable_field, 'b2bking_custom_field_field_type', true);
                                        $afield_billing_connection = get_post_meta($editable_field, 'b2bking_custom_field_billing_connection', true);
                                        if ($afield_type === 'file'){
                                            continue;
                                        }
                                        if ($afield_type === 'checkbox'){
                                            continue;
                                        }
                                        if ($afield_billing_connection !== 'billing_vat' && $afield_billing_connection !== 'none' && $afield_billing_connection !== 'custom_mapping'){
                                            continue;
                                        }

                                        array_push($custom_fields_array_exploded,$editable_field);
                                        $custom_fields.=$editable_field.',';
                                    }
                                }
                                $custom_fields = substr($custom_fields, 0, -1);
                                foreach ($custom_fields_array_exploded as $field_id){
                                    $field_type = get_post_meta($field_id, 'b2bking_custom_field_field_type', true);
                                    $label = get_post_meta($field_id, 'b2bking_custom_field_field_label', true);

                                    if ($field_type === 'select'){
                                        $select_options = get_post_meta(apply_filters( 'wpml_object_id', $field_id, 'post', true ), 'b2bking_custom_field_user_choices', true);
                                        $select_options = explode(',', $select_options);

                                        ?>
                                        <div class="form-group">
                                            <label class="form-label" for="salesking_field_<?php echo esc_attr($field_id);?>"><?php echo esc_html($label);?></label>
                                            <div class="form-control-wrap">
                                        <?php
                                        echo '<select id="salesking_field_'.esc_attr($field_id).'" class="form-control" name="salesking_field_'.esc_attr($field_id).'">';
                                            foreach ($select_options as $option){
                                                // check if option is simple or value is specified via option:value
                                                $optionvalue = explode(':', $option);
                                                if (count($optionvalue) === 2 ){
                                                    // value is specified
                                                    echo '<option value="'.esc_attr(trim($optionvalue[0])).'" '.selected(trim($optionvalue[0]), $previous_value, false).'>'.esc_html(trim($optionvalue[1])).'</option>';
                                                } else {
                                                    // simple
                                                    echo '<option value="'.esc_attr(trim($option)).'" '.selected($option, $previous_value, false).'>'.esc_html(trim($option)).'</option>';
                                                }
                                            }
                                        echo '</select></div></div>';
                                    } else {
                                        ?>
                                        <div class="form-group">
                                            <label class="form-label" for="salesking_field_<?php echo esc_attr($field_id);?>"><?php echo esc_html($label);?></label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="salesking_field_<?php echo esc_attr($field_id);?>" name="salesking_field_<?php echo esc_attr($field_id);?>">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    

                                }

                                // show group optionally
                                if (apply_filters('salesking_show_b2bking_groups_new_customer',true)){
                                    $groups = get_posts( array( 'post_type' => 'b2bking_group','post_status'=>'publish','numberposts' => -1) );
                                    
                                    ?>
                                    <div class="form-group">
                                        <label class="form-label" for="salesking_b2bking_group"><?php esc_html_e('B2B Group','salesking');?></label>
                                        <div class="form-control-wrap">
                                            <select id="salesking_b2bking_group" name="salesking_b2bking_group" class="form-control">
                                                <option value="b2c"><?php esc_html_e('B2C Users','salesking');?></option>
                                                <?php
                                                foreach ($groups as $group){

                                                    echo '<option value="' . $group->ID . '">' . get_the_title($group) . '</option>';
                                                }
                                                ?>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }
                                echo '<input type="hidden" id="salesking_b2bking_custom_fields" value="'.esc_attr($custom_fields).'">';

                            }

                            // B2b & Wholesalesuite custom fields
                            // b2bking custom fields (optional)
                            if (defined('B2BWHS_DIR') && apply_filters('salesking_show_b2bwhs_fields_customer', true)){
                                // add editable fields
                                $custom_fields_editable = get_posts([
                                                'post_type' => 'b2bwhs_custom_field',
                                                'post_status' => 'publish',
                                                'numberposts' => -1,
                                                'meta_key' => 'b2bwhs_custom_field_sort_number',
                                                'orderby' => 'meta_value_num',
                                                'order' => 'ASC',
                                                'fields' => 'ids',
                                                'meta_query'=> array(
                                                    'relation' => 'AND',
                                                    array(
                                                        'key' => 'b2bwhs_custom_field_status',
                                                        'value' => 1
                                                    ),
                                                )
                                            ]);
                                $custom_fields = '';
                                $custom_fields_array_exploded = array();

                                
                                foreach ($custom_fields_editable as $editable_field){
                                    if (!in_array($editable_field, $custom_fields_array_exploded)){

                                        // don't show files
                                        $afield_type = get_post_meta($editable_field, 'b2bwhs_custom_field_field_type', true);
                                        $afield_billing_connection = get_post_meta($editable_field, 'b2bwhs_custom_field_billing_connection', true);
                                        if ($afield_type === 'file'){
                                            continue;
                                        }
                                        if ($afield_type === 'checkbox'){
                                            continue;
                                        }
                                        if ($afield_billing_connection !== 'billing_vat' && $afield_billing_connection !== 'none' && $afield_billing_connection !== 'custom_mapping'){
                                            continue;
                                        }

                                        array_push($custom_fields_array_exploded,$editable_field);
                                        $custom_fields.=$editable_field.',';
                                    }
                                }
                                $custom_fields = substr($custom_fields, 0, -1);
                                foreach ($custom_fields_array_exploded as $field_id){
                                    $label = get_post_meta($field_id, 'b2bwhs_custom_field_field_label', true);
                                    ?>
                                    <div class="form-group">
                                        <label class="form-label" for="salesking_field_<?php echo esc_attr($field_id);?>"><?php echo esc_html($label);?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="salesking_field_<?php echo esc_attr($field_id);?>" name="salesking_field_<?php echo esc_attr($field_id);?>">
                                        </div>
                                    </div>

                                    <?php
                                }

                                // show group optionally
                                if (apply_filters('salesking_show_b2bwhs_groups_new_customer',true)){
                                    $groups = get_posts( array( 'post_type' => 'b2bwhs_group','post_status'=>'publish','numberposts' => -1) );
                                    
                                    ?>
                                    <div class="form-group">
                                        <label class="form-label" for="salesking_b2bwhs_group"><?php esc_html_e('B2B Group','salesking');?></label>
                                        <div class="form-control-wrap">
                                            <select id="salesking_b2bwhs_group" name="salesking_b2bwhs_group" class="form-control">
                                                <option value="b2c"><?php esc_html_e('B2C Users','salesking');?></option>
                                                <?php
                                                foreach ($groups as $group){

                                                    echo '<option value="' . $group->ID . '">' . get_the_title($group) . '</option>';
                                                }
                                                ?>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }
                                echo '<input type="hidden" id="salesking_b2bwhs_custom_fields" value="'.esc_attr($custom_fields).'">';

                            }

                            do_action('salesking_add_customer_custom_fields');
                            $custom_fields_code = apply_filters('salesking_custom_fields_code_list_comma',''); // comma separated list

                            echo '<input type="hidden" id="salesking_custom_fields_code" value="'.esc_attr($custom_fields_code).'">';

                            ?>
                            <div class="form-group">
                                <label class="form-label" for="username"><?php esc_html_e('Username','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email-address"><?php esc_html_e('Email address','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="email" class="form-control" id="email-address" name="email-address" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password"><?php esc_html_e('Password','salesking'); ?></label>
                                <div class="form-control-wrap">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="button" id="salesking_add_customer" class="btn btn-lg btn-primary"><?php esc_html_e('Add Customer','salesking'); ?></button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>