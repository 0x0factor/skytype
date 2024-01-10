<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/blog#">

<meta charset="UTF-8">
<meta name="viewport" content="width= device-width">	

<?php if(get_option("favicon_setting")){ ?>
<link rel="shortcut icon" href="<?php echo get_option("favicon_setting") ?>" >
<?php } ?>

<?php if("apple_setting"){ ?>
<link rel="apple-touch-icon" href="<?php echo get_option("apple_setting") ?>">
<?php } ?>

<!-- rss feed -->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />

<!-- IE8以下をhtml5に対応させる -->
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html.js" async></script>
<![endif]-->

<!-- ページタイトルを取得 -->
<?php
$page_title = wp_title('|', false, 'right').get_bloginfo('name');
?>

<title><?php echo $page_title ?></title>



<!-- description、サムネイルurlを取得 -->
<?php
$description;
$image_url = "";
if(get_option("logo_setting")){$image_url = get_option("logo_setting");}

if(is_front_page()){
	$description = get_bloginfo('description');
}else if(is_singular()){
	if(have_posts()){
		if(is_attachment()){
			$description = get_the_title();
		}else{
			$description = $post -> post_excerpt; 
			if($description===""){
				$description = mb_substr(str_replace("\"", "'", strip_tags($post-> post_content)),0,100).'...';
			}
			$image_id = get_post_thumbnail_id();
			$image_array = wp_get_attachment_image_src($image_id, true);
			if($image_array[0]!=""){
				$image_url = $image_array[0];
			}
		}
	}
}else if(is_category()){
	$description = category_description();
}else if(is_tag()){
	$description = get_bloginfo("name")."の".single_cat_title("", false)."に関する記事一覧";
}else if(is_date()){
	$description = get_bloginfo("name")."の".year_month().day()."に書かれた記事一覧";
}else{
	$description = get_bloginfo("name");
}

?>

<meta name="description" content="<?php echo $description; ?>">

<!-- ogp -->
<meta property="og:title" content="<?php echo $page_title ?>" >
<meta property="og:type" content="blog" />
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:url" content="<?php echo (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" >
<meta property="og:image" content="<?php echo $image_url ?>" >
<meta property="og:site_name" content="<?php bloginfo('name'); ?>" >
<meta property="fb:app_id" content="<?php echo get_option('app_id') ?>" >

<!-- twitter card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="https://twitter.com/<?php echo get_option('twitter_account') ?>">

<!-- タグページはnoindex -->
<?php if(is_tag()){ ?>
	<meta name="robots" content="noindex,follow">
<?php } ?>

<?php if (is_singular()) wp_enqueue_script( "comment-reply" ); ?>

<!-- 分割ページSEO -->
<?php error_reporting(0);
	global $paged, $wp_query;
	if ( !$max_page )$max_page = $wp_query->max_num_pages;
	if ( !$paged )
	$paged = 1;
	$nextpage = intval($paged) + 1;
	if ( null === $label )$label = __( 'Next Page &raquo;' );
	if ( !is_singular() && ( $nextpage <= $max_page ) ) {
?>
<link rel="next" href="<?php echo next_posts( $max_page, false ); ?>" />
<?php }
	global $paged;
	if ( null === $label )$label = __( '&laquo; Previous Page' );
	if ( !is_singular() && $paged > 1  ) 
	{
?>
<link rel="prev" href="<?php echo previous_posts( false ); ?>" />
<?php } ?>

<!-- Analytics -->
<?php if(get_option("analy_code")){
	if(get_option("reject_logged_in")){
		if(!is_user_logged_in()){
			echo get_option("analy_code");
		}
	}else{
		echo get_option("analy_code");
	} 
} ?>

<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>
<div class="header main-color-background">
<div class="header-inner">
<?php
$site_title;
if(get_header_image()){
	$site_title = "<img class='header-img' src='".get_header_image()."' alt='".get_bloginfo('name')."'>";
}else{
	$site_title = get_bloginfo('name');
} ?>

<?php if(is_front_page()){ ?>
	<?php if(get_header_image()){ ?>
		<h1><a href="<?php echo home_url() ?>"><?php echo $site_title ?></a></h1>
	<?php }else{ ?>
		<h1 class="site-title"><a href="<?php echo home_url() ?>"><?php echo $site_title ?></a></h1>
	<?php } ?>
<?php }else{ ?>
	<?php if(get_header_image()){ ?>
		<p><a href="<?php echo home_url() ?>"><?php echo $site_title ?></a></p>
	<?php }else{ ?>
		<p class="site-title"><a href="<?php echo home_url() ?>"><?php echo $site_title ?></a></p>
	<?php } ?>
<?php } ?>

<?php if(is_desc()){ ?><p class="site-desc"><?php bloginfo("description") ?></p><?php } ?>
</div><!-- .header-inner -->
</div><!-- .header -->
<nav class="sub-color-background">
<div class="nav-inner">
<p class="menu-mobile">MENU</p>
<?php wp_nav_menu(array(
		'theme_location' => 'nav'
	)); ?>
</div>
</nav>
<div class="main-side">
<div class="main">