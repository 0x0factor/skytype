<?php
/*
Plugin Name: TS Webfonts for お名前.com
Version: 1.0.0
Description: お名前.comレンタルサーバーで株式会社モリサワが提供するWebフォントを利用できるプラグインです。
Author: GMO Internet, Inc.
Author URI: http://www.onamae.com/
Plugin URI: https://wordpress.org/plugins/ts-webfonts-for-onamaecom/
Text Domain: typesquare
Domain Path: /languages
*/

require_once( dirname( __FILE__ ).'/typesquare-admin.php' );
require_once( dirname( __FILE__ ).'/inc/class/class.font.data.php' );
require_once( dirname( __FILE__ ).'/inc/class/class.auth.php' );
define( 'TS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
$ts = TypeSquare_ST::get_instance();
$ts->add_hook();
$admin = TypeSquare_Admin::get_instance();
$admin->add_hook();

class TypeSquare_ST {
	private static $instance;
	private $styles = false;
	const OPTION_NAME = 'ts_settings';
	private function __construct(){}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	public function add_hook() {
		add_action( 'wp_enqueue_scripts' , array( $this, 'load_scripts' ) , 0 , 3 );
		add_action( 'wp_head'  , array( $this, 'load_font_styles' ) );
		add_action( 'pre_get_posts', array( $this, 'get_archive_font_styles' ) );
	}

	public static function version() {
		static $version;

		if ( ! $version ) {
			$data = get_file_data( __FILE__ , array( 'version' => 'Version' ) );
			$version = $data['version'];
		}
		return $version;
	}

	public static function text_domain() {
		static $text_domain;

		if ( ! $text_domain ) {
			$data = get_file_data( __FILE__ , array( 'text_domain' => 'Text Domain' ) );
			$text_domain = $data['text_domain'];
		}
		return $text_domain;
	}

	public function load_scripts() {
		$query = '';
		$version = $this->version();

		wp_register_script( 'typesquare_std', "//code.typesquare.com/static/ZDbTe4IzCko%253D/ts106f.js?$query", array( 'jquery' ), $version, false );
		wp_enqueue_script( 'typesquare_std' );
	}

	private function get_fonts( $type = false, $post_font_data = false, $fonts = false ) {
		$font_class = TypeSquare_ST_Fonts::get_instance();
		$selected_font = $font_class->get_selected_font( $type );
		if ( $selected_font ) {
			$fonts = $selected_font;
		}
		if ( $post_font_data ) {
			if ( isset( $post_font_data[ $type ] ) ) {
				$fonts = $post_font_data[ $type ]['font'];
			}
		}
		if ( is_array( $fonts ) ) {
			$text_font = '';
			foreach ( $fonts as $key => $font ) {
				$text_font .= '"'. esc_attr( $font ). '"';
				if ( count( $fonts ) - 1 > $key  ) {
					$text_font .= ',';
				}
			}
		} else {
			$text_font    = '"'. esc_attr( $fonts ). '"';
		}
		return $text_font;
	}

	public function load_font_styles() {
		if ( is_archive() || is_home() ) {
			if ( $this->styles ) {
				echo $this->styles;
			}
			return;
		}
		$auth  = TypeSquare_ST_Auth::get_instance();

		$fonts = TypeSquare_ST_Fonts::get_instance();
		$fonttheme = $fonts->get_selected_fonttheme();
		if ( ! isset( $fonttheme ) && ! $fonttheme ) {
			return;
		}
		$use_font = $fonts->load_font_data( $fonttheme['font_theme'] );
		if ( is_singular() ) {
			$param = $fonts->get_fonttheme_params();
			if ( isset( $param['typesquare_themes']['show_post_form'] ) && 'false' != $param['typesquare_themes']['show_post_form']  ) {
				$post_theme = $fonts->get_selected_post_fonttheme( get_the_ID() );
				$post_theme = $fonts->load_font_data( $post_theme );
				if ( $post_theme ) {
					$use_font = $post_theme;
				}
			}
		}

		$style = $this->_get_font_styles( $use_font, $fonttheme );
		if ( $style ) {
			$html = "<style type='text/css'>{$style}</style>";
			echo $html;
		}
	}

	public function get_archive_font_styles( $query ) {
		if (  is_admin() || ! $query->is_main_query() || is_singular() ) {
			return;
		}

		$fonts = TypeSquare_ST_Fonts::get_instance();
		$fonttheme = $fonts->get_selected_fonttheme();
		if ( ! isset( $fonttheme ) && ! $fonttheme ) {
			return;
		}
		$font_param = $fonts->get_fonttheme_params();
		$use_font = $fonts->load_font_data( $fonttheme['font_theme'] );


		if ( ! $query->query ) {
			$query->query = apply_filters( 'ts-default-query', array(
				'post_type' => 'post',
			) );
		}
		$the_query = new WP_Query( $query->query );
		$style = false;
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$id = get_the_ID();
			if ( isset( $font_param['typesquare_themes']['show_post_form'] ) && 'false' != $font_param['typesquare_themes']['show_post_form']  ) {
				$post_theme = $fonts->get_selected_post_fonttheme( $id );
				$post_theme = $fonts->load_font_data( $post_theme );
				if ( $post_theme ) {
					$use_font = $post_theme;
				}
			}
			$style .= $this->_get_font_styles( $use_font, $fonttheme, $id );
		endwhile;

		if ( $style ) {
			$this->styles = "<style type='text/css'>{$style}</style>";
		}
	}

	private function _merge_post_id_to_target( $post_key, $target_text ) {
		$target_list = explode( ',', $target_text );
		$merged_target = false;
		foreach ( $target_list as $target ) {
			if ( '.hentry' == $target ) {
				$merged_target .= "{$post_key}{$target},";
			} else {
				$merged_target .= "{$post_key} {$target},";
			}
		}
		$merged_target = rtrim( $merged_target, ',' );
		return $merged_target;
	}

	private function _get_font_styles( $use_font, $fonttheme, $post_id = false, $post_font_data = false ) {
		$style  = '';
		if ( $post_id ) {
			$post_key = '#post-'. $post_id;
			$title_target = $this->_merge_post_id_to_target( $post_key, esc_attr( $fonttheme['title_target'] ) );
			$lead_target  = $this->_merge_post_id_to_target( $post_key, esc_attr( $fonttheme['lead_target'] ) );
			$text_target  = $this->_merge_post_id_to_target( $post_key, esc_attr( $fonttheme['text_target'] ) );
			$bold_target  = $this->_merge_post_id_to_target( $post_key, esc_attr( $fonttheme['bold_target'] ) );
		} else {
			$title_target = esc_attr( $fonttheme['title_target'] );
			$lead_target  = esc_attr( $fonttheme['lead_target'] );
			$text_target  = esc_attr( $fonttheme['text_target'] );
			$bold_target  = esc_attr( $fonttheme['bold_target'] );
		}

		$title_font = $lead_font = $text_font = $bold_font = false;
		if ( isset( $use_font['title'] ) ) {
			$title_font   = $this->get_fonts( 'title' , $post_font_data, $use_font['title'] );
		}
		if ( isset( $use_font['lead'] ) ) {
			$lead_font    = $this->get_fonts( 'lead' , $post_font_data, $use_font['lead'] );
		}
		if ( ! isset( $use_font['content'] ) && isset( $use_font['text'] ) ) {
			$use_font['content'] = $use_font['text'];
		}
		if ( isset( $use_font['content'] ) ) {
			$text_font    = $this->get_fonts( 'text' , $post_font_data, $use_font['content'] );
		}
		if ( isset( $use_font['bold'] ) ) {
			$bold_font    = $this->get_fonts( 'bold' , $post_font_data, $use_font['bold'] );
		}

		if ( $title_target && $title_font ) {
			$style .= "{$title_target}{ font-family: {$title_font};}";
		}
		if ( $lead_target && $lead_font ) {
			$style .= "{$lead_target}{ font-family: {$lead_font};}";
		}
		if ( $text_target && $text_font ) {
			$style .= "{$text_target}{ font-family: {$text_font};}";
		}
		if ( $bold_target && $bold_font ) {
			$style .= "{$bold_target}{ font-family: {$bold_font};}";
		}
		return $style;
	}
}

register_uninstall_hook( __FILE__, 'ts_uninstall' );
function ts_uninstall() {
	delete_option( 'typesquare_auth' );
	delete_option( 'typesquare_fonttheme' );
	delete_option( 'typesquare_custom_theme' );
	return;
}
