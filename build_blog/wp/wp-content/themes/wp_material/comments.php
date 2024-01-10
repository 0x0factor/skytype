<div id="comments">
<?php if(have_comments()): ?>
	<h2 class="comments-header">コメント</h2>
	<ul>
		<?php wp_list_comments('callback=mydesign'); ?>
	</ul>
	
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :?>
		<div class="com-nav" role="navigation">
			<div class="com-back"><?php previous_comments_link( __( '&laquo; 古いコメント', '' ) ); ?></div>
			<div class="com-next"><?php next_comments_link( __( '新しいコメント &raquo;', '' ) ); ?></div>
		</div><!-- .com-nav -->
		<div class="clear"></div>
	<?php endif; ?>

<?php endif; ?>


<?php
// デフォルト値取得
$commenter = wp_get_current_commenter();
$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );

//$fields設定
$fields = array(
    'author' => '<p class="input-info"><label for="author">' . 
    			'Name' . ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
                '<br /><input id="author" name="author" type="text" value="' . 
                esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',

    'email'  => '<p class="input-info"><label for="email">' . 
    			'Email'.( $req ? '<span class="required">*</span>（公開されません）' : '' ) . '</label> ' .
    			'<br /><input id="email" name="email" type="text" value="'.
				esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',

    'url'    => '<p class="input-info"><label for="url">' . 
    			'Website' . '</label>' .
                '<br /><input id="url" name="url" type="text" value="' . 
                esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
    ); 

$fields = apply_filters( 'comment_form_default_fields', $fields );

// $comment_notes_before設定
$comment_notes_before = NULL;

// $comment_notes_after
$comment_notes_after = NULL;

// $args設定
$args = array(
	'fields' => apply_filters( 'comment_form_default_fields', $fields ),
	'title_reply' => '<p class="go-comment-arrow lsf main-color-background">down</p>コメントする',
	'comment_notes_before' 	=> $comment_notes_before,
	'comment_notes_after'   => $comment_notes_after
);
?>

<?php comment_form($args); ?>


</div><!-- #comments -->