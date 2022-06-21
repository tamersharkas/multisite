<?php
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Class_Aqbp_Admin')) {
	class Class_Aqbp_Admin extends Class_Addify_Quick_Buy {

		//class constructor
		public function __construct() {

			include_once ABSPATH . 'wp-includes/pluggable.php';

			add_action('admin_enqueue_scripts' , array( $this, 'addify_aqbp_admin_assets' ));

			add_action('admin_menu', array( $this, 'aqbp_custom_menu_admin' ));

			add_option('aqbp_multiselect_products' , serialize(array()));

			add_filter('woocommerce_product_data_tabs', array( $this, 'addify_aqbp_product_settings_tabs' ) );

			add_action( 'woocommerce_product_data_panels', array( $this, 'addify_aqbp_product_panels' ) );

			add_action('woocommerce_process_product_meta', array($this , 'addify_aqbp_save_product_setting') );

			add_action('init' , array($this , 'aqbp_admin_settings_submit'));

			add_action('wp_ajax_afqbsearchProducts', array($this, 'afqbsearchProducts'));
		   
		}

		public function aqbp_admin_settings_submit() {
			
			if (isset($_POST['aqbp_save_settings']) && '' != $_POST['aqbp_save_settings']) {

				if (!empty($_REQUEST['afqb_nonce_field'])) {

						$retrieved_nonce = sanitize_text_field($_REQUEST['afqb_nonce_field']);
				} else {
						$retrieved_nonce = 0;
				}

				if (!wp_verify_nonce($retrieved_nonce, 'afqb_nonce_action')) {

					die('Failed security check');
				}

				$this->aqbp_save_data();
				add_action('admin_notices', array($this, 'aqbp_author_admin_notice'));
			}
		}

		//add assets for admin
		public function addify_aqbp_admin_assets() {

			$screen = get_current_screen();

			if ('woocommerce_page_addify-quick-buy' == $screen->id) {
				wp_enqueue_style( 'aqbpadminc', plugins_url( '/includes/styles/admin_style.css', __FILE__ ), false, '1.0' );

				wp_enqueue_script( 'aqbpadminj', plugins_url( '/includes/scripts/admin_script.js', __FILE__ ), array('jquery'), '1.0'  );
				wp_enqueue_script( 'aqbpjqueryui', plugins_url( '/includes/scripts/jquery-ui.js', __FILE__ ), array('jquery') , '1.0'  );
				
				wp_enqueue_style( 'select2', plugins_url( '/includes/styles/select2.css', __FILE__ ), false, '1.0' );
				wp_enqueue_script( 'select2', plugins_url( '/includes/scripts/select2.js', __FILE__ ), false, '1.0' );
				 
				wp_enqueue_script('jquery');
				$phpInfo = array(
					'admin_url'  => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce('afqb-ajax-nonce'),

				);
				wp_localize_script( 'aqbpadminj', 'phpInfo', $phpInfo );
			}

			
			
		}


		public function addify_aqbp_save_product_setting() {

			if (!empty($_REQUEST['afqb_nonce_field'])) {

				$retrieved_nonce = sanitize_text_field($_REQUEST['afqb_nonce_field']);
			} else {
				$retrieved_nonce = 0;
			}

			if (!wp_verify_nonce($retrieved_nonce, 'afqb_nonce_action')) {

				die('Failed security check');
			}

			if (isset($_POST['aqbp_ext_link'])) {
				update_post_meta( get_the_ID() , 'aqbp_ext_link' , esc_url_raw($_POST['aqbp_ext_link'] ));
			}
			if (isset($_POST['aqbp_red_option'])) {
				update_post_meta( get_the_ID() , 'aqbp_red_option' , sanitize_text_field($_POST['aqbp_red_option'] ));
			}
			if (isset( $_POST['aqbp_global_option'])) {
				update_post_meta( get_the_ID() , 'aqbp_global_option' , sanitize_text_field($_POST['aqbp_global_option'] ));
			} else {
				update_post_meta( get_the_ID() , 'aqbp_global_option' , 'no' );
			}
			if (isset($_POST['aqbp_but_label'])) {
				update_post_meta( get_the_ID() , 'aqbp_but_label' , sanitize_text_field($_POST['aqbp_but_label'] ));
			}
			if (isset($_POST['aqbp_rep_atc'])) {
				update_post_meta( get_the_ID() , 'aqbp_rep_atc' , sanitize_text_field($_POST['aqbp_rep_atc'] ));
			}
		}

		public function addify_aqbp_product_settings_tabs( $tabs) {
			$tabs['aqbp'] = array(
				'label'    => 'Addify Quick Buy',
				'target'   => 'aqbp_product_data',
				'class'    => array(''),
				'priority' => 2,
			);
			return $tabs;
		}

		public function addify_aqbp_product_panels() {

			echo '<div id="aqbp_product_data" class="panel woocommerce_options_panel hidden">';
			wp_nonce_field('afqb_nonce_action', 'afqb_nonce_field');
 
			 woocommerce_wp_text_input( array(
				'id'                => 'aqbp_but_label',
				'value'             => get_post_meta( get_the_ID(), 'aqbp_but_label', true ),
				'label'             => 'Button Label',
				'description'       => 'Enter Text to Show on Button for this product'
				) );

			woocommerce_wp_select( array(
				'id'          => 'aqbp_red_option',
				'value'       => get_post_meta( get_the_ID(), 'aqbp_red_option', true ),
				'label'       => 'Redirect Location',
				'options'     => array( '' => 'Please Select', 'cart' => 'Cart page', 'checkout' => 'Checkout Page' , 'custom' => 'Custom Page'),
				'description'       => 'Select where to redirect for this product'
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'aqbp_ext_link',
				'value'             => get_post_meta( get_the_ID(), 'aqbp_ext_link', true ),
				'label'             => 'External Link',
				'description'       => 'Enter External link Only for custom redirect like Amazon product link'
			) );

			woocommerce_wp_select( array(
				'id'          => 'aqbp_rep_atc',
				'value'       => get_post_meta( get_the_ID(), 'aqbp_rep_atc', true ),
				'label'       => 'Replace Add to Cart',
				'options'     => array( '' => 'Please Select' ,'yes' => 'Yes', 'no' => 'No'),
				'description'       => 'Replace Add to Cart Button'
			) );

					 
			woocommerce_wp_checkbox(array(
				'id'          => 'aqbp_global_option',
				'value'       => get_post_meta( get_the_ID(), 'aqbp_global_option', true ),
				'label'       => 'Disable Quick Buy',
				'description'       => 'Disable Quick buy Button for this product'
			) );
		 
			echo '</div>';
		}

		public function aqbp_author_admin_notice() {
			?>
			<div class="notice notice-success is-dismissible">
				<h2><?php echo esc_html__( 'Addify-Quick Buy Plugin Settings Saved Successfully.', 'addify_TextDomain' ); ?></h2>
			</div>
			<?php
		}


		//Save Settings
		public function aqbp_save_data() {

			if (!empty($_REQUEST['afqb_nonce_field'])) {

					$retrieved_nonce = sanitize_text_field($_REQUEST['afqb_nonce_field']);
			} else {
					$retrieved_nonce = 0;
			}

			if (!wp_verify_nonce($retrieved_nonce, 'afqb_nonce_action')) {

				die('Failed security check');
			}

			if (isset($_POST['aqbp_rep_atc'])) {
				update_option('aqbp_rep_atc' , sanitize_text_field($_POST['aqbp_rep_atc']));
			} else { 
				update_option('aqbp_rep_atc' , 'off');
			}

			if (isset($_POST['aqbp_set_Red_loc'])) {
				update_option('aqbp_set_Red_loc' , sanitize_text_field($_POST['aqbp_set_Red_loc']));
			}

			if (isset($_POST['aqbp_set_cus_url'])) {
				update_option('aqbp_set_cus_url' , esc_url_raw($_POST['aqbp_set_cus_url']));
			}

			if (isset($_POST['aqbp_multiselect_product_cats'])) {
				update_option('aqbp_multiselect_product_cats' , serialize(sanitize_meta('aqbp_multiselect_product_cats', $_POST['aqbp_multiselect_product_cats'], '')));
			} else {
				update_option('aqbp_multiselect_product_cats' , serialize(array()));
			}

			if (isset($_POST['aqbp_multiselect_products'])) {
				update_option('aqbp_multiselect_products' , serialize(sanitize_meta('aqbp_multiselect_products', $_POST['aqbp_multiselect_products'], '')));
			} else {
				update_option('aqbp_multiselect_products' , serialize(array()));
			}

			if (isset($_POST['aqbp_set_cart_quan'])) {
				if (!empty($_POST['aqbp_set_cart_quan'])) {
					update_option('aqbp_set_cart_quan' , sanitize_text_field($_POST['aqbp_set_cart_quan'])); 
				} else {
					update_option('aqbp_set_cart_quan' , '1');
				}
			}

			if (isset($_POST['aqbp_but_label'])) {
				update_option('aqbp_but_label' , sanitize_text_field($_POST['aqbp_but_label']));
			}

			if (isset($_POST['aqbp_single_button'])) {
				update_option('aqbp_single_button' , sanitize_text_field($_POST['aqbp_single_button']));
			}

			if (isset($_POST['aqbp_single_button_pos'])) {
				update_option('aqbp_single_button_pos' , sanitize_text_field($_POST['aqbp_single_button_pos']));
			}

			if (isset($_POST['aqbp_shop_button'])) {
				update_option('aqbp_shop_button' , sanitize_text_field($_POST['aqbp_shop_button']));
			}

			if (isset($_POST['aqbp_shop_button_pos'])) {
				update_option('aqbp_shop_button_pos' , sanitize_text_field($_POST['aqbp_shop_button_pos']));
			}

			if (isset($_POST['aqbp_style_button'])) {
				update_option('aqbp_style_button' , sanitize_text_field($_POST['aqbp_style_button']));
			}
		}

		//Custom Menu for Plugin Setting
		public function aqbp_custom_menu_admin() {

			add_submenu_page( 'woocommerce', esc_html__('Quick Buy Button', 'addify_TextDomain'), esc_html__('Quick Buy Button', 'addify_TextDomain'), 'manage_options', 'addify-quick-buy', array($this, 'aqbp_settings') );
		}

		//Setting Page
		public function aqbp_settings() {
			?>
			<div id="addify_settings_tabs">

				<div class="addify_setting_tab_ulli">
					<div class="addify-logo">
						<img src="<?php echo esc_url(AQBP_URL . 'images/addify-logo.png'); ?>" width="200">
						
					</div>

					<ul>
						<li><a href="#tabs-1"><span class="dashicons dashicons-admin-tools"></span><?php echo esc_html__('General Settings', 'addify_TextDomain'); ?></a></li>
						<li><a href="#tabs-2"><span class="dashicons dashicons-admin-tools"></span><?php echo esc_html__('Button Settings', 'addify_TextDomain'); ?></a></li>
						<li><a href="#tabs-3"><span class="dashicons dashicons-admin-tools"></span><?php echo esc_html__('Short Code', 'addify_TextDomain'); ?></a></li>

					</ul>
				</div>

				<div class="addify-tabs-content">
					<form id="addify_setting_form" action="" method="post">
						<?php wp_nonce_field('afqb_nonce_action', 'afqb_nonce_field'); ?>
						<div class="addify-top-content">
							<h1><?php echo esc_html__('Addify WooCommerce Quick Buy Settings', 'addify_TextDomain'); ?></h1>
						</div>

						<div class="addify-singletab" id="tabs-1">
							<h2><?php echo esc_html__('General Settings', 'addify_TextDomain'); ?></h2>
							<p><?php echo esc_html__('The following configurations enable you to create a global quick buy button and place it on specific products and categories.', 'addify_TextDomain'); ?></p>

							<table class="addify-table-optoin">
								<tbody>

								 <tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Replace add to Cart:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<input class="afrfq_input_class" type="checkbox" name="aqbp_rep_atc" id="aqbp_rep_atc" 
										<?php checked( 'on', get_option( 'aqbp_rep_atc' ) ); ?> />
										<p><?php echo esc_html__('Check this box if you want to replace "add to cart" with the following custom buy button.', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Redirect Location:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class='afrfq_input_class' id='aqbp_set_Red_loc' name="aqbp_set_Red_loc">
											<option value="cart" <?php echo get_option('aqbp_set_Red_loc') == 'cart' ? 'selected':''; ?> ><?php echo esc_html__('Cart Page' , 'addify_TextDomain'); ?></option>
											<option value="checkout" <?php echo get_option('aqbp_set_Red_loc') == 'checkout' ? 'selected':''; ?>><?php echo esc_html__('Checkout Page' , 'addify_TextDomain'); ?></option>
											
											<option value='custom' <?php echo get_option('aqbp_set_Red_loc') == 'custom' ? 'selected':''; ?>><?php echo esc_html__('Custom Page' , 'addify_TextDomain'); ?></option>
										</select>

										<p><?php echo esc_html__('Select the page where you want to redirect users once they click on your new buy button.', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								<tr class="addify-option-field" id='addify_custm_url_row'>
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Custom Redirect Location:', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<input value="<?php echo esc_attr__(get_option('aqbp_set_cus_url') , 'addify_TextDomain'); ?>" class="afrfq_input_class" type="text" name="aqbp_set_cus_url" id="aqbp_set_cus_url" />
										<p><?php echo esc_html__('Write global custom url to redirect: if you want to place url against products, please use "Addify Quick Buy" tab in "product Data" Menu section', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Show Quick Buy Button for Categories', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<?php 
										$categories = get_terms('product_cat');
										if ( !empty( $categories ) && !is_wp_error( $categories ) ) {
											echo '<select name="aqbp_multiselect_product_cats[]" id="aqbp_multiselect_product_cats" data-placeholder="Choose Catagories..." class="chosen-select" multiple="" tabindex="-1">';
											echo '<option value="all" ';
											echo in_array('all' , unserialize(get_option('aqbp_multiselect_product_cats'))) ? 'selected' : '';
											echo '>' . esc_html__('All Categories' , 'addify_TextDomain') . '</option>';
											foreach ( $categories as $category ) {
												echo '<option value="' . intval($category->term_id) . '"';
												echo in_array($category->term_id , unserialize(get_option('aqbp_multiselect_product_cats'))) ? 'selected' : '';
												echo '>' . esc_attr($category->name) . '</option>';
											}
											echo '</select>';
										}
										?>
										
										<p><?php echo esc_html__('Specify categories to apply new button. The button will show on all products of the above specified categories.', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Show Quick Buy Button for Products', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class="select_box wc-enhanced-select aqbp_multiselect_products" name="aqbp_multiselect_products[]" id="aqbp_multiselect_products"  multiple='multiple'>
											
											<?php

											$afrfq_hide_products = unserialize(get_option('aqbp_multiselect_products'));

											if (!empty($afrfq_hide_products)) {

												foreach ( $afrfq_hide_products as $pro) {

													$prod_post = get_post($pro);

													?>

													<option value="<?php echo intval($pro); ?>" selected="selected"><?php echo esc_attr($prod_post->post_title); ?></option>

													<?php 
												}
											}
											?>

										</select>
									</td>

								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Quick Buy Cart Quantity:', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<input type="number" value="<?php echo esc_attr__(get_option('aqbp_set_cart_quan'), 'addify_TextDomain'); ?>" class="afrfq_input_class" type="text" name="aqbp_set_cart_quan" id="aqbp_set_cart_quan" />
										<p><?php echo esc_html__('Set the default quantity that needs to be added to the cart when clicked on the quick buy button. Works only on shop page (Product listing) & [wc_quick_buy_link] shortcode.' , 'addify_TextDomain' ); ?></p>
									</td>

								</tr>
								</tbody>
							</table>
						</div>

						<div class="addify-singletab" id="tabs-2">
							<h2><?php echo esc_html__('Button Settings', 'addify_TextDomain'); ?></h2>

							<table class="addify-table-optoin">
								<tbody>
								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Button Label:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										
										 <input placeholder="Quick Buy" value="<?php echo esc_attr__(get_option('aqbp_but_label') , 'addify_TextDomain'); ?>" class="afrfq_input_class" type="text" name="aqbp_but_label" id="aqbp_but_label" />
										<p><?php echo esc_html__('Insert label to show on quick buy button.', 'addify_TextDomain'); ?></p>
									</td>

								</tr>
								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Button Style', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class='afrfq_input_class' id='aqbp_style_button' name="aqbp_style_button">
											<option value="theme" <?php echo get_option('aqbp_style_button') == 'theme' ? 'selected' : ''; ?> ><?php echo esc_html__('Theme Style' , 'addify_TextDomain'); ?></option>
											<option value="plugin" <?php echo get_option('aqbp_style_button') == 'plugin' ? 'selected' : ''; ?> ><?php echo esc_html__('Plugin style' , 'addify_TextDomain'); ?></option>
											
										</select>
										<p><?php echo esc_html__('Select style for quick buy button.', 'addify_TextDomain'); ?></p>
									</td>
								</tr>
								<tr class="addify-heading-2">
									<th><?php echo esc_html__('Single product page', 'addify_TextDomain'); ?></th>
								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Show Button', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class='afrfq_input_class' id='aqbp_single_button' name="aqbp_single_button">
											<option value="yes" <?php echo get_option('aqbp_single_button') == 'yes' ? 'selected' : ''; ?> ><?php echo esc_html__('Yes' , 'addify_TextDomain'); ?></option>
											<option value="no" <?php echo get_option('aqbp_single_button') == 'no' ? 'selected' : ''; ?> ><?php echo esc_html__('No' , 'addify_TextDomain'); ?></option>
											
										</select>
										<p><?php echo esc_html__('Show button on single product page.', 'addify_TextDomain'); ?></p>
									</td>
								</tr>
								<tr id='aqbp_single_button_pos_row'>
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Button Position', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class='afrfq_input_class' id='aqbp_single_button_pos' name="aqbp_single_button_pos">
											<option value="before-button" <?php echo get_option('aqbp_single_button_pos') == 'before-button' ? 'selected' : ''; ?>><?php echo esc_html__('Before Add to Cart Button' , 'addify_TextDomain'); ?></option>
											<option value="after-button" <?php echo get_option('aqbp_single_button_pos') == 'after-button' ? 'selected' : ''; ?> ><?php echo esc_html__('After Add to Cart Button' , 'addify_TextDomain'); ?></option>
											
										</select>
										<p><?php echo esc_html__('Select where you want to show button.', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								<tr class="addify-heading-2">
									<th class="option-head"><?php echo esc_html__('Shop Page', 'addify_TextDomain'); ?></th>
								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Show Button', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class='afrfq_input_class' id='aqbp_shop_button' name="aqbp_shop_button">
											<option value="yes" <?php echo get_option('aqbp_shop_button') == 'yes' ? 'selected' : ''; ?>><?php echo esc_html__('Yes' , 'addify_TextDomain'); ?></option>
											<option value="no" <?php echo get_option('aqbp_shop_button') == 'no' ? 'selected' : ''; ?> ><?php echo esc_html__('No' , 'addify_TextDomain'); ?></option>
											
										</select>
										<p><?php echo esc_html__('Show button on shop/listing pages.', 'addify_TextDomain'); ?></p>
									</td>
								</tr>
								<tr id='aqbp_shop_button_pos_row'>
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Button Position', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class='afrfq_input_class' id='aqbp_shop_button_pos' name="aqbp_shop_button_pos">
											<option value="before-button" <?php echo get_option('aqbp_shop_button_pos') == 'before-button' ? 'selected' : ''; ?> ><?php echo esc_html__('Before Add to Cart Button' , 'addify_TextDomain'); ?></option>
											<option value="after-button" <?php echo get_option('aqbp_shop_button_pos') == 'after-button' ? 'selected' : ''; ?> ><?php echo esc_html__('After Add to Cart Button' , 'addify_TextDomain'); ?></option>
											
										</select>
										<p><?php echo esc_html__('Select where you want to show button.', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								</tbody>
							</table>
						</div>
						<div class="addify-singletab" id="tabs-3">
							<h2><?php echo esc_html__('Short code', 'addify_TextDomain'); ?></h2>
							<table class="addify-table-optoin">
								<tbody>
								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Button Link and Text:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td class="bg-silver">
										<h4>[AQBP_QUICK_BUY link='http://localhost/wordpress/' text='Buy Product on Amazon' class='your_css_class']</h4>
										
									</td>
									<td class="bg-silver">
										<center>
										 <p>'http://localhost/wordpress/' URL to redirect <br> "Buy Product on Amazon": is label to show on button</p>
									 </center>
									</td>

								</tr>
								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Button Link and Text With add to cart:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td class="bg-silver">
										<h4>[AQBP_QUICK_BUY link='http://localhost/wordpress/' text='Buy Product' add-to-cart='408']</h4>
										
									</td>
									<td class="bg-silver">
										<center>
										<p>'http://localhost/wordpress/' URL to redirect <br> 408 is product id</p>
										</center>
									</td>

								</tr>
								<tr class="addify-option-field">
									<th >
										<div class="option-head">
											<h3><?php echo esc_html__('Button Link and Text with add to cart and Quantity:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td class="bg-silver">
										<h4>[AQBP_QUICK_BUY link='http://localhost/wordpress/' text='Buy Bundle' add-to-cart='408' quantity='2']</h4>
										
									</td>
									<td class="bg-silver">
										<center>
										<p><b>'http://localhost/wordpress/'</b> URL to redirect <br><b> 408:</b> is product id <br>
										<b>2</b> is quantity of product</p>
										</center>
									</td>

								</tr>
								<tr class="addify-option-field">
									 <th >
										<div class="option-head">
											<h3><?php echo esc_html__('Missing Attribute:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>
									<td class="bg-silver">
										<center>
											<b>
										<p><?php echo esc_html__('LINK MISSING: global custom url will be used' , 'addify_TextDomain'); ?></p>
										<p><?php echo esc_html__('TEXT MISSING: global button Text will be used' , 'addify_TextDomain'); ?></p>
										<p><?php echo esc_html__('CLASS MISSING: Plugin Default Class will be used for Button' , 'addify_TextDomain'); ?></p>
									</b>
										</center>
									</td>
								</tr>
							</tbody>
						</table>
						</div>
						<?php 
							submit_button(esc_html__('Save Settings', 'addify_TextDomain' ), 'primary', 'aqbp_save_settings');
						?>

					</form>
				</div>

			</div>

			<?php
		}


		public function afqbsearchProducts() {

			

			if (isset($_POST['nonce']) && '' != $_POST['nonce']) {

				$nonce = sanitize_text_field( $_POST['nonce'] );
			} else {
				$nonce = 0;
			}

			if (isset($_POST['q']) && '' != $_POST['q']) {

				if ( ! wp_verify_nonce( $nonce, 'afqb-ajax-nonce' ) ) {

					die ( 'Failed ajax security check!');
				}
				

				$pro = sanitize_text_field( $_POST['q'] );

			} else {

				$pro = '';

			}


			$data_array = array();
			$args       = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'numberposts' => -1,
				's'	=>  $pro
			);
			$pros       = get_posts($args);

			if ( !empty($pros)) {

				foreach ($pros as $proo) {

					$title        = ( mb_strlen( $proo->post_title ) > 50 ) ? mb_substr( $proo->post_title, 0, 49 ) . '...' : $proo->post_title;
					$data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title )
				}
			}
			
			echo wp_json_encode( $data_array );

			die();
		}

	}
	new Class_Aqbp_Admin();
}
