<?php
/**
	*WordPress 文章列表分页导航
	*http://www.endskin.com/page-navi/
*/
function Bing_page_navi( $args = array() ){
	global $wp_query;
	$args = wp_parse_args( $args, array(
		'before'                       => '<div class="page-navi">',
		'after'                        => '</div>',
		'pages_text'                   => '%CURRENT_PAGE%/%TOTAL_PAGES%',
		'current_text'                 => '%PAGE_NUMBER%',
		'page_text'                    => '%PAGE_NUMBER%',
		'first_text'                   => __( '&laquo; 首页', 'Bing' ),
		'last_text'                    => __( '尾页 &raquo;', 'Bing' ),
		'next_text'                    => __( '&raquo;', 'Bing' ),
		'prev_text'                    => '&laquo;',
		'dotright_text'                => '...',
		'dotleft_text'                 => '...',
		'num_pages'                    => 5,
		'always_show'                  => 0,
		'num_larger_page_numbers'      => 3,
		'larger_page_numbers_multiple' => 10
	) );
	if( $wp_query->max_num_pages <= 1 || is_single() ) return;
	$max_page = $wp_query->max_num_pages;
	$paged = intval( get_query_var( 'paged' ) );
	if( empty( $paged ) || $paged == 0 ) $paged = 1;
	$pages_to_show = intval( $args['num_pages'] );
	$larger_page_to_show = intval( $args['num_larger_page_numbers'] );
	$larger_page_multiple = intval( $args['larger_page_numbers_multiple'] );
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor( $pages_to_show_minus_1 / 2 );
	$half_page_end = ceil( $pages_to_show_minus_1 / 2 );
	$start_page = $paged - $half_page_start;
	if( $start_page <= 0 ) $start_page = 1;
	$end_page = $paged + $half_page_end;
	if( ( $end_page - $start_page ) != $pages_to_show_minus_1 ) $end_page = $start_page + $pages_to_show_minus_1;
	if( $end_page > $max_page ){
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if( $start_page <= 0 ) $start_page = 1;
	$larger_per_page = $larger_page_to_show * $larger_page_multiple;
	$larger_start_page_start = ( ( floor( $start_page / 10 ) * 10 ) + $larger_page_multiple ) - $larger_per_page;
	$larger_start_page_end = floor( $start_page / 10 ) * 10 + $larger_page_multiple;
	$larger_end_page_start = floor( $end_page / 10 ) * 10 + $larger_page_multiple;
	$larger_end_page_end = floor( $end_page / 10 ) * 10 + ( $larger_per_page );
	if( $larger_start_page_end - $larger_page_multiple == $start_page ){
		$larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
		$larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
	}
	if( $larger_start_page_start <= 0 ) $larger_start_page_start = $larger_page_multiple;
	if( $larger_start_page_end > $max_page ) $larger_start_page_end = $max_page;
	if( $larger_end_page_end > $max_page ) $larger_end_page_end = $max_page;
	if( $max_page > 1 || intval( $args['always_show'] ) == 1 ){
		$pages_text = str_replace( '%CURRENT_PAGE%', number_format_i18n( $paged ), $args['pages_text'] );
		$pages_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $max_page ), $pages_text);
		echo $args['before'];
		if( !empty( $pages_text ) ) echo '<span class="pages">' . $pages_text . '</span>';
		if( $start_page >= 2 && $pages_to_show < $max_page ){
			$first_page_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $max_page ), $args['first_text'] );
			echo '<a href="' . esc_url( get_pagenum_link() ) . '" class="first" title="' . $first_page_text . '">' . $first_page_text . '</a>';
		}
		if( $larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page ){
			for( $i = $larger_start_page_start;$i < $larger_start_page_end;$i += $larger_page_multiple ){
				$page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $args['page_text'] );
				echo '<a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="page" title="' . $page_text . '">' . $page_text . '</a>';
			}
		}
		previous_posts_link( $args['prev_text'] );
		for( $i = $start_page;$i <= $end_page;$i++ ){						
			if( $i == $paged ){
				$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $args['current_text'] );
				echo '<span class="current">' . $current_page_text . '</span>';
			}else{
				$page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $args['page_text'] );
				echo '<a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="page" title="' . $page_text . '">' . $page_text . '</a>';
			}
		}
		echo '<span class="next-page">';
			next_posts_link( $args['next_text'], $max_page );
		echo '</span>';
	}
	if( $larger_page_to_show > 0 && $larger_end_page_start < $max_page ){
		for( $i = $larger_end_page_start;$i <= $larger_end_page_end;$i += $larger_page_multiple ){
			$page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $args['page_text'] );
			echo '<a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="page" title="' . $page_text . '">' . $page_text . '</a>';
		}
	}
	if( $end_page < $max_page ){
		$last_page_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $max_page ), $args['last_text'] );
		echo '<a href="' . esc_url( get_pagenum_link( $max_page ) ) . '" class="last" title="' . $last_page_text . '">' . $last_page_text . '</a>';
	}
	echo $args['after'];
}

//End of page.
