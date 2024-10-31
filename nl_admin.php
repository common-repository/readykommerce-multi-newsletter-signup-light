<?php
/**
 * Copyright (c) 2014 readyKommcerce. All rights reserved.
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

/**
 * Settings for admin page
 * @package admin
 * @author readyKommerce
 * @see http://www.readykommerce.com
 * @since 1.0
 **/


class NL_SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    private $default_value = Array
        (
            'mc_api' => '',
            'form_title' => 'Subscribe our newsletter',
            'form_desc' => 'We will not sell your email address',
            'mc_list_id' => '',
            'form_name' => 'hide_name',
            'form_email_type' => 'text'
        );

    /**
     * Start up
     */
    public function __construct()
    {
        $value = get_option( 'nl_option_name' );
        if (empty($value) || $value=='') {
            update_option( 'nl_option_name', $this->default_value );
        }
        
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            __('readyKommerce Multiple Newsletter Signup - Light', 'nl_signup'), 
            __('readyKommerce Newsletter - Light', 'nl_signup'), 
            'manage_options', 
            'nl-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'nl_option_name' );
        ?>
        <div class="wrap nl_signup readykommerce">
            <?php //screen_icon(); ?>
            <div id="icon-readykommerce" class="icon32"><br></div>
            <h2><?php _e( 'readyKommerce Multiple Newsletter Signup - Light', 'nl_signup' ); ?></h2>
            <p class="offer"><a href='http://www.readykommerce.com/newsletter-plugin-for-mailchimp/'>Get full version for FREE!</a></p>
            <div id="st_promo_msg"></div>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'nl_option_group' );   
                do_settings_sections( 'nl-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'nl_option_group', // Option group
            'nl_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        // MailChimp API setup section
        $this->mc_api_settings_block();

        // MailChimp setup section
        $this->form_settings_block();

        // MailChimp setup section
        $this->form_additional_setup_block();

        // Video setup section
        $this->video_settings_section();

        // Additional setup section
        $this->additional_settings_block();
        
        // add styles to admin
        add_action( 'settings_page_nl-setting-admin', array($this, 'nl_signup_admin_styles') );
    }

    public function mc_api_settings_block()
    {
        // MailChimp API setup section
        add_settings_section(
            'mc_api_settings', // ID
            __('Set up your MailChimp', 'nl_signup'), // Title
            array( $this, 'api_section_info' ), // Callback
            'nl-setting-admin' // Page
        );

        add_settings_field(
            'mc_api', // ID
            __('Mailchimp API Key', 'nl_signup'), // Title 
            array( $this, 'mc_api_callback' ), // Callback
            'nl-setting-admin', // Page
            'mc_api_settings' // Section           
        );
        add_settings_field(
            'mc_list_id', 
            __('Default List Unique ID', 'nl_signup'), 
            array( $this, 'mc_list_id_callback' ), 
            'nl-setting-admin', 
            'mc_api_settings'
        );
    }

    public function form_settings_block()
    {
        add_settings_section(
            'form_field_settings', // ID
            __('Form Settings', 'nl_signup'). ' <span class="nl-help-wrap"><a href="http://en.wikipedia.org/wiki/Web_form" target="_blank" class="nl-help-text">What is form?</a></span>', // Title
            array( $this, 'form_setup_section_info' ), // Callback
            'nl-setting-admin' // Page
        );

        // form fields setup
        add_settings_field(
            'form_title', 
            __('Title', 'nl_signup'), 
            array( $this, 'form_title_callback' ), 
            'nl-setting-admin', 
            'form_field_settings'
        );

        add_settings_field(
            'form_desc', 
            __('Short Description', 'nl_signup'), 
            array( $this, 'form_desc_callback' ), 
            'nl-setting-admin', 
            'form_field_settings'
        );

        add_settings_field(
            'form_name', 
            __('Get Subscribers Name', 'nl_signup'), 
            array( $this, 'form_name_callback' ), 
            'nl-setting-admin', 
            'form_field_settings'
        );

        add_settings_field(
            'form_email_type', 
            __('Newsletter Email Type', 'nl_signup'), 
            array( $this, 'form_email_type_callback' ), 
            'nl-setting-admin', 
            'form_field_settings'
        );
    }

    public function form_additional_setup_block()
    {
        add_settings_section(
            'form_additional_settings', // ID
            __('Advanced Form Customizations', 'nl_signup'), // Title
            array( $this, 'form_setup_section_info' ), // Callback
            'nl-setting-admin' // Page
        );

        add_settings_field(
            'form_field_label', 
            __('Customized the form in many ways', 'nl_signup'), 
            array( $this, 'form_field_label_callback' ),
            'nl-setting-admin',
            'form_additional_settings'
        );
    }

    public function video_settings_section()
    {
        add_settings_section(
            'video_embed_settings', // ID
            __('Video Settings', 'nl_signup'), // Title
            array( $this, 'form_setup_section_info' ), // Callback
            'nl-setting-admin' // Page
        );

        add_settings_field(
            'video_embed', 
            __('Embed YouTube/Vimeo Video in Newsletter Signup Form', 'nl_signup'), 
            array( $this, 'video_embed_callback' ),
            'nl-setting-admin',
            'video_embed_settings'
        );
    }

    public function additional_settings_block()
    {
        // Aweber API setup section
        add_settings_section(
            'additional_settings_section', // ID
            __('Additional Settings', 'nl_signup'), // Title
            array( $this, 'api_section_info' ), // Callback
            'nl-setting-admin' // Page
        );
        add_settings_field(
            'additional_setup', // ID
            __('Advanced Configurations [Optional]', 'nl_signup'), // Title 
            array( $this, 'additional_setup_callback' ), // Callback
            'nl-setting-admin', // Page
            'additional_settings_section' // Section           
        );
    }

    public function nl_signup_admin_styles()
    {
        wp_register_style( 'nl_signup_admin_css', plugins_url('nl_signup_admin.css', __FILE__) );
        wp_enqueue_style( 'nl_signup_admin_css' );
        // wp_register_style( 'nl_admin_css', plugins_url('admin.css', __FILE__) );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['mc_api'] ) )
            $new_input['mc_api'] = sanitize_text_field( $input['mc_api'] );

        if( isset( $input['choose_api'] ) )
            $new_input['choose_api'] = sanitize_text_field( $input['choose_api'] );

        // mc setup
        if( isset( $input['form_title'] ) )
            $new_input['form_title'] = sanitize_text_field( $input['form_title'] );

        if( isset( $input['form_desc'] ) )
            $new_input['form_desc'] = sanitize_text_field( $input['form_desc'] );

        if( isset( $input['mc_list_id'] ) )
            $new_input['mc_list_id'] = sanitize_text_field( $input['mc_list_id'] );

        if( isset( $input['form_name'] ) )
            $new_input['form_name'] = sanitize_text_field( $input['form_name'] );

        if( isset( $input['form_email_type'] ) )
            $new_input['form_email_type'] = sanitize_text_field( $input['form_email_type'] );

        return $new_input;
        exit;
    }

    public function api_section_info()
    {
        // print __('API key will help this plugin to connect an account.', 'nl_signup');
    }

    public function form_setup_section_info()
    {
        // print __('Setup the newsletter signup/subscriber form for front-end.', 'nl_signup');
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function mc_api_callback()
    {
        printf(
            '<input type="text" id="mc_api" name="nl_option_name[mc_api]" value="%s" />',
            isset( $this->options['mc_api'] ) ? esc_attr( $this->options['mc_api']) : ''
        );
        echo '<div class="nl-admin-info input-info">'. __('Get your MailChimp API key from', 'nl_signup') . '<a href="https://us6.admin.mailchimp.com/account/api/">'. __('here', 'nl_signup') . '</a> '. __('or read', 'nl_signup') . ' <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key/">'. __('Where can I find my API Key?', 'nl_signup') . '</a>' .'</div>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function mc_list_id_callback()
    {
        printf(
            '<input type="text" id="mc_list_id" name="nl_option_name[mc_list_id]" value="%s" />',
            isset( $this->options['mc_list_id'] ) ? esc_attr( $this->options['mc_list_id']) : ''
        );
        echo '<div class="nl-admin-info input-info">'. __('Read', 'nl_signup') .' <a href="http://kb.mailchimp.com/article/how-can-i-find-my-list-id/">'. __('How can I find my List ID?', 'nl_signup') .'</a> '. __('to get your List ID', 'nl_signup') .'</div>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function form_title_callback()
    {
        printf(
            '<input type="text" id="form_title" name="nl_option_name[form_title]" value="%s" />',
            isset( $this->options['form_title'] ) ? esc_attr( $this->options['form_title']) : ''
        );
        echo '<div class="nl-admin-info input-info">'. __('Title is the header of the newsletter section.', 'nl_signup') .'<br>'. __('for example "Subscribe to our newsletter"', 'nl_signup') .'</div>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function form_desc_callback()
    {
        $value = isset( $this->options['form_desc'] ) ? esc_attr( $this->options['form_desc']) : '';
        echo '<textarea type="text" id="form_desc" name="nl_option_name[form_desc]" value="%s" cols="30" rows="3" />'.$value.'</textarea>';
        echo '<div class="nl-admin-info input-info">'. __('Below the title, you can put more details for your visitors.', 'nl_signup') .' '. __('for example "We will not sell your email address" or "Get our latest updates using email newsletter"', 'nl_signup') .'</div>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function form_name_callback()
    {
        $value = isset( $this->options['form_name'] ) ? esc_attr( $this->options['form_name']) : '';
        echo '<label><input type="radio" id="form_name" name="nl_option_name[form_name]" value="first_last_name" '.checked( $value, 'first_last_name', false ).' /> '. __('First and Last Name', 'nl_signup') .'</label>';
        echo "<br>";
        echo '<label><input type="radio" id="form_name" name="nl_option_name[form_name]" value="hide_name" '.checked( $value, 'hide_name', false ).' /> '. __('I don\' need Name', 'nl_signup') .'</label>';
        echo '<div class="nl-admin-info input-info">'. __('Choose if you need the subscriber name or not.', 'nl_signup') .'</div>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function form_email_type_callback()
    {
        $value = isset( $this->options['form_email_type'] ) ? esc_attr( $this->options['form_email_type']) : '';
        echo '<select name="nl_option_name[form_email_type]"><option value="html" ' . selected( $value, 'html', false ) . '>'. __('HTML', 'nl_signup') .'</option><option value="text" ' . selected( $value, 'text', false ) . '>'. __('Plain Text', 'nl_signup') .'</option></select>';
        echo "<br>";
        echo '<div class="nl-admin-info input-info">'. __('Do you want to send a formatted, nice looking newsletter? Choose the HTML type  from the dropdown menu. Otherwise, Choose the Plain Text option.', 'nl_signup') .'</div>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function form_field_label_callback()
    {
        echo "Please download the full version for FREE from <a href='http://www.readykommerce.com/newsletter-plugin-for-mailchimp/'>readyKommerce Newsletter Plugin for MailChimp</a>";
    }

    public function video_embed_callback()
    {
        
        echo "Please download the full version for FREE from <a href='http://www.readykommerce.com/newsletter-plugin-for-mailchimp/'>readyKommerce Newsletter Plugin for MailChimp</a> to use this feature";
    }

    public function additional_setup_callback()
    {
        echo "Please download the full version for FREE from <a href='http://www.readykommerce.com/newsletter-plugin-for-mailchimp/'>readyKommerce Newsletter Plugin for MailChimp</a>";
    }
 
    function get_self(){
        return admin_url( 'options-general.php?page=nl-setting-admin' );
        // return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
} // END NL_SettingsPage class 