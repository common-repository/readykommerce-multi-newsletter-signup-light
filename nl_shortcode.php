<?php

/**
 * Shortcode
 *
 * @author Kawsar Ahmad
 * @author_url http://readykommerce.com
 * @since 1.0
 **/

// add shortcode
// =============
add_shortcode( 'nl_mc_form', 'nl_mc_form_shortcode' );

function nl_mc_form_shortcode( $atts )
{
	$options = get_option( 'nl_option_name' );
	extract( shortcode_atts( array(
		'id' => '',
		'class' => '',
		'form_class' => 'nl_subscribe_form',
		'title' => $options['form_title'],
		'desc' => $options['form_desc'],
		'email_type' => $options['form_email_type'],
		'list_id' => $options['mc_list_id']
	), $atts ) );

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

	if ($options['form_name'] == 'first_last_name') {
		$form .= '<div class="input_row first_name">';
			$form .= '<label>'. __('First Name:', 'nl_signup').'</label>';
			$form .= '<input name="fname" type="text" class="nl_input_item nl_f_name"';
			$form .= '>';
		$form .= '</div>';
		$form .= '<div class="input_row last_name">';
			$form .= '<label>'. __('Last Name:', 'nl_signup').'</label>';
			$form .= '<input name="lname" type="text" class="nl_input_item nl_l_name"';
			$form .= '>';
		$form .= '</div>';
	}

	$form .= '<div class="input_row email_add">';
		$form .= '<label>'. __('Email Address:', 'nl_signup').'</label>';
		$form .= '<input name="email" type="text" class="nl_input_item nl_email"';
		$form .= '>';
	$form .= '</div>';
	
	// hidden inputs
	$form .= '<input name="nl_mc_email_type" type="hidden" value="' .$email_type. '">';
	$form .= '<input name="nl_mc_list_id" type="hidden" value="' .$list_id. '">';

	// submit
	$form .= '<input type="submit" class="nl_submit nl_input_item" value="'.__('Subscribe', 'nl_signup').'">';

	$form .= '</form>';

	$form .= '</div>';

	return $form;
}