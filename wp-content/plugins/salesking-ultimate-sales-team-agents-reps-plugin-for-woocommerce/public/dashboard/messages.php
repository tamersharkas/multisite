<?php
if (intval(get_option( 'salesking_enable_messages_setting', 1 )) === 1){
    ?>
    <div class="nk-content p-0 salesking_messages_page">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-msg">
                    <div class="nk-msg-aside">
                        <div class="nk-msg-nav">
                            <ul class="nk-msg-menu">
                                <?php
                                // if this page is CLOSED messages, or current message is closed
                                $closed = sanitize_text_field(get_query_var('closed'));
                                $is_closed = 'no';
                                // get currently selected message
                                $aid = sanitize_text_field(get_query_var('id'));

                                if (!empty($aid)){
                                    $anr_messages = get_post_meta ($aid, 'salesking_message_messages_number', true);
                                    $alast_message = get_post_meta ($aid, 'salesking_message_message_'.$anr_messages, true);

                                    // check if message is closed
                                    $alast_closed_time = get_user_meta($user_id,'salesking_message_last_closed_'.$aid, true);
                                    if (!empty($alast_closed_time)){
                                        $alast_message_time = get_post_meta ($aid, 'salesking_message_message_'.$anr_messages.'_time', true);
                                        if (floatval($alast_closed_time) > floatval($alast_message_time)){
                                             $is_closed = 'yes';
                                        }
                                    }
                                }
                                ?>

                                <li class="nk-msg-menu-item <?php if ($closed !== 'yes' && $is_closed !== 'yes'){ echo 'active'; }?>"><a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true)))).'messages';?>"><?php esc_html_e('Active','salesking');?></a></li>
                                <li class="nk-msg-menu-item <?php if ($closed === 'yes' || $is_closed === 'yes'){ echo 'active'; }?>"><a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true)))).'messages?closed=yes';?>"><?php esc_html_e('Closed','salesking');?></a></li>
                                <li class="nk-msg-menu-item ml-auto"><a href="#" class="link link-primary" data-toggle="modal" data-target="#compose-mail"><em class="icon ni ni-plus"></em> <span><?php esc_html_e('Compose','salesking');?></span></a></li>
                            </ul><!-- .nk-msg-menu -->
                            
                        </div><!-- .nk-msg-nav -->

                        <?php
                        $closedmsg = array();
                        // remove messages which are not closed
                        foreach ($messages as $message){
                            $last_closed_time = get_user_meta($user_id,'salesking_message_last_closed_'.$message, true);
                            if (!empty($last_closed_time)){
                                $nr_messages = get_post_meta ($message, 'salesking_message_messages_number', true);
                                $last_message_time = get_post_meta ($message, 'salesking_message_message_'.$nr_messages.'_time', true);
                                if (floatval($last_closed_time) > floatval($last_message_time)){
                                    array_push($closedmsg, $message);
                                }
                            }
                        }
                            
                        $activemessages = array_diff($messages, $closedmsg);
                        // if this page is CLOSED messages, or current msg is closed
                        if ($closed === 'yes' || $is_closed === 'yes'){
                            $messages = $closedmsg;
                        } else {
                            $messages = $activemessages;
                        }

                        // cut messages for pagination
                        $items_per_page = 30;
                        $pagenr = sanitize_text_field(get_query_var('pagenr', 1));
                        if (empty($pagenr)){
                            $pagenr = 1;
                        }
                        $pagesnr = count($messages)/$items_per_page;

                        $messages = array_slice($messages, (($pagenr-1)*$items_per_page), $items_per_page);

                        ?>
                        <div class="nk-msg-list" data-simplebar>
                            <?php
                            
                            foreach ($messages as $message){ // message is a message thread e.g. conversation

                                $title = get_the_title($message);
                                $nr_messages = get_post_meta ($message, 'salesking_message_messages_number', true);

                                $last_message_time = get_post_meta ($message, 'salesking_message_message_'.$nr_messages.'_time', true);
                                // build time string
                                // if today
                                if((time()-$last_message_time) < 86400){
                                    // show time
                                    $timestring = date_i18n( 'h:i A', $last_message_time+(get_option('gmt_offset')*3600) );
                                } else if ((time()-$last_message_time) < 172800){
                                // if yesterday
                                    $timestring = 'Yesterday at '.date_i18n( 'h:i A', $last_message_time+(get_option('gmt_offset')*3600) );
                                } else {
                                // date
                                    $timestring = date_i18n( get_option('date_format'), $last_message_time+(get_option('gmt_offset')*3600) ); 
                                }

                                $last_message = get_post_meta ($message, 'salesking_message_message_'.$nr_messages, true);
                                // first 100 chars
                                $last_message = substr($last_message, 0, 100);

                                // check if message is unread
                                $is_unread = '';
                                $last_message_author = get_post_meta ($message, 'salesking_message_message_'.$nr_messages.'_author', true);
                                if ($last_message_author !== $currentuserlogin){
                                    $last_read_time = get_user_meta($user_id,'salesking_message_last_read_'.$message, true);
                                    if (!empty($last_read_time)){
                                        $last_message_time = get_post_meta ($message, 'salesking_message_message_'.$nr_messages.'_time', true);
                                        if (floatval($last_read_time) < floatval($last_message_time)){
                                            $is_unread = 'is-unread';
                                        }
                                    } else {
                                        $is_unread = 'is-unread';
                                    }
                                } 

                          

                                // get the other party in the chat
                                $author = get_post_meta ($message, 'salesking_message_message_1_author', true);
                                $convuser = get_post_meta ($message, 'salesking_message_user', true);
                                if ($convuser === 'shop'){
                                    $convuser = esc_html__('Shop','salesking');
                                    if (get_post_meta ($message, 'salesking_message_message_2_author', true) !== $author && !empty(get_post_meta ($message, 'salesking_message_message_2_author', true))){
                                        $convuser = get_post_meta ($message, 'salesking_message_message_2_author', true);
                                    }
                                }
                                if ($author === $currentuserlogin){
                                    $author = $convuser;
                                }
                                ?>
                                 <a href="<?php echo trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true))).'messages?id='.esc_attr($message);?>">
                                    <div class="nk-msg-item <?php if(intval($aid)===intval($message)){echo 'current ';}?><?php echo esc_attr($is_unread);?>" data-msg-id="1">
                                        <div class="nk-msg-media user-avatar">
                                            <span><?php echo esc_html(mb_strtoupper(mb_substr($author, 0, 2)))    ;?></span>
                                        </div>
                                       
                                        <div class="nk-msg-info">
                                            <div class="nk-msg-from">
                                                <div class="nk-msg-sender">
                                                    <div class="name"><?php echo esc_html($author);?></div>
                                                </div>
                                                <div class="nk-msg-meta">
                                                    <div class="date"><?php echo esc_html($timestring); ?></div>
                                                </div>
                                            </div>
                                            
                                                <div class="nk-msg-context">
                                                    <div class="nk-msg-text">
                                                        <h6 class="title"><?php echo esc_html($title);?></h6>
                                                        <p><?php echo esc_html($last_message);?></p>
                                                    </div>
                                                    <div class="nk-msg-lables">
                                                        <?php 
                                                        if ($is_unread !== ''){
                                                            ?>
                                                            <div class="unread"><span class="badge badge-primary"><?php esc_html_e('New','salesking');?></span></div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            
                                        </div>
                                    </div><!-- .nk-msg-item -->
                                </a>
                                <?php
                            }
                            ?>

                        </div><!-- .nk-msg-list -->
                        <ul class="pagination justify-content-center ">
                        <?php
                        // pagination
                        if ($pagesnr > 1){
                            $i = 1;
                            $closedcls = '';
                            if ($closed === 'yes' || $is_closed === 'yes'){
                                $closedcls = '?closed=yes';
                                $pagecls = '&pagenr=';
                            } else {
                                $pagecls = '?pagenr=';
                            }
                            while ($pagesnr > 0){
                                ?>
                                <li class="page-item"><a class="page-link" href="<?php echo trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'salesking_agents_page_setting', 'disabled' ), 'post' , true))).'messages/'.$closedcls.$pagecls.esc_attr($i);?>"><?php echo esc_html($i);?></a></li>
                                <?php
                                $i++;
                                $pagesnr--;
                            }
                        }
                        ?>
                        </ul>
                    </div><!-- .nk-msg-aside -->
                    <?php
                    // get currently selected message
                    $message = $id = sanitize_text_field(get_query_var('id'));

                    if (empty($id)){
                        // no message selected, get the first active one, if any.
                        if (isset($activemessages[0])){
                            $message = $id = $activemessages[0];
                        }
                    }


                    $title = get_the_title($message);
                    $nr_messages = get_post_meta ($message, 'salesking_message_messages_number', true);

                    $last_message = get_post_meta ($message, 'salesking_message_message_'.$nr_messages, true);
                    // first 100 chars
                    $last_message = substr($last_message, 0, 100);

                    // check if message is closed
                    $is_closed = 'no';
                    $last_closed_time = get_user_meta($user_id,'salesking_message_last_closed_'.$message, true);
                    if (!empty($last_closed_time)){
                        $last_message_time = get_post_meta ($message, 'salesking_message_message_'.$nr_messages.'_time', true);
                        if (floatval($last_closed_time) > floatval($last_message_time)){
                             $is_closed = 'yes';
                        }
                    }

                    if (!empty($id)){

                        // check that user has permission
                        // get the other party in the chat
                        $author = get_post_meta ($id, 'salesking_message_message_1_author', true);
                        $convuser = get_post_meta ($id, 'salesking_message_user', true);
                        if ($currentuserlogin === $author || $currentuserlogin === $convuser){

                        ?>
                        <div class="nk-msg-body bg-white">
                            <div class="nk-msg-head">
                                <h4 class="title d-none d-lg-block"><?php echo esc_html($title);?></h4>
                                <div class="nk-msg-head-meta">
                                    <div class="d-none d-lg-block">
                                    </div>
                                    <div class="d-lg-none"><a href="#" class="btn btn-icon btn-trigger nk-msg-hide ml-n1"><em class="icon ni ni-arrow-left"></em></a></div>
                                    <ul class="nk-msg-actions">
                                        <li><button id="salesking_mark_conversation_read" value="<?php echo esc_attr($id);?>" class="btn btn-dim btn-sm btn-outline-light"><em class="icon ni ni-eye"></em><span><?php esc_html_e('Mark as Read','salesking');?></span></button></li>
                                        <?php
                                        if ($is_closed === 'yes'){
                                            ?>
                                            <li><button id="salesking_mark_conversation_closed" value="<?php echo esc_attr($id);?>" class="btn btn-dim btn-sm btn-outline-light"><em class="icon ni ni-check"></em><span><?php esc_html_e('Mark as Active','salesking');?></span></button></li>
                                            <?php
                                        } else {
                                            ?>
                                            <li><button id="salesking_mark_conversation_closed" value="<?php echo esc_attr($id);?>" class="btn btn-dim btn-sm btn-outline-light"><em class="icon ni ni-check"></em><span><?php esc_html_e('Mark as Closed','salesking');?></span></button></li>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if ($is_closed === 'yes'){
                                            ?>
                                            <li><span class="badge badge-dim badge-success badge-sm"><em class="icon ni ni-check"></em><span><?php esc_html_e('Closed','salesking');?></span></span></li>
                                            <?php
                                        }
                                        ?>
                                        <li class="d-lg-none"><a href="#" class="btn btn-icon btn-sm btn-white btn-light profile-toggle"><em class="icon ni ni-info-i"></em></a></li>
                                    </ul>
                                </div>
                            </div><!-- .nk-msg-head -->
                            <div class="nk-msg-reply nk-reply" data-simplebar>
                                <div class="nk-msg-head py-4 d-lg-none">
                                    <h4 class="title"><?php echo esc_html($title);?></h4>
                                </div>

                                <?php
                                // display all messages in the thread
                                for ($i = 1; $i <= $nr_messages; $i++) {

                                    $message_time = get_post_meta ($message, 'salesking_message_message_'.$i.'_time', true);
                                    // build time string
                                    // if today
                                    if((time()-$message_time) < 86400){
                                        // show time
                                        $timestring = date_i18n( 'h:i A', $message_time+(get_option('gmt_offset')*3600) );
                                    } else if ((time()-$message_time) < 172800){
                                    // if yesterday
                                        $timestring = 'Yesterday at '.date_i18n( 'h:i A', $message_time+(get_option('gmt_offset')*3600) );
                                    } else {
                                    // date
                                        $timestring = date_i18n( get_option('date_format'), $message_time+(get_option('gmt_offset')*3600) ); 
                                    }

                                    $messagecontent = get_post_meta( $message, 'salesking_message_message_'.$i, true);
                                    $author = get_post_meta( $message, 'salesking_message_message_'.$i.'_author', true);


                                    ?>
                                    <div class="nk-reply-item">
                                        <div class="nk-reply-header">
                                            <div class="user-card">
                                                <div class="user-avatar sm bg-blue">
                                                    <span><?php echo esc_html(mb_strtoupper(mb_substr($author, 0, 2)));?></span>
                                                </div>
                                                <div class="user-name"><?php echo esc_html($author);?></div>
                                            </div>
                                            <div class="date-time"><?php echo esc_html($timestring);?></div>
                                        </div>
                                        <div class="nk-reply-body">
                                            <div class="nk-reply-entry entry">
                                                <?php echo nl2br($messagecontent); ?>
                                            </div>
                                        </div>
                                    </div><!-- .nk-reply-item -->
                                <?php
                                }

                                ?>
                                <div class="nk-reply-form">
                                    <div class="nk-reply-form-header">
                                        <ul class="nav nav-tabs-s2 nav-tabs nav-tabs-sm">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#reply-form"><?php esc_html_e('Reply','salesking');?></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="reply-form">
                                            <div class="nk-reply-form-editor">
                                                <div class="nk-reply-form-field">
                                                    <textarea id="salesking_dashboard_reply_message_content" class="form-control form-control-simple no-resize" placeholder="<?php esc_attr_e('Enter your message here...','salesking');?>"></textarea>
                                                </div>
                                                <div class="nk-reply-form-tools">
                                                    <ul class="nk-reply-form-actions g-1">
                                                        <li class="mr-2"><button class="btn btn-primary" type="submit" id="salesking_dashboard_reply_message" value="<?php echo esc_attr($id);?>"><?php esc_html_e('Send','salesking');?></button></li>
                                                        
                                                    </ul>
                                                    
                                                </div><!-- .nk-reply-form-tools -->
                                            </div><!-- .nk-reply-form-editor -->
                                        </div>
                                        
                                    </div>
                                </div><!-- .nk-reply-form -->
                            </div><!-- .nk-reply -->
                        </div><!-- .nk-msg-body -->
                        <?php
                        }
                    }
                    ?>
                </div><!-- .nk-msg -->
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="compose-mail">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?php esc_html_e('Compose Message','salesking');?></h6>
                    <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
                </div>
                <div class="modal-body p-0">
                    <div class="nk-reply-form-header">
                        <div class="nk-reply-form-group">
                            <div class="nk-reply-form-input-group">
                                <div class="nk-reply-form-input nk-reply-form-input-to">
                                    <label class="label">To</label>
                                    <select name="salesking_dashboard_recipient" id="salesking_dashboard_recipient">
                                        <optgroup label="<?php esc_html_e('Shop', 'salesking'); ?>">
                                            <option value="shop"><?php esc_html_e('Shop','salesking'); ?></option>
                                        </optgroup>
                                       
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-reply-form-editor">
                        <div class="nk-reply-form-field">
                            <input type="text" class="form-control form-control-simple" id="salesking_compose_send_message_title" placeholder="<?php esc_attr_e('Subject','salesking');?>">
                        </div>
                        <div class="nk-reply-form-field">
                            <textarea class="form-control form-control-simple no-resize ex-large" id="salesking_compose_send_message_content" placeholder="<?php esc_attr_e('Enter your message here...','salesking');?>"></textarea>
                        </div>
                    </div><!-- .nk-reply-form-editor -->
                    <div class="nk-reply-form-tools">
                        <ul class="nk-reply-form-actions g-1">
                            <li class="mr-2"><button class="btn btn-primary" id="salesking_compose_send_message" type="submit"><?php esc_html_e('Send','salesking');?></button></li>
                        </ul>
                       
                    </div><!-- .nk-reply-form-tools -->
                </div><!-- .modal-body -->
            </div><!-- .modal-content -->
        </div><!-- .modla-dialog -->
    </div><!-- .modal -->

    <div class="nk-footer">
        <div class="container-fluid">
            <div class="nk-footer-wrap">
                <div class="nk-footer-copyright"><?php esc_html_e('Messages & Inbox','salesking'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
