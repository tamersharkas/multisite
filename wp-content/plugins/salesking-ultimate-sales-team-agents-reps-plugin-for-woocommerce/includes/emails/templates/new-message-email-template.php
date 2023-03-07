<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
$user_info = get_userdata($userid);
?>

<p>
	<?php esc_html_e( 'You have a new message.', 'salesking');	?>
	<br /><br />
	<?php esc_html_e( 'Sender: ','salesking'); echo $user_info->first_name.' '.$user_info->last_name; ?>
	<br /><br />
 	<?php esc_html_e( 'Content: ','salesking'); echo apply_filters('the_content',$message); ?>


</p>
<?php

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
