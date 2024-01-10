<?php

class TypeSquare_ST_Fonttheme {
	private static $fonttheme;
	private static $instance;

	private function __construct(){}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	public static function get_fonttheme() {
		static $fonttheme;

		$fonttheme = array(
			'basic' => array(
				'name'	=> 'ベーシック',
				'fonts' => array(
					'title'   => 'ゴシックMB101 M',
					'lead'    => 'UD黎ミン M',
					'content' => 'ゴシックMB101 L',
					'bold'    => 'ゴシックMB101 M',
				),
			),
			'business' => array(
				'name'	=> 'ビジネス',
				'fonts' => array(
					'title'   => 'UD黎ミン EB',
					'lead'    => 'TBUD明朝 M',
					'content' => 'UD新ゴNT L',
					'bold'    => 'UD新ゴNT M',
				),
			),
			'pop' => array(
				'name'	=> 'ポップ',
				'fonts' => array(
					'title'   => 'ぽっくる',
					'lead'    => '新丸ゴ B',
					'content' => '丸フォーク R',
					'bold'    => '丸フォーク B',
				),
			),
			'retro' => array(
				'name'	=> 'レトロ',
				'fonts' => array(
					'title'   => 'シネマレター',
					'lead'    => 'TBカリグラゴシック R',
					'content' => 'UD黎ミン M',
					'bold'    => 'UD黎ミン EB',
				),
			),
			'luxury' => array(
				'name'	=> 'ラグジュアリー',
				'fonts' => array(
					'title'   => 'A1明朝',
					'lead'    => '新正楷書CBSK1',
					'content' => '新丸ゴ L',
					'bold'    => '新丸ゴ M',
				),
			),
			'wafu' => array(
				'name'	=> '和風',
				'fonts' => array(
					'title'   => '新正楷書CBSK1',
					'lead'    => '解ミン 月 B',
					'content' => 'リュウミン M-KL',
					'bold'    => 'リュウミン EB-KL',
				),
			),
			'blog' => array(
				'name'	=> 'ブログ',
				'fonts' => array(
					'title'   => '新ゴ B',
					'lead'    => '丸フォーク B',
					'content' => '新ゴ L',
					'bold'    => '新ゴ M',
				),
			),
			'smartphone' => array(
				'name'	=> 'スマホ',
				'fonts' => array(
					'title'   => 'UD新ゴNT B',
					'lead'    => 'UD新ゴNT M',
					'content' => 'UD新ゴ コンデンス90 L',
					'bold'    => 'UD新ゴ コンデンス90 M',
				),
			),
		);

		$options = get_option( 'typesquare_custom_theme' );
		if ( $options && isset( $options['theme'] ) && is_array( $options['theme'] ) ) {
			$fonttheme = $fonttheme + $options['theme'];
		}
		return $fonttheme;
	}

	public static function get_custom_theme_json() {
		$options = get_option( 'typesquare_custom_theme' );
		return json_encode($options['theme']);
	}
}
