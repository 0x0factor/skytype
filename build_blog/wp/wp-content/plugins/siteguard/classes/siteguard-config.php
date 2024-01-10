<?php

class SiteGuard_Config {
	protected $config;
	function __construct() {
		$this->config = get_option( 'siteguard_config' );
	}
	function set( $key, $value ) {
		$this->config[ $key ] = $value;
	}
	function get( $key ) {
		return isset( $this->config[ $key ] ) ? $this->config[ $key ] : '';
	}
	function update( ) {
		update_option( 'siteguard_config', $this->config );
	}
}
