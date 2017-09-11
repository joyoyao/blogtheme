<?php
/**
	*建立菜单
	*http://www.bgbk.org
*/
function Bing_register_nav_menus(){
	register_nav_menus( array(
		'header_menu' => __( '导航菜单', 'Bing' ),
		'footer_menu' => __( '页脚菜单', 'Bing' )
	) );
}
add_action( 'after_setup_theme', 'Bing_register_nav_menus', 6 );

/**
	*获取菜单名称
	*http://www.bgbk.org
*/
function Bing_menu_name( $menu ){
	$locations = get_registered_nav_menus();

	$menu_locations = get_nav_menu_locations();
	if( empty( $menu_locations[$menu] ) ) return $locations[$menu];

	$menu_object = wp_get_nav_menu_object( $menu_locations[$menu] );
	if( empty( $menu_object ) || is_wp_error( $menu_object ) ) return $locations[$menu];

	return $menu_object->name;
}

/**
	*导航菜单未设置回调函数
	*http://www.bgbk.org
*/
function Bing_not_set_menu_fallback( $args ){
	$menus = array(
		array(
			'url'     => home_url(),
			'name'    => __( '首页', 'Bing' ),
			'current' => is_home()
		),
		array(
			'url'  => admin_url( 'nav-menus.php?action=locations' ),
			'name' => __( '⊕添加菜单', 'Bing' )
		)
	);
	$code = '<ul id="' . esc_attr( $args['theme_location'] ) . '">';
		foreach( $menus as $menu ){
			$current_clsss = isset( $menu['current'] ) && $menu['current'] ? 'current-menu-item"' : '';
			$code .= sprintf( '<li class="%s"><a href="%s">%s</a></li>', $current_clsss, esc_url( $menu['url'] ), $menu['name'] );
		}
	$code .= '</ul>';
	if( !$args['echo'] ) return $code;
	echo $code;
}

/**
	*调用菜单
	*http://www.bgbk.org
*/
function Bing_nav_menu( $theme_location, $args = array() ){
	$defaults = array(
		'theme_location' => $theme_location,
		'container'      => false,
		'items_wrap'     => '<ul id="' . esc_attr( $theme_location ) . '">%3$s</ul>',
		'fallback_cb'    => 'Bing_not_set_menu_fallback'
	);
	$r = wp_parse_args( $args, $defaults );
	$r['echo'] = false;
	return wp_nav_menu( $r );
}

//End of page.
