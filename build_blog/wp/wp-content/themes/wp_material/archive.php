<?php get_header() ?>
<?php get_template_part("ad_728") ?>

<div class="box big-box">
<?php get_template_part("bread") ?>


<h1 class="box-header main-color-font">
<?php if(is_category()){ ?><span class="lsf">folder </span><?php single_cat_title(); ?>
<?php }else if(is_tag()){ ?><span class="lsf">tag </span><?php single_cat_title(); ?>
<?php }else if(is_date()){ ?><span class="lsf">time </span><?php echo year_month(); echo day() ?>
<?php }else if(is_author()){ ?><span class="lsf">user </span><?php the_author(); ?>が書いた記事
<?php } ?>
</h1>

<?php error_reporting(0);
if(is_category() && category_description != ""){ ?><div class="cat-desc"><?php echo category_description(); ?></div><?php } ?>

<?php get_template_part("loop") ?>

</div><!-- .big-box -->

<?php get_template_part("cat_lists") ?>

<?php get_sidebar() ?>
<?php get_footer() ?>