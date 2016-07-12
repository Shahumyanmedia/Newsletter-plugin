<?php
//add menu in admin left sidebar
function mail_menu() {

    add_menu_page('Sh Newsletter', 'Sh Newsletter', 'read', 'sh_newsletter', 'sh_newsletter_admin_content', plugins_url('sh-newsletter/sources/img/gmail.png'));
    add_submenu_page('sh_newsletter', 'Mail Preview', 'Mail Preview', 10, 'settings', 'sh_newsletter_settings_content');
}

if (is_admin()) {
    // add content in plugin admin page
    function sh_newsletter_admin_content() {
        require_once(SH_NEWSLETTER_DIR.'/includes/admin_main_content.php');
    }

    // add content in plugin settings page
    function sh_newsletter_settings_content() {


            require_once(SH_NEWSLETTER_DIR.'/includes/settings/settings_content.php');
    }
}

//add scripts and styles in admin page
function sender_my_plugin_scripts() {

    if (is_admin() && isset($_GET['page'])) {

        wp_enqueue_script('jquery');
        wp_enqueue_media();
        wp_register_style('custom_styles', plugins_url().'/sh-newsletter/sources/admin/style.css',  false, false, 'all');
        wp_enqueue_style('custom_styles');
        wp_register_style('custom_styles2', plugins_url().'/sh-newsletter/sources/admin/jquery.mCustomScrollbar.css',  false, false, 'all');
        wp_enqueue_style('custom_styles2');
        wp_register_script('custom_scripts', plugins_url().'/sh-newsletter/sources/admin/jquery.mCustomScrollbar.js', array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('custom_scripts');
        wp_register_script('custom_scripts2', plugins_url().'/sh-newsletter/sources/admin/ajaxupload.js', array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('custom_scripts2');
        wp_register_script('custom_scripts3', plugins_url().'/sh-newsletter/sources/admin/custom.js', array('jquery', 'jquery-ui-core'));
        wp_enqueue_script('custom_scripts3');
    }

}
add_action('admin_init', 'sender_my_plugin_scripts');
add_action('admin_menu', 'mail_menu');