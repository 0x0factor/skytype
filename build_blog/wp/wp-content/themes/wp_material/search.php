<?php get_header() ?>

<?php get_template_part("ad_728") ?>

<div class="box big-box">
<?php get_template_part("bread") ?>

<h1 class="box-header main-color-font"><span class="lsf">search </span>「<?php the_search_query(); ?>」を含む記事</h1>

<?php get_template_part("loop") ?>

</div><!-- .big-box -->


<?php get_template_part("cat_lists") ?>


<?php get_sidebar() ?>
<?php get_footer() ?>