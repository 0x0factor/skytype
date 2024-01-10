<div class="footer sub-color-background">

<p><a class="footer-name" href="<?php echo home_url() ?>">&copy;<?php bloginfo("name") ?></a> All Rights Reserved.</p>

<?php if(is_button_set()){ ?>
	<p class="lsf move-button to-top sub-color-background">arrowup</p>
	<?php if(!is_front_page()){ ?>
		<a href="<?php echo home_url() ?>"><p class="lsf to-home move-button sub-color-background">home</p></a>
	<?php } ?>
<?php } ?>

</div><!-- .footer -->

<?php if(get_option("prof_fb")){ ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&appId=<?php get_option("app_id") ?>&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php } ?>

<?php wp_footer() ?>
<?php if(is_menu_fixed()){ ?><script src='<?php echo get_template_directory_uri() ?>/ui_script.js' async></script><?php } ?>
</body>
</html>