<?php error_reporting(0);
/****  script css読み込み  ****/
function load_script_css(){
	wp_enqueue_script( "main_script", get_template_directory_uri()."/script.js", array("jquery"), false, true );
	wp_enqueue_style( "style", get_stylesheet_uri(), false );
}
add_action('wp_enqueue_scripts', 'load_script_css');


/**** ウィジェット ****/
if (function_exists('register_sidebar')) {
	register_sidebar(array(
		'name' => 'サイドバー（広告上）',
		'id' => 'sidebar-1',
		'before_widget' => "<div class='box'>",
		'after_widget' => "</div>",
		'before_title' => "<h2 class='box-header main-color-font'>",
		'after_title' => '</h2>'
	));

	register_sidebar(array(
		'name' => 'サイドバー（広告下）',
		'id' => 'sidebar-2',
		'before_widget' => "<div class='box'>",
		'after_widget' => "</div>",
		'before_title' => "<h2 class='box-header main-color-font'>",
		'after_title' => '</h2>'
	));

	register_sidebar(array(
		'name' => 'サイドバー最上部にaffiバナー（300px以内）',
		'id' => 'sidebar-3',
		'before_widget' => "<div class='affi-banner'>",
		'after_widget' => "</div>",
		'before_title' => "<h2 class='box-header main-color-font'>",
		'after_title' => '</h2>'
	));

	register_sidebar(array(
		'name' => 'サイドバー最下部にaffiバナー（300px以内）',
		'id' => 'sidebar-4',
		'before_widget' => "<div class='affi-banner'>",
		'after_widget' => "</div>",
		'before_title' => "<h2 class='box-header main-color-font'>",
		'after_title' => '</h2>'
	));
}


/****  サムネイル  ****/
add_theme_support('post-thumbnails'); 


/****  カスタム背景  ****/
$custom_background_defaults = array(
        'default-color' => 'F6F7F9',
        'default-image' => ''
);
add_theme_support( 'custom-background', $custom_background_defaults );


/****  カスタムヘッダー  ****/
$defaults = array(
	'random-default'         => false, //ランダム表示
	'flex-height'            => false, //フレキシブル対応（高さ）
	'flex-width'             => false, //フレキシブル対応（幅）
	'default-text-color'     => '#fff', //デフォルトのテキストの色
	'header-text'            => false, //ヘッダー画像上にテキストを表示する
	'uploads'                => true //ファイルアップロードを許可する
);
add_theme_support('custom-header', $defaults);


/****  カスタムフィールド設置  ****/
add_action('admin_menu', 'add_custom_box');
function add_custom_box(){
	add_meta_box( 'ad_view_setting_in_post','アドセンス表示設定', 'view_custom_box', 'post', 'side' );
	add_meta_box( 'ad_view_setting_in_page','アドセンス表示設定', 'view_custom_box', 'page', 'side' );
}
function view_custom_box(){
	global $post;

	$is_checked = get_post_meta(get_the_ID(),'is_no_adsense',true);

	echo '<label><input type="checkbox" name="is_no_adsense"';
	if($is_checked){echo " checked";}
	echo '>アドセンスを非表示にする</label>';

}

add_action('save_post', 'save_custom_data');
function save_custom_data(){
	$is_no_adsense = $_POST["is_no_adsense"];
	$id = get_the_ID();
	$meta_key = "is_no_adsense";

	add_post_meta($id, $meta_key, $is_no_adsense, true);
	update_post_meta($id, $meta_key, $is_no_adsense);
}

/**** コメント表示 ****/
function mydesign($comment, $args, $depth){
$GLOBALS['comment'] = $comment; 
?>
<li class="compost" id="comment-<?php comment_ID() ?>">
	<div class="combody">
		<?php comment_text(); ?>
	</div><!-- .combody -->
	<p class="cominfo">
		by <?php comment_author_link(); ?> <?php comment_date(); ?>  <?php comment_time(); ?>
	</p>
</li>
<?php
}


/****  ページネーション  ****/
function pagination($pages = '', $range = 2){  
     $showitems = ($range * 2)+1;
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == ''){
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages){
             $pages = 1;
         }
     }   
 
     if(1 != $pages){
         echo "<div class='pagenavi'>";
         
         if($paged > 2 && $paged > $range+1 && $showitems < $pages){
         	echo "<a href='".get_pagenum_link(1)."'>1</a>";
         }

         for ($i=1; $i <= $pages; $i++){
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages-2 &&  $paged+$range-2 < $pages && $showitems < $pages){
         	echo "<a href='".get_pagenum_link($pages)."'>".$pages."</a>";
         }
         
         echo "</div>";
     }
}

/**** テーマカスタマイザー設定 ****/
define("MAIN_COLOR_DEFAULT", "#1aba67");
define("SUB_COLOR_DEFAULT", "#414852");
define("LINK_HOVER_COLOR_DEFAULT", "#EA3382");

add_action( 'customize_register', 'my_customize_register' );
function my_customize_register($wp_customize) {
	/*title_tagline*/
	$wp_customize->add_setting(
			'is_desc',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'is_desc',
			array(
				'section' => 'title_tagline',
				'settings' => 'is_desc',
				'label' => 'ブログタイトル下にキャッチフレーズを表示する',
				'type' => 'checkbox'
			)
	);

	/*タイトル文字色*/
	$wp_customize->add_setting(
			'title_color',
			array(
				'default' => '#fff'
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control(
    				$wp_customize,
				'title_color',
				array(
					'section' => 'title_tagline',
					'settings' => 'title_color',
					'label' =>'タイトル及びキャッチフレーズの文字色'
				)
			)
	);

	/*メインカラー*/
	$wp_customize->add_setting(
			'main_color',
			array(
				'default' => MAIN_COLOR_DEFAULT
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control(
    				$wp_customize,
				'main_color',
				array(
					'section' => 'colors',
					'settings' => 'main_color',
					'label' =>'メインカラー'
				)
			)
	);

	/*サブカラー*/
	$wp_customize->add_setting(
			'sub_color',
			array(
				'default' => SUB_COLOR_DEFAULT
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control(
    				$wp_customize,
				'sub_color',
				array(
					'section' => 'colors',
					'settings' => 'sub_color',
					'label' =>'サブカラー'
				)
			)
	);

	/*リンクhoverカラー*/
	$wp_customize->add_setting(
			'link_hover_color',
			array(
				'default' => LINK_HOVER_COLOR_DEFAULT
			)
	);
	$wp_customize->add_control(
			new WP_Customize_Color_Control(
    				$wp_customize,
				'link_hover_color',
				array(
					'section' => 'colors',
					'settings' => 'link_hover_color',
					'label' =>'リンクテキストhoverカラー'
				)
			)
	);

	/*見出しテキストの影*/
	$wp_customize->add_setting(
			'is_shadow',
			array('type' => 'option')
	);
	$wp_customize->add_control(
			'is_shadow',
			array(
				'section' => 'colors',
				'settings' => 'is_shadow',
				'label' => '見出しテキストに影をつける',
				'type' => 'checkbox'
			)
	);

	/*記事数の設定*/
	$wp_customize->add_section(
			'post_count',
			array(
				'title' => '表示記事数の設定',
				'priority' => 107
			)
	);

	/*New postに表示する行数*/
	$wp_customize->add_setting(
			'row_new_post',
			array(
				'default' => '2'
			)
	);
	$wp_customize->add_control(
			'row_new_post',
			array(
				'section' => 'post_count',
				'settings' => 'row_new_post',
				'label' => 'New postに表示する記事数',
				'type' => 'select',
				'choices' => array(
					1 => '3件',
					2 => '6件',
					3 => '9件',
					4 => '12件',
					5 => '15件'
				)
			)
	);

	/*カテゴリーごとに表示する記事数*/
	$wp_customize->add_setting(
			'count_cat_post',
			array(
				'default' => '3'
			)
	);
	$wp_customize->add_control(
			'count_cat_post',
			array(
				'section' => 'post_count',
				'settings' => 'count_cat_post',
				'label' => 'カテゴリーごとに表示する記事数',
				'type' => 'select',
				'choices' => array(
					1 => '1件',
					2 => '2件',
					3 => '3件',
					4 => '4件',
					5 => '5件',
					6 => '6件',
					7 => '7件',
					8 => '8件',
					9 => '9件'
				)
			)
	);

	/*関連記事の記事数*/
	$wp_customize->add_setting(
			'count_kanren',
			array(
				'default' => '6'
			)
	);
	$wp_customize->add_control(
			'count_kanren',
			array(
				'section' => 'post_count',
				'settings' => 'count_kanren',
				'label' => '関連記事として表示される記事数',
				'type' => 'select',
				'choices' => array(
					0 => '表示しない',
					3 => '3件',
					4 => '4件',
					5 => '5件',
					6 => '6件',
					7 => '7件',
					8 => '8件',
					9 => '9件',
					10 => '10件'
				)
			)
	);

	/*アイキャッチの設定*/
	$wp_customize->add_section(
			'thumbnail_setting',
			array(
				'title' => 'アイキャッチ表示設定',
				'priority' => 108
			)
	);

	/*記事上部のアイキャッチ*/
	$wp_customize->add_setting(
			'is_eyecatch',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'is_eyecatch',
			array(
				'section' => 'thumbnail_setting',
				'settings' => 'is_eyecatch',
				'label' => '記事タイトル下にアイキャッチ画像を表示する',
				'type' => 'checkbox'
			)
	);

	/*サムネイルの高さ*/
	$wp_customize->add_setting(
			'thumbnail_height',
			array(
				'default' => '180px'
			)
	);
	$wp_customize->add_control(
			'thumbnail_height',
			array(
				'section' => 'thumbnail_setting',
				'settings' => 'thumbnail_height',
				'label' => 'サムネイル画像の高さ（最大値）',
				'type' => 'select',
				'choices' => array(
					"120px" => "120px",
					"130px" => "130px",
					"140px" => "140px",
					"150px" => "150px",
					"160px" => "160px",
					"170px" => "170px",
					"180px" => "180px",
					"190px" => "190px",
					"200px" => "200px",
					"210px" => "210px",
					"220px" => "220px"
				)
			)
	);

	/*SNS設定*/
	$wp_customize->add_section(
			'sns_setting',
			array(
				'title' => 'SNS設定',
				'priority' => 101
			)
	);

	/*記事タイトル下にシェアボタン設置*/
	$wp_customize->add_setting(
			'is_shere_top_of_content',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'is_shere_top_of_content',
			array(
				'section' => 'sns_setting',
				'settings' => 'is_shere_top_of_content',
				'label' => '記事タイトルの下にシェアボタンを設置する',
				'type' => 'checkbox'
			)
	);

	/*Twitterフォローボタン設置*/
	$wp_customize->add_setting(
			'is_twitter_follow',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'is_twitter_follow',
			array(
				'section' => 'sns_setting',
				'settings' => 'is_twitter_follow',
				'label' => 'Twitterフォローボタンを設置する',
				'type' => 'checkbox'
			)
	);

	/*Twiiter*/
	$wp_customize->add_setting(
			'twitter_account',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'twitter_account',
			array(
				'section' => 'sns_setting',
				'settings' => 'twitter_account',
				'label' => 'Twiiterアカウント（@は不要）',
				'type' => 'text'
			)
	);

	/*@メンションを含める*/
	$wp_customize->add_setting(
			'mention',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'mention',
			array(
				'section' => 'sns_setting',
				'settings' => 'mention',
				'label' => 'Tweetにメンションを含める',
				'type' => 'checkbox'
			)
	);

	/*Facebookフォローボタン設置*/
	$wp_customize->add_setting(
			'is_fb_follow',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'is_fb_follow',
			array(
				'section' => 'sns_setting',
				'settings' => 'is_fb_follow',
				'label' => 'Facebookフォローボタンを設置する',
				'type' => 'checkbox'
			)
	);

	/*Facebook*/
	$wp_customize->add_setting(
			'facebook_url',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'facebook_url',
			array(
				'section' => 'sns_setting',
				'settings' => 'facebook_url',
				'label' => 'FacebookページURL',
				'type' => 'text'
			)
	);

	/*appID*/
	$wp_customize->add_setting(
			'app_id',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'app_id',
			array(
				'section' => 'sns_setting',
				'settings' => 'app_id',
				'label' => 'App ID',
				'type' => 'text'
			)
	);

	/*Google+フォローボタン設置*/
	$wp_customize->add_setting(
			'is_g_follow',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'is_g_follow',
			array(
				'section' => 'sns_setting',
				'settings' => 'is_g_follow',
				'label' => 'Google+フォローボタンを設置する',
				'type' => 'checkbox'
			)
	);

	/*g+*/
	$wp_customize->add_setting(
			'prof_g',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'prof_g',
			array(
				'section' => 'sns_setting',
				'settings' => 'prof_g',
				'label' => 'google+ページのID',
				'type' => 'text',
				'description' => 'google+のIDとは、google+のプロフィールページのURLに含まれる20桁ほどの数字の羅列です'
			)
	);

	/*Line@フォローボタン設置*/
	$wp_customize->add_setting(
			'is_line_follow',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'is_line_follow',
			array(
				'section' => 'sns_setting',
				'settings' => 'is_line_follow',
				'label' => 'Line@のフォローボタンを設置する',
				'type' => 'checkbox'
			)
	);

	/*Line*/
	$wp_customize->add_setting(
			'line_id',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'line_id',
			array(
				'section' => 'sns_setting',
				'settings' => 'line_id',
				'label' => 'Line@ID　（@は不要）',
				'type' => 'text'
			)
	);

	/*テキストエリア入力欄作成*/
	if(class_exists('WP_Customize_Control')){
		class WP_Customize_Textarea_Control extends WP_Customize_Control {
			public $type = 'textarea';

			public function render_content() {
				?>
				<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
				</label>
				<?php
			}
		}
	}

	/*AdSense設定*/
	$wp_customize->add_section(
			'ad_setting',
			array(
				'title' => 'アドセンス設定',
				'priority' => 102
			)
	);

	/*ビッグバナー728*/
	$wp_customize->add_setting(
			'ad_728',
			array(
				'type' => 'option',
			)
	);
	if(class_exists('WP_Customize_Textarea_Control')){
		$wp_customize->add_control(new WP_Customize_Textarea_Control(
			$wp_customize,
			'ad_728',
			array(
				'section' => 'ad_setting',
				'settings' => 'ad_728',
				'label' => 'ビッグバナー(728×90)'
			)
		));
	}

	/*レクタングル大*/
	$wp_customize->add_setting(
			'ad_336',
			array(
				'type' => 'option',
			)
	);
	if(class_exists('WP_Customize_Textarea_Control')){
		$wp_customize->add_control(new WP_Customize_Textarea_Control(
			$wp_customize,
			'ad_336',
			array(
				'section' => 'ad_setting',
				'settings' => 'ad_336',
				'label' => 'レクタングル大(336×280)'
			)
		));
	}

	/*ラージスカイスクレイパー*/
	$wp_customize->add_setting(
			'ad_large',
			array(
				'type' => 'option',
			)
	);
	if(class_exists('WP_Customize_Textarea_Control')){
		$wp_customize->add_control(new WP_Customize_Textarea_Control(
			$wp_customize,
			'ad_large',
			array(
				'section' => 'ad_setting',
				'settings' => 'ad_large',
				'label' => 'ラージスカイスクレイパー(300×600)'
			)
		));
	}

	/*レスポンシブ*/
	$wp_customize->add_setting(
			'ad_responsive',
			array(
				'type' => 'option',
			)
	);
	if(class_exists('WP_Customize_Textarea_Control')){
		$wp_customize->add_control(new WP_Customize_Textarea_Control(
			$wp_customize,
			'ad_responsive',
			array(
				'section' => 'ad_setting',
				'settings' => 'ad_responsive',
				'label' => 'レスポンシブ'
			)
		));
	}

	/*UI設定*/
	$wp_customize->add_section(
			'ui_setting',
			array(
				'title' => 'UI設定',
				'priority' => 106
			)
	);

	/*menuバー固定*/
	$wp_customize->add_setting(
			'ui_menu',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'ui_menu',
			array(
				'section' => 'ui_setting',
				'settings' => 'ui_menu',
				'label' => 'メニューバーを画面上部に常に表示する',
				'type' => 'checkbox'
			)
	);

	/*Topへ戻るボタン*/
	$wp_customize->add_setting(
			'ui_scroll',
			array(
				'default' => 'checked'
			)
	);
	$wp_customize->add_control(
			'ui_scroll',
			array(
				'section' => 'ui_setting',
				'settings' => 'ui_scroll',
				'label' => 'Topへ戻るボタン、Homeボタンを表示する',
				'type' => 'checkbox'
			)
	);

	/*ロゴ・ファビコン・appleなんとか登録*/
	$wp_customize->add_section(
			'image_setting',
			array(
				'title' => 'ロゴ、ファビコン設定',
				'priority' => 105
			)
	);

	/*ロゴ登録*/
	$wp_customize->add_setting(
			'logo_setting',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control( new WP_Customize_Image_Control(
        		$wp_customize,
        		'logo_setting',
        		array(
				'section'   => 'image_setting',
				'settings'  => 'logo_setting',
				'label'     => 'ロゴ画像',
				'description' => 'サイトには表示されませんがSNSで拡散される際にアイキャッチとして使用されます'
			)
	));

	/*favicon*/
	$wp_customize->add_setting(
			'favicon_setting',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control( new WP_Customize_Image_Control(
        		$wp_customize,
        		'favicon_setting',
        		array(
				'section'   => 'image_setting',
				'settings'  => 'favicon_setting',
				'label'     => 'ファビコン（.ico）'
			)
	));

	/*apple_touch*/
	$wp_customize->add_setting(
			'apple_setting',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control( new WP_Customize_Image_Control(
        		$wp_customize,
        		'apple_setting',
        		array(
				'section'   => 'image_setting',
				'settings'  => 'apple_setting',
				'label'     => 'アップルタッチアイコン（.png）'
			)
	));

	/*analytics*/
	$wp_customize->add_section(
			'analy_setting',
			array(
				'title' => 'アナリィティクス設定',
				'priority' => 103
			)
	);

	/*アナリィティクスのコード*/
	$wp_customize->add_setting(
			'analy_code',
			array(
				'type' => 'option',
			)
	);
	if(class_exists('WP_Customize_Textarea_Control')){
		$wp_customize->add_control(new WP_Customize_Textarea_Control(
			$wp_customize,
			'analy_code',
			array(
				'section' => 'analy_setting',
				'settings' => 'analy_code',
				'label' => 'アナリィティクスのコード'
			)
		));
	}

	/*ログイン時除外*/
	$wp_customize->add_setting(
			'reject_logged_in',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control(
			'reject_logged_in',
			array(
				'section' => 'analy_setting',
				'settings' => 'reject_logged_in',
				'label' => 'ログイン中のアクセスをカウントしない',
				'type' => 'checkbox'
			)
	);



	/*プロフィール欄*/
	$wp_customize->add_section(
			'profile_setting',
			array(
				'title' => 'プロフィール欄設定',
				'priority' => 104
			)
	);

	/*プロフィール設置？*/
	$wp_customize->add_setting(
			'is_prof',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control(
			'is_prof',
			array(
				'section' => 'profile_setting',
				'settings' => 'is_prof',
				'label' => 'プロフィール欄を設置する',
				'type' => 'checkbox'
			)
	);

	/*プロフィール欄タイトル*/
	$wp_customize->add_setting(
			'prof_title',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'prof_title',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_title',
				'label' => 'プロフィール欄のタイトル',
				'type' => 'text'
			)
	);

	/*名前*/
	$wp_customize->add_setting(
			'prof_name',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'prof_name',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_name',
				'label' => '名前',
				'type' => 'text'
			)
	);

	/*自画像*/
	$wp_customize->add_setting(
			'prof_image',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control( new WP_Customize_Image_Control(
        		$wp_customize,
        		'prof_image',
        		array(
				'section'   => 'profile_setting',
				'settings'  => 'prof_image',
				'label'     => 'アイコン'
			)
	));

	/*自己紹介*/
	$wp_customize->add_setting(
			'prof_text',
			array(
				'type' => 'option',
			)
	);
	if(class_exists('WP_Customize_Textarea_Control')){
		$wp_customize->add_control(new WP_Customize_Textarea_Control(
			$wp_customize,
			'prof_text',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_text',
				'label' => '簡単な自己紹介文'
			)
		));
	}

	/*リンク*/
	$wp_customize->add_setting(
			'prof_url',
			array(
				'type' => 'option',
			)
	);
	$wp_customize->add_control(
			'prof_url',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_url',
				'label' => 'プロフィールページのurl',
				'type' => 'text',
				'description' => 'プロフィールページがあれば、そのURLを入力してください'
			)
	);

	/*Twitter フォローボタン設置*/
	$wp_customize->add_setting(
			'prof_twitter',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control(
			'prof_twitter',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_twitter',
				'label' => 'Twitterフォローボタンを設置する',
				'type' => 'checkbox'
			)
	);

	/*Google+フォローボタン設置*/
	$wp_customize->add_setting(
			'prof_g+',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control(
			'prof_g+',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_g+',
				'label' => 'Google+フォローボタンを設置する',
				'type' => 'checkbox'
			)
	);




	/*Facebookページ設置*/
	$wp_customize->add_setting(
			'prof_fb',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control(
			'prof_fb',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_fb',
				'label' => 'Facebook LikeBox（Page Plugin）を設置する',
				'type' => 'checkbox'
			)
	);

	/*Facebookページ タイムライン*/
	$wp_customize->add_setting(
			'prof_fb_timeline',
			array(
				'type' => 'option'
			)
	);
	$wp_customize->add_control(
			'prof_fb_timeline',
			array(
				'section' => 'profile_setting',
				'settings' => 'prof_fb_timeline',
				'label' => 'Facebook LikeBoxにタイムラインを表示する',
				'type' => 'checkbox'
			)
	);
}


/****  get_theme_mod関数  ****/
function is_desc(){
	return get_theme_mod("is_desc", true);
}

function get_title_color(){
	return get_theme_mod("title_color", "#fff");
}

function get_main_color(){
	return get_theme_mod("main_color", MAIN_COLOR_DEFAULT);
}

function get_sub_color(){
	return get_theme_mod("sub_color", SUB_COLOR_DEFAULT);
}

function get_link_hover_color(){
	return get_theme_mod("link_hover_color", LINK_HOVER_COLOR_DEFAULT);
}

function get_row_new_post(){
	return get_theme_mod("row_new_post", 2);
}

function get_count_cat_post(){
	return get_theme_mod("count_cat_post", 3);
}

function get_count_kanren(){
	return get_theme_mod("count_kanren", 6);
}

function is_eyecatch(){
	return get_theme_mod("is_eyecatch", true);
}

function get_thumbnail_height(){
	return get_theme_mod("thumbnail_height", "180px");
}

function is_menu_fixed(){
	return get_theme_mod("ui_menu", true);
}

function is_button_set(){
	return get_theme_mod("ui_scroll", true);
}

function is_twitter_follow(){
	return get_theme_mod("is_twitter_follow", true);
}

function is_fb_follow(){
	return get_theme_mod("is_fb_follow", true);
}

function is_g_follow(){
	return get_theme_mod("is_g_follow", true);
}

function is_line_follow(){
	return get_theme_mod("is_line_follow", true);
}

function is_shere_top_of_content(){
	return get_theme_mod("is_shere_top_of_content", true);
}


/****  wp_head出力  ****/
add_action( 'wp_head', 'customize_css');
function customize_css(){ ?>
	<style>
	.main-color-background,
	.content h2{background-color: <?php echo get_main_color() ?>;}
	.main-color-font{color: <?php echo get_main_color() ?>;}
	.site-title, .site-title a, .site-desc, .content h2, .more-link, .min-more-link, .go-comment-arrow{color: <?php echo get_title_color() ?>;}
	.content h3{border-bottom: 3px solid <?php echo get_main_color() ?>;}
	.content h4{border-left: 8px solid <?php echo get_main_color() ?>;}
	.content h5{border-left: 3px solid <?php echo get_main_color() ?>}
	.share, .comments-header{border-bottom: 2px solid <?php echo get_main_color() ?>;}
	.sub-color-background{background-color: <?php echo get_sub_color() ?>;}

	.no-thumbnail p:hover,
	.post-title a:hover,
	.bread ul li a:hover,
	.site-title a:hover,
	.kanren-post-name a:hover,
	.same-tag a:hover,
	.same-category a:hover,
	.side .box a:hover,
	.footer a:hover{color: <?php echo get_link_hover_color() ?>;}

	.nav-inner ul li a:hover,
	.cat-link a:hover,
	.more-link:hover,
	.min-more-link:hover,
	.pagenavi .current,
	.pagenavi a:hover,
	.com-nav a:hover,
	.go-comment-arrow:hover,
	.search-submit:hover,
	.move-button:hover{background-color: <?php echo get_link_hover_color() ?>;}
	.no-thumbnail{height: <?php echo get_thumbnail_height() ?>;}
	.thumb-box{max-height: <?php echo get_thumbnail_height() ?>;}
	<?php if(get_option("is_shadow")){echo ".main-color-font, .content h2{text-shadow: 1px 1px 1px #333;}";} ?>
<?php
$count_followButtons = 1;
if(is_twitter_follow()){ $count_followButtons += 1; }
if(is_fb_follow()){ $count_followButtons += 1; }
if(is_g_follow()){ $count_followButtons += 1; }
if(is_line_follow()){ $count_followButtons += 1; }
$width_followButton = floor(100 / $count_followButtons);
?>
	.follow-icon{width: <?php echo $width_followButton; ?>%;}
	</style>
<?php
}

error_reporting(0);
/****  グローバルナビゲーション  ****/
add_action( 'after_setup_theme', 'register_my_menu' );
function register_my_menu() {
  register_nav_menu( 'nav', 'グローバルナビゲーション' );
}


/****  記事中にレクタングル大  ****/
function add_ads_before_1st_h2($the_content) {
	$is_no_adsense = get_post_meta(get_the_ID(),'is_no_adsense',true);

	if (is_singular() && !$is_no_adsense) {
		$ad;
		if(wp_is_mobile()){
			if(get_option("ad_responsive")){
				$ad = "<div class='ad'><p style='font-size:0.9em; color:#666; margin-bottom:0 !important;'>sponsored link</p>".get_option("ad_responsive")."</div>";
			}
		}else{
			if(get_option("ad_336")){
				$ad = "<div class='ad'><p style='font-size:0.9em; color:#666; margin-bottom:0 !important;'>sponsored link</p>".get_option("ad_336")."</div>";
			}
		}
		

		$h2 = '/<h2.*?>/i';
		if ( preg_match( $h2, $the_content, $h2s )) {//H2見出しが本文中にあるかどうか
			$the_content  = preg_replace($h2, $ad.$h2s[0], $the_content, 1);//最初のH2を置換
		}
	}
  return $the_content;
}
add_filter('the_content','add_ads_before_1st_h2');


/****  category_description()の<p>を削除  ****/
remove_filter('term_description', 'wpautop');


/****  関数定義  ****/
function year_month(){
	$date = single_month_title('',false);
	$point = strpos($date,'月');
	return mb_substr($date,$point+1).'年'.mb_substr($date,0,$point+1);
}

function day(){
	if(is_date() && !is_month()){
		$date = split(" ", wp_title('|', false, 'right'));
		$date = $date[0];
		return $date."日";
	}
}



?>