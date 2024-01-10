<?php 
$count_cat = get_count_cat_post(); //カテゴリーごとに表示する記事数
?>

<!--  カテゴリーごと  -->
<?php $args = array('parent'=>0);
$categories = get_categories($args);
$count = 1;
foreach($categories as $category){ ?>
	<div class="min-box<?php if($count%2==0){echo " right";}else{echo " left";} ?>">
		<a class="no-deco" href="<?php echo home_url() ?>/?cat=/<?php echo $category->cat_ID ?>"><p class="min-more-link main-color-background">more</p></a>
		<h2 class="box-header main-color-font"><span class="lsf-icon" title="folder"></span><?php echo $category->name; ?></h2>
		
		<?php $cat_id = $category->cat_ID;
		$args = array('cat'=>$cat_id, 'posts_per_page' => $count_cat);
		$the_query = new WP_Query($args);
		if($the_query->have_posts()){
			while($the_query->have_posts()){
				$the_query->the_post(); ?>

				<div class="post">
					<div class="thumb-box">
					<?php if(has_post_thumbnail()){ ?>
						<a href="<?php the_permalink() ?>">
						<?php $title= get_the_title(); the_post_thumbnail(array(100, 100), array( 'alt' =>$title, 'title' => $title)); ?>
						</a>
					<?php }else{ ?>
						<a href="<?php the_permalink() ?>" class="no-deco">
							<div class="no-thumbnail sub-color-background"><p>No thumbnail</p></div>
						</a>
					<?php } ?>
					</div><!-- .thumb-box -->

					<div class="post-info">
						<p class="up-date"><?php the_time('Y/m/d') ?></p>
						<?php get_template_part("sns_count") ?>
						<h3 class="post-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
					</div><!-- .post-info -->
				</div><!-- .post -->
<?php
			}
		}
		
		
?>
	</div><!-- .min-box -->
<?php
if($count==2 && !is_singular()){get_template_part("ad_728");}
$count++;
}
?>