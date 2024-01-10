</div><!-- .main -->
<div class="side">

<?php
$twitter_account = get_option('twitter_account'); 
$facebook_url = get_option('facebook_url'); 
$line_id = strtolower(get_option('line_id')); 
?>

<div class="side-left">

<?php dynamic_sidebar(3); ?>

<!--  検索フォーム  -->
<form role="search" method="get" id="searchform" class="searchform" action="<?php echo home_url() ?>">
<div class="search-box">
	<input class="search-text" type="text" value="" name="s" id="s" placeholder="サイト内検索">
	<input class="search-submit lsf sub-color-background" type="submit" id="searchsubmit" value="search">
</div>
</form>
<div class="clear"></div>

<!--  シェアボタン  -->
<?php if(!is_singular()){ ?>
<h2 class="side-share main-color-font">シェアする</h2>
<?php get_template_part("share") ?>
<?php } ?>

<?php get_template_part("follow") ?>

<!-- プロフィール欄 -->
<?php if(get_option("is_prof")){ ?>
<div class="box prof-box">
<h2 class="box-header main-color-font"><?php echo get_option("prof_title") ?></h2>
<div class="image-text">
<?php if(get_option("prof_image")){ ?>
	<img class="prof-image" src="<?php echo get_option("prof_image") ?>" alt="<?php echo get_option("prof_name") ?>">
<?php } ?>
<p class="prof-name"><?php echo get_option("prof_name") ?></p>
<p class="prof-text">
	<?php echo get_option("prof_text") ?>
	<?php if(get_option("prof_url")){ ?><a style="text-decoration:underline; color:blue; display:inline-block" href="<?php echo get_option("prof_url") ?>">[詳細]</a><?php } ?></p>
</div><!-- .image-text -->

<?php if(get_option("prof_twitter")){ ?>
	<div class="twiiter-follow">
	<a href="https://twitter.com/<?php echo $twitter_account; ?>" class="twitter-follow-button" data-show-count="true" data-lang="en" data-dnt="true">Follow @<?php echo $twitter_account; ?></a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
	</div><!-- .prof-twitter -->
<?php } ?>

<?php if(get_option("prof_g")){ ?>
	<script src="https://apis.google.com/js/platform.js" async defer></script>
	<div class="g-follow" data-annotation="bubble" data-height="20" data-href="//plus.google.com/<?php echo get_option("prof_g") ?>" data-rel="publisher"></div>
<?php } ?>

<?php if(get_option("prof_fb")){
	if(get_option("facebook_url")){ ?>
		<div class="like-box">
		<div class="fb-page" data-href="<?php echo get_option("facebook_url") ?>" data-width="500px" data-hide-cover="false" data-show-facepile="true" data-show-posts="<?php if(get_option("prof_fb_timeline")){echo "true";}else{echo "false";} ?>"></div>
		</div>
	<?php }else{ ?>
		<p style="font-size:0.8em;">LikeBoxを表示するには、<span class="b">テーマカスタマイザー</span>→<span class="b">SNS設定</span>→<span class="b">FacebookページURL</span>を入力してください</p>
	<?php } ?>
<?php } ?>

</div><!-- .prof-box -->
<?php } ?>


<?php dynamic_sidebar(1); ?>
</div><!-- .side-left -->

<div class="side-right">
<?php if(!wp_is_mobile()){
	get_template_part("ad_large");
}else{
	if(!is_singular()){get_template_part("ad_large");}
} ?>

<?php dynamic_sidebar(2); ?>
<?php dynamic_sidebar(4); ?>
</div><!-- .side-right -->

</div><!-- .side -->
</div><!-- .main-side -->