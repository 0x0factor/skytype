<?php
$url=(empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$title = wp_title('|', false, 'right').get_bloginfo('name');
$url_encoded=urlencode(mb_convert_encoding($url, "UTF-8"));
$title_encoded=urlencode(mb_convert_encoding($title, "UTF-8"));
?>


<?php error_reporting(0);
$is_scc_old_version;
if(function_exists('scc_get_share_twitter') || function_exists('scc_get_share_facebook') || function_exists('scc_get_share_hatebu') || function_exists('scc_get_share_pocket')){ 
	$path = WP_PLUGIN_DIR."/sns-count-cache/sns-count-cache.php";
	$scc_data = get_file_data($path, array('version' => 'Version'));
	$scc_version = $scc_data[version];
	$ver_num = intval(str_replace(".", "", $scc_version));
	$is_scc_old_version = $ver_num < 60;
}
?>


<div class="share-buttons">

<!-- twitter -->
<?php error_reporting(0);
$count_blind_twitter = false;
if(!function_exists('scc_get_share_twitter')){
	$count_blind_twitter = true;
}
if($is_scc_old_version && !is_singular()){
	$count_blind_twitter = true;
}
if(!$is_scc_old_version && !is_singular() && !is_front_page()){
	$count_blind_twitter = true;
}
?>
<div class="share-count-button">
<a class="no-deco" target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo $url_encoded ?>&text=<?php echo $title_encoded; if(get_option("mention")){echo "&via=".get_option("twitter_account");} ?>">
<p class="share-button twitter lsf" style="<?php if($count_blind_twitter){ ?>line-height:55px; font-size:2.2em;<?php } ?>">twitter</p>
</a>
<?php if(function_exists('scc_get_share_twitter')){
	if(is_singular()){ ?>
		<p class="share-count"><?php echo scc_get_share_twitter(); ?></p>
	<?php }else if(is_front_page() && !$is_scc_old_version){ ?>
		<p class="share-count"><?php echo scc_get_share_twitter(array( post_id => 'home' )); ?></p>
	<?php } ?>
<?php } ?>
</div>

<!-- facebook -->
<?php error_reporting(0);
$count_blind_fb = false;
if(!function_exists('scc_get_share_facebook')){
	$count_blind_fb = true;
}
if($is_scc_old_version && !is_singular()){
	$count_blind_fb = true;
}
if(!$is_scc_old_version && !is_singular() && !is_front_page()){
	$count_blind_fb = true;
}
?>
<div class="share-count-button">
<a class="no-deco" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url_encoded; ?>&t=<?php echo $title_encoded; ?>">
<p class="share-button facebook lsf" style="<?php if($count_blind_fb){ ?>line-height:55px; font-size:2.2em;<?php } ?>">facebook</p>
</a>
<?php if(function_exists('scc_get_share_facebook')){
	if(is_singular()){ ?>
		<p class="share-count"><?php echo scc_get_share_facebook(); ?></p>
	<?php }else if(is_front_page() && !$is_scc_old_version){ ?>
		<p class="share-count"><?php echo scc_get_share_facebook(array( post_id => 'home' )); ?></p>
	<?php } ?>
<?php } ?>
</div>

<!-- hatebu -->
<?php error_reporting(0);
$count_blind_hatebu = false;
if(!function_exists('scc_get_share_hatebu')){
	$count_blind_hatebu = true;
}
if($is_scc_old_version && !is_singular()){
	$count_blind_hatebu = true;
}
if(!$is_scc_old_version && !is_singular() && !is_front_page()){
	$count_blind_hatebu = true;
}
?>
<div class="share-count-button">
<a class="no-deco" target="_blank" href="http://b.hatena.ne.jp/add?mode=confirm&url=<?php echo $url_encoded; ?>&title=<?php echo $title_encoded; ?>">
<p class="share-button hatebu lsf" style="<?php if($count_blind_hatebu){ ?>line-height:55px; font-size:2.2em;<?php } ?>">hatenabookmark</p>
</a>
<?php if(function_exists('scc_get_share_hatebu')){
	if(is_singular()){ ?>
		<p class="share-count"><?php echo scc_get_share_hatebu(); ?></p>
	<?php }else if(is_front_page() && !$is_scc_old_version){ ?>
		<p class="share-count"><?php echo scc_get_share_hatebu(array( post_id => 'home' )); ?></p>
	<?php } ?>
<?php } ?>
</div>

<!-- pocket -->
<?php error_reporting(0);
$count_blind_pocket = false;
if(!function_exists('scc_get_share_pocket')){
	$count_blind_pocket = true;
}
if($is_scc_old_version && !is_singular()){
	$count_blind_pocket = true;
}
if(!$is_scc_old_version && !is_singular() && !is_front_page()){
	$count_blind_pocket = true;
}
?>
<div class="share-count-button">
<a class="no-deco" target="_blank" href="http://getpocket.com/edit?url=<?php echo $url_encoded; ?>&title=<?php echo $title_encoded; ?>">
<p class="share-button pocket" style="<?php if($count_blind_pocket){ ?>line-height:55px; font-size:2.2em;<?php } ?>"><span class="icon-pocket"></span></p>
</a>
<?php if(function_exists('scc_get_share_pocket')){
	if(is_singular()){ ?>
		<p class="share-count"><?php echo scc_get_share_pocket(); ?></p>
	<?php }else if(is_front_page() && !$is_scc_old_version){ ?>
		<p class="share-count"><?php echo scc_get_share_pocket(array( post_id => 'home' )); ?></p>
	<?php } ?>
<?php } ?>
</div>


<div class="share-count-button">
<a class="no-deco" target="_blank" href="http://line.me/R/msg/text/?<?php echo $title_encoded." "; ?><?php echo $url_encoded; ?>">
<p class="share-button lsf line">line</p>
</a>
</div>
</div><!-- .share-buttons -->