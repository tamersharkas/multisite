<?php
if (!defined('ABSPATH')) {
	exit;
}

//Setting Page
function aqbp_settings() {
	require plugins_url( 'includes/setting.php', __FILE__ );
	?>
<div id="addify_settings_tabs">

				<div class="addify_setting_tab_ulli">
					<div class="addify-logo">
						<img src="<?php echo esc_url(AQBP_URL . 'images/addify-logo.png'); ?>" width="200">
						<h3><?php echo esc_html__('Addify Plugin Options', 'addify_TextDomain'); ?></h3>
					</div>

					<ul>
						<li><a href="#tabs-1"><span class="dashicons dashicons-admin-tools"></span><?php echo esc_html__('General Settings', 'addify_TextDomain'); ?></a></li>

					</ul>
				</div>

				<div class="addify-tabs-content">
					<form id="addify_setting_form" action="" method="post">
						<div class="addify-top-content">
							<h1><?php echo esc_html__('Addify WooCommerce Quick Buy Settings', 'addify_TextDomain'); ?></h1>
						</div>

						<div class="addify-singletab" id="tabs-1">
							<h2><?php echo esc_html__('General Settings', 'addify_TextDomain'); ?></h2>

							<table class="addify-table-optoin">
								<tbody>
								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Redirect Location:' , 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select class='afrfq_input_class' id='aqbp_set_Red_loc' name="aqbp_set_Red_loc">
											<option value="cart"><?php echo esc_html__('Cart Page' , 'addify_TextDomain'); ?></option>
											<option value="checkout"><?php echo esc_html__('Checkout Page'); ?></option>
											<option value='custom'><?php echo esc_html__('Custom Page'); ?></option>
										</select>

										<p><?php echo esc_html__('Select the page where to redirect after Quick buy button pressed', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Custom Redirect Location:', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<input value="<?php echo esc_attr__('http://localhost:8080/wordpress/checkout/' , 'addify_TextDomain'); ?>" class="afrfq_input_class" type="text" name="aqbp_set_cus_url" id="aqbp_set_cus_url" />
										<p><?php echo esc_html__('Write custom URL to redirect:', 'addify_TextDomain'); ?></p>
									</td>

								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Show Quick Buy Button For:', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<select multiple="multiple" class='afrfq_input_class' id='aqbp_set_Red_loc' name="aqbp_set_Red_loc">
											<option value="cart"><?php echo esc_html__('Cart Page' , 'addify_TextDomain'); ?></option>
											<option value="checkout"><?php echo esc_html__('Checkout Page'); ?></option>
											<option value='custom'><?php echo esc_html__('Custom Page'); ?></option>
										</select>
										<p><?php echo esc_html__('Choose types of products'); ?></p>
									</td>

								</tr>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Quick Buy Cart Quantity:', 'addify_TextDomain'); ?></h3>
										</div>
									</th>

									<td>
										<input value="<?php echo esc_attr('0'); ?>" class="afrfq_input_class" type="text" name="aqbp_set_cart_quan" id="aqbp_set_cart_quan" />
										<p><?php echo esc_html__(' You can set min product Quantity. works only with shop page (Product listing) & [wc_quick_buy_link] short code' , 'addify_TextDomain' ); ?></p>
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
