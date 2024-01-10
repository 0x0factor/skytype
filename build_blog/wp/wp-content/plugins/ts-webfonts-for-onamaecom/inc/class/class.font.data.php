<?php

require_once( dirname( __FILE__ ).'/class.fonttheme.php' );
class TypeSquare_ST_Fonts {
	private static $instance;
	private static $text_domain;

	private function __construct() {
		self::$text_domain = TypeSquare_ST::text_domain();
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	public function load_all_font_data() {
		$font_theme = TypeSquare_ST_Fonttheme::get_instance();
		$font_data = $font_theme->get_fonttheme();
		return $font_data;
	}

	public function load_font_data( $theme = '' ) {
		if ( '' === $theme || false == $theme || 'false' == $theme ) {
			$theme = $this->get_selected_fonttheme();
			$theme = $theme['font_theme'];
			if ( '' === $theme || false == $theme || 'false' == $theme ) {
				return false;
			}
		}
		$font_data = $this->load_all_font_data( );
		if ( isset( $font_data[ $theme ] ) ) {
			return $font_data[ $theme ]['fonts'];
		} else {
			return false;
		}
	}

	public function get_selected_fonttheme() {
		$fonththeme = $this->get_fonttheme_params();
		return $fonththeme['typesquare_themes'];
	}

	public function get_selected_post_fonttheme( $post_id ) {
		$meta = get_post_meta( $post_id , 'typesquare_fonttheme' , true );
		if ( isset( $meta['theme'] ) ) {
			$theme = $meta['theme'];
		} elseif ( isset( $meta['fonts'] ) ) {
			$theme = '';
		} else {
			$theme = $meta;
		}
		/*
		if ( '' ==  $theme || 'false' == $theme ) {
			$theme = $this->get_selected_fonttheme();
			$theme = $theme['font_theme'];
		}
		*/
		return $theme;
	}

	public function get_fonttheme_keys() {
		return array(
			'font_theme' 			=> __( 'フォントテーマ', self::$text_domain ),
			'title_target'    => __( '見出しタグ', self::$text_domain ),
			'lead_target'     => __( 'リードタグ', self::$text_domain ),
			'text_target'     => __( '本文タグ', self::$text_domain ),
			'bold_target'     => __( '強調タグ', self::$text_domain ),
			'fade_in'         => __( 'フェードイン', self::$text_domain ),
			'fade_time'       => __( 'フェード時間', self::$text_domain ),
			'show_post_form'  => __( '記事ごとにフォントを設定', self::$text_domain ),
		);
	}

	public function get_fonttheme_options() {
		$default_param = array(
			'font_theme' 		=> false,
			'title_target' 	=> 'h1,h2,h3,.entry-title',
			'lead_target' 	=> 'h4,h5,h6',
			'text_target' 	=> '.hentry',
			'bold_target' 	=> 'strong,b',
			'show_post_form'	=> false,
		);

		$option_name = 'typesquare_fonttheme';
		$param = get_option( $option_name );
		if ( isset( $param['fonts'] ) ) {
			unset( $param['fonts'] );
		}
		if ( ! isset( $param['show_post_form'] ) ) {
			$param['show_post_form'] = false;
		}
		if ( ! $param ) {
			$param = $default_param;
		} else {
			foreach ( $default_param as $key => $value ) {
				if ( ! isset( $param[ $key ] ) ) {
					$param[ $key ] = $value;
				}
			}
		}

		return $param;
	}

	public function get_fonttheme_params() {
		$param['typesquare_themes'] = $this->get_fonttheme_options();
		$param['typesquare_themes_keys'] = $this->get_fonttheme_keys();
		return $param;
	}

	public function update_font_setting() {
		if ( ! isset( $_POST['typesquare_custom_theme'])) {
			return;
		}

		$options = get_option( 'typesquare_custom_theme' );
		$options = $this->parse_font_setting_param( $options, $_POST['typesquare_custom_theme'] );

		if(isset($options['theme']['']['id'])){
			$this->update_font_theme_setting();
		}else{
			update_option( 'typesquare_custom_theme', $options );
		}
		$result = __( 'フォントテーマの設定に成功しました。', self::$text_domain );
		$this->show_result( $result );
	}

	public function delete_custom_theme() {
		if ( ! isset( $_POST['typesquare_custom_theme'] ) ) {
			return;
		}
		$options = get_option( 'typesquare_custom_theme' );
		$id = $_POST['typesquare_custom_theme']['id'];
		unset( $options['theme'][ $id ] );
		unset( $options['fonts'][ $id ] );
		update_option( 'typesquare_custom_theme', $options );
		$result = __( 'フォントテーマの削除に成功しました。', self::$text_domain );
		$this->show_result( $result );
	}

	public function show_result( $result ) {
		$html  = "<div class='notice updated'><ul>";
		$html .= "<li>{$result}</li>";
		$html .= '</ul></div>';
		echo $html;
	}

	public function parse_font_setting_param( $current, $param ) {
		$name = esc_attr( $param['name'] );
		$id = (string) esc_attr( $param['id'] );
		$current['theme'][ $id ]['name'] = $name;
		$current['theme'][ $id ]['id'] = $id;
		foreach ( $param['fonts'] as $type => $font ) {
			$type = esc_attr( $type );
			foreach ( $font as $key => $value ) {
				if ( 'false' == $value ) {
					unset( $current['theme'][ $id ]['fonts'][ $type ] );
					unset( $current['fonts'][ $id ][ $type ] );
					continue;
				}
				$key = (string) esc_attr( $key );
				$current['fonts'][ $id ][ $type ][ $key ] = esc_attr( $value );
				$current['theme'][ $id ]['fonts'][ $type ] = esc_attr( $value );
			}
		}
		return $current;
	}

	public function update_font_theme_setting() {
		if ( ! isset( $_POST['typesquare_fonttheme'] ) || isset( $_POST['ts_change_edit_theme'] )) {
			return;
		}
		$options = get_option( 'typesquare_fonttheme' );
		foreach ( $_POST['typesquare_fonttheme'] as $key => $target ) {
			$key = esc_attr( $key );
			$options[ $key ] = esc_attr( $target );
		}
		if ( isset( $_POST['typesquare_fonttheme']['fade_time'] ) ) {
			if ( ! isset( $_POST['typesquare_fonttheme']['fade_in'] ) ) {
				$options['fade_in'] = false;
			}
		}
		if($options['font_theme'] === 'new'){
			$options['font_theme'] = $_POST['typesquare_custom_theme']['id'];
		}
		update_option( 'typesquare_fonttheme', $options );
	}

	public function get_font_script_param() {
		$script  = 'var current_font = ';
		$param = $this->get_fonttheme_options();
		if ( ! isset( $param['fonts'] ) ) {
			$script .= 'false;';
			return $script;
		}
		$fonts = $param['fonts'];
		$fonts = $this->_parse_font_script_data( $fonts );
		$script .= json_encode( $fonts ) . ';';
		return $script;
	}

	private function _parse_font_script_data( $fonts ) {
		foreach ( $fonts as $type => $font ) {
			if ( ! array_key_exists( 'type', $font ) || ! array_key_exists( 'family', $font ) ) {
				unset( $fonts[ $type ] );
			}
		}
		return $fonts;
	}

	public function get_selected_font( $type ) {
		$font = false;
		$param = $this->get_fonttheme_options();
		if ( ! isset( $param['fonts'] ) || ! isset( $param['fonts'][ $type ] ) ) {
			return $font;
		}
		$font = $param['fonts'][ $type ]['font'];
		return $font;
	}
}
