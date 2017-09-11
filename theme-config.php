<?php
/**
	*主题名称
	*http://www.bgbk.org
*/
if( !defined( 'THEME_NAME' ) ) define( 'THEME_NAME', wp_get_theme()->get( 'Name' ) );

/**
	*主题别名
	*http://www.bgbk.org
*/
if( !defined( 'THEME_SLUG' ) ) define( 'THEME_SLUG', trim( THEME_NAME ) );

/**
	*主题版本
	*http://www.bgbk.org
*/
if( !defined( 'THEME_VERSION' ) ) define( 'THEME_VERSION', wp_get_theme()->get( 'Version' ) );

/**
	*主题设置数据库字段名
	*http://www.bgbk.org
*/
if( !defined( 'THEME_MPANEL_NAME' ) ) define( 'THEME_MPANEL_NAME', 'Bing_mpanel_' . THEME_SLUG );

/**
	*主题小工具前缀
	*http://www.bgbk.org
*/
if( !defined( 'THEME_WIDGET_PREFIX' ) ) define( 'THEME_WIDGET_PREFIX', THEME_NAME . ' - ' );

/**
	*主题网络服务接口地址
	*http://www.bgbk.org
*/
if( !defined( 'THEME_API_URL' ) ) define( 'THEME_API_URL', 'http://apis.bgbk.org/wordpress/themes/' . THEME_SLUG );

/**
	*头像存储目录
	*相对于 WP_CONTENT_DIR 目录
	*http://www.bgbk.org
*/
if( !defined( 'THEME_AVATAR_PATH' ) ) define( 'THEME_AVATAR_PATH', '/cache/' . THEME_SLUG . '/avatar' );

/**
	*缩略图存储目录
	*相对于 WP_CONTENT_DIR 目录
	*http://www.bgbk.org
*/
if( !defined( 'THEME_THUMBNAIL_PATH' ) ) define( 'THEME_THUMBNAIL_PATH', '/cache/' . THEME_SLUG . '/thumbnail' );

//End of page.
