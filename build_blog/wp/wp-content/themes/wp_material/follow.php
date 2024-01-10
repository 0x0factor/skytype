<?php
$twitter_account = get_option('twitter_account'); 
$facebook_url = get_option('facebook_url');
$g = get_option('prof_g');
$line_id = strtolower(get_option('line_id')); 
?>

<!--  フォローボタン  -->
<div class="box follow-box">
<h2 class="box-header main-color-font">フォローする</h2>

<?php if(is_twitter_follow()){ ?>
	<?php if($twitter_account){ ?>
		<a href="https://twitter.com/<?php echo $twitter_account ?>" rel="nofollow" target="_blank">
	<?php } ?>
	<p class="lsf follow-icon twitter<?php if(!$twitter_account){ echo " not";} ?>">twitter</p>
	<?php if($twitter_account){ ?>
		</a>
	<?php } ?>
<?php } ?>

<?php if(is_fb_follow()){ ?>
	<?php if($facebook_url){ ?>
		<a href="<?php echo $facebook_url ?>" rel="nofollow" target="_blank">
	<?php } ?>
	<p class="lsf follow-icon fb<?php if(!$facebook_url){ echo " not";} ?>">facebook</p>
	<?php if($facebook_url){ ?>
		</a>
	<?php } ?>
<?php } ?>

<?php if(is_g_follow()){ ?>
	<?php if($g){ ?>
		<a href="https://plus.google.com/u/0/<?php echo $g?>" rel="nofollow" target="_blank">
	<?php } ?>
	<p class="lsf follow-icon g-plus<?php if(!$g){ echo " not";} ?>">google</p>
	<?php if($g){ ?>
		</a>
	<?php } ?>
<?php } ?>

<a href="http://feedly.com/i/subscription/feed/<?php bloginfo('rss2_url'); ?>" rel="nofollow" target="_blank">
<p class="lsf follow-icon feedly">feed</p>
</a>

<?php if(is_line_follow()){ ?>
	<?php if($line_id){ ?>
		<a href="http://line.me/ti/p/%40<?php echo $line_id; ?>" rel="nofollow" target="_blank">
	<?php } ?>
	<p class="lsf follow-icon line<?php if(!$line_id){ echo " not";} ?>">line</p>
	<?php if($line_id){ ?>
		</a>
	<?php } ?>
<?php } ?>

<div class="clear"></div>
</div>