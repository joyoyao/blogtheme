<?php
/**
	*生成样式表
	*http://www.bgbk.org
*/
function Bing_style(){
	if( !has_action( 'css' ) ) return;
	echo '<style id="' . esc_attr( THEME_SLUG ) . '-css">';
		do_action( 'css' );
	echo '</style>';
}
add_action( 'wp_head', 'Bing_style', 12 );

/**
	*生成编辑器样式表
	*http://www.bgbk.org
*/
function Bing_editor_style(){
	header( 'Content-type: text/css' );
	ob_start();
		include( get_template_directory() . '/css/editor-style.css' );
		do_action( 'editor_css' );
	wp_die( ob_get_clean() );
}
add_action( 'wp_ajax_theme_editor_style', 'Bing_editor_style' );
add_action( 'wp_ajax_nopriv_theme_editor_style', 'Bing_editor_style' );

/**
	*输出 CSS 代码
	*http://www.bgbk.org
*/
function Bing_print_css( $selector, $style, $width = null ){
	$code = implode( ',', (array) $selector ) . '{';
		foreach( $style as $key => $value ) $code .= "$key: $value;";
	$code .= '}';
	if( !empty( $width ) ) $code = '@media screen and (max-width:' . $width . 'px){' . $code . '}';
	echo $code;
}

/**
	*Logo 配置
	*http://www.bgbk.org
*/
function Bing_logo_style(){
	if( Bing_mpanel( 'logo' ) ){
		$url = Bing_mpanel( 'logo_url' );
		if( !empty( $url ) ) Bing_print_css( '#header .logo a', array( 'background-image' => 'url(' . esc_url( $url ) . ')' ) );
	}else{
		$css = array(
			'background-image' => 'none',
			'text-indent'      => 0,
			'width'            => 'auto'
		);
		Bing_print_css( '#header .logo a', $css );
	}
}
add_action( 'css', 'Bing_logo_style' );

/**
	*首行缩进两格
	*http://www.bgbk.org
*/
function Bing_first_line_indent_style(){
	if( Bing_mpanel( 'first_line_indent' ) ) Bing_print_css( '#post-box.type-post .context p', array( 'text-indent' => '2em' ) );
}
add_action( 'css', 'Bing_first_line_indent_style' );

/*
	*用户自定义 CSS
	*http://www.bgbk.org
*/
function Bing_custom_css(){
	echo Bing_mpanel( 'custom_css' );
}
add_action( 'css', 'Bing_custom_css', 12 );

/*
	*用户自定义响应式 CSS
	*http://www.bgbk.org
*/
function Bing_custom_responsive_css(){
	if( !Bing_mpanel( 'responsive' ) ) return;
	foreach( array( 1220, 1200, 1100, 1000, 900, 800, 700, 600, 500, 400 ) as $px ){
		$css = Bing_mpanel( 'custom_responsive_css_' . $px );
		if( !empty( $css ) ) echo '@media screen and (max-width:' . $px . 'px){' . $css . '}';
	}
}
add_action( 'css', 'Bing_custom_responsive_css', 14 );

/*
	*设置主颜色
	*http://www.bgbk.org
*/
function Bing_main_color(){
	if( !Bing_mpanel( 'custom_main_color' ) || !$color = Bing_mpanel( 'main_color' ) ) return;
	Bing_print_css( array(
		'a:hover',

		/*
		2.0 remove
		'#header .logo a:hover',
		'#header_menu > li:hover > a',
		'#header_menu > li.current-menu-item > a',
		'#header_menu > li.current-menu-parent > a',
		'#header_menu > li.current_page_item > a',
		'#header_menu > li.current-post-ancestor > a',
		*/

		'#header_menu .sub-menu .sub-menu > li.current-menu-item > a',
		'#header_menu .sub-menu .sub-menu > li.current-menu-parent > a',
		'#header_menu .sub-menu .sub-menu > li.current_page_item > a',
		'#header_menu .sub-menu .sub-menu > li.current-post-ancestor > a',
		'#header_menu .sub-menu .sub-menu > li.current-menu-item:hover > a',
		'#header_menu .sub-menu .sub-menu > li.current-menu-parent:hover > a',
		'#header_menu .sub-menu .sub-menu > li.current_page_item:hover > a',
		'#header_menu .sub-menu .sub-menu > li.current-post-ancestor:hover > a',
		'#header .control > li > a:hover',
		'.related-posts > li .post-meta > li a:hover',
		'.context a',
		'.comments-list li .right-box > .comment-meta .edit-link a',
		'.comments-list li .right-box > .comment-meta .reply a',
		'.comments-list li .right-box > .waiting',
		'.empty-sidebar .set-widget a',
		'.sidebar-posts-list > li .post-meta > li:hover',
		'.sidebar-posts-list > li .post-meta > li:hover a',
		'.widget_rss cite',
		'.widget_categories li a:hover',
		'.widget_archive li a:hover',
		'.widget_calendar tbody tr td a',
		'#footer_menu > li.current-menu-item > a',
		'#footer_menu > li.current-menu-parent > a',
		'#footer_menu > li.current_page_item > a',
		'#footer_menu > li.current-post-ancestor > a',
		'#footer_menu > li > .sub-menu > li.current-menu-item > a',
		'#footer_menu > li > .sub-menu > li.current-menu-parent > a',
		'#footer_menu > li > .sub-menu > li.current_page_item > a',
		'#footer_menu > li > .sub-menu > li.current-post-ancestor > a',
		'#footer a:hover',
		'#mobile-header .mobile-return',
		'#mobile-menu li.current a',
		'.hot-searches-list > li a'
	), array( 'color' => $color ) );
	Bing_print_css( array(
		'input[type=text]:focus',
		'input[type=text]:hover:focus',
		'input[type=password]:focus',
		'input[type=password]:hover:focus',
		'input[type=email]:focus',
		'input[type=email]:hover:focus',
		'input[type=url]:focus',
		'input[type=url]:hover:focus',
		'textarea:focus',
		'textarea:hover:focus',
		'select:focus',
		'select:focus',
		'select:hover:focus',
		'select:hover:focus',
		'.posts-list > li .thumbnail-link .thumbnail:hover',
		'.posts-list > li .post-meta > li:hover',
		'.page-navi a:hover',
		'.page-navi .current',
		'.related-posts > li .related-posts-panel:hover',
		'.context img:hover',
		'.comments-list > li:hover',
		'.sidebar-posts-list > li:hover',
		'.widget_calendar tbody #today',
		'.widget_calendar tfoot tr td a',
		'.widget_recent_comments li:hover',
		'.widget_tag_cloud .list-box > a:hover',
		'#header_menu > li > .sub-menu > li',

		//1.3
		'#post-box .post-title',
		'#post-box .post-meta-box .post-meta > li:hover'
	), array( 'border-color' => $color ) );
	Bing_print_css( '#nprogress .spinner-icon', array( 'border-top-color' => $color, 'border-left-color' => $color ) );
	Bing_print_css( array(
		'input[type=submit]',
		'button',
		'.posts-list > li .post-meta > li:hover',
		'.page-navi a:hover',
		'.page-navi .current',
		'.widget_calendar tbody #today',
		'.widget_tag_cloud .list-box > a:hover',
		'#nprogress .bar',

		//1.2.1
		'#post-box .post-meta-box .post-meta > li:hover',

		//1.3
		'#return-top',

		//2.0
		'#header'
	), array( 'background-color' => $color ) );
	Bing_print_css( array(
		'.posts-list > li .thumbnail-link .thumbnail:hover',
		'.related-posts > li .related-posts-panel:hover',
		'.context img:hover',
		'.comments-list > li:hover',
		'.sidebar-posts-list > li:hover',
		'.widget_recent_comments li:hover'
	), array( 'box-shadow' => '0 0 2px ' . $color, '-moz-box-shadow' => '0 0 2px ' . $color ) );
	Bing_print_css( '#nprogress .peg', array( 'box-shadow' => '0 0 10px ' . $color . ', 0 0 5px ' . $color, '-moz-box-shadow' => '0 0 10px ' . $color . ', 0 0 5px ' . $color ) );
	do_action( 'theme_main_color' );
}
add_action( 'css', 'Bing_main_color' );

/**
	*编辑器首行缩进两格
	*http://www.bgbk.org
*/
function Bing_editor_first_line_indent(){
	if( Bing_mpanel( 'first_line_indent' ) ) Bing_print_css( '#tinymce.post-type-post p', array( 'text-indent' => '2em' ) );
}
add_action( 'editor_css', 'Bing_editor_first_line_indent' );

/**
	*编辑器设置主颜色
	*http://www.bgbk.org
*/
function Bing_editor_main_color(){
	if( !Bing_mpanel( 'custom_main_color' ) || !$color = Bing_mpanel( 'main_color' ) ) return;
	Bing_print_css( '#tinymce a', array( 'color' => $color ) );
	Bing_print_css( '#tinymce img:hover', array(
		'border-color'    => $color,
		'box-shadow'      => '0 0 2px ' . $color,
		'-moz-box-shadow' => '0 0 2px ' . $color
	) );
}
add_action( 'editor_css', 'Bing_editor_main_color' );

//End of page.
