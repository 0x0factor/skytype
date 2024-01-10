<?php

class SiteGuard_Disable_Pingback extends SiteGuard_Base {

	function __construct( ) {
		global $siteguard_config;
		if ( '1' == $siteguard_config->get( 'disable_pingback_enable' ) ) {
			add_filter( 'xmlrpc_methods', array( $this, 'handler_xmlrpc_methods' ) );
		}
	}
	function init( ) {
		global $siteguard_config;
		if ( true === siteguard_check_multisite( ) ) {
			$siteguard_config->set( 'disable_pingback_enable', '1' );
		} else {
			$siteguard_config->set( 'disable_pingback_enable', '0' );
		}
		$siteguard_config->update( );
	}
	function handler_xmlrpc_methods( $methods ) {
		unset( $methods['pingback.ping'] );
		unset( $methods['pingback.extensions.getPingbacks'] );
		return $methods;
	}
}
