<?php

class SiteGuard_LoginLock extends SiteGuard_Base {
	const SITEGUARD_FAIL_ONCE_ERROR_CODE = 'siteguard-fail-once';
	protected $status = SITEGUARD_LOGIN_FAILED;
	function __construct( ) {
		global $siteguard_config;
		if ( '1' == $siteguard_config->get( 'loginlock_enable' ) ) {
			add_action( 'wp_login_failed', array( $this, 'handler_wp_login_failed' ) );
			add_filter( 'authenticate', array( $this, 'handler_authenticate' ), 20, 3 );
		}
		if ( '1' == $siteguard_config->get( 'loginlock_fail_once' ) ) {
			add_filter( 'wp_authenticate_user', array( $this, 'handler_wp_authenticate_user' ), 99, 2 );
		}
	}
	function init( ) {
		global $siteguard_config;
		if ( true === siteguard_check_multisite( ) ) {
			$siteguard_config->set( 'loginlock_enable',     '1' );
		} else {
			$siteguard_config->set( 'loginlock_enable',     '0' );
		}
		$siteguard_config->set( 'loginlock_interval',   '5' );
		$siteguard_config->set( 'loginlock_threshold',  '3' );
		$siteguard_config->set( 'loginlock_locksec',    '60' );
		$siteguard_config->set( 'loginlock_fail_once',  '0' );
		$siteguard_config->set( 'fail_once_admin_only', '1' );
		$siteguard_config->update( );
	}
	function get_status( ) {
		return $this->status;
	}
	function handler_wp_login_failed( $username ) {
		global $wpdb, $siteguard_config, $siteguard_login_history;
		$table_name = $wpdb->prefix . SITEGUARD_TABLE_LOGIN;

		$ip_address = $_SERVER['REMOTE_ADDR'];

		$wpdb->query( 'START TRANSACTION' );
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE status <> %d AND last_login_time < SYSDATE() - INTERVAL 1 HOUR;", SITEGUARD_LOGIN_SUCCESS ) );
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT status, count, last_login_time from $table_name WHERE ip_address = %s", $ip_address ) );
		$data = array(
			'ip_address' => $ip_address,
			'status' => SITEGUARD_LOGIN_FAILED,
			'count' => 1,
			'last_login_time' => 0,
		);
		$now_str = current_time( 'mysql' );
		$now_bin = strtotime( $now_str );
		if ( null == $result ) {
			$data['last_login_time'] = $now_str;
			$wpdb->insert( $table_name, $data );
		} else {
			$data['last_login_time'] = $result->last_login_time;
			$interval = intval( $siteguard_config->get( 'loginlock_interval' ) );
			$limit = strtotime( $result->last_login_time ) + $interval;
			if ( SITEGUARD_LOGIN_SUCCESS == $result->status ) {
				$data['last_login_time'] = $now_str;
				$wpdb->update( $table_name, $data, array( 'ip_address' => $ip_address ) );
			} else if ( SITEGUARD_LOGIN_FAILED == $result->status ) {
				if ( $now_bin <= $limit ) {
					$data['count'] = $result->count + 1;
				} else {
					$data['count'] = 1;
					$data['last_login_time'] = $now_str;
				}
				if ( $data['count'] >= intval( $siteguard_config->get( 'loginlock_threshold' ) ) ) {
					$data['status'] = SITEGUARD_LOGIN_LOCKED;
					$data['last_login_time'] = $now_str;
					$this->status = SITEGUARD_LOGIN_LOCKED;
				}
				$wpdb->update( $table_name, $data, array( 'ip_address' => $ip_address ) );
			} else if ( SITEGUARD_LOGIN_FAIL_ONCE == $result->status || ( SITEGUARD_LOGIN_LOCKED == $result->status && $now_bin > strtotime( $result->last_login_time ) + intval( $siteguard_config->get( 'loginlock_locksec' ) ) ) ) {
				$data['status'] = SITEGUARD_LOGIN_FAILED;
				$data['count'] = 1;
				$data['last_login_time'] = $now_str;
				$wpdb->update( $table_name, $data, array( 'ip_address' => $ip_address ) );
			}
		}

		$wpdb->query( 'COMMIT' );

		return;
	}
	function is_locked( $ip_address ) {
		global $wpdb, $siteguard_config;

		$now_bin = strtotime( current_time( 'mysql' ) );
		$table_name = $wpdb->prefix . SITEGUARD_TABLE_LOGIN;
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT status, last_login_time from $table_name WHERE ip_address = %s", $ip_address ) );
		if ( null != $result ) {
			if ( SITEGUARD_LOGIN_LOCKED == $result->status && $now_bin <= strtotime( $result->last_login_time ) + intval( $siteguard_config->get( 'loginlock_locksec' ) ) ) {
				return true;
			}
		}
		return false;
	}
	function handler_authenticate( $user, $username, $password ) {
		if ( $this->is_locked( $_SERVER['REMOTE_ADDR'] ) ) {
				$new_errors = new WP_Error( );
				$new_errors->add( 'siteguard-error', esc_html__( 'ERROR: LOGIN LOCKED', 'siteguard' ) );
				$this->status = SITEGUARD_LOGIN_LOCKED;
				return $new_errors;
		}
		return $user;
	}
	function handler_login_shake( $shake_error_codes ) {
		$shake_error_codes[] = self::SITEGUARD_FAIL_ONCE_ERROR_CODE;
		return $shake_error_codes;
	}
	function handler_wp_authenticate_user( $user, $password ) {
		global $siteguard_login_history, $siteguard_config;

		if ( basename( $_SERVER['SCRIPT_NAME'] ) == 'xmlrpc.php' ) {
			return $user;
		}

		// Login failed
		if ( is_wp_error( $user ) ) {
			return $user;
		}
		if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
			return $user;
		}
		if ( '1' == $siteguard_config->get( 'fail_once_admin_only' ) ) {
			if ( ! $user->has_cap( 'administrator' ) ) {
				return $user;
			}
		}

		$user_login = $user->user_login;

		if ( ! $siteguard_login_history->is_exist( $user_login, SITEGUARD_LOGIN_FAIL_ONCE, 5/* secs after */, 60/* secs less */ ) ) {
			$this->status = SITEGUARD_LOGIN_FAIL_ONCE;

			$new_error = new WP_Error( );
			$new_error->add( self::SITEGUARD_FAIL_ONCE_ERROR_CODE, esc_html__( 'ERROR: Please login entry again', 'siteguard' ) );
			add_filter( 'shake_error_codes', array( $this, 'handler_login_shake' ) );
			return $new_error;
		}
		return $user;
	}
}
