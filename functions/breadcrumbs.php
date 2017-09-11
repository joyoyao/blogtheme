<?php
/**
	*面包屑导航
	*http://www.bgbk.org
*/
function Bing_breadcrumbs( $separator = '/', $before = '<div class="breadcrumb"%s>', $after = '</div>', $home = '' ){
	if( is_home() || is_front_page() ) return;
	printf( $before, ' itemscope itemtype="http://schema.org/WebPage"' );
		$post = get_post();
		$home = empty( $home ) ? __( '首页', 'Bing' ) : $home;
		echo '<a itemprop="breadcrumb" href="' . esc_url( home_url() ) . '" title="' . esc_attr__( '首页', 'Bing' ) . '">' . "$home</a>$separator";
		if( is_category() || is_tag() || is_tax() ){
			if( get_queried_object()->parent != 0 ) echo Bing_taxonomy_parents( get_queried_object()->parent, get_queried_object()->taxonomy, $separator );
			single_term_title();
		}elseif( is_attachment() ){
			$parent = get_post( $post->post_parent );
			echo '<a itemprop="breadcrumb" href="' . esc_url( get_permalink( $parent ) ) . '">' . get_the_title( $parent ) . '</a>' . $separator;
			echo get_the_title();
		}elseif( is_single() ){
			if( get_post_type() == 'post' ){
				$post_category = get_the_category();
				echo Bing_taxonomy_parents( $post_category[0], 'category', $separator );
				_e( '正文', 'Bing' );
			}else{
				$post_type = get_post_type_object( get_post_type() );
				echo '<a itemprop="breadcrumb" href="' . esc_url( home_url( $post_type->rewrite ) ) . '">' . $post_type->labels->singular_name . '</a>' . $separator;
				_e( '正文', 'Bing' );
			}
		}elseif( is_page() ){
			if( empty( $post->post_parent ) ) echo get_the_title();
			else{
				$parent_id = $post->post_parent;
				$breadcrumbs = array();
				while( $parent_id ){
					$page = get_page( $parent_id );
					$breadcrumbs[] = '<a itemprop="breadcrumb" href="' . esc_url( get_permalink( $page->ID ) ) . '">' . get_the_title( $page->ID ) . '</a>';
					$parent_id = $page->post_parent;
				}
				foreach( array_reverse( $breadcrumbs ) as $breadcrumb ) echo $breadcrumb . $separator;
				echo get_the_title();
			}
		}elseif( is_day() ){
			echo '<a itemprop="breadcrumb" href="' . esc_url( get_year_link( get_post_time( 'Y' ) ) ) . '">' . get_post_time( 'Y' ) . '</a>' . $separator;
			echo '<a itemprop="breadcrumb"  href="' . esc_url( get_month_link( get_post_time( 'Y' ), get_post_time( 'm' ) ) ) . '">' . get_post_time( 'F' ) . '</a> ' . $separator . ' ';
			echo get_post_time( 'd' );
		}elseif( is_month() ){
			echo '<a itemprop="breadcrumb" href="' . esc_url( get_year_link( get_post_time( 'Y' ) ) ) . '">' . get_post_time( 'Y' ) . '</a>' . $separator;
			echo get_post_time( 'F' );
		}elseif( is_year() ) echo get_post_time( 'Y' );
		elseif( is_search() ) _e( '搜索', 'Bing' );
		elseif( is_author() ) printf( __( '作者：%s', 'Bing' ), $GLOBALS['authordata']->display_name );
		elseif( is_404() ) echo 'HTTP 404: Not Found';
		elseif( !is_single() && !is_page() && get_post_type() != 'post' ) echo get_post_type_object( get_post_type() )->labels->singular_name;
		if( get_query_var( 'paged' ) && ( is_category() || is_date() || is_search() || is_tag() || is_author() ) ) printf( __( '（第 %s 页）', 'Bing' ), get_query_var( 'paged' ) );
	echo $after;
}

/**
	*生成分类法层级目录
	*http://www.bgbk.org
*/
function Bing_taxonomy_parents( $ID, $taxonomy = 'category', $separator = '/' ){
	$parent = get_term( $ID, $taxonomy );
	$visited = array();
	$result = '';
	if( $parent->parent != 0 && $parent->parent != $parent->term_id && !in_array( $parent->parent, $visited ) ){
		$visited[] = $parent->parent;
		$result .= Bing_taxonomy_parents( $parent->parent, $taxonomy, $separator );
	}
	$result .= sprintf( '<a itemprop="breadcrumb" href="%s">%s</a>%s', esc_url( get_term_link( $parent->term_id, $taxonomy ) ), $parent->name, $separator );
	return $result;
}

//End of page.
