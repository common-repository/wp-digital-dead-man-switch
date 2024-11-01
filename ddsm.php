<?php
/**
 * Plugin Name: Digital Deads Man Switch (DDSM)
 * Plugin URI: http://DDSM.digitaldeathguide.net/
 * Description: This plugin will send a text of your choice to an email address(es) in the unfortunate case in which you lose ability to access your blog, due to disability or, I hope not, untimely death. Useful for sending your person of choice your blog password, domain password, web host's control panel password, your e-mail password, etc. -- and to avoid your digital legacy being lost. .
 * Version: 0.1
 * Author: DigitalDeathGuide
 * Author URI: http://digitaldeathguide.com
 * License: GPL2
 * Text Domain: DDSM
 * Domain Path: /lang/
 */

/**
 * Copyright 2015  E.C.
 * Based on the Next of Kin plugin, by Tzafrir Rehan, with is kind go-ahead nod  (email : tzafrir@tzafrir.net)
 */


if ( ! defined( 'ABSPATH' )  ) {
    exit; // Exit if accessed directly
}

// plugin constants and vars
define("DDSM_WEEK", 604800);
define("DDSM_TWOHOURS", 7200);



/**
 * Wrapper around wp_mail() to send emails
 */
function ddsm_send_mail($from, $fromemail, $to, $subject, $message) {
    $subject = '[' . $from . '] ' . $subject;
    $charset = get_settings('blog_charset');
    $headers  = "From: \"{$from}\" <{$fromemail}>\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
    return wp_mail($to, $subject, $message, $headers);
}

/**
 * Text domain load
 */
function DDSM_load_textdomain() {
    load_plugin_textdomain( 'DDSM', false, dirname( plugin_basename(__FILE__) ) . '/lang' );
}

/**
 * Change default page to what is configured in the backend
 */
function DDSM_set_default_page(){
    $ddsm_page_default = get_option('ddsm_page_default');
    
    if (!empty($ddsm_page_default)) {
        // set page as published
        $current_post = get_post( $ddsm_page_default, 'ARRAY_A' );
        $current_post['post_status'] = 'publish';
        wp_update_post($current_post);

        // set page as homepage
        update_option( 'page_on_front', $ddsm_page_default );
        update_option( 'show_on_front', 'page' );
    }
}

/**
 * Add WP Dashboard menu item
 */
function DDSM_menu() {

    // register settings
    add_action( 'admin_init', 'ddsm_register_settings' );

    // function that calls the option menu function
    if (function_exists('add_options_page')) {
        add_options_page(__('Digital Deads Man Switch (DDSM) Options', 'DDSM'), __('Digital Deads Man Switch (DDSM)', 'DDSM'), 8, basename(__FILE__), 'DDSM_menupage');
    }
}

/**
 * Content for the backend plugin page
 */
function DDSM_menupage() {
    //the options menu
    global $current_user;
    
    wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ) . 'admin/css/ddsm-admin.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_css' );

    wp_register_style( 'custom_wp_admin_tabs', plugin_dir_url( __FILE__ ) . 'admin/css/jquery-tabs/jquery-ui-tabs.min.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_tabs' );
    
    require_once("admin/ddsm-admin.php");

}

/**
 * Standard plugin settings
 */
function ddsm_register_settings(){
    register_setting( 'ddsm-settings-group', 'ddsm_page_default' );
}


/**
 * Activation action
 */
function DDSM_activate() {
    //Create option in database
    global $current_user;
    //Last Login
    add_option('tz_DDSM_ll_'.$current_user->user_login, time(), $current_user->user_login.'\'s last login time', 'yes');
    //Last Iteration
    add_option('tz_DDSM_lastiteration', (time() - DDSM_TWOHOURS));
}

/**
 * Plugin init, to run on every WP load
 */
function DDSM_init() {
    global $current_user;	//but only if
    if (get_option('tz_DDSM_active_'.$current_user->user_login) == true 
        && (time() - get_option('tz_DDSM_ll_'.$current_user->user_login)) > 43200) {
        //the plugin is activated in own menu per current user (not just plugins menu)
        //and writes the current time as last login time if 12 hours passed since last write
        update_option('tz_DDSM_ll_'.$current_user->user_login, time());
    }
}



// #######################



// actions
add_action('plugins_loaded', 'DDSM_load_textdomain');
if (is_admin()) add_action('admin_menu', 'DDSM_menu');
add_action('activate_DDSM/DDSM.php', 'DDSM_activate');
add_action('init', 'DDSM_init');




// Here is the actual work of the plugin:
//seconds in a week
if ((time() - get_option('tz_DDSM_lastiteration')) > DDSM_TWOHOURS) {
    // The following is a bit heavy, so we'll only do it once every two hours
    $ddg_ddsm_users = get_option('tz_DDSM_users');// get users list
    if (is_array($ddg_ddsm_users)) {
        foreach ($ddg_ddsm_users as $user) {
            if ($user != '') {
                $cur_options = get_option('tz_DDSM_options_'.$user);
                $cur_optionsm = get_option('tz_DDSM_optionsm_'.$user);
                if ((time() - get_option('tz_DDSM_ll_'.$user)) > ($cur_options['interval1'] * DDSM_WEEK) 
                    && get_option('tz_DDSM_step1_'.$user) != true) {
                    // After interval 1
                    if ($cur_optionsm['email1'] != '') { ddsm_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email'], $cur_optionsm['subject1'], $cur_optionsm['email1']);}
                    update_option('tz_DDSM_step1_'.$user, true);
                }
                if ((time() - get_option('tz_DDSM_ll_'.$user)) > (($cur_options['interval1'] + $cur_options['interval2']) * DDSM_WEEK)
                    && get_option('tz_DDSM_step2_'.$user) != true) {
                    // After interval 2
                    if ($cur_optionsm['email2'] != '') { ddsm_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email'], $cur_optionsm['subject2'], $cur_optionsm['email2']);}
                    if ($cur_optionsm['email3'] != '') { ddsm_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email_other'], $cur_optionsm['subject3'], $cur_optionsm['email3']);}
                    update_option('tz_DDSM_step2_'.$user, true);
                }
                if ((time() - get_option('tz_DDSM_ll_'.$user)) > (($cur_options['interval1'] + $cur_options['interval2'] + $cur_options['interval3']) * DDSM_WEEK)
                    && get_option('tz_DDSM_step3_'.$user) != true) {
                    // After interval 3 (Death)
                    if ($cur_optionsm['email4'] != '') {
                        DDSM_set_default_page(); // set configured homepage
                        ddsm_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email_other'], $cur_optionsm['subject4'], $cur_optionsm['email4']);
                    }
                    update_option('tz_DDSM_step3_'.$user, true);
                }
            }
        }
        update_option('tz_DDSM_lastiteration', time());
    }
}





?>
