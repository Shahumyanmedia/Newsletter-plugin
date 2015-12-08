<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$option_name = 'plugin_option_name';

delete_option( $option_name );

// For site options in multisite
delete_site_option( $option_name );

//drop a custom db table
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sh_newsletter_settings" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sh_newsletter_show" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sh_newsletter" );


?>