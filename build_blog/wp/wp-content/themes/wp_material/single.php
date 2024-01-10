<?php get_header() ?>
<div class="box content-box">
	<div class="content-header">
		<?php get_template_part("bread") ?>
		<h1 class="title"><?php the_title() ?></h1>
		<p class="up-date"><span class="lsf">time </span><?php the_time('Y/m/d') ?></p>
		<p class="cat-link"><span class="lsf cat-folder">folder </span><?php the_category(' ') ?></p>
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

<div class="space"></div>

<!--同タグ・同カテゴリー記事を出力-->
<?php error_reporting(0);
$count = get_count_kanren();
if($count > 0){
	$tags = get_the_tags();
	$categories = get_the_category();
	$id = get_the_ID();
	$args;


	if($categories){
		$cat_array = array();
		foreach($categories as $category){
			array_push($cat_array, $category->slug);
			if($cats_string)$cats_string .= ",";
			$cats_string .= $category->slug;
		}
	}

	if($tags){
		$tag_array = array();
		foreach($tags as $tag){	
			array_push($tag_array, $tag->slug);
		}

		$args = array(
			'tax_query' => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => $cat_array,
					'include_children' => true,
					'operator' => 'IN'
				),
				array(
					'taxonomy' => 'post_tag',
					'field' => 'slug',
					'terms' => $tag_array,
					'include_children' => false,
					'operator' => 'IN'
				)
			),
			'post__not_in' => array($id),
			'orderby'=>'rand',
			'posts_per_page' => $count
		);
	}else{
		$args = array(
			'category_name' => $cats_string,
			'post__not_in' => array($id),
			'orderby'=>'rand',
			'posts_per_page' => $count
		);
	}

	$the_query = new WP_Query($args);
	if($the_query->post_count > 0){ ?>
		<div class="kanren">
		<h3 class="tag-header">関連記事</h3>
		<?php if($the_query->have_posts()){ ?>
			<ul>
			<?php while($the_query->have_posts()){$the_query->the_post(); ?>
			<li class="same-tag-post">
				<div class="thumb-box">
				<?php if(has_post_thumbnail()){ ?>
					<a href="<?php the_permalink() ?>">
						<?php $title= get_the_title(); the_post_thumbnail(array(100, 100), array( 'alt' =>$title, 'title' => $title)); ?>
					</a>
				<?php }else{ ?>
					<a href="<?php the_permalink() ?>" class="no-deco">
						<div class="no-thumbnail sub-color-background"><p>　No thumbnail</p></div>
					</a>
				<?php } ?>
				</div><!-- .thumb-box -->
				<p class="kanren-post-name"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></p>
				<?php get_template_part("sns_count") ?>
			</li>
			<?php } ?>
			</ul>
			<div class="clear"></div>
		<?php	}

		if($tags){ ?>
			<div class="same-tags">
			<?php foreach($tags as $tag){ ?>
				<p class="same-tag">
				<a href="<?php echo home_url().'/?tag='.$tag->slug; ?>"><span class="lsf">tag </span><?php echo $tag->name; ?></a>
				</p>
			<?php } ?>
			</div><!-- .same-tags -->
		<?php } ?>

		<?php if($categories){ ?>
			<div class="same-categories">
			<?php foreach($categories as $category){ ?>
				<p class="same-category">
				<a href="<?php echo home_url().'/?cat='.$category->cat_ID; ?>"><span class="lsf">folder </span><?php echo $category->name; ?></a>
				</p>
			<?php } ?>
			</div><!-- .same-categories -->
		<?php } ?>
		</div><!-- .kanren -->
	<?php }
} ?>

<?php endwhile; endif; ?>

</div><!-- .content-box -->

<?php wp_reset_query(); ?>
<?php if(wp_is_mobile()){get_template_part("ad_336");} ?>

<?php get_template_part("cat_lists") ?>

<?php get_sidebar() ?>
<?php get_footer() ?>