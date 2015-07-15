<?php
/*
Plugin Name: Users to admin contact form
PLugin URI: http://www.webai.lt
Description: It`s simple contact form that shows only for registerted users. By using this contact form plugin, users can send messages to admin email;
Version: 0.1
Author: Artūras Z.
Author URI: http://www.webai.lt
 */
 
defined( 'ABSPATH' ) or die( 'No direct access, please?' );

function ucf_style() {
	wp_register_style('ucf_style', plugins_url('/css/style.css',__FILE__ ));
	wp_enqueue_style('ucf_style');
}
add_action( 'wp_enqueue_scripts','ucf_style');

/**
 * [tr_txt language function]
 * @return [array] [langs array return ]
 */
function tr_txt(){
	return array(
		'user_name' => __('User name'),
		'user_mail' => __('User mail'),
		'subject' => __('Subject'),
		'subject' => __('Subject'),
		'message' => __('Message'),
		'send' => __('Send'),
		'from' => __('From: '),
		'mess_succ' => __('Thanks, your message has been sent to admin'),
		'mess_error' => __('Error'),
		'mess_empty_fields' => __('Empty fields'),
	);
}


/**
 * [html_form_code show contact form]
 */
function html_form_code(){
	$lang = tr_txt();

	if ( is_user_logged_in() ) {
	echo '<form action="'.esc_url($_SERVER['REQUEST_URI']).'" method="POST" class="ucf_form">';

	echo '<input type="text" name="subject_field"  class="ucf_field" svalue="'.(isset($_POST['subject_field']) ? esc_attr($_POST['subject_field']) : '' ).'" size="100%" placeholder='.$lang["subject"].' />';

	echo '<textarea name="message_field" class="ucf_field" placeholder='.$lang["message"].'>'.(isset($_POST['message_field']) ? esc_attr($_POST['message_field']) : '' ).'</textarea>';

	echo '<input type="submit" name="submit_field" class="ucf_button" value="'.$lang["send"].'" />';
	echo '</form>';

	} else {}
}

function send_email(){
    global $current_user;
    $lang = tr_txt();

    if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['subject_field'])){

    if(!empty($_POST['message_field']) || !empty($_POST['message_field'])){
    

        
        $uname = $current_user->user_login;
        $umail = $current_user->user_email;
        $subject = sanitize_text_field($_POST['subject_field']);
        $message =
        "<p><strong>".$lang["user_name"]."</strong>: ".$uname."</p>".
        "<p><strong>".$lang["user_mail"]."</strong>: ".$umail."</p>".
        "<p><strong>".$lang["subject"]."</strong>: ".$subject."</p>".
        "<p><strong>".$lang["message"]."</strong>: ".esc_textarea($_POST['message_field'])."</p>";

        $to = get_option('admin_email');
        
        $headers = ''.$lang["from"].' '.$uname.' < '.$umail.' >';

        add_filter('wp_mail_content_type',create_function('', 'return "text/html"; ')); //send html formated
        
        if(wp_mail($to, $subject, $message, $headers)){
            echo '<div class="ucf_label_success">'; echo '<p>'.$lang["mess_succ"].'</p>';
            echo "</div>";
        } else {
            echo '<div class="ucf_label_alert">'; echo '<p>'.$lang["mess_error"].'</p>';
            echo '</div>';
        }
        
        remove_filter( 'wp_mail_content_type', 'set_html_content_type' ); //remove html formated 
        function set_html_content_type() { return 'text/html'; }

    } else { echo '<div class="ucf_label_alert">'; echo '<p>'.$lang["mess_empty_fields"].'</p>'; echo '</div>'; }
    
    }
}

function cf_shortcode(){
	ob_start();
	send_email();
	html_form_code();
	return ob_get_clean();
}

add_shortcode( 'ucf_contact_form', 'cf_shortcode' );