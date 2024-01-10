<?php get_header() ?>

<div class="box content-box">
	<div class="content-header">
		<?php get_template_part("bread") ?>
		<h1 class="title"><?php the_title() ?></h1>
	</div><!-- content-header -->

<?php if(is_shere_top_of_content()){ ?>
	<?php get_template_part("share") ?>
<?php } ?>

<?php if(have_posts()): while(have_posts()): the_post(); ?>
	<div class="content">
		<?php if(has_post_thumbnail() && is_eyecatch()){
			$title= get_the_title(); the_post_thumbnail('full', array( 'alt' =>$title, 'class' => 'eye-catch'));
		} ?>
		<?php the_content() ?>
		<div class="clear"></div>
	</div><!-- .content -->
	
<?php endwhile; endif; ?>

	<?php get_template_part("ad_336") ?>

	<h2 class="share">シェアする</h2>
	<?php get_template_part("share") ?>

<!-- 記事下プロフィール欄 -->
<?php if(get_option("is_prof")){ ?>
	<div class="follow-underContent">
	<h2 class="share">書いている人</h2>
	<div class="box prof-box">
	<!--<h2 class="box-header main-color-font"><?php echo get_option("prof_title") ?></h2>-->
	<div class="image-text">
	<?php if(get_option("prof_image")){ ?>
		<img class="prof-image" src="<?php echo get_option("prof_image") ?>" alt="<?php echo get_option("prof_name") ?>">
	<?php } ?>
	<p class="prof-name"><?php echo get_option("prof_name") ?></p>
	<p class="prof-text">
	<?php echo get_option("prof_text") ?>
	<?php if(get_option("prof_url")){ ?><a style="text-decoration:underline; color:blue; display:inline-block" href="<?php echo get_option("prof_url") ?>">[詳細]</a><?php } ?></p>
	</div><!-- .image-text -->
	</div><!-- .prof-box-->
	
	<?php get_template_part("follow") ?>
	</div><!-- .folow-underContent -->
<?php } ?>

	<?php comments_template(); ?>
</div><!-- .content-box -->

<?php wp_reset_query(); ?>
<?php if(wp_is_mobile()){get_template_part("ad_336");} ?>

<?php get_template_part("cat_lists") ?>

<?php get_sidebar() ?>
<?php get_footer() ?>