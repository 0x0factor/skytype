<?php

define( 'SITEGUARD_WAF_EXCLUDE_RULE', 'waf_exclude_rule' );

class SiteGuard_WAF_Exclude_Rule extends SiteGuard_Base {
	const HTACCESS_MARK = '#==== SITEGUARD_SG_WHITE_LIST_SETTINGS';

	function __construct( ) {
	}
	static function get_mark( ) {
		return self::HTACCESS_MARK;
	}
	function init( ) {
		global $siteguard_config;
		$siteguard_config->set( 'waf_exclude_rule_enable', '0' );
		$this->clear_rules( );
		$siteguard_config->update( );
	}
	function get_enable( ) {
		global $siteguard_config;
		$enable = $siteguard_config->get( 'waf_exclude_rule_enable' );
		return $enable;
	}
	function set_enable( $enable ) {
		global $siteguard_config;
		$values = array( '0', '1' );
		if ( ! in_array( $enable, $values ) ) {
			return false;
		}
		$siteguard_config->set( 'waf_exclude_rule_enable', $enable );
		$siteguard_config->update( );
		return true;
	}
	function cvt_exclude( $exclude ) {
		return str_replace( ',', '|', $exclude );
	}
	function get_max_id( $rules ) {
		$result = 0;
		if ( ! is_array( $rules ) ) {
			return $result;
		}
		foreach ( $rules as $rule ) {
			if ( isset( $rule['ID'] ) && $result < $rule['ID'] ) {
				$result = $rule['ID'];
			}
		}
		return $result;
	}
	function input_check( $id, $filename, &$sig, $comment ) {
		$errors = new WP_Error( );
		if ( ! is_numeric( $id ) ) {
			$errors->add( 'white_list_error', esc_html__( 'ERROR: Invalid input value.', 'siteguard' ) );
		}
		if ( empty( $sig ) ) {
			$errors->add( 'white_list_error', esc_html__( 'ERROR: Signature is required', 'siteguard' ) );
		} else {
			$tmp_sig = str_ireplace( 'SiteGuard_User_ExcludeSig ', '', $sig );
			$tmp_sig = str_replace( ' ', '', $tmp_sig );
			$tmp_sig = preg_replace( "/\r\n(\r\n)+/", "\r\n", $tmp_sig );
			$tmp_sig = preg_replace( "/\r\n$/", '', $tmp_sig );
			$tmp_sig = preg_replace( "/\n\n+/", "\n", $tmp_sig );
			$tmp_sig = preg_replace( "/\n$/", '', $tmp_sig );
			if ( 1 != preg_match( '/^[a-zA-Z0-9-\r\n]+$/', $tmp_sig ) ) {
				$errors->add( 'white_list_error', esc_html__( 'ERROR: Syntax Error in Signature', 'siteguard' ) );
			} else {
				$sig = $tmp_sig;
			}
		}

		if ( count( $errors->errors ) > 0 ) {
			return $errors;
		}
		return true;
	}
	function add_rule( $filename, $sig, $comment ) {
		global $siteguard_config;

		// check
		$errors = $this->input_check( 1, $filename, $sig, $comment );
		if ( is_wp_error( $errors ) ) {
			return $errors;
		}
		$sig = str_ireplace( 'SiteGuard_User_ExcludeSig', '', $sig );
		$sig = str_replace( ' ', '', $sig );
		$rules = $siteguard_config->get( SITEGUARD_WAF_EXCLUDE_RULE );
		$rule = array(
			'ID' => $this->get_max_id( $rules ) + 1,
			'filename' => $filename,
			'sig' => $sig,
			'comment' => $comment,
		);
		if ( ! is_array( $rules ) ) {
			$rules = (array) $rules;
		}
		array_push( $rules, $rule );
		$siteguard_config->set( SITEGUARD_WAF_EXCLUDE_RULE, $rules );
		$siteguard_config->update( );
		return true;
	}
	function clear_rules( ) {
		global $siteguard_config;
		$empty = array();
		$siteguard_config->set( SITEGUARD_WAF_EXCLUDE_RULE, $empty );
		$siteguard_config->update( );
	}
	function get_rules( ) {
		global $siteguard_config;
		$rules = $siteguard_config->get( SITEGUARD_WAF_EXCLUDE_RULE );
		return $rules;
	}
	function get_rule( $id, &$offset ) {
		global $siteguard_config;
		$rules = $siteguard_config->get( SITEGUARD_WAF_EXCLUDE_RULE );
		$idx = 0;
		foreach ( $rules as $rule ) {
			if ( isset( $rule['ID'] ) && $rule['ID'] == $id ) {
				$offset = $idx;
				return $rule;
			}
			$idx ++;
		}
		$offset = -1;
		return false;
	}
	function delete_rule( $ids ) {
		global $siteguard_config;
		$rules = $siteguard_config->get( SITEGUARD_WAF_EXCLUDE_RULE );
		if ( ! is_array( $ids ) ) {
			$ids = (array) $ids;
		}
		foreach ( $ids as $id ) {
			$offset = 0;
			$rule = $this->get_rule( $id, $offset );
			if ( false === $rule ) {
				continue;
			}
			array_splice( $rules, $offset, 1 );
			$siteguard_config->set( SITEGUARD_WAF_EXCLUDE_RULE, $rules );
		}
		$siteguard_config->update( );
		return true;
	}
	function update_rule_itr( $new_rule ) {
		global $siteguard_config;
		$errors = new WP_Error();

		$rules = $siteguard_config->get( SITEGUARD_WAF_EXCLUDE_RULE );
		if ( isset( $new_rule['ID'] ) ) {
			$id = $new_rule['ID'];
		} else {
			$errors->add( 'white_list_error', esc_html__( 'ERROR: Invalid input value.', 'siteguard' ) );
			return $errors;
		}
		$offset = 0;
		$rule = $this->get_rule( $id, $offset );
		if ( false === $rule ) {
			$errors->add( 'white_list_error', esc_html__( 'ERROR: Invalid input value.', 'siteguard' ) );
			return $errors;
		}
		array_splice( $rules, $offset, 1, array( $new_rule ) );
		$siteguard_config->set( SITEGUARD_WAF_EXCLUDE_RULE, $rules );
		$siteguard_config->update( );
		return true;
	}
	function update_rule( $id, $filename, $sig, $comment ) {
		// check
		$errors = $this->input_check( $id, $filename, $sig, $comment );
		if ( is_wp_error( $errors ) ) {
			return $errors;
		}

		$new_rule = array(
			'ID' => (int) $id,
			'filename' => $filename,
			'sig' => $sig,
			'comment' => $comment,
		);
		return $this->update_rule_itr( $new_rule );
	}
	function cvt_csrf2comma( $signatures ) {
		$result = preg_replace( "/(\r\n)+/", "\r\n", $signatures );
		$result = str_replace( "\r\n", ',', $result );
		$result = str_replace( "\r\n", ',', $result );
		$result = str_replace( "\r", ',', $result );
		$result = str_replace( "\n", ',', $result );
		return $result;
	}
	// for SiteGuard Lite Ver1.x
	function output_exclude_sig_1( $sig_str ) {
		$result = '';
		$csv = $this->cvt_csrf2comma( $sig_str );
		$sigs = preg_split( '/,/', $csv );
		foreach ( $sigs as $sig ) {
			$sig = str_replace( ' ', '', $sig );
			if ( strlen( $sig ) > 0 ) {
				$result .= '        SiteGuard_User_ExcludeSig '. $sig . "\n";
			}
		}
		return $result;
	}
	// for SiteGuard Lite Ver2.x
	function output_exclude_sig_2( $sig_str ) {
		return '        SiteGuard_User_ExcludeSig '. $this->cvt_csrf2comma( $sig_str ) . "\n";
	}
	function update_settings( ) {
		global $siteguard_config;
		$htaccess_str = '';
		$rules = $siteguard_config->get( SITEGUARD_WAF_EXCLUDE_RULE );
		if ( '' === $rules ) {
			return;
		}

		$htaccess_str .= "<IfModule mod_siteguard.c>\n";
		foreach ( $rules as $rule ) {
			if ( isset( $rule['filename'] ) && isset( $rule['sig'] ) ) {
				$filename = $rule['filename'];
				$sig    = $rule['sig'];
				if ( ! empty( $filename ) ) {
					$htaccess_str .= "    <Files $filename >\n";
					$htaccess_str .= $this->output_exclude_sig_1( $sig );
					$htaccess_str .= "    </Files>\n";
				} else {
					$htaccess_str .= $this->output_exclude_sig_1( $sig );
				}
			}
		}
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
		$mark = SiteGuard_WAF_Exclude_Rule::get_mark( );
		return SiteGuard_Htaccess::clear_settings( $mark );
	}
}
