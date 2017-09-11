<?php
/**
	*自动更新
	*http://www.bgbk.org
*/
function Bing_theme_auto_update( $update ){
	static $update_data;
	if( !isset( $update_data ) ){
		$delete_update_content = true;
		$options = array(
			'timeout' => defined( 'DOING_CRON' ) && DOING_CRON ? 30 : 5,
			'body'    => array(
				'url'        => home_url(),
				'name'       => get_bloginfo( 'name' ),
				'version'    => THEME_VERSION,
				'wp_version' => $GLOBALS['wp_version'],
				'locale'     => get_locale()
			)
		);
		$response = wp_remote_post( THEME_API_URL . '/update-check/', $options );
		if( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) return $update;
		$update_data = false;
		$result = json_decode( wp_remote_retrieve_body( $response ) );
		if( empty( $result->version ) ) return $update;
		if( version_compare( $result->version, THEME_VERSION, '>' ) ){
			$update_data = array(
				'theme'       => get_stylesheet(),
				'new_version' => $result->version,
				'url'         => isset( $result->url )     ? $result->url     : '',
				'package'     => isset( $result->package ) ? $result->package : ''
			);
			if( !empty( $result->update_content ) ){
				set_site_transient( THEME_SLUG . '_update_content', $result->update_content );
				$delete_update_content = false;
			}
		}
		if( $delete_update_content ) delete_site_transient( THEME_SLUG . '_update_content' );
	}
	if( $update_data ) $update->response[get_stylesheet()] = $update_data;
	return $update;
}
add_filter( 'pre_set_site_transient_update_themes', 'Bing_theme_auto_update' );

/**
	*启用主题时强制检测更新
	*http://www.bgbk.org
*/
function Bing_theme_activate_update_check(){
	$last_update = get_site_transient( 'update_themes' );
	if( isset( $last_update->last_checked ) ){
		unset( $last_update->last_checked );
		set_site_transient( 'update_themes', $last_update );
	}
	wp_update_themes();
}
add_action( 'after_switch_theme', 'Bing_theme_activate_update_check' );

/**
	*保存主题版本
	*http://www.bgbk.org
*/
function Bing_save_theme_version(){
	if( ( $version = get_option( THEME_SLUG . '_version' ) ) === false ) do_action( 'start_theme' );
	elseif( version_compare( $version, THEME_VERSION, '<' ) ) do_action( 'theme_update', $version, THEME_VERSION );
	update_option( THEME_SLUG . '_version', THEME_VERSION );
}
add_action( 'init', 'Bing_save_theme_version', 16 );

/**
	*主题更新统计
	*http://www.bgbk.org
*/
function Bing_theme_update_statistics( $form, $to ){
	if( get_option( 'blog_public' ) != '0' ) wp_remote_post( THEME_API_URL . '/update/', array( 'body' => array(
		'url'        => home_url(),
		'name'       => get_bloginfo( 'name' ),
		'form'       => $form,
		'to'         => $to,
		'version'    => THEME_VERSION,
		'wp_version' => $GLOBALS['wp_version'],
		'locale'     => get_locale()
	) ) );
}
add_action( 'theme_update', 'Bing_theme_update_statistics', 18, 2 );

/**
	*获取新版本更新内容
	*http://www.bgbk.org
*/
function Bing_update_content(){
	return get_site_transient( THEME_SLUG . '_update_content' );
}

/**
	*主题更新时删除新版本更新内容
	*http://www.bgbk.org
*/
function Bing_theme_update_clear_content(){
	delete_site_transient( THEME_SLUG . '_update_content' );
}
add_action( 'theme_update', 'Bing_theme_update_clear_content' );

/**
	*添加主题更新版本钩子
	*http://www.bgbk.org
*/
function Bing_theme_update_versions_action( $form, $to ){
	$versions = array(
		'1.1',
		'1.2',
		'1.2.1',
		'1.3',
		'1.3.1',
		'2.0',
		'2.0.1',
		'2.1'
	);
	foreach( $versions as $version ) if( version_compare( $version, $form, '>' ) ) do_action( 'theme_update_version_' . $version );
}
add_action( 'theme_update', 'Bing_theme_update_versions_action', 2, 2 );

/**
	*更新主题到 1.1 版本
	*http://www.bgbk.org
*/
function Bing_theme_update_version_1_1(){
	$mpanel = Bing_mpanel();

	if( $mpanel->get( 'crop_thumbnail' ) === false ) $mpanel->update( 'crop_thumbnail', $mpanel->get( 'timthumb' ) );
	$mpanel->delete( 'timthumb' );

	foreach( array( 'header', 'footer', 'post_bottom' ) as $banner ){
		$banner_id = 'banner_' . $banner;
		$defaults = array(
			'type'        => 'img',
			'tab'         => true,
			'mobile_show' => true
		);
		foreach( $defaults as $key => $value ){
			$option = $banner_id . '_' . $key;
			if( $mpanel->get( $option ) === false ) $mpanel->update( $option, $value );
		}
	}
}
add_action( 'theme_update_version_1.1', 'Bing_theme_update_version_1_1' );

/**
	*更新主题到 1.2 版本
	*http://www.bgbk.org
*/
function Bing_theme_update_version_1_2(){
	$mpanel = Bing_mpanel();

	foreach( array( 'progress', 'hot_searches', 'first_line_indent' ) as $option ) if( $mpanel->get( $option ) === false ) $mpanel->update( $option, true );

	if( $mpanel->get( 'related_posts_number' ) === false ) $mpanel->update( 'related_posts_number', 3 );

	if( $mpanel->get( 'main_color' ) === false ) $mpanel->update( 'main_color', '#237DED' );

	foreach( array( 'header', 'footer', 'post_bottom' ) as $banner ){
		$banner_id = 'banner_' . $banner;
		if( $mpanel->get( $banner_id . '_client' ) === false ){
			$option_value = array( 'pc' );
			if( $mpanel->get( $banner_id . '_mobile_show' ) ) $option_value[] = 'mobile';
			$mpanel->update( $banner_id . '_client', $option_value );
		}
		$mpanel->delete( $banner_id . '_mobile_show' );
	}
}
add_action( 'theme_update_version_1.2', 'Bing_theme_update_version_1_2' );

/**
	*更新主题到 1.3 版本
	*http://www.bgbk.org
*/
function Bing_theme_update_version_1_3(){
	$mpanel = Bing_mpanel();

	foreach( array( 'return_top', 'sidebar' ) as $option ) if( $mpanel->get( $option ) === false ) $mpanel->update( $option, true );

	$new_default_sidebar = sanitize_title( THEME_SLUG . '_default' );
	$sidebars_widgets = wp_get_sidebars_widgets();
	if( is_array( $sidebars_widgets ) && !empty( $sidebars_widgets['widget_sidebar'] ) && empty( $sidebars_widgets[$new_default_sidebar] ) ){
		$sidebars_widgets[$new_default_sidebar] = $sidebars_widgets['widget_sidebar'];
		unset( $sidebars_widgets['widget_sidebar'] );
		wp_set_sidebars_widgets( $sidebars_widgets );
	}
}
add_action( 'theme_update_version_1.3', 'Bing_theme_update_version_1_3' );

/**
	*更新主题到 1.3.1 版本
	*http://www.bgbk.org
*/
function Bing_theme_update_version_1_3_1(){
	foreach( array( 'header_menu', 'footer_menu' ) as $theme_location ) delete_transient( 'nav_menu_' . $theme_location );
}
add_action( 'theme_update_version_1.3.1', 'Bing_theme_update_version_1_3_1' );

/**
	*更新主题到 2.0 版本
	*http://www.bgbk.org
*/
function Bing_theme_update_version_2_0(){
	$mpanel = Bing_mpanel();

	if( strtoupper( $mpanel->get( 'main_color' ) ) == '#237DED' ) $mpanel->update( 'main_color', '#2D6DCC' );

	delete_transient( THEME_SLUG . '_update_content' );
}
add_action( 'theme_update_version_2.0', 'Bing_theme_update_version_2_0' );

//End of page.
