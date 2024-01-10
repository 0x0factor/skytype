<?php

class SiteGuard_LoginHistory extends SiteGuard_Base {

	function __construct( ) {
		define( 'SITEGUARD_TABLE_HISTORY', 'siteguard_history' );
		add_action( 'wp_login', array( $this, 'handler_wp_login' ), 1, 2 );
		add_action( 'wp_login_failed', array( $this, 'handler_wp_login_failed' ), 30 );
		add_action( 'xmlrpc_call', array( $this, 'handler_xmlrpc_call' ), 10, 1 );
	}
	function init( ) {
		global $wpdb;
		# operation
		#  0: Login failure
		#  1: Login success
		#  2: Fail once
		#  3: Login lock
		# type
		#  0: login page
		#  1: xmlrpc
		$table_name = $wpdb->prefix . SITEGUARD_TABLE_HISTORY;
		$sql = "CREATE TABLE $table_name  (
		  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  login_name VARCHAR(40) NOT NULL DEFAULT '',
		  ip_address VARCHAR(40) NOT NULL DEFAULT '',
		  operation INT NOT NULL DEFAULT 0,
		  time datetime,
		  type INT NOT NULL DEFAULT 0,
		  UNIQUE KEY id (id)
		  )
		  CHARACTER SET 'utf8';";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );
	}
	function get_type( ) {
		$type = SITEGUARD_LOGIN_TYPE_NORMAL;
		if ( basename( $_SERVER['SCRIPT_NAME'] ) == 'xmlrpc.php' ) {
			$type = SITEGUARD_LOGIN_TYPE_XMLRPC;
		}
		return $type;
	}
	function check_operation( $operation ) {
		$items = array( SITEGUARD_LOGIN_SUCCESS, SITEGUARD_LOGIN_FAILED, SITEGUARD_LOGIN_FAIL_ONCE, SITEGUARD_LOGIN_LOCKED );
		if ( in_array( $operation, $items ) ) {
			return true;
		}
		return false;
	}
	function check_type( $type ) {
		$items = array( SITEGUARD_LOGIN_TYPE_NORMAL, SITEGUARD_LOGIN_TYPE_XMLRPC );
		if ( in_array( $type, $items ) ) {
			return true;
		}
		return false;
	}
	function handler_wp_login( $login, $current_user ) {

		if ( '' == $current_user->user_login ) {
			return;
		}
		$this->add_operation( SITEGUARD_LOGIN_SUCCESS, $current_user->user_login, $this->get_type( ) );
	}
	function handler_wp_login_failed( $username ) {
		global $siteguard_loginlock;
		$this->add_operation( $siteguard_loginlock->get_status( ), $username, $this->get_type( ) );
	}
	function handler_xmlrpc_call( $method ) {
		$current_user = wp_get_current_user( );
		if ( '' == $current_user->user_login ) {
			return;
		}
		$this->add_operation( SITEGUARD_LOGIN_SUCCESS, $current_user->user_login, SITEGUARD_LOGIN_TYPE_XMLRPC );
	}
	function is_exist( $user, $operation, $after_sec, $less_sec ) {
		global $wpdb;

		if ( $after_sec > $less_sec ) {
			return false;
		}

		$table_name = $wpdb->prefix . SITEGUARD_TABLE_HISTORY;
		$ip_address = $_SERVER['REMOTE_ADDR'];
		$now = current_time( 'mysql' );
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_name WHERE ip_address = %s AND login_name = %s AND operation = %d AND time BETWEEN %s - INTERVAL %d SECOND AND %s - INTERVAL %d SECOND; ", $ip_address, $user, $operation, $now, $less_sec, $now, $after_sec ) );
		if ( null == $id ) {
			return false;
		}
		return true;
	}
	function add_operation( $operation, $user_login, $type = SITEGUARD_LOGIN_TYPE_NORMAL ) {
		global $current_user;
		global $wpdb;

		if ( '' != $user_login ) {
			$user = $user_login;
		} else {
			get_currentuserinfo();
			$user = $current_user->user_login;
		}
		$table_name = $wpdb->prefix . SITEGUARD_TABLE_HISTORY;

		$wpdb->query( 'START TRANSACTION' );
		// delete old event
		$id = $wpdb->get_var( "SELECT id FROM $table_name ORDER BY id DESC LIMIT 9999,1;", 0, 0 );
		if ( null != $id ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id <= %d;", $id ) );
		}
		$ip_address = $_SERVER['REMOTE_ADDR'];
		$data = array(
			'operation'  => $operation,
			'login_name' => $user,
			'ip_address' => $ip_address,
			'time'       => current_time( 'mysql' ),
			'type'       => $type,
		);
		$wpdb->insert( $table_name, $data );

		$wpdb->query( 'COMMIT' );
	}
	static function convert_operation( $operation ) {
		$result = '';
		switch ( $operation ) {
			case SITEGUARD_LOGIN_FAILED:
				$result = esc_html__( 'Failed', 'siteguard' );
				break;
			case SITEGUARD_LOGIN_SUCCESS:
				$result = esc_html__( 'Success', 'siteguard' );
				break;
			case SITEGUARD_LOGIN_FAIL_ONCE:
				$result = esc_html__( 'Fail once', 'siteguard' );
				break;
			case SITEGUARD_LOGIN_LOCKED:
				$result = esc_html__( 'Locked', 'siteguard' );
				break;
			default:
				$result = esc_html__( 'Unknown', 'siteguard' );
		}
		return $result;
	}
	static function convert_type( $type ) {
		$result = '';
		switch ( $type ) {
			case SITEGUARD_LOGIN_TYPE_NORMAL:
				$result = esc_html__( 'Login Page', 'siteguard' );
				break;
			case SITEGUARD_LOGIN_TYPE_XMLRPC:
				$result = esc_html__( 'XMLRPC', 'siteguard' );
				break;
			default:
				$result = esc_html__( 'Unknown', 'siteguard' );
		}
		return $result;
	}
	function get_history( $operation, $login_name, $ip_address, $type, $login_name_not, $ip_address_not ) {
		global $wpdb;
		$where = '';
		$values = array( );
		if ( true === $this->check_operation( $operation ) ) {
			$where = 'operation = %d';
			array_push( $values, $operation );
		}
		if ( ! empty( $login_name ) ) {
			if ( ! empty( $where ) ) {
				$where .= ' and ';
			}
			if ( true === $login_name_not ) {
				$where .= 'login_name <> %s';
			} else {
				$where .= 'login_name = %s';
			}
			array_push( $values, $login_name );
		}
		if ( ! empty( $ip_address ) ) {
			if ( ! empty( $where ) ) {
				$where .= ' and ';
			}
			if ( true === $ip_address_not ) {
				$where .= 'ip_address <> %s';
			} else {
				$where .= 'ip_address = %s';
			}
			array_push( $values, $ip_address );
			
		}
		if ( true === $this->check_type( $type ) ) {
			if ( ! empty( $where ) ) {
				$where .= ' and ';
			}
			$where .= 'type = %d';
			array_push( $values, $type );
		}
		if ( ! empty( $where ) ) {
			$where = 'WHERE ' . $where;
		} else {
			$where = "WHERE operation >= %d";
			array_push( $values, '0' );
		}
		$table_name = $wpdb->prefix . SITEGUARD_TABLE_HISTORY;
		$prepare = array( );
		$prepare[] = "SELECT id, operation, login_name, ip_address, time, type FROM $table_name $where";
		foreach ( $values as $v ) {
			$prepare[] = $v;
		}
		$results = $wpdb->get_results( call_user_func_array( array( $wpdb, 'prepare' ), $prepare ), ARRAY_A );
		return $results;
	}
}
