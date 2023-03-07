<?php
if (!class_exists('Class_Aqbp_User')) {
	class Class_Aqbp_User extends Class_Addify_Quick_Buy {
		private $aqbp_shop_but_pr;
		private $aqbp_hide;
		public function __construct() {

			include_once ABSPATH . 'wp-includes/pluggable.php';
			
			$this->aqbp_set_button_priority();

			add_action('wp_enqueue_scripts' , array($this , 'aqbp_user_assets'));
			add_action('init' , array($this , 'aqbp_quick_buy_submit'));

			add_shortcode('AQBP_QUICK_BUY' , array($this , 'addify_aqbp_show_button'));
			
			if ( 'yes' == get_option('aqbp_single_button')) {
				
				if ( 'after-button' == get_option('aqbp_single_button_pos')) {
					add_action( 'woocommerce_after_add_to_cart_button', array($this , 'addify_aqbp_button_on_product_page'));
				} else {
					add_action( 'woocommerce_before_add_to_cart_button', array($this , 'addify_aqbp_button_on_product_page'));
				}
			}

			if ( 'yes' == get_option('aqbp_shop_button') ) {
				// Replace add to cart button.
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'addify_aqbp_hide_atc' ), 100, 2 );
				add_action( 'woocommerce_after_shop_loop_item', array($this , 'add_loop_custom_button'), $this->aqbp_shop_but_pr );
			}
		}

		public function aqbp_quick_buy_submit() {
			if (isset($_POST['aqbp_quick_buy_btn'])) {
				if (!empty($_REQUEST['afqb_nonce_field'])) {

					$retrieved_nonce = sanitize_text_field($_REQUEST['afqb_nonce_field']);
				} else {
					$retrieved_nonce = 0;
				}

				if (!wp_verify_nonce($retrieved_nonce, 'afqb_nonce_action')) {

					die('Failed security check');
				}
				
				$this->aqbp_custom_add_to_cart();
			}
		}
		
		public function addify_aqbp_show_button( $attr) {
			$red_param   = '';
			$buton_class = '';

			ob_start();
			if (isset($attr['class'])) {
				$buton_class = $attr['class'] . ' button';
			} else {
				$buton_class = 'aqbp_quick_buy_btn button';
			}
			if (isset($attr['add-to-cart'])) {	
				$red_param = '?add-to-cart=' . $attr['add-to-cart'];
				if (isset($attr['quantity'])) {
					$red_param .= '&quantity' . $attr['quantity'];
				}
			}
			
			if (isset($attr['link']) && isset($attr['text'])) {
				echo '<a class="' . esc_attr($buton_class) . '" href="' . esc_url($attr['link'] . $red_param) . '">' . esc_attr($attr['text']) . '</a>' ;
			} elseif (isset($attr['link'])) {
				echo '<a class="' . esc_attr($buton_class) . '" href="' . esc_url($attr['link'] . $red_param ) . '">' . esc_attr(get_option('aqbp_but_label')) . '</a>' ;
			} elseif (isset($attr['text'])) {
				echo '<a class="' . esc_attr($buton_class) . '" href="' . esc_url(get_option('aqbp_set_cus_url') . $red_param) . '">' . esc_attr($attr['text']) . '</a>' ;
			} else {
				return '';
			}

			return ob_get_clean();
		}

		public function aqbp_user_assets() {
			wp_enqueue_style( 'aqbpuserc', plugins_url( '/includes/styles/front_style.css', __FILE__ ) , false, '1.0'  );
			wp_enqueue_script('jquery');
			//adding Jquery file
			 wp_enqueue_script( 'aqbpuserj', plugins_url( '/includes/scripts/front_script.js', __FILE__ ), array('jquery'), '1.0'  );

			$phpInfo = array('button_style' => get_option('aqbp_style_button') , 
						'button_position' => get_option('aqbp_single_button_pos') , 
						'button_shop_pos' => get_option('aqbp_shop_button_pos') ,
						'hide_button'	=> $this->aqbp_hide,
						);
			wp_localize_script( 'aqbpuserj', 'phpInfo', $phpInfo );
		}

		public function aqbp_custom_add_to_cart() {
			if (!empty($_REQUEST['afqb_nonce_field'])) {

				$retrieved_nonce = sanitize_text_field($_REQUEST['afqb_nonce_field']);
			} else {
				$retrieved_nonce = 0;
			}

			if (isset($_POST['add-to-cart'])) {

				if (!wp_verify_nonce($retrieved_nonce, 'afqb_nonce_action')) {

					die('Failed security check');
				}

				$aqbp_red_option = get_post_meta( sanitize_text_field($_POST['add-to-cart']), 'aqbp_red_option' , true);
			} else {
				$aqbp_red_option = '';
			}
			

			if ( 'cart' == $aqbp_red_option ) {
				$aqbp_red = get_option('aqbp_cart_url');
			} elseif ( 'checkout' == $aqbp_red_option ) {
				$aqbp_red = get_option('aqbp_checkout_url');
			} elseif ( 'cart' == get_option('aqbp_set_Red_loc')) {
				$aqbp_red = get_option('aqbp_cart_url');
			} elseif ( 'checkout' == get_option('aqbp_set_Red_loc')) {
				$aqbp_red = get_option('aqbp_checkout_url');
			} else {
				$aqbp_red = get_option('aqbp_set_cus_url');
				header("Location: $aqbp_red");
				exit;
			}

			if (isset($_POST['quantity'])) {
				$qty = sanitize_meta('', $_POST['quantity'], '');
			}

			if (isset($_POST['add-to-cart'])) {
				$addcart = sanitize_meta('', $_POST['add-to-cart'], '');
			}

			if (isset($_POST['add-to-cart']) && isset($_POST['product_id']) && isset($_POST['variation_id']) ) {
				$aqbp_red = '' . $aqbp_red . '?add-to-cart=' . sanitize_text_field($_POST['add-to-cart']) . '&product_id=' . intval($_POST['product_id']) . '&variation_id=' . intval($_POST['variation_id']) ;
				foreach ( $_POST as $key => $value ) {
					$arr = explode('_' , $key );
					if ( 'attribute' == $arr[0] ) {
						$aqbp_red .= "&$key=" . sanitize_text_field( $value );
					} 
				}
				$aqbp_red .= '&quantity=' . intval($qty);	
				header("Location: $aqbp_red");
				exit;
			} elseif (isset($_POST['add-to-cart']) && isset($_POST['quantity'])) {
				
				$aqbp_red = $aqbp_red . '?add-to-cart=' . $addcart;

				if (is_array($_POST['quantity'])) {
					foreach ($qty as $key => $value) {
						$aqbp_red .= '&quantity[' . $key . ']=' . $value;
					}
				} else {
					$aqbp_red .= '&quantity=' . $qty ;
				}
				header("Location: $aqbp_red");
				exit;
			}
		}

		public function aqbp_set_button_priority() {
			if (get_option('aqbp_shop_button_pos') == 'after-button') {
				$this->aqbp_shop_but_pr = 11;
			} else {
				$this->aqbp_shop_but_pr = 9;
			}
		}


		public function add_loop_custom_button() {
			global $product;
			update_option('aqbp_cart_url' , wc_get_cart_url());
			update_option('aqbp_checkout_url' , wc_get_checkout_url());
			$global_disable = get_post_meta( get_the_ID(), 'aqbp_global_option', true );
			//return if global setting is not applicable
			if ( 'yes' == $global_disable ) {
				return;
			}

			if ( !$product->is_purchasable() || ! $product->is_in_stock() ) {
				return;
			}

			if ( 'simple' == $product->get_type() ) {
				$aqbp_but_label = get_post_meta( $product->get_id() , 'aqbp_but_label' , true);
				if (empty($aqbp_but_label)) {
					$aqbp_but_label = get_option('aqbp_but_label');
				}
				if (empty($aqbp_but_label)) {
					$aqbp_but_label = 'Quick Buy';
				}
				$aqbp_red_option = get_post_meta( $product->get_id() , 'aqbp_red_option' , true);
				$terms           = get_the_terms( $product->get_id() , 'product_cat' );

				if ( 'cart' == $aqbp_red_option ) {
					$aqbp_red_url = get_option('aqbp_cart_url');
				} elseif ( 'checkout' == $aqbp_red_option ) {
					$aqbp_red_url = get_option('aqbp_checkout_url');
				} elseif ( 'custom' == $aqbp_red_option) {
					$aqbp_red_url = get_post_meta($product->get_id() , 'aqbp_ext_link' , true);
					if (empty($aqbp_red_url)) {
						$aqbp_red_url = get_option('aqbp_set_cus_url');
					}		
				} elseif ( 'cart' == get_option('aqbp_set_Red_loc')) {
					$aqbp_red_url = get_option('aqbp_cart_url');
				} elseif ( 'checkout' == get_option('aqbp_set_Red_loc')) {
					$aqbp_red_url = get_option('aqbp_checkout_url');
				} else {
					$aqbp_red_url = get_option('aqbp_set_cus_url');
				}
				if ($this->addify_aqbp_is_button($product)) {
					echo '<a href="' . esc_url($aqbp_red_url);

					if ( 'custom' == $aqbp_red_option || 'custom' == get_option('aqbp_set_Red_loc') ) {
						echo '" target="_blank"';
					} else {
						echo '?add-to-cart=' . intval($product->get_id()) . '&quantity=' . intval(get_option('aqbp_set_cart_quan')) . '"';
					}

					echo 'id="aqbp_quick_buy_shop_btn" data-quantity="' . intval(get_option('aqbp_set_cart_quan')) . '" class="button" data-product_id="' . intval($product->get_id()) . '" data-product_sku="' . esc_attr($product->get_sku()) . '" aria-label="' . esc_html__('Add ' . esc_attr($product->get_name()) . ' to your cart' , 'addify_TextDomain'  ) . '" rel="nofollow">' . esc_html__( $aqbp_but_label , 'addify_TextDomain' ) . '</a>';
					return;
				}	
			} else {
				return;
			}
		}

		public function addify_aqbp_hide_atc( $link, $product ) {
			
			global $product;

			if ( !$product->is_purchasable() || ! $product->is_in_stock() ) {
				return $link;
			}

			if ( 'simple' == $product->get_type()) {
				
				if ( $this->addify_aqbp_is_button( $product) ) {
					if ( 'yes' == get_post_meta($product->get_id() , 'aqbp_rep_atc' , true)) {
						
						return '';	
					}
					
					if ( 'on' == get_option('aqbp_rep_atc')) {
						
						return '';
					}	
				}
			}

			return $link;
		}

		public function addify_aqbp_is_button( $product) {
			$terms = get_the_terms( $product->get_id() , 'product_cat' );

			if (in_array('all' , unserialize(get_option('aqbp_multiselect_products'))) || in_array($product->get_id() , unserialize(get_option('aqbp_multiselect_products')))) {
				return true;
			}
			if ( 'yes' == get_post_meta($product->get_id() , 'aqbp_global_option' , true)) {
				return false;
			}
			if (in_array('all' , unserialize(get_option('aqbp_multiselect_product_cats'))) ) {
				return true;
			} else {
				foreach ( $terms as $key) {
					if (in_array( $key->term_id , unserialize(get_option('aqbp_multiselect_product_cats'))) ) {
						return true;
					}
				}
			}
			return false;	
		}

		public function addify_aqbp_button_on_product_page() {
			global $product;
			$terms = get_the_terms( $product->get_id() , 'product_cat' );
			update_option('aqbp_cart_url' , wc_get_cart_url());
			update_option('aqbp_checkout_url' , wc_get_checkout_url());
			$aqbp_but_label = get_post_meta( $product->get_id() , 'aqbp_but_label' , true);
			if (empty($aqbp_but_label)) {
				$aqbp_but_label = get_option('aqbp_but_label');
			}
			if (empty($aqbp_but_label)) {
				$aqbp_but_label = 'Quick Buy';
			}
			$aqbp_red_option = get_post_meta( $product->get_id() , 'aqbp_red_option' , true);
			$global_disable  = get_post_meta( get_the_ID(), 'aqbp_global_option', true );
			//return if global setting is not applicable
			if ( 'yes' == $global_disable ) {
				return;
			}

			$disable = true;
			if ( $product->is_type('variable' ) ) {
				$disable = 'disabled wc-variation-selection-needed';
			}

			wp_nonce_field('afqb_nonce_action', 'afqb_nonce_field'); 

			if (in_array('all' , unserialize(get_option('aqbp_multiselect_products'))) || in_array($product->get_id() , unserialize(get_option('aqbp_multiselect_products')))) {
				if ( 'yes' == get_post_meta(get_the_ID() , 'aqbp_rep_atc' , true) ) {
					echo '<input type="hidden" id="hide_singel_add_to_cart">';
				} elseif ( empty( get_post_meta(get_the_ID() , 'aqbp_rep_atc' , true) ) ) {
					if ( 'on' == get_option('aqbp_rep_atc') ) {
						echo '<input type="hidden" id="hide_singel_add_to_cart">';
					}
				}
				if ('custom' == $aqbp_red_option) {
					 $aqbp_red_url = get_post_meta( $product->get_id() , 'aqbp_ext_link' , true);
					 echo '<a target="_blank" id="aqbp_quick_buy_btn" href="' . esc_url($aqbp_red_url) . '">' . esc_html__( $aqbp_but_label , 'addify_TextDomain' ) . '</a>';
					 return;
				} elseif ( 'custom' == get_option('aqbp_set_Red_loc') && empty($aqbp_red_option)) {
					echo '<a target="_blank" id="aqbp_quick_buy_btn" href="' . esc_url(get_option('aqbp_set_cus_url')) . '">' . esc_html__( $aqbp_but_label , 'addify_TextDomain' ) . '</a>';
					return;
				}
				echo '<input name="aqbp_quick_buy_btn" class="single-add-to-cart-button ' . esc_attr( $disable ) . '" id="aqbp_quick_buy_btn" value="' . esc_html__( $aqbp_but_label , 'addify_TextDomain' ) . '" type="submit" ></input>';

				if ('simple' == $product->get_type()) {
					echo '<input type="hidden" name="add-to-cart" value="' . intval($product->get_id()) . '">';
				}
				return;
			}

			$flag = false;

			if ( in_array('all' , unserialize(get_option('aqbp_multiselect_product_cats'))) ) {
				$flag = true;
			} else {
				foreach ($terms as $key) {
					if (in_array( $key->term_id , unserialize(get_option('aqbp_multiselect_product_cats'))) ) {
						$flag = true;
					}
				}
			}
			if ( $flag) {
				if ( 'yes' == get_post_meta(get_the_ID() , 'aqbp_rep_atc' , true) ) {
					echo '<input type="hidden" id="hide_singel_add_to_cart">';
				} elseif ( empty( get_post_meta(get_the_ID() , 'aqbp_rep_atc' , true) ) ) {
					if ( 'on' == get_option('aqbp_rep_atc') ) {
						echo '<input type="hidden" id="hide_singel_add_to_cart">';
					}
				}
				if ('custom' == $aqbp_red_option) {
					$aqbp_red_url = get_post_meta( $product->get_id() , 'aqbp_ext_link' , true);
					echo '<a target="_blank" id="aqbp_quick_buy_btn" href="' . esc_url($aqbp_red_url) . '">' . esc_html__( $aqbp_but_label , 'addify_TextDomain' ) . '</a>';
					return;
				} elseif ( 'custom' == get_option('aqbp_set_Red_loc') && empty($aqbp_red_option)) {
					echo '<a target="_blank" id="aqbp_quick_buy_btn" href="' . esc_url(get_option('aqbp_set_cus_url')) . '">' . esc_html__( $aqbp_but_label , 'addify_TextDomain' ) . '</a>';
					 return;
				}
				echo '<input name="aqbp_quick_buy_btn" id="aqbp_quick_buy_btn" class="single-add-to-cart-button ' . esc_attr( $disable ) . '" value="' . esc_html__( $aqbp_but_label , 'addify_TextDomain' ) . '" type="submit" ></input>';
				if ('simple' == $product->get_type()) {
					echo '<input type="hidden" id="aqbp_quick_buy_btn" name="add-to-cart" value="' . intval($product->get_id()) . '">';
				}
				return;
			}
		}
	}
	new Class_Aqbp_User();
}
