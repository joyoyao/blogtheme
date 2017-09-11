<?php
/**
	*小工具配置
	*http://www.bgbk.org
*/
function Bing_widgets_init(){
	$default_args = array(
		'name'          => __( '默认侧边栏', 'Bing' ),
		'id'            => sanitize_title( THEME_SLUG . '_default' ),
		'description'   => __( '没有单独指定的页面使用此侧边栏', 'Bing' ),
		'before_widget' => '<li class="widget span12 %2$s"><article class="panel">',
		'after_widget'  => '</article></li>',
		'before_title'  => '<header class="panel-header"><h3 class="widget-title">',
		'after_title'   => '</h3></header>'
	);
	register_sidebar( $default_args );

	if( $sidebars_list = Bing_mpanel( 'sidebars_list' ) ) foreach( $sidebars_list as $sidebar ) register_sidebar( array_merge( $default_args, array(
		'name'        => $sidebar,
		'id'          => sanitize_title( THEME_SLUG . '_user_' . md5( $sidebar ) ),
		'description' => sprintf( __( '用户自定义侧边栏：%s' ), $sidebar ),
	) ) );

	foreach( glob( get_template_directory() . '/includes/widgets/widget-*.php' ) as $file_path ) include( $file_path );

	$unregister_widgets = array(
		'Tag_Cloud',
		'Recent_Comments',
		'Recent_Posts',
		'Search'
	);
	foreach( $unregister_widgets as $widget ) unregister_widget( 'WP_Widget_' . $widget );
}
add_action( 'widgets_init', 'Bing_widgets_init' );

/**
	*获取当前页面的侧边栏
	*http://www.bgbk.org
*/
function Bing_current_sidebar(){
	if(     is_home()         ) $sidebar = Bing_mpanel( 'sidebar_location_home'                                );
	elseif( is_category()     ) $sidebar = Bing_mpanel( 'sidebar_location_category_' . get_queried_object_id() );
	elseif( is_singular()     ) $sidebar = Bing_mpanel( 'sidebar_location_post'                                );
	elseif( is_archive()      ) $sidebar = Bing_mpanel( 'sidebar_location_archive'                             );
	elseif( is_search()       ) $sidebar = Bing_mpanel( 'sidebar_location_search'                              );
	if( !empty( $sidebar ) && isset( $GLOBALS['wp_registered_sidebars'][$user_sidebar = sanitize_title( THEME_SLUG . '_user_' . md5( $sidebar ) )] ) ) return $user_sidebar;
	return sanitize_title( THEME_SLUG . '_default' );
}

//End of page.
