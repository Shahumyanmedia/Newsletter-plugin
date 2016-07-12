<?php
/*
  * Plugin Name: SH Newsletter
  * Description: Subscribe newsletter
  * Version: 1.0
  * Author: Shahumyan Media
  * Author URI: http://smusa.net
  * Plugin URI: http://x.shahum.net
  *
*/

/*plugin directory*/
define('SH_NEWSLETTER_DIR', plugin_dir_path(__FILE__));
/*plugin start*/
function sh_newsletter_load()
{
    /*unsubscribing*/
    if ($_GET['key'] && strlen($_GET['key']) == 32) {
        global $wpdb;
        $table_name_del = $wpdb->prefix . "sh_newsletter";
        $user_del = $wpdb->get_results(
            "
            SELECT *
            FROM $table_name_del WHERE keygen = '" . $_GET['key'] . "'
            "
        );
        if (sizeof($user_del) > 0) {
            echo '<div style="display:none" id="shKey" data-secret-key="' . $_GET['key'] . '"></div>';

            function pluginResourcesPop()
            {
                echo '<link rel="stylesheet" href="' . plugins_url() . '/sh-newsletter/sources/popup/stylePop.css" type="text/css">';
                echo '<script defer type="text/javascript" src="' . plugins_url() . '/sh-newsletter/sources/popup/customPop.js"></script>';
            }

            add_action('wp_footer', 'pluginResourcesPop');
        } else {
            header('Location:' . get_home_url());
            exit;
        }
    } elseif ($_GET['key'] && strlen($_GET['key']) !== 32) {
        header('Location:' . get_home_url());
        exit;
    }


    /*admin pages*/
    if (is_admin()) { // including file for admin
        require_once(SH_NEWSLETTER_DIR . '/includes/admin_Sh.php');
    }
    require_once(SH_NEWSLETTER_DIR . '/includes/core.php');
    function get_newsletter()
    {
        require_once(SH_NEWSLETTER_DIR . '/includes/frontend/form.php');
    }

    function pluginResources()
    {
        echo '<link rel="stylesheet" href="' . plugins_url() . '/sh-newsletter/sources/css/style.css" type="text/css">';
        echo '<script defer type="text/javascript" src="' . plugins_url() . '/sh-newsletter/sources/js/sh_custom.js"></script>';
    }

    add_action('wp_footer', 'pluginResources');
}
sh_newsletter_load();

/* plugin activation  and deactivation actions */
register_activation_hook(__FILE__, ['SH_new', 'sh_newsletter_activation'] );
register_deactivation_hook(__FILE__, ['SH_new', 'sh_newsletter_activation']);





