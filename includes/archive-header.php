<?php
/**
	*存档页信息
	*http://www.bgbk.org
*/
function Bing_archive_header(){
	if( !is_archive() ) return;
	if( is_author() ){
		$dashicons = 'admin-users';
		$name = $GLOBALS['authordata']->display_name;
		$description = get_the_author_meta( 'description' );
		$feed_link = get_author_feed_link( $GLOBALS['authordata']->ID );
	}elseif( is_date() ){
		$dashicons = 'calendar';
		if( is_day() ) $format = __( 'Y年m月d日', 'Bing' );
		elseif( is_month() ) $format = __( 'Y年m月', 'Bing' );
		else $format = __( 'Y年', 'Bing' );
		$name = get_the_date( $format );
		$description = sprintf( __( '%s发布的文章', 'Bing' ), $name );
	}else{
		$dashicons = is_tag() ? 'tag' : 'category';
		$name = single_term_title( '', false );
		$description = term_description();
		$feed_link = get_term_feed_link( get_queried_object_id(), get_queried_object()->taxonomy );
	}
	$description = strip_tags( $description );
?>
	<div class="span12 archive-header">
		<article class="panel">
			<header class="panel-header">
				<h2 class="archive-title">
					<span class="dashicons dashicons-<?php echo $dashicons; ?>"></span><?php echo $name; ?>
				</h2>
				<?php if( Bing_mpanel( 'breadcrumbs' ) ) Bing_breadcrumbs( '<span class="separator dashicons dashicons-arrow-right-alt2"></span>', '<span class="right breadcrumb"%s>', '</span>', '<span class="dashicons dashicons-admin-home"></span>' . __( '首页', 'Bing' ) ); ?>
			</header>
			<?php
			echo empty( $description ) ? __( '无描述', 'Bing' ) : $description;
			if( !empty( $feed_link ) ) printf( '<a href="%s" title="%s" class="feed-link"><span class="dashicons dashicons-rss"></span></a>', esc_url( $feed_link ), esc_attr( __( '此存档的 Feed 源，可以使用 RSS 阅读器订阅这些内容', 'Bing' ) ) );
			?>
		</article>
	</div>
<?php
}

//End of page.
