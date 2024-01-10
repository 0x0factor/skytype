<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit ();
}

function delete_siteguard_plugin( ) {
	global $wpdb;

	delete_option( 'siteguard_config' );

	$table_name = $wpdb->prefix .  'siteguard_login';
	$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );

	$table_name = $wpdb->prefix .  'siteguard_history';
	$wpdb->query( "DROP TABLE IF EXISTS $table_name;" );
}

delete_siteguard_plugin( );
