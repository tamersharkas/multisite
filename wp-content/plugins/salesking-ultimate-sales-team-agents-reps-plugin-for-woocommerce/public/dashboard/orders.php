<?php
if (intval(get_option( 'salesking_agents_can_manage_orders_setting', 1 )) === 1){
    ?>
    <div class="nk-content salesking_orders_page">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title"><?php esc_html_e('Orders','salesking');?></h3>
                                <div class="nk-block-des text-soft">
                                    <p><?php esc_html_e('Here you can view and manage all orders assigned to you.', 'salesking');?></p>
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
                                                    <?php
                                                    $search = get_query_var('search');
                                                    ?>
                                                    <input type="text" class="form-control" id="salesking_orders_search" placeholder="<?php esc_html_e('Search orders...','salesking');?>" <?php if (!empty($search)){ echo 'value="'.$search.'"'; }?>>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->
                    <table id="salesking_dashboard_orders_table" class="nk-tb-list is-separate mb-3">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Order','salesking'); ?></span></th>
                                <th class="nk-tb-col tb-col-md"><span class="sub-text"><?php esc_html_e('Date','salesking'); ?></span></th>
                                <th class="nk-tb-col"><span class="sub-text d-none d-mb-block"><?php esc_html_e('Status','salesking'); ?></span></th>
                                <th class="nk-tb-col tb-col-sm"><span class="sub-text"><?php esc_html_e('Customer','salesking'); ?></span></th>
                                <?php do_action('salesking_my_orders_custom_columns'); ?>
                                <th class="nk-tb-col tb-col-md"><span class="sub-text"><?php esc_html_e('Purchased','salesking'); ?></span></th>
                                <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Order Total','salesking'); ?></span></th>
                                <?php 
                                    if (apply_filters('salesking_show_actions_my_orders_page', true)){
                                        ?>
                                            <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Actions','salesking'); ?></span></th>
                                        <?php
                                    }
                                ?>

                               
                                

                            </tr>
                        </thead>
                        <?php
                        if (!apply_filters('salesking_load_orders_table_ajax', false)){
                            ?>
                            <tfoot>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-md"><?php esc_html_e('order','salesking'); ?></th>
                                    <th class="nk-tb-col tb-col-md"><?php esc_html_e('date','salesking'); ?></th>
                                    <th class="nk-tb-col tb-col-md"><?php esc_html_e('status','salesking'); ?></th>
                                    <th class="nk-tb-col tb-col-md"><?php esc_html_e('customer','salesking'); ?></th>
                                    <?php do_action('salesking_my_orders_custom_columns_footer'); ?>
                                    <th class="nk-tb-col tb-col-md"><?php esc_html_e('purchased','salesking'); ?></th>
                                    <th class="nk-tb-col tb-col-md"><?php esc_html_e('order total','salesking'); ?></th>
                                    <?php 
                                        if (apply_filters('salesking_show_actions_my_orders_page', true)){
                                            ?>
                                                <th class="nk-tb-col tb-col-md"><?php esc_html_e('actions','salesking'); ?></th>
                                            <?php
                                        }
                                    ?>

                                </tr>
                            </tfoot>
                            <?php
                        }
                        ?>
                        <tbody>
                            <?php


                            if (!apply_filters('salesking_load_orders_table_ajax', false)){

                                $agent_orders = get_posts( array( 
                                    'post_type' => 'shop_order',
                                    'numberposts' => -1,
                                    'post_status'    => 'any',
                                    'meta_key'   => 'salesking_assigned_agent',
                                    'meta_value' => get_current_user_id(),
                                ));

                                foreach ($agent_orders as $order){
                                    $orderobj = wc_get_order($order);
                                    if ($orderobj !== false){
                                        ?>
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">

                                                <div>
                                                    <span class="tb-lead">#<?php echo esc_html($orderobj->get_order_number());?></span>
                                                </div>

                                            </td>
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
                                            <td class="nk-tb-col tb-col-sm">
                                                <div>
                                                     <span class="tb-sub"><?php
                                                     $customer_id = $orderobj -> get_customer_id();
                                                     $data = get_userdata($customer_id);
                                                     $name = $orderobj->get_billing_first_name().' '.$orderobj->get_billing_last_name();

                                                     // if guest user, show name by order
                                                     if ($data === false){
                                                        $name = $orderobj -> get_formatted_billing_full_name() . ' '.esc_html__('(guest user)','salesking');
                                                     }
                                                     $name = apply_filters('salesking_customers_page_name_display', $name, $customer_id);

                                                     echo $name;
                                                     ?></span>
                                                </div>
                                            </td>

                                            <?php do_action('salesking_my_orders_custom_columns_content', $orderobj); ?>

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
                                            <td class="nk-tb-col" data-order="<?php echo esc_attr(apply_filters('salesking_orders_order_total', $orderobj->get_total(), $orderobj));?>"> 
                                                <div>
                                                    <span class="tb-lead"><?php echo wc_price(apply_filters('salesking_orders_order_total', $orderobj->get_total(), $orderobj), array('currency' => $orderobj->get_currency()));?></span>
                                                </div>
                                            </td>
                                            <?php 
                                                if (apply_filters('salesking_show_actions_my_orders_page', true)){
                                                    ?>
                                                        <td class="nk-tb-col">
                                                            <div class="salesking_manage_order_container"> 
                                                                <a href="<?php echo esc_attr(get_edit_post_link($order->ID));?>"><button class="btn btn-sm btn-primary salesking_manage_order" value="<?php echo esc_attr($order->ID);?>"><em class="icon ni ni-bag-fill"></em><span><?php esc_html_e('Manage Order','salesking');?></span></button></a>
                                                            </div>
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
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>