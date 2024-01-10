<?php if(!is_front_page()){ ?>
<div class="bread" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
<ul>
	<li>
		<a href="<?php echo home_url(); ?>" itemprop="url"><span itemprop="title"><span class="lsf">home </span>Home</span></a>
	</li>

	<?php if(is_search()){ ?>
		&raquo;<li>検索結果</li>
	<?php }else if(is_tag()){ ?>
		&raquo;<li><span class="lsf">tag </span><?php single_tag_title(); ?></li>
	<?php }else if(is_category()){ ?>
		<?php
		$parents = array();

		//現在表示しているページのカテゴリー情報を$tmpに格納
		$cat = get_queried_object();
		$tmp = $cat;

		//現在のページの親が無くなるまで処理を繰り返す	
		while( $tmp->parent ){

			//現在のページの親カテゴリーの情報を取得して$parentsの先頭に追加
			$tmp = get_category( $tmp->parent );
			array_unshift($parents , $tmp);
		}

		//パンくずの変数に格納されている情報の数だけ繰り返しカテゴリーページへのリンクとカテゴリー名を表示
		foreach( $parents as $parent ){ 
		?>
			&raquo;
			<li>
				<a href="<?php echo get_category_link( $parent->term_id ); ?>" itemprop="url">
					<span class="lsf">folder </span><span itemprop="title"><?php echo $parent->name; ?></span>
				</a>
			</li>
		<?php } ?>
		&raquo;
		<li>
			<span class="lsf">folder </span><span itemprop="title"><?php echo $cat->name; ?></span>
		</li>

	<?php }else if(is_404()){ ?>
		&raquo;<li>404 NOT FOUND</li>
	<?php }else if(is_month()){ ?>
		&raquo;
		<li>
			<a href="<?php echo get_month_link(get_query_var('year'), get_query_var('monthnum')); ?>">
				<?php echo get_query_var('year'); ?>年<?php echo get_query_var('monthnum'); ?>月
			</a>
		</li>
	
	<?php }else if(is_singular()){ ?>
		<?php error_reporting(0);
		$postcat = get_the_category();
		$catid = $postcat[0]->cat_ID;
		$allcats = array($catid);
		while(!$catid==0) {
			$mycat = get_category($catid);
			$catid = $mycat->parent;
			array_push($allcats, $catid);
		}
		array_pop($allcats);
		$allcats = array_reverse($allcats);
		
		?>
		<?php foreach($allcats as $catid): ?>
			&raquo;
			<li>
				<a href="<?php echo get_category_link($catid); ?>" itemprop="url">
				<span class="lsf">folder </span><span itemprop="title"><?php echo get_cat_name($catid); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
		&raquo;
	<?php } ?>
</ul>
</div><!-- .bread -->
<?php } ?>