<?php if(function_exists('get_scc_twitter')){ ?>
	<p class="share-count-top"><span class="lsf twitter">twitter </span><?php echo get_scc_twitter() ?></p>
<?php } ?>

<?php if(function_exists('get_scc_facebook')){ ?>
	<p class="share-count-top"><span class="lsf fb">facebook </span><?php echo get_scc_facebook(); ?></p>
<?php } ?>

<?php if(function_exists('get_scc_hatebu')){ ?>
	<p class="share-count-top"><span class="lsf hatebu">hatenabookmark </span><?php echo get_scc_hatebu(); ?></p>
<?php } ?>