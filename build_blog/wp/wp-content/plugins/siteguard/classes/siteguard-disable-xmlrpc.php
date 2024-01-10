<?php

class SiteGuard_Disable_XMLRPC extends SiteGuard_Base {
	public static $htaccess_mark = '#==== SITEGUARD_DISABLE_XMLRPC_SETTINGS';

	function __construct( ) {
	}
	static function get_mark( ) {
		return SiteGuard_Disable_XMLRPC::$htaccess_mark;
	}
	function init( ) {
		global $siteguard_config;
		$siteguard_config->set( 'disable_xmlrpc_enable', '0' );
		$siteguard_config->update( );
	}
	function update_settings( ) {
		global $siteguard_config;

		$htaccess_str = "<Files xmlrpc.php>\n";
		$htaccess_str .= "    Order allow,deny\n";
		$htaccess_str .= "    Deny from all \n";
		$htaccess_str .= "</Files>\n";

		return $htaccess_str;
	}
	function feature_on( ) {
		global $siteguard_htaccess;
		if ( false === SiteGuard_Htaccess::check_permission( ) ) {
			return false;
		}
		$data = $this->update_settings( );
		$mark = $this->get_mark( );
		return $siteguard_htaccess->update_settings( $mark, $data );
	}
	static function feature_off( ) {
		$mark = SiteGuard_Disable_XMLRPC::get_mark( );
		return SiteGuard_Htaccess::clear_settings( $mark );
	}
}
