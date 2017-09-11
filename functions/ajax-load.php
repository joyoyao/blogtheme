<?php
/**
	*判断 AJAX 加载页面
	*http://www.bgbk.org
*/
function Bing_is_ajax_load_page(){
	return isset( $_GET['ajax_load'] ) && $_GET['ajax_load'] == 'page' && Bing_mpanel( 'ajax_load_page' ) && !is_robots() && !is_feed() && !is_trackback();
}

/**
	*AJAX 加载页面
	*http://www.bgbk.org
*/
function Bing_ajax_load_page(){
	if( Bing_is_ajax_load_page() ){
		do_action( 'ajax_load_page' );
		$GLOBALS['ajax_load_page_data'] = apply_filters( 'ajax_load_page_data', array() );
		ob_start( 'Bing_ajax_load_page_data' );
	}
}
add_action( 'template_redirect', 'Bing_ajax_load_page', 2 );

/**
	*处理 AJAX 加载页面数据
	*http://www.bgbk.org
*/
function Bing_ajax_load_page_data( $code ){
	$defaults = array(
		'code'            => $code,
		'title'           => function_exists( 'wp_get_document_title' ) ? wp_get_document_title() : wp_title( '|', false, 'right' ),
		'body_class'      => join( ' ', get_body_class( $class ) ),
		'refresh_sidebar' => false,
		'last_change'     => get_option( THEME_SLUG . '_last_change' )
	);
	$data = array_merge( $defaults, $GLOBALS['ajax_load_page_data'] );
	return wp_json_encode( $data );
}

/**
	*AJAX 加载页面时删除请求 AJAX 内容链接参数
	*http://www.bgbk.org
*/
function Bing_ajax_load_page_delete_url_query(){
	$search = array(
		'?ajax_load=page&',
		'?ajax_load=page',
		'&ajax_load=page&',
		'&ajax_load=page'
	);
	$replace = array(
		'?',
		'',
		'&',
		''
	);
	$_SERVER['REQUEST_URI'] = str_replace( $search, $replace, $_SERVER['REQUEST_URI'] );
}
add_action( 'ajax_load_page', 'Bing_ajax_load_page_delete_url_query', 2 );

/**
	*AJAX 加载页面时移除侧边栏
	*http://www.bgbk.org
*/
add_action( 'ajax_load_page', 'Bing_remove_sidebar' );

/**
	*AJAX 加载页面时移除页脚
	*http://www.bgbk.org
*/
function Bing_ajax_load_page_remove_footer(){
	add_action( 'get_footer', '_ajax_wp_die_handler', 1, 0 );
}
add_action( 'ajax_load_page', 'Bing_ajax_load_page_remove_footer' );

/**
	*AJAX 加载页面时禁止缓存
	*http://www.bgbk.org
*/
add_action( 'ajax_load_page', 'nocache_headers', 16 );

/**
	*AJAX 加载页面时某些情况重新加载侧边栏
	*http://www.bgbk.org
*/
function Bing_ajax_load_page_refresh_sidebar( $data ){
	if( Bing_mpanel( 'sidebar' ) && ( Bing_mpanel( 'sidebars_list' ) || is_date() ) ){
		ob_start();
			get_sidebar();
		$data = array_merge( $data, array(
			'refresh_sidebar' => true,
			'sidebar_code'    => ob_get_clean()
		) );
	}
	return $data;
}
add_action( 'ajax_load_page_data', 'Bing_ajax_load_page_refresh_sidebar' );

/**
	*重定向时如果是 AJAX 加载页面则添加查询参数
	*http://www.bgbk.org
*/
function Bing_redirect_ajax_load_page_query( $location ){
	if( Bing_is_ajax_load_page() ) $location = add_query_arg( 'ajax_load', 'page', $location );
	return $location;
}
add_filter( 'wp_redirect', 'Bing_redirect_ajax_load_page_query' );

//End of page.
