<?php
class TypeSquare_ST_Auth {
	private static $instance;
	private static $text_domain;

	private function __construct(){}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();
		}

		return self::$instance;
	}

	public static function text_domain() {
		static $text_domain;

		if ( ! $text_domain ) {
			$data = get_file_data( __FILE__ , array( 'text_domain' => 'Text Domain' ) );
			$text_domain = $data['text_domain'];
		}

		return $text_domain;
	}

	public function get_auth_params() {
		$param = array();
		$param['typesquare_auth'] = array(
			'auth_status' => true,
			'api_status' => false,
		);

			return $param;
		}
	}
