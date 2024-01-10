<?php

include_once( SITEGUARD_PATH . 'really-simple-captcha/siteguard-really-simple-captcha.php' );

class SiteGuard_CAPTCHA extends SiteGuard_Base {
	protected $captcha;
	protected $prefix;
	protected $word;

	function __construct( ) {
		global $siteguard_config;
		if ( '1' == $siteguard_config->get( 'captcha_enable' ) && 'xmlrpc.php' != basename( $_SERVER['SCRIPT_NAME'] ) ) {
			$this->captcha = new SiteGuardReallySimpleCaptcha( );

			add_filter( 'shake_error_codes', array( $this, 'handler_shake_error_codes' ) );

			// for logiin
			if ( '0' !== $siteguard_config->get( 'captcha_login' ) ) {
				add_filter( 'login_form', array( $this, 'handler_login_form' ) );
				add_filter( 'wp_authenticate_user', array( $this, 'handler_wp_authenticate_user' ), 1, 2 );
			}
			// for lost password
			if ( '0' !== $siteguard_config->get( 'captcha_lostpasswd' ) ) {
				add_filter( 'lostpassword_form', array( $this, 'handler_lostpassword_form' ) );
				add_filter( 'lostpassword_post', array( $this, 'handler_lostpassword_post' ), 1 );
			}
			// for register user
			if ( '0' !== $siteguard_config->get( 'captcha_registuser' ) ) {
				add_filter( 'register_form', array( $this, 'handler_register_form' ) );
				add_action( 'registration_errors', array( $this, 'handler_registration_errors' ), 10, 3 );
			}
			// for comment
			if ( '0' !== $siteguard_config->get( 'captcha_comment' ) ) {
				add_action( 'comment_form_after_fields', array( $this, 'handler_comment_form' ), 1 );
				add_action( 'comment_form_logged_in_after', array( $this, 'handler_comment_form' ), 1 );
				add_action( 'comment_form', array( $this, 'handler_comment_form' ) );
				add_filter( 'preprocess_comment', array( $this, 'handler_process_comment_post' ) );
			}
		}
		if ( '1' == $siteguard_config->get( 'same_login_error' ) ) {
			add_filter( 'login_errors', array( $this, 'handler_login_errors' ) );
		}
	}
	function check_requirements( ) {
		$error = siteguard_check_multisite( );
		if ( is_wp_error( $error ) ) {
			return $error;
		}
		$error = $this->check_extensions( );
		if ( is_wp_error( $error ) ) {
			return $error;
		}
		$error = $this->check_image_access( );
		if ( is_wp_error( $error ) ) {
			return $error;
		}
		return true;
	}
	function check_extensions( ) {
		$error_extensions = array();
		$extensions = array(
			'mbstring',
			'gd',
		);
		foreach ( $extensions as $extension ) {
			if ( ! extension_loaded( $extension ) ) {
				$error_extensions[] = $extension;
			}
		}
		if ( empty( $error_extensions ) ) {
			return true;
		}

		$message  = esc_html__( 'In order to enable this function, it is necessary to install expanded modules', 'siteguard' );
		$message .= ' ( ';
		$message .= implode( ', ', $error_extensions );
		$message .= ' ) ';
		$message .= esc_html__( 'in the server.', 'siteguard' );

		$error = new WP_Error( 'siteguard_captcha', $message );
		return $error;
	}
	function check_image_access( ) {
		if ( is_object( $this->captcha ) ) {
			$this->captcha->make_tmp_dir( );
		} else {
			$captcha = new SiteGuardReallySimpleCaptcha( );
			$captcha->make_tmp_dir( );
		}
		$result = wp_remote_get( SITEGUARD_URL_PATH . 'really-simple-captcha/tmp/dummy.png' );
		if ( ! is_wp_error( $result ) && 200 === $result['response']['code'] ) {
			return true;
		}
		$message  = esc_html__( 'In order to enable this function, it is necessary to specify Limit to AllowOverride in httpd.conf.', 'siteguard' );
		$error = new WP_Error( 'siteguard_captcha', $message );
		return $error;
	}
	function handler_login_errors( $error ) {
		if ( strlen( $error ) > 0 && false === strpos( $error, esc_html__( 'ERROR: LOGIN LOCKED', 'siteguard' ) ) ) {
			$error = esc_html__( 'ERROR: Please check the input and resend.', 'siteguard' );
		}
		return $error;
	}
	function handler_shake_error_codes( $shake_error_codes ) {
		array_push( $shake_error_codes, 'siteguard-captcha-error' );
		return $shake_error_codes;
	}

	function init( ) {
		global $siteguard_config;
		$errors = $this->check_requirements( );
		if ( ! is_wp_error( $errors ) ) {
			$switch = '1';
		} else {
			$switch = '0';
		}
		$siteguard_config->set( 'captcha_enable', $switch );
		$language = get_bloginfo( 'language' );
		if ( 'ja' == $language ) {
			$mode = '1'; // hiragana
		} else {
			$mode = '2'; // alphanumeric
		}
		$siteguard_config->set( 'captcha_login',      $mode );
		$siteguard_config->set( 'captcha_comment',    $mode );
		$siteguard_config->set( 'captcha_lostpasswd', $mode );
		$siteguard_config->set( 'captcha_registuser', $mode );
		if ( true === siteguard_check_multisite( ) ) {
			$siteguard_config->set( 'same_login_error',   '1' );
		} else {
			$siteguard_config->set( 'same_login_error',   '0' );
		}
		$siteguard_config->update( );
	}
	function get_captcha( ) {
		$result  = '<p>';
		$result .= '<img src="'. SITEGUARD_URL_PATH . 'really-simple-captcha/tmp/' . $this->prefix . '.png" alt="CAPTCHA">';
		$result .= '</p><p>';
		$result .= '<label for="siteguard_captcha">' . esc_html__( 'Please input characters displayed above.', 'siteguard' ) . '</label><br />';
		$result .= '<input type="text" name="siteguard_captcha" id="siteguard_captcha" class="input" value="" size="10" aria-required="true" />';
		$result .= '<input type="hidden" name="siteguard_captcha_prefix" id="siteguard_captcha_prefix" value="'.$this->prefix.'" />';
		$result .= '</p>';

		return $result;
	}
	function put_captcha(  ) {
		$this->word = $this->captcha->generate_random_word( );
		$this->prefix = mt_rand( );
		$this->captcha->generate_image( $this->prefix, $this->word );
		echo $this->get_captcha( );
	}
	function handler_login_form( ) {
		global $siteguard_config;
		( '2' === $siteguard_config->get( 'captcha_login' ) ) ?  $this->captcha->set_lang_mode( 'en' ) : $this->captcha->set_lang_mode( 'jp' );
		$this->put_captcha( );
	}
	function handler_comment_form( $post_id ) {
		global $siteguard_config;
		if ( defined( 'SITEGUARD_PUT_COMMENT_FORM' ) ) {
			return;
		}
		( '2' === $siteguard_config->get( 'captcha_comment' ) ) ?  $this->captcha->set_lang_mode( 'en' ) : $this->captcha->set_lang_mode( 'jp' );
		$this->put_captcha( );
		define( 'SITEGUARD_PUT_COMMENT_FORM', '1' );
	}
	function handler_lostpassword_form( ) {
		global $siteguard_config;
		( '2' === $siteguard_config->get( 'captcha_lostpasswd' ) ) ?  $this->captcha->set_lang_mode( 'en' ) : $this->captcha->set_lang_mode( 'jp' );
		$this->put_captcha( );
	}
	function handler_register_form( ) {
		global $siteguard_config;
		( '2' == $siteguard_config->get( 'captcha_registuser' ) ) ?  $this->captcha->set_lang_mode( 'en' ) : $this->captcha->set_lang_mode( 'jp' );
		$this->put_captcha( );
	}
	function handler_wp_authenticate_user( $user, $password ) {
		if ( array_key_exists( 'siteguard_captcha', $_POST ) && array_key_exists( 'siteguard_captcha_prefix', $_POST ) ) {
			if ( $this->captcha->check( $_POST['siteguard_captcha_prefix'], $_POST['siteguard_captcha'], false ) ) {
				return $user;
			}
		}
		$error = new WP_Error( );
		$error->add( 'siteguard-captcha-error', esc_html__( 'ERROR: Invalid CAPTCHA.', 'siteguard' ) );
		return $error;
	}
	function add_captcha_error( ) {
		return new WP_Error( 'siteguard-captcha-error', esc_html__( 'ERROR: Invalid CAPTCHA.', 'siteguard' ) );
	}
	function handler_lostpassword_post( ) {
		if ( array_key_exists( 'siteguard_captcha', $_POST ) &&  array_key_exists( 'siteguard_captcha_prefix', $_POST ) ) {
			if ( $this->captcha->check( $_POST['siteguard_captcha_prefix'], $_POST['siteguard_captcha'], false ) ) {
				return;
			}
		}
		add_filter( 'allow_password_reset', array( $this, 'add_captcha_error' ) );
	}
	function handler_registration_errors( $errors, $sanitized_user_login, $user_email ) {
		if ( array_key_exists( 'siteguard_captcha', $_POST ) &&  array_key_exists( 'siteguard_captcha_prefix', $_POST ) ) {
			if ( $this->captcha->check( $_POST['siteguard_captcha_prefix'], $_POST['siteguard_captcha'], false ) ) {
				return $errors;
			}
		}
		$new_errors = new WP_Error( );
		$new_errors->add( 'siteguard-captcha-error', esc_html__( 'ERROR: Invalid CAPTCHA.', 'siteguard' ) );
		return $new_errors;
	}
	function handler_process_comment_post( $comment ) {
		if ( is_admin() ) {
			return $comment;
		}
		if ( array_key_exists( 'siteguard_captcha', $_POST ) &&  array_key_exists( 'siteguard_captcha_prefix', $_POST ) ) {
			if ( ! empty( $_POST['siteguard_captcha'] ) ) {
				if ( $this->captcha->check( $_POST['siteguard_captcha_prefix'], $_POST['siteguard_captcha'], false ) ) {
					return $comment;
				}
			}
		}
		wp_die( esc_html__( 'ERROR: Invalid CAPTCHA.', 'siteguard' ) );
	}
}
