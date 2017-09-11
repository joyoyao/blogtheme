<article class="span12<?php if( is_single() ) echo ' mobile-hide'; ?>" id="comments">
	<div class="panel">
		<header class="panel-header">
			<h3>
				<span class="dashicons dashicons-admin-comments"></span><?php _e( '评论', 'Bing' ); ?>
			</h3>
			<span class="right">
				<?php printf( __( '%s条评论', 'Bing' ), '<span class="comments-number">' . get_comments_number() . '</span>' ); ?>
			</span>
		</header>
		<ol class="comments-list<?php echo get_option( 'show_avatars' ) ? ' show-avatars' : ''; ?>">
			<?php if( have_comments() ) wp_list_comments( 'type=comment&callback=Bing_comments_list_loop&end-callback=Bing_comments_list_loop_end' ); ?>
		</ol>
		<?php
		$nav_code = paginate_comments_links( 'echo=0' );
		if( !empty( $nav_code ) ) echo '<nav class="comments-list-nav page-navi">' . $nav_code . '</nav>';
		unset( $nav_code );
		comment_form();
		?>
	</div>
</article>