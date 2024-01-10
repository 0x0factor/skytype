<?php 
wp_reset_query();
$is_no_adsense = get_post_meta(get_the_ID(),'is_no_adsense',true);
if(!$is_no_adsense || !is_singular()){
	if(!is_404()){ ?>
		<?php if(!wp_is_mobile()){
			if(get_option("ad_large")){ ?>
				<div class="ad">
				<p style="font-size:0.8em; color:#666; margin-bottom:0; text-align:center;">sponsored link</p>
				<?php echo get_option("ad_large"); ?>
				</div>
			<?php }
		}else{
			if(get_option("ad_responsive")){ ?>
				<div class="ad">
	<p style="font-size:0.8em; color:#666; margin-bottom:0; text-align:center;">sponsored link</p>
				<?php echo get_option("ad_responsive"); ?>
				</div>
			<?php }
		}
	}
} ?>