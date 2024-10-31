<?php

/**
 * shortcode for newsletter signup plugin
 * @author readyKommerce
 * @see http://www.readykommerce.com
 * @since 1.0
 */

// add shortcode
// =============
add_shortcode( 'nl_mc_form', 'nl_lg_mc_form_shortcode' );

function nl_lg_mc_form_shortcode( $atts )
{
	$default_value = Array(
			'mc_api' => '',
            'form_title' => 'Subscribe our newsletter',
            'form_desc' => 'We will not sell your email address',
            'mc_list_id' => '',
            'form_name' => 'hide_name',
            'form_email_type' => 'text'
        );

	$options = wp_parse_args( get_option( 'nl_option_name' ), $default_value );

	extract( shortcode_atts( array(
		'id' => '',
		'class' => '',
		'form_class' => 'nl_subscribe_form',
		'title' => $options['form_title'],
		'desc' => $options['form_desc'],
		'email_type' => $options['form_email_type'],
		'list_id' => $options['mc_list_id']
	), $atts ) );
	
	$no_name_class = '';
	$form = '';
	$form .= '<div class="nl_signup_wrapper nl_signup_shortcode '.$class.'" id="' .$id. '">';
	if ($title!='') {
		$form .= '<h3 class="nl_title">' .$title. '</h3>';
	}
	if ($desc!='') {
		$form .= '<p class="nl_desc">' . $desc . '</p>';
	}

	$form .= '<form action="" class="' . $form_class . '" method="post">';
	$form .= '<div class="nl_msg_box"></div>';

	if ($options['form_name'] == 'full_name') {
		$form .= '<div class="input_row full_name">';
			$form .= '<label>Your Name: </label>';
			$form .= '<input name="full_name" type="text" class="nl_input_item nl_full_name">';
		$form .= '</div>';
	} elseif ($options['form_name'] == 'first_last_name') {
		$form .= '<div class="input_row first_name">';
			$form .= '<label>First Name: </label>';
			$form .= '<input name="fname" type="text" class="nl_input_item nl_f_name">';
		$form .= '</div>';
		$form .= '<div class="input_row last_name">';
			$form .= '<label>Last Name: </label>';
			$form .= '<input name="lname" type="text" class="nl_input_item nl_l_name">';
		$form .= '</div>';
	} else {
		$no_name_class = 'no_name';
	}

	$form .= '<div class="input_row email_add '.$no_name_class.'">';
		$form .= '<label>Your Email: </label>';
		$form .= '<input name="email" type="text" class="nl_input_item nl_email" required>';
	$form .= '</div>';
	
	// hidden inputs
	$form .= '<input name="nl_mc_email_type" type="hidden" value="' .$email_type. '">';
	$form .= '<input name="nl_mc_list_id" type="hidden" value="' .$list_id. '">';

	// submit
	$form .= '<input type="submit" class="nl_submit nl_input_item" value="Subscribe">';

	$form .= '</form>';

	$form .= '</div>';

	return $form;
}