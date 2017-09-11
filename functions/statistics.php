<?php
/**
	*文章访问计数
	*http://www.bgbk.org
*/
function Bing_post_statistics_views(){
	if( !is_single() ) return;
	$post_views = (int) get_post_meta( get_the_ID(), 'views', true );
	update_post_meta( get_the_ID(), 'views', $post_views + 1 );
}
add_action( 'template_redirect', 'Bing_post_statistics_views' );
 
/**
	*获取计数
	*http://www.bgbk.org
*/
function Bing_post_views( $post = null ){
	$post = get_post( $post );
	$views = (int) get_post_meta( $post->ID, 'views', true );
	return apply_filters( 'post_views', number_format_i18n( $views ), $post, $views );
}

//End of page.
