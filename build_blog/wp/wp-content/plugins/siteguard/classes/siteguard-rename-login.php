<?php

class SiteGuard_RenameLogin extends SiteGuard_Base {
	protected static $incompatible_plugins = array(
		'WordPress HTTPS (SSL)' => 'wordpress-https/wordpress-https.php',
		'qTranslate X' => 'qtranslate-x/qtranslate.php',
	 );
	public static $htaccess_mark = '#==== SITEGUARD_RENAME_LOGIN_SETTINGS';

	function __construct( ) {
		global $siteguard_config;
		if ( '1' == $siteguard_config->get( 'renamelogin_enable' ) ) {
			if ( null !== $this->get_active_incompatible_plugins( ) ) {
				$siteguard_config->set( 'renamelogin_enable', '0' );
				$siteguard_config->update( );
				$this->feature_off( );
				return;
			}
			$this->add_filter( );
		}
	}
	static function get_mark( ) {
		return SiteGuard_RenameLogin::$htaccess_mark;
	}
	function init( ) {
		global $siteguard_config;
		$siteguard_config->set( 'renamelogin_path', 'login_' . sprintf( '%05d', mt_rand( 1, 99999 ) ) );
		$siteguard_config->update();
		if ( $this->check_module( 'rewrite' ) && null === $this->get_active_incompatible_plugins( ) && true === siteguard_check_multisite( ) ) {
			$siteguard_config->set( 'renamelogin_enable', '1' );
			$siteguard_config->update( );
			if ( false === $this->feature_on( ) ) {
				$siteguard_config->set( 'renamelogin_enable', '0' );
				$siteguard_config->update( );
			}
		} else {
			$siteguard_config->set( 'renamelogin_enable', '0' );
			$siteguard_config->update( );
		}
	}
	function get_active_incompatible_plugins( ) {
		$result = array();
		foreach ( self::$incompatible_plugins as $name => $path ) {
			if ( $this->is_active_plugin( $path ) ) {
				array_push( $result, $name );
			}
		}
		if ( empty( $result ) ) {
			return null;
		} else {
			return $result;
		}
	}
	function add_filter( ) {
		add_filter( 'login_init',       array( $this, 'handler_login_init' ),  10, 2 );
		add_filter( 'site_url',         array( $this, 'handler_site_url' ),    10, 2 );
		add_filter( 'network_site_url', array( $this, 'handler_site_url' ),    10, 2 );
		add_filter( 'wp_redirect',      array( $this, 'handler_wp_redirect' ), 10, 2 );
		add_filter( 'register',         array( $this, 'handler_register' ) );
		remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
	}
	function handler_login_init( ) {
		global $siteguard_config;
		$new_login_page = $siteguard_config->get( 'renamelogin_path' );
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$link = $_SERVER['REQUEST_URI'];
		} else {
			$link = '';
		}
		if ( false !== strpos( $link, 'wp-login' ) ) {
			$referer = wp_get_referer( );
			if ( false === strpos( $referer, $new_login_page ) ) {
				$this->set_404( );
			} else {
				$result = $this->convert_url( $link );
				wp_redirect( $result );
			}
		}
	}
	function convert_url( $link ) {
		global $siteguard_config;
		$custom_login_url = $siteguard_config->get( 'renamelogin_path' );
		if ( false !== strpos( $link, 'wp-login.php' ) ) {
			$result = str_replace( 'wp-login.php', $custom_login_url, $link );
		} else {
			$result = $link;
		}
		return $result;
	}
	function handler_site_url( $link ) {
		$result = $this->convert_url( $link );
		return $result;
	}
	function handler_register( $link ) {
		$result = $this->convert_url( $link );
		return $result;
	}
	function handler_wp_redirect( $link, $status_code ) {
		$result = $this->convert_url( $link );
		return $result;
	}
	function insert_rewrite_rules( $rules ) {
		global $siteguard_config;
		$custom_login_url = $siteguard_config->get( 'renamelogin_path' );
		$newrules = array();
		$newrules[ $custom_login_url.'(.*)$' ] = 'wp-login.php$1';
		return $newrules + $rules;
	}
	function update_settings( ) {
		global $siteguard_config;
		$custom_login_url = $siteguard_config->get( 'renamelogin_path' );
		$parse_url = parse_url( site_url( ) );
		if ( false == $parse_url ) {
			$base = '/';
		} else {
			if ( isset( $parse_url['path'] ) ) {
				$base = $parse_url['path'] . '/';
			} else {
				$base = '/';
			}
		}

		$htaccess_str = "<IfModule mod_rewrite.c>\n";
		$htaccess_str .= "    RewriteEngine on\n";
		$htaccess_str .= "    RewriteBase $base\n";
		$htaccess_str .= "    RewriteRule ^wp-signup\.php 404-siteguard [L]\n";
		$htaccess_str .= "    RewriteRule ^wp-activate\.php 404-siteguard [L]\n";
		$htaccess_str .= "    RewriteRule ^$custom_login_url(.*)$ wp-login.php$1 [L]\n";
		$htaccess_str .= "</IfModule>\n";

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
		$mark = SiteGuard_RenameLogin::get_mark( );
		return SiteGuard_Htaccess::clear_settings( $mark );
	}
	function set_404( ) {
		global $wp_query;
		status_header( 404 );
		$wp_query->set_404( );
		if ( ( ( $template = get_404_template( ) ) || ( $template = get_index_template( ) ) )
		&& ( $template = apply_filters( 'template_include', $template ) ) ) {
			include( $template );
		}
		die;
	}
	function send_notify( ) {
		global $siteguard_config;
		$subject = esc_html__( 'WordPress: Login page URL was changed', 'siteguard' );
		$body    = sprintf( esc_html__( "Please bookmark following of the new login URL.\n\n%s\n\n--\nSiteGuard WP Plugin", 'siteguard' ), site_url( ) . '/' . $siteguard_config->get( 'renamelogin_path' ) );

		$user_query = new WP_User_Query( array( 'role' => 'Administrator' ) );
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$user_email = $user->get( 'user_email' );
				if ( true !== @wp_mail( $user_email, $subject, $body ) ) {
					siteguard_error_log( 'Failed send mail. To:' . $user_email . ' Subject:' . esc_html( $subject ) );
				}
			}
		}
	}
}
