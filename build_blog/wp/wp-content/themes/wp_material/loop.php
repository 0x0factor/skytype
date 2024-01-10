<?php
$row_new = get_row_new_post();   //New postで表示する行数
$count = 1;
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php if($count == $row_new*3+1 && !is_paged() && is_home()){ echo "<div class='hide'>"; } ?>
	<div class="post<?php if($count%2==0){echo ' col-2';} if($count%3==0){echo ' col-3';} ?>">
	<div class="thumb-box sub-color-background">
	<?php if(has_post_thumbnail()){ ?>
		<a href="<?php the_permalink() ?>">

		<?php $title= get_the_title(); the_post_thumbnail(array(400, 400), array( 'alt' =>$title, 'title' => $title)); ?>
		</a>
	<?php }else{ ?>
		<a href="<?php the_permalink() ?>" class="no-deco">
		<div class="no-thumbnail"><p>No thumbnail</p></div>
		</a>
	<?php } ?>
	</div><!-- .thumb-box -->
	<div class="post-info">
	<p class="up-date"><span class="lsf">time </span><?php the_time('Y/m/d') ?></p>
	<h3 class="post-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
	<?php get_template_part("sns_count") ?>
	<p class="cat-link"><span class="lsf cat-folder">folder </span><?php the_category(' ') ?></p>
	</div><!-- .post-info -->
	</div><!-- .post -->
<?php
if($count==6){ ?>
	
<?php }
if($count%2==0){echo "<div class='clear2'></div>";}
if($count%3==0){echo "<div class='clear3'></div>";}
$count++;
?>

<?php
endwhile;
if (function_exists("pagination")) {
	pagination();
}
?>

<?php if($count > $row_new*3+1 && !is_paged() && is_home()){ ?>
	</div><!-- .hide -->
<div class="clear"></div>
	<!--<div class="big-box-bottom-line"></div>-->
	<p class="more-link main-color-background">more<br><span class="lsf">down</span></p>
<?php }else{ ?>
	<div class="clear"></div>
<?php } ?>
<?php else: ?>
	<p style="margin-bottom:30px;">お探しの記事はありません</P>
<?php endif; ?>