<div class="nk-content salesking_dashboard_page">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title page-title"><?php esc_html_e('Dashboard', 'salesking');?></h4>
                            <div class="nk-block-des text-soft fs-15px">
                                <?php
                                $name = $currentuser->first_name.' '.$currentuser->last_name;
                                ?>
                                <p><?php esc_html_e('Welcome to your dashboard', 'salesking');?><?php 
                                if (!empty($name)){
                                    echo ', '.esc_html($name).'!';
                                } else{
                                    echo '!';
                                }
                                esc_html_e(' Here\'s everything at a glance...', 'salesking');
                                ?></p>
                            </div>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <?php
                        if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
                            ?>
                            <div class="<?php echo esc_attr(apply_filters('salesking_dashboard_card_classes', 'col-xxl-4 col-md-6'));?>">
                                <div class="card is-dark h-100">
                                    <div class="nk-ecwg nk-ecwg1">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title"><?php esc_html_e('Balance available', 'salesking');?></h6>
                                                </div>
                                                <div class="card-tools">
                                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true)))).'earnings';?>" class="link"><?php esc_html_e('View earnings', 'salesking');?></a>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="amount"><?php
                                                        $outstanding_balance = get_user_meta($user_id,'salesking_outstanding_earnings', true);
                                                        if (empty($outstanding_balance)){
                                                            $outstanding_balance = 0;
                                                        }
                                                        echo wc_price($outstanding_balance);
                                                        ?></div>
                                                <div class="info"><strong><?php
                                                        $site_time = time()+(get_option('gmt_offset')*3600);
                                                        $current_day = date_i18n( 'd', $site_time );

                                                        $earnings_number = 0;
                                                        $earnings = get_posts( array( 
                                                            'post_type' => 'salesking_earning',
                                                            'numberposts' => -1,
                                                            'post_status'    => 'any',
                                                            'date_query' => array(
                                                                    'after' => date('Y-m-d', strtotime('-30 days')) 
                                                                ),
                                                            'fields'    => 'ids',
                                                            'meta_key'   => 'agent_id',
                                                            'meta_value' => get_current_user_id(),
                                                        ));

                                                        foreach ($earnings as $earning_id){
                                                            $order_id = get_post_meta($earning_id,'order_id', true);
                                                            $orderobj = wc_get_order($order_id);
                                                            if ($orderobj !== false){
                                                                $status = $orderobj->get_status();
                                                                $earnings_total = get_post_meta($earning_id,'salesking_commission_total', true);
                                                                // check if approved
                                                                if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
                                                                    $earnings_number+=$earnings_total;
                                                                }
                                                            }
                                                        }

                                                        // also get all earnings where this agent is parent
                                                        $earnings = get_posts( array( 
                                                            'post_type' => 'salesking_earning',
                                                            'numberposts' => -1,
                                                            'post_status'    => 'any',
                                                            'date_query' => array(
                                                                    'after' => date('Y-m-d', strtotime('-30 days')) 
                                                                ),
                                                            'fields'    => 'ids',
                                                            'meta_key'   => 'parent_agent_id_'.get_current_user_id(),
                                                            'meta_value' => get_current_user_id(),
                                                        ));

                                                        foreach ($earnings as $earning_id){
                                                            $order_id = get_post_meta($earning_id,'order_id', true);
                                                            $orderobj = wc_get_order($order_id);
                                                            if ($orderobj !== false){
                                                                $status = $orderobj->get_status();
                                                                $earnings_total = get_post_meta($earning_id,'parent_agent_id_'.get_current_user_id().'_earnings', true);
                                                                // check if approved
                                                                if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
                                                                    $earnings_number+=$earnings_total;
                                                                }
                                                            }
                                                        }

                                                        echo strip_tags(wc_price($earnings_number));

                                                        ?></strong> <?php esc_html_e('earnings in the last 30 days','salesking');?></div>
                                            </div>
                                            <div class="data">
                                                <h6 class="sub-title"><?php esc_html_e('Earnings this month so far', 'salesking');?></h6>
                                                <div class="data-group">
                                                    <div class="amount"><?php
                                                        $current_day = date_i18n( 'd', $site_time );

                                                        $earnings_number = 0;
                                                        $earnings = get_posts( array( 
                                                            'post_type' => 'salesking_earning',
                                                            'numberposts' => -1,
                                                            'post_status'    => 'any',
                                                            'date_query' => array(
                                                                    'after' => date('Y-m-d', strtotime('-'.$current_day.' days')) 
                                                                ),
                                                            'fields'    => 'ids',
                                                            'meta_key'   => 'agent_id',
                                                            'meta_value' => get_current_user_id(),
                                                        ));

                                                        foreach ($earnings as $earning_id){
                                                            $order_id = get_post_meta($earning_id,'order_id', true);
                                                            $orderobj = wc_get_order($order_id);
                                                            if ($orderobj !== false){
                                                                $status = $orderobj->get_status();
                                                                $earnings_total = get_post_meta($earning_id,'salesking_commission_total', true);
                                                                // check if approved
                                                                if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
                                                                    $earnings_number+=$earnings_total;
                                                                }
                                                            }
                                                        }

                                                        // also get all earnings where this agent is parent
                                                        $earnings = get_posts( array( 
                                                            'post_type' => 'salesking_earning',
                                                            'numberposts' => -1,
                                                            'post_status'    => 'any',
                                                            'date_query' => array(
                                                                    'after' => date('Y-m-d', strtotime('-'.$current_day.' days')) 
                                                                ),
                                                            'fields'    => 'ids',
                                                            'meta_key'   => 'parent_agent_id_'.get_current_user_id(),
                                                            'meta_value' => get_current_user_id(),
                                                        ));

                                                        foreach ($earnings as $earning_id){
                                                            $order_id = get_post_meta($earning_id,'order_id', true);
                                                            $orderobj = wc_get_order($order_id);
                                                            if ($orderobj !== false){
                                                                $status = $orderobj->get_status();
                                                                $earnings_total = get_post_meta($earning_id,'parent_agent_id_'.get_current_user_id().'_earnings', true);
                                                                // check if approved
                                                                if (in_array($status,apply_filters('salesking_earning_completed_statuses', array('completed')))){
                                                                    $earnings_number+=$earnings_total;
                                                                }
                                                            }
                                                        }

                                                        echo wc_price($earnings_number);

                                                        ?></div>
                                                  
                                                </div>
                                            </div>
                                        </div><!-- .card-inner -->
                                        <div class="nk-ecwg1-ck">
                                            <canvas class="ecommerce-line-chart-s1" id="totalSales"></canvas>
                                        </div>
                                    </div><!-- .nk-ecwg -->
                                </div><!-- .card -->
                            </div><!-- .col -->
                            <?php
                        }?>
                        <div class="<?php echo esc_attr(apply_filters('salesking_dashboard_card_classes', 'col-xxl-4 col-md-6'));?>">
                            <div class="card card-full overflow-hidden">
                                <div class="nk-ecwg nk-ecwg7 h-100">
                                    <div class="card-inner flex-grow-1">
                                        <div class="card-title-group mb-4">
                                            <div class="card-title">
                                                <h6 class="title"><?php esc_html_e('Order Statistics (last 30 days)', 'salesking');?></h6>
                                            </div>
                                        </div>
                                        <div class="nk-ecwg7-ck">
                                            <canvas class="ecommerce-doughnut-s1" id="orderStatistics"></canvas>
                                        </div>
                                        <ul class="nk-ecwg7-legends">
                                            <li>
                                                <div class="title">
                                                    <span class="dot dot-lg sq" data-bg="#816bff"></span>
                                                    <span><?php esc_html_e('Completed', 'salesking');?></span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title">
                                                    <span class="dot dot-lg sq" data-bg="#13c9f2"></span>
                                                    <span><?php esc_html_e('Pending', 'salesking');?></span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="title">
                                                    <span class="dot dot-lg sq" data-bg="#ff82b7"></span>
                                                    <span><?php esc_html_e('Cancelled', 'salesking');?></span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div><!-- .card-inner -->
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="<?php echo esc_attr(apply_filters('salesking_dashboard_card_classes', 'col-xxl-4 col-md-6'));?>">
                            <div class="card h-100">
                                <div class="card-inner">
                                    <div class="card-title-group mb-2">
                                        <div class="card-title">
                                            <h6 class="title"><?php esc_html_e('Store Statistics (last 30 days)', 'salesking');?></h6>
                                        </div>
                                    </div>
                                    <ul class="nk-store-statistics">
                                        <li class="item">
                                            <div class="info">
                                                <div class="title"><?php esc_html_e('Orders', 'salesking');?></div>
                                                <div class="count"><?php
                                                // if earnings enabled,
                                                $number = 0;

                                                if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
                                                    $earnings = get_posts( array( 
                                                        'post_type' => 'salesking_earning',
                                                        'numberposts' => -1,
                                                        'post_status'    => 'any',
                                                        'date_query' => array(
                                                                'after' => date('Y-m-d', strtotime('-30 days')) 
                                                            ),
                                                        'fields'    => 'ids',
                                                        'meta_key'   => 'agent_id',
                                                        'meta_value' => get_current_user_id(),
                                                    ));

                                                    $number+=count($earnings);
                                                    

                                                    // also get all earnings where this agent is parent
                                                    $earnings = get_posts( array( 
                                                        'post_type' => 'salesking_earning',
                                                        'numberposts' => -1,
                                                        'post_status'    => 'any',
                                                        'date_query' => array(
                                                                'after' => date('Y-m-d', strtotime('-30 days')) 
                                                            ),
                                                        'fields'    => 'ids',
                                                        'meta_key'   => 'parent_agent_id_'.get_current_user_id(),
                                                        'meta_value' => get_current_user_id(),
                                                    ));

                                                    $number+=count($earnings);

                                                    
                                                } else { // else show orders
                                                    $agent_orders = get_posts( array( 
                                                        'post_type' => 'shop_order',
                                                        'numberposts' => -1,
                                                        'post_status'    => 'any',
                                                        'meta_key'   => 'salesking_assigned_agent',
                                                        'meta_value' => get_current_user_id(),
                                                    ));

                                                    $number+=count($agent_orders);

                                                }
                                                echo esc_html($number);


                                                ?></div>
                                            </div>
                                            <em class="icon bg-primary-dim ni ni-bag"></em>
                                        </li>
                                        <?php
                                        if (apply_filters('salesking_dashboard_show_customer_column', true)){
                                            ?>
                                            <li class="item">
                                                <div class="info">
                                                    <div class="title"><?php esc_html_e('Customers', 'salesking');?></div>
                                                    <div class="count"><?php
                                                    // get all customers of the user

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
                                                            if (!empty($assigned_agent) && $assigned_agent !== $user_id ){
                                                                unset($user_ids_in_groups_with_agent[$array_key]);
                                                            }
                                                        }
                                                        $customers = array_merge($user_ids_assigned, $user_ids_in_groups_with_agent);
                                                    } else {
                                                        $customers = $user_ids_assigned;
                                                    }
                                                    echo count($customers);
                                                    ?></div>
                                                </div>
                                                <em class="icon bg-info-dim ni ni-users"></em>
                                            </li>
                                            <?php
                                        }
                                        if (intval(get_option( 'salesking_enable_announcements_setting', 1 )) === 1){
                                            ?>
                                            <li class="item">
                                                <div class="info">
                                                    <div class="title"><?php esc_html_e('Announcements','salesking');?></div>
                                                    <div class="count"><?php

                                                        $site_time = time()+(get_option('gmt_offset')*3600);
                                                        $current_day = date_i18n( 'd', $site_time );
                                                        $announcements = get_posts(array( 'post_type' => 'salesking_announce',
                                                                  'post_status'=>'publish',
                                                                  'numberposts' => -1,
                                                                  'date_query' => array(
                                                                          'after' => date('Y-m-d', strtotime('-30 days')) 
                                                                      ),
                                                                  'meta_query'=> array(
                                                                        'relation' => 'OR',
                                                                        array(
                                                                            'key' => 'salesking_group_'.$agent_group,
                                                                            'value' => '1',
                                                                        ),
                                                                        array(
                                                                            'key' => 'salesking_user_'.$user, 
                                                                            'value' => '1',
                                                                        ),
                                                                    )));
                                                        echo count($announcements);
                                                    ?></div>
                                                </div>
                                                <em class="icon bg-pink-dim ni ni-box"></em>
                                            </li>
                                            <?php 
                                        } 
                                        if (intval(get_option( 'salesking_enable_messages_setting', 1 )) === 1){
                                            ?>
                                            <li class="item">
                                                <div class="info">
                                                    <div class="title"><?php esc_html_e('Messages','salesking');?></div>
                                                    <div class="count"><?php
                                                        $site_time = time()+(get_option('gmt_offset')*3600);
                                                        $current_day = date_i18n( 'd', $site_time );
                                                        $messages = get_posts(
                                                            array( 
                                                                'post_type' => 'salesking_message', // only conversations
                                                                'post_status' => 'publish',
                                                                'numberposts' => -1,
                                                                'fields' => 'ids',
                                                                'date_query' => array(
                                                                        'after' => date('Y-m-d', strtotime('-30 days')) 
                                                                    ),
                                                                'meta_query'=> array(   // only the specific user's conversations
                                                                    'relation' => 'OR',
                                                                    array(
                                                                        'key' => 'salesking_message_user',
                                                                        'value' => $currentuserlogin, 
                                                                    ),
                                                                    array(
                                                                        'key' => 'salesking_message_message_1_author',
                                                                        'value' => $currentuserlogin, 
                                                                    )

                                                                )
                                                            )
                                                        );

                                                        echo count($messages);
                                                    ?></div>
                                                </div>
                                                <em class="icon bg-purple-dim ni ni-server"></em>
                                            </li>
                                            <?php 
                                        } 
                                        ?>
                                    </ul>
                                </div><!-- .card-inner -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        <?php
                        if (intval(get_option( 'salesking_enable_earnings_setting', 1 )) === 1){
                            ?>
                            <div class="col-xxl-12">
                                <div class="card card-full">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><?php esc_html_e('Recent Earnings', 'salesking');?></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-list mt-n2">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span><?php esc_html_e('Order No.', 'salesking');?></span></div>
                                            <?php
                                                if (apply_filters('salesking_dashboard_show_customer_column', true)){
                                                    ?>
                                                    <div class="nk-tb-col tb-col-sm"><span><?php esc_html_e('Customer', 'salesking');?></span></div>
                                                    <?php
                                                }
                                            ?>
                                            <div class="nk-tb-col tb-col-md"><span><?php esc_html_e('Date', 'salesking');?></span></div>
                                            <?php
                                                if (apply_filters('salesking_dashboard_show_amount_column', true)){
                                                    ?>
                                                    <div class="nk-tb-col"><span><?php esc_html_e('Amount', 'salesking');?></span></div>
                                                    <?php
                                                }
                                            ?>
                                            <div class="nk-tb-col"><span class="d-none d-sm-inline"><?php esc_html_e('Status', 'salesking');?></span></div>
                                        </div>

                                            <?php
                                            $earnings = get_posts( array( 
                                                'post_type' => 'salesking_earning',
                                                'numberposts' => 5,
                                                'post_status'    => 'any',
                                                'fields'    => 'ids',
                                                'meta_key'   => 'agent_id',
                                                'meta_value' => get_current_user_id(),
                                            ));

                                            foreach ($earnings as $earning_id){
                                                $order_id = get_post_meta($earning_id,'order_id', true);
                                                $orderobj = wc_get_order($order_id);
                                                if ($orderobj !== false){
                                                    $earnings_total = get_post_meta($earning_id,'salesking_commission_total', true);
                                                    $date = explode('T',$orderobj->get_date_created())[0];
                                                    if (!empty($earnings_total) && floatval($earnings_total) !== 0){
                                                        ?>
                                                        <div class="nk-tb-item">
                                                            <div class="nk-tb-col">
                                                                <span class="tb-lead">#<?php echo esc_html($order_id);?></span>
                                                            </div>
                                                            <?php
                                                            if (apply_filters('salesking_dashboard_show_customer_column', true)){
                                                                ?>
                                                                <div class="nk-tb-col tb-col-sm">
                                                                    <div class="user-card">
                                                                        <div class="user-avatar sm bg-purple-dim">
                                                                            <span><?php
                                                                     $customer_id = $orderobj -> get_customer_id();
                                                                     $data = get_userdata($customer_id);
                                                                     if (is_a($data,'WP_User')){
                                                                        $name = $data->first_name.' '.$data->last_name;
                                                                     } else {
                                                                        $name = $orderobj->get_billing_first_name().' '.$orderobj->get_billing_last_name();
                                                                     }
                                                                     echo mb_strtoupper(mb_substr($name,0, 2));
                                                                     ?></span>
                                                                        </div>
                                                                        <div class="user-name">
                                                                            <span class="tb-lead"><?php echo esc_html($name);?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                            <div class="nk-tb-col tb-col-md">
                                                                <span class="tb-sub"><?php 

                                                                echo apply_filters('salesking_dashboard_date_display',date_i18n( get_option('date_format'), strtotime($date)+(get_option('gmt_offset')*3600) ), $orderobj->get_date_created()); 

                                                                ?></span>
                                                            </div>
                                                            <?php
                                                                if (apply_filters('salesking_dashboard_show_amount_column', true)){
                                                                    ?>
                                                                    <div class="nk-tb-col">
                                                                        <span class="tb-sub tb-amount"><?php echo wc_price($orderobj->get_total());?></span>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            <div class="nk-tb-col">
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
                                                                <span class="badge badge-dot badge-dot-xs <?php echo esc_attr($badge);?>"><?php
                                                                    echo esc_html($statustext);
                                                                ?></span>

                                                            </div>
                                                        </div>
                                                    <?php
                                                    }
                                                } else {
                                                    // order has been deleted, let's delete earning as well
                                                    wp_delete_post($earning_id);
                                                }
                                            }
                                            ?>
                                        
                                        
                                    </div>
                                </div><!-- .card -->
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="col-xxl-12">
                                <div class="card card-full">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title"><?php esc_html_e('Recent Orders', 'salesking');?></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nk-tb-list mt-n2">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span><?php esc_html_e('Order No.', 'salesking');?></span></div>
                                            <?php
                                                if (apply_filters('salesking_dashboard_show_customer_column', true)){
                                                ?>
                                                <div class="nk-tb-col tb-col-sm"><span><?php esc_html_e('Customer', 'salesking');?></span></div>
                                                 <?php
                                                }
                                            ?>
                                            <div class="nk-tb-col tb-col-md"><span><?php esc_html_e('Date', 'salesking');?></span></div>
                                            <?php
                                                if (apply_filters('salesking_dashboard_show_amount_column', true)){
                                                    ?>
                                                     <div class="nk-tb-col"><span><?php esc_html_e('Amount', 'salesking');?></span></div>
                                                     <?php
                                                 }
                                                 ?>
                                            <div class="nk-tb-col"><span class="d-none d-sm-inline"><?php esc_html_e('Status', 'salesking');?></span></div>
                                        </div>

                                            <?php

                                            $agent_orders = get_posts( array( 
                                                'post_type' => 'shop_order',
                                                'numberposts' => 5,
                                                'fields'    => 'ids',
                                                'post_status'    => 'any',
                                                'meta_key'   => 'salesking_assigned_agent',
                                                'meta_value' => get_current_user_id(),
                                            ));

                                            foreach ($agent_orders as $order_id){
                                                $orderobj = wc_get_order($order_id);
                                                if ($orderobj !== false){
                                                    $date = explode('T',$orderobj->get_date_created())[0];
                                                    ?>
                                                        <div class="nk-tb-item">
                                                            <div class="nk-tb-col">
                                                                <span class="tb-lead">#<?php echo esc_html($orderobj->get_order_number());?></span>
                                                            </div>
                                                            <?php
                                                                if (apply_filters('salesking_dashboard_show_customer_column', true)){
                                                                ?>
                                                                <div class="nk-tb-col tb-col-sm">
                                                                    <div class="user-card">
                                                                        <div class="user-avatar sm bg-purple-dim">
                                                                            <span><?php
                                                                     $customer_id = $orderobj -> get_customer_id();
                                                                     $data = get_userdata($customer_id);
                                                                     if (is_a($data,'WP_User')){
                                                                        $name = $data->first_name.' '.$data->last_name;
                                                                     } else {
                                                                        $name = $orderobj->get_billing_first_name().' '.$orderobj->get_billing_last_name();
                                                                     }
                                                                     echo mb_strtoupper(mb_substr($name,0, 2));
                                                                     ?></span>
                                                                        </div>
                                                                        <div class="user-name">
                                                                            <span class="tb-lead"><?php echo esc_html($name);?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                            <div class="nk-tb-col tb-col-md">
                                                                <span class="tb-sub"><?php 

                                                                echo apply_filters('salesking_dashboard_date_display',date_i18n( get_option('date_format'), strtotime($date)+(get_option('gmt_offset')*3600) ), $orderobj->get_date_created()); 

                                                                ?></span>
                                                            </div>
                                                            <?php
                                                                if (apply_filters('salesking_dashboard_show_amount_column', true)){
                                                                    ?>
                                                                    <div class="nk-tb-col">
                                                                        <span class="tb-sub tb-amount"><?php echo wc_price($orderobj->get_total());?></span>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            <div class="nk-tb-col">
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
                                                                <span class="badge badge-dot badge-dot-xs <?php echo esc_attr($badge);?>"><?php
                                                                    echo esc_html($statustext);
                                                                ?></span>

                                                            </div>
                                                        </div>
                                                    <?php
                                                }

                                            }
                                            ?>
                                        
                                        
                                    </div>
                                </div><!-- .card -->
                            </div>
                            <?php
                        }
                        ?>
                     
                    </div><!-- .row -->
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>
</div>