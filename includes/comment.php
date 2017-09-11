<?php
/**
	*评论列表样式
	*http://www.bgbk.org
*/
function Bing_comments_list_loop( $comment, $args = null, $depth = 0 ){
	$GLOBALS['comment'] = $comment;
?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( get_option( 'show_avatars' ) ? 'show-avatars' : '' ); ?>>
		<article id="comment-box-<?php comment_ID(); ?>" class="comment-box">
			<?php echo get_avatar( $comment, 42 ); ?>
			<div class="right-box">
				<p class="comment-meta">
					<span class="author"><?php comment_author_link(); ?></span>
					<time class="time" pubdate="pubdate"><?php comment_time( 'Y-m-d H:i:s' ); ?></time>
					<?php
					edit_comment_link( __( '编辑', 'Bing' ), '<span class="edit-link">', '</span>' );
					if( !empty( $args ) ) comment_reply_link( wp_parse_args( $args, array(
						'add_below'  => 'comment-box',
						'before'     => ' <span class="reply">',
						'after'      => '</span>',
						'reply_text' => __( '回复', 'Bing' ),
						'depth'      => $depth
					) ) );
					?>
				</p>
				<?php
				comment_text();
				if( $comment->comment_approved == '0' ) echo '<span class="waiting">' . __( '评论正在审核中...', 'Bing' ) . '</span>';
				?>
			</div>
		</article>
<?php
}

/**
	*评论列表样式（结尾）
	*http://www.bgbk.org
*/
function Bing_comments_list_loop_end(){
	echo '</li>';
}

/**
	*添加表情按钮
	*http://www.bgbk.org
*/
function Bing_smiley_button(){
	if( !get_option( 'use_smilies' ) ) return;
?>
	<div class="comment-form-smiley no-js-hide">
		<a href="javascript:;" title="<?php esc_attr_e( '插入表情', 'Bing' ); ?>" class="button">
			<span class="dashicons dashicons-smiley"></span>
		</a>
		<div class="smiley-box"></div>
	</div>
<?php
}
add_action( 'comment_form', 'Bing_smiley_button' );

//End of page.
