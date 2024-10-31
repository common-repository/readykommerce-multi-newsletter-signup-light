<?php 

/*
Plugin Name: readyKommerce Multi Newsletter Signup - Light
Text Domain: nlsignup
Plugin URI: http://www.readykommerce.com/newsletter-plugin/
Description: readyKommerce Newsletter signup plugin will help you to manage users by using MailChimp API
Version: 0.1
Author: readyKommerce
Author URI: http://www.readykommerce.com/
*/

/**
 * Copyright (c) 2014 readyKommerce. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

if( is_admin() ){
    require_once "nl_admin.php";
    $nl_settings_page = new NL_SettingsPage();

    add_action( 'admin_footer', 'nl_admin_javascript' );
    // add_action('wp_ajax_nl_submit', 'nl_submit_callback');
}

function nl_admin_javascript()
{
    wp_register_script( 'nl-signup-admin-js', plugins_url( 'nl_signup-light/js/nl_signup_admin.js' ), array( 'jquery' ), false, true );
    wp_enqueue_script( 'nl-signup-admin-js', plugins_url( 'nl_signup-light/js/nl_signup_admin.js' ), array( 'jquery' ) );
}

// Add settings link on plugin page
function nl_signup_settings_link($links) { 
    $settings_link = '<a href="options-general.php?page=nl-setting-admin">Settings</a>'; 
    array_unshift($links, $settings_link); 
    return $links; 
}

add_filter("plugin_action_links_".plugin_basename(__FILE__), 'nl_signup_settings_link' );


// add front end elements
// ======================
require_once "nl_widget.php";
require_once "nl_shortcode.php";


// add Textdomain
// ==============
function nl_signup_textdomain_init() {
    load_plugin_textdomain( 'nl_signup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'nl_signup_textdomain_init');


// add default theme on front-end
// ==============================
function nl_signup_enqueue_scripts() {
    wp_enqueue_style( 'style-name', plugins_url('nl_signup-light/css/nl_signup.css') );
}

add_action( 'wp_enqueue_scripts', 'nl_signup_enqueue_scripts' );


// add ajax url
// ============
if (!function_exists('rk_plg_nlsignup_ajaxurl')) {
    function rk_plg_nlsignup_ajaxurl() {
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    }

    add_action('wp_head','rk_plg_nlsignup_ajaxurl');
}


// ajax submission on frontend
// ===========================
add_action( 'wp_footer', 'nl_action_javascript' );
add_action('wp_ajax_nl_front_submit', 'nl_front_submit_callback');
add_action('wp_ajax_nopriv_nl_front_submit', 'nl_front_submit_callback');

function nl_front_submit_callback() {
    global $wpdb; // this is how you get access to the database

    // $whatever = intval( $_POST['whatever'] );
    $options = get_option( 'nl_option_name' );

    if ( isset($_POST['email']) && $_POST['email']=='' ) {
        echo json_encode(array('success'=>false, 'msg'=>__('Email field is required', 'nl_signup'), 'redirect'=>false));
        die();
    }

    $choose_api = 'mc';

    // if ($options['choose_api']=='mc') {
    if ($choose_api=='mc') {
        require_once('mcapi/inc/MCAPI.class.php');  // same directory as store-address.php

        // grab an API Key from http://admin.mailchimp.com/account/api/
        $api = new MCAPI($options['mc_api']);
        $redirect = false;

        $fname = (isset($_POST['fname'])&&$_POST['fname']!='')?$_POST['fname']:'';
        $lname = (isset($_POST['lname'])&&$_POST['lname']!='')?$_POST['lname']:'';

        $merge_vars = Array( 
            'EMAIL' => $_POST['email'],
            'FNAME' => $fname, 
            'LNAME' => $lname
        );

        // grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
        // Click the "settings" link for the list - the Unique Id is at the bottom of that page. 
        $list_id = ($_POST['nl_mc_list_id']=='') ? $options['mc_list_id']:$_POST['nl_mc_list_id'];
        $email_type = ($_POST['nl_mc_email_type']=='') ? $options['mc_email_type']:$_POST['nl_mc_email_type'];

        if($api->listSubscribe($list_id, $_POST['email'], $merge_vars , $email_type) === true) {
            session_start();

            $close_popup = false;
            $redirect = true;
            if (isset($_POST['nl_from_widget'])) {
                $redirect = false;
            }
            if (isset($_SESSION['nl_subscribe'])) {
                $close_popup = true;
                $redirect = false;
            }

            $_SESSION['nl_subscribe'] = uniqid();
            
            echo json_encode(array('success'=>true, 'msg'=>__('Success! Check your inbox or spam folder for a message containing a confirmation link.', 'nl_signup'), 'redirect'=>$redirect, 'close_popup'=>$close_popup));
            
        }else{
            // An error ocurred, return error message. Doc: http://apidocs.mailchimp.com/api/1.3/exceptions.field.php
            // make an exception for already subscribed user
            session_start();
            if ($api->errorCode==214 && !isset($_SESSION['nl_subscribe'])) {
                $_SESSION['nl_subscribe'] = uniqid();
                $redirect = true;
            }
            $close_popup = false;
            if ($api->errorCode==214) {
                $close_popup = true;
            }

            if (isset($_POST['nl_from_widget'])) {
                $redirect = false;
            }
            echo json_encode(array('success'=>false, 'msg'=>$api->errorMessage, 'redirect'=>$redirect, 'close_popup'=>$close_popup));
        }
    } elseif($options['choose_api']=='aw') {
        echo json_encode(array('success'=>false, 'msg'=>__('Sorry! We\'re not endup with it yet. It\'ll be in your hand very soon.', 'nl_signup'), 'redirect'=>$redirect));
    }
    
    die(); // this is required to return a proper result
}


// ajax submission on ajax
// =======================
function nl_action_javascript() {
    wp_enqueue_script( 'nl-signup-js', plugins_url( 'nl_signup-light/js/nl_signup.js' ), array( 'jquery' ) );
}


// add meta box to content lock
// ============================
/**
 * Prepare subscribe to unlock(content locker) meta box
 * @return NULL
 * @since 1.0
 */
function nl_content_lock_meta() {
    add_meta_box( 'nl_content_lock_meta', __('Newsletter subscribe to unlock', 'nl_signup'), 'nl_content_lock_metaboxes', 'post', 'side', 'high' );
    add_meta_box( 'nl_content_lock_meta', __('Newsletter subscribe to unlock', 'nl_signup'), 'nl_content_lock_metaboxes', 'page', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'nl_content_lock_meta' );



/**
 * Generate subscribe to unlock(content locker) meta box
 * 
 * @param mixed $post required the current post
 * @return string
 * @since 1.0
 */
function nl_content_lock_metaboxes( $post ) {
    wp_nonce_field( 'nl_locker_meta_box_nonce', 'meta_box_nonce' );
    $content_lock_checked = get_post_meta($post->ID, 'nl_content_lock_enable', true);
    ?>
    <p><label for="nl_content_lock_enable"><input type="checkbox" name="nl_content_lock_enable" id="nl_content_lock_enable" value="enable" <?php checked( $content_lock_checked, 'enable', true ); ?> /> <b><?php _e('Enable subscribe to unlock (content locker)', 'nl_signup'); ?></b></label></p>

    <p><label for="nl_file_upload"><b><?php echo __('File upload and lock:', 'nl_signup'); ?></b></label></p>
    <p><input class="large-text upload_link" type="text" name="nl_file_upload" id="nl_file_upload" value="<?php echo get_post_meta($post->ID, 'nl_file_upload', true); ?>" /> <input class="upload_data" id="performance_1_btn" type="button" value="<?php echo __('Upload', 'nl_signup'); ?>"></p>
    <?php
}


/**
 * Save subscribe to unlock(content locker) meta data
 * 
 * @param INT $post->ID required the current post id
 * @return string
 * @since 1.0
 */
add_action( 'save_post', 'nl_content_lock_meta_save' );

function nl_content_lock_meta_save( $post_id ) {
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'nl_locker_meta_box_nonce' ) ) return;
    
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post', $post_id ) ) return;

    // delete_post_meta($post_id, 'nl_content_lock_enable');
    if ( isset($_POST['nl_content_lock_enable']) ) {
        update_post_meta($post_id, 'nl_content_lock_enable', $_POST['nl_content_lock_enable']);
    } else {
        delete_post_meta($post_id, 'nl_content_lock_enable');
    }

    if( isset( $_POST['nl_file_upload'] ) )
        update_post_meta( $post_id, 'nl_file_upload', $_POST['nl_file_upload'] );
}



add_filter( 'the_content', 'my_excerpts', 20 );
/**
 * Add newslettersignup box of every post page.
 *
 * @uses is_single()
 */
function my_excerpts($content = false) {
    // If is the home page, an archive, or search results
    if(is_front_page() || is_archive() || is_search() || is_single() || is_page()) :
        global $post;
        // $content = $post->post_excerpt;
        $file = get_post_meta( $post->ID, 'nl_file_upload', true );
        $file_dl_link = '';
        if ($file!='') {
            $file_dl_link .= ' <a class="nl_file_dl_link" href="'.$file.'" target="_blank">'. __('Download', 'nl_signup') .'</a>';
        }
        $content = $post->post_content . $file_dl_link;
        $options = get_option( 'nl_option_name' );

        $nl_content_lock_enable = get_post_meta( $post->ID, 'nl_content_lock_enable', true );
        ob_start();
        session_start();

        if ($_GET['tester']=='nushan' && isset($_SESSION['nl_subscribe'])) {
            unset($_SESSION['nl_subscribe']);
            session_destroy();
        }

        if ($nl_content_lock_enable == 'enable' && !isset($_SESSION['nl_subscribe']) ) {
            $_SESSION['nl_content_lock_enable'] = 'enable';

            $content = $post->post_content;
            $excerpt_length = 55;
            $words = explode(' ', $content, $excerpt_length + 1);
            if(count($words) > $excerpt_length) :
                array_pop($words);
                array_push($words, ' <span class="content_unlocker_text">[' . __('subscribe to unlock', 'nl_signup') . ']</span>');
                $content = implode(' ', $words);
            endif;
            $content = $content . '[nl_mc_form class="nl_subscribe_form" id="nl_subscribe_box"]';
        }
        ob_clean();
    endif;

    // Make sure to return the content
    return do_shortcode( $content );
}


// end of nl signup