<?php
class TypeSquare_Admin_Fonttheme extends TypeSquare_Admin_Base {
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

	public function fonttheme_setting() {
		$option_name = 'typesquare_auth';
		$nonce_key = TypeSquare_ST::OPTION_NAME;
		echo "<div class='wrap'>";
		echo '<h2>'. __( 'カスタムフォントテーマ' , self::$text_domain ). '</h2>';
		echo '<p>'. __( 'オリジナルのフォントテーマの作成や編集が行えます。' , self::$text_domain ). '</p>';
		do_action( 'typesquare_add_setting_before' );
		$auth_param = $this->get_auth_params();
		if ( false !== $auth_param['typesquare_auth']['auth_status'] ) {
			echo $this->_get_custome_font_theme_list_form();
		}
		do_action( 'typesquare_add_setting_after' );
	}

	/**
	 * @TODO: to use font theme generator
	 **/
	public function get_font_select_form() {
		$options = get_option( 'typesquare_custom_theme' );
		$edit_theme = false;
		if ( isset( $_POST['edit_theme'] ) ) {
			$edit_theme = esc_attr( $_POST['edit_theme'] );
		}
		$fonts = TypeSquare_ST_Fonts::get_instance();
		/* @TODO Debug Code
			$option_name = 'typesquare_fonttheme';
			$param = get_option( $option_name );
			unset($param['fonts']);
			update_option( $option_name, $param );
			$param = $fonts->get_fonttheme_options();
			*/
		$font_list = $theme_id = $theme_name = '';
		if ( $edit_theme && $options['theme'][ $edit_theme ] ) {
			$theme_name = $options['theme'][ $edit_theme ]['name'];
			$theme_id = $options['theme'][ $edit_theme ]['id'];
			$font_list = $options['fonts'][ $edit_theme ];
		}

		$html  = '';
		$html .= '<hr/>';
		$query = './admin.php?page='. self::MENU_FONTTHEME;
		$html .= "<form method='post' action='{$query}' id='". self::MENU_FONTTHEME. "'>";
		$html .= wp_nonce_field( 'ts_update_font_name_setting', 'ts_update_font_name_setting', true , false );
		if ( $theme_id ) {
			$theme_input = "<input type='hidden' name='typesquare_custom_theme[id]' value='{$theme_id}' />";
			$html .= "<input type='hidden' name='ts_edit_mode' value='update' />";
			$html .= $theme_input;
		} else {
			$theme_id = uniqid();
			$theme_input = false;
			$html .= "<input type='hidden' name='ts_edit_mode' value='new' />";
			$html .= "<input type='hidden' name='typesquare_custom_theme[id]' value='{$theme_id}' maxlength='16' style='width:100%;' pattern='^[0-9A-Za-z]+$' required/>";
		}
		$html .= '<h4>■'. __( 'フォントテーマ名' , self::$text_domain ). '</h4>';
		$html .= '<p>'. __( 'フォントテーマの名称を設定します。（最大16文字まで）' , self::$text_domain ). '</p>';
		$html .= "<input type='text' name='typesquare_custom_theme[name]' value='{$theme_name}' maxlength='16' style='width:100%;' required/>";
		$html .= '<h4>■'. __( 'フォントテーマの設定' , self::$text_domain ). '</h4>';
		$html .= '<p>'. __( '各クラスに設定するフォントを選択します。' , self::$text_domain ). '</p>';
		$html .= "<div id='ts-react-search-font'></div>";
		$html .= get_submit_button( __( 'フォントテーマを設定する', self::$text_domain ) );
		$html .= '</form>';
		$html .= $this->_get_delete_fonttheme_form( $theme_input );
		$html .= $this->_get_script( $font_list );
		return $html;
	}

	private function _get_script( $font_list ) {
		$vars  = "var form_id = '#". self::MENU_FONTTHEME. "';";
		$vars .= "var notify_text = '". __( 'フォントを１種類以上選択してください。', self::$text_domain ). "';";
$script = <<<EOM
{$vars}
jQuery( document ).ready(function() {
	jQuery( form_id ).submit(function() {
		var title = jQuery( 'select[name="typesquare_custom_theme[fonts][title][font]"]' ).val();
		var lead = jQuery( 'select[name="typesquare_custom_theme[fonts][lead][font]"]' ).val();
		var text = jQuery( 'select[name="typesquare_custom_theme[fonts][text][font]"]' ).val();
		var bold = jQuery( 'select[name="typesquare_custom_theme[fonts][bold][font]"]' ).val();
		if (
			title === 'false' &&
			lead === 'false' &&
			text === 'false' &&
			bold === 'false'
		) {
			alert( notify_text );
			return false;
		}
	});
});
EOM;
		$html = '<script>';
		$endpoint = path_join( TS_PLUGIN_URL , 'inc/font.json' );
		$html .= "var json_endpoint = '{$endpoint}';";
		if ( $font_list ) {
			$html .= "var current_font = ". json_encode( $font_list ) .';';
		} else {
			$html .= "var current_font = false;";
		}
		$html .= $script;
		$html .= '</script>';
		return $html;
	}

	private function _get_delete_fonttheme_form( $theme_input ) {
		$html = '';
		$query = './admin.php?page='. self::MENU_FONTTHEME;
		if ( $theme_input ) {
			$html .= "<form method='post' action='{$query}'>";
			$html .= "<input type='hidden' name='ts_edit_mode' value='delete' />";
			$html .= $theme_input;
			$html .= wp_nonce_field( 'ts_update_font_name_setting', 'ts_update_font_name_setting', true , false );
			$html .= get_submit_button( __( 'フォントテーマを削除する', self::$text_domain ) );
			$html .= '</form>';
		}
		return $html;
	}

	private function _get_custome_font_theme_list_form() {
		$options = get_option( 'typesquare_custom_theme' );
		$query = './admin.php?page='. self::MENU_FONTGEN;
		$html  = '';
		if ( is_array( $options['theme'] ) && ! empty( $options['theme'] ) ) {
			$html .= '<h3>■'. __( 'カスタムフォントテーマ一覧' , self::$text_domain ). '</h3>';
			$html .= "<table class='widefat form-table'>";
			$html .= '<tbody>';
			$html .= '<tr><td>';
			$html .= "<form method='POST' action='{$query}'>";
			$html .= "<select name='edit_theme'>";
			foreach ( $options['theme'] as $option ) {
				$name = $option['name'];
				$id = $option['id'];
				$fonts = $this->_get_font_data( $option['fonts'] );
				$fonts = "( {$fonts} )";
				$html .= "<option value='{$id}'>{$name}{$fonts}</option>";
			}
			$html .= '</select>';
			$html .= "<input type='hidden' name='ts_edit_mode' value='update' />";
			$html .= get_submit_button( __( '編集する', self::$text_domain ) );
			$html .= wp_nonce_field( 'ts_update_font_list' , 'ts_update_font_list' , true , false );
			$html .= '</form>';
			$html .= '</td></tr>';
			$html .= '</tbody>';
			$html .= '</table>';
		}
		if (count($options['theme']) < self::FONT_THEME_MAX) {
			$html .= "<form method='POST' action='{$query}'>";
			$html .= "<input type='hidden' name='ts_edit_mode' value='new' />";
			$html .= get_submit_button( __( '新しいカスタムフォントテーマを作成する', self::$text_domain ) );
			$html .= wp_nonce_field( 'ts_update_font_list' , 'ts_update_font_list' , true , false );
			$html .= '</form>';
		}
		return $html;
	}

	private function _get_font_data( $fonts ) {
		$text  = '';
		if ( isset( $fonts['title'] ) ) {
			$text .= __( '見出し：', self::$text_domain ). $fonts['title']. ',';
		}
		if ( isset( $fonts['lead'] ) ) {
			$text .= __( 'リード：', self::$text_domain ). $fonts['lead']. ',';
		}
		if ( isset( $fonts['text'] ) ) {
			$text .= __( '本文：', self::$text_domain ). $fonts['text']. ',';
		}
		if ( isset( $fonts['bold'] ) ) {
			$text .= __( '太字：', self::$text_domain ). $fonts['bold']. ',';
		}
		$text = rtrim( $text, ',' );
		$text = str_replace( ",", " / ", $text );
		return $text;
	}

	public function fonttheme_generator() {
		$option_name = 'typesquare_auth';
		$nonce_key = TypeSquare_ST::OPTION_NAME;
		echo "<div class='wrap'>";
		echo '<h2>'. __( 'フォントテーマエディタ' , self::$text_domain ). '</h2>';
		do_action( 'typesquare_add_setting_before' );
		$auth_param = $this->get_auth_params();
		if ( false !== $auth_param['typesquare_auth']['auth_status'] ) {
			echo $this->get_font_select_form();
		}
		echo '</div>';
	}
}
