<?php
/**
	*选项初始化和调用
	*http://www.bgbk.org
*/
function Bing_mpanel( $option = false ){
	static $mpanel;
	if( !isset( $mpanel ) ) $mpanel = new Bing_mpanel;
	if( $option === false ) return $mpanel;
	return apply_filters( 'Bing_mpanel_' . $option, $mpanel->get( $option ), $option, $mpanel );
}
add_action( 'after_setup_theme', 'Bing_mpanel', 2, 0 );

/**
	*语言本地化
	*http://www.bgbk.org
*/
function Bing_theme_localize(){
	load_theme_textdomain( 'Bing', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'Bing_theme_localize' );

/**
	*激活欢迎使用提示语
	*http://www.bgbk.org
*/
function Bing_open_theme_welcome(){
	update_option( THEME_SLUG . '_welcome', true );
}
add_action( 'start_theme', 'Bing_open_theme_welcome' );

/**
	*显示欢迎使用提示语
	*http://www.bgbk.org
*/
function Bing_theme_welcome(){
	if( !get_option( THEME_SLUG . '_welcome' ) || !current_user_can( 'switch_themes' ) ) return;
	$text = sprintf(
		__( '欢迎使用 %s 主题，你可以%s联系作者，也可以%s加入我们的 QQ 群；主题支持自动更新，不建议直接修改主题的代码。', 'Bing' ),
		THEME_NAME,
		'<a href="http://www.bgbk.org/go/qq" target="_blank">' . __( '点击此处', 'Bing' ) . '</a>',
		'<a href="http://www.bgbk.org/go/qq-group" target="_blank">' . __( '点击此处', 'Bing' ) . '</a>'
	);
	echo '
	<div class="updated notice is-dismissible" id="close-theme-welcome">
		<p>' . $text . '</p>
	</div>
	<script type="text/javascript">
		jQuery( document ).on( \'click\', \'#close-theme-welcome .notice-dismiss\', function(){
			jQuery.get( \'' . esc_url( admin_url( 'admin-ajax.php' ) ) . '\', \'action=close_theme_welcome\' );
		} );
	</script>';
}
add_action( 'admin_notices', 'Bing_theme_welcome' );

/**
	*显示欢迎使用提示语
	*http://www.bgbk.org
*/
function Bing_theme_wp_version_compatible(){
	global $wp_version;
	$compatible_version = '4.2';
	if( version_compare( $compatible_version, $wp_version, '>' ) && current_user_can( 'update_core' ) ) echo '
	<div class="error" id="theme-wp-version-compatible">
		<p><strong>' . __( '警告：', 'Bing' ) . '</strong>' . sprintf( __( '%s 主题能完美运行至少需要 WordPress %s 版本，您当前的 WordPress 版本（%s）可能会使主题部分功能出现问题；为了安全性和兼容性考虑，请尽快升级 WordPress 到最新版本，或者至少升级到能使主题完美运行的最低版本。', 'Bing' ), THEME_NAME, $compatible_version, $wp_version ) . '</p>
	</div>';
}
add_action( 'admin_notices', 'Bing_theme_wp_version_compatible', 8 );

/**
	*关闭欢迎使用提示语
	*http://www.bgbk.org
*/
function Bing_close_theme_welcome(){
	if( current_user_can( 'switch_themes' ) ) update_option( THEME_SLUG . '_welcome', false );
}
add_action( 'wp_ajax_close_theme_welcome', 'Bing_close_theme_welcome' );

/**
	*关闭 Admin Bar
	*http://www.bgbk.org
*/
add_filter( 'show_admin_bar', '__return_false', 8 );

/**
	*添加文章编辑器样式表
	*http://www.bgbk.org
*/
function Bing_add_editor_style(){
	$url = add_query_arg( array(
		'action' => 'theme_editor_style',
		'ver'    => THEME_VERSION
	), admin_url( 'admin-ajax.php' ) );
	add_editor_style( $url );
}
add_action( 'after_setup_theme', 'Bing_add_editor_style' );

/**
	*添加主题功能支持
	*http://www.bgbk.org
*/
function Bing_add_theme_support(){
	//HTML5
	add_theme_support( 'html5' );

	//网页标题
	add_theme_support( 'title-tag' );

	//特色图片
	add_theme_support( 'post-thumbnails' );

	//自定义背景
	add_theme_support( 'custom-background', array( 'default-color' => 'F0F0F5' ) );
}
add_action( 'after_setup_theme', 'Bing_add_theme_support' );

/**
	*优化搜索页标题
	*http://www.bgbk.org
*/
function Bing_optimize_search_title( $title ){
	if( is_search() && !get_search_query() ) $title['title'] = __( '搜索', 'Bing' );
	return $title;
}
add_filter( 'document_title_parts', 'Bing_optimize_search_title', 12 );

/**
	*挂载脚本
	*http://www.bgbk.org
*/
function Bing_enqueue_scripts(){
	//IE8
	wp_enqueue_style( THEME_SLUG . '-ie8', get_template_directory_uri() . '/css/ie8.css', array( THEME_SLUG . '-style' ), THEME_VERSION );
	wp_style_add_data( THEME_SLUG . '-ie8', 'conditional', 'lt IE 9' );

	//NProgress
	wp_register_style( THEME_SLUG . '-NProgress', get_template_directory_uri() . '/css/nprogress.css', array( THEME_SLUG . '-style' ), THEME_VERSION );
	wp_register_script( THEME_SLUG . '-NProgress', get_template_directory_uri() . '/js/nprogress.js', array( THEME_SLUG . '-base' ), THEME_VERSION );
	if( Bing_mpanel( 'progress' ) && Bing_mpanel( 'ajax_load_page' ) ){
		wp_enqueue_style( THEME_SLUG . '-NProgress' );
		wp_enqueue_script( THEME_SLUG . '-NProgress' );
	}

	//Responsive
	wp_register_style( THEME_SLUG . '-responsive', get_template_directory_uri() . '/css/responsive.css', array( THEME_SLUG . '-style' ), THEME_VERSION );
	if( Bing_mpanel( 'responsive' ) ) wp_enqueue_style( THEME_SLUG . '-responsive' );

	//Comment reply
	if( get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );

	//Style
	wp_enqueue_style( THEME_SLUG . '-style', get_stylesheet_uri(), array( 'dashicons' ), THEME_VERSION );

	//Base
	wp_enqueue_script( THEME_SLUG . '-base', get_template_directory_uri() . '/js/base.js', array( 'jquery' ), THEME_VERSION );
	wp_localize_script( THEME_SLUG . '-base', THEME_SLUG . '_base_args', array(
		'admin_ajax'      => admin_url( 'admin-ajax.php' ),
		'ajax_comment'    => Bing_mpanel( 'ajax_comment' ),
		'comment_loading' => __( '正在提交...', 'Bing' ),
		'ajax_load_page'  => Bing_mpanel( 'ajax_load_page' ),
		'insert_smiley'   => Bing_insert_smiley(),
		'progress'        => Bing_mpanel( 'progress' ) && Bing_mpanel( 'ajax_load_page' )
	) );
}
add_action( 'wp_enqueue_scripts', 'Bing_enqueue_scripts' );

/**
	*添加 IE8 兼容脚本
	*http://www.bgbk.org
*/
function Bing_print_ie8_scripts(){
	if( !did_action( 'wp_enqueue_scripts' ) ) return;
	$scripts = array( get_template_directory_uri() . '/js/html5.js?ver=' . THEME_VERSION );
	echo '<!--[if lt IE 9]>';
		foreach( $scripts as $script ) echo '<script type="text/javascript" src="' . esc_url( $script ) . '"></script>';
	echo '<![endif]-->';
}
add_action( 'wp_print_scripts', 'Bing_print_ie8_scripts' );

/**
	*生成空白首页文件
	*http://www.bgbk.org
*/
function Bing_build_empty_index( $path ){
	$index = $path . '/index.php';
	if( is_file( $index ) ) return;
	wp_mkdir_p( $path );
	file_put_contents( $index, "<?php\n// Silence is golden.\n" );
}

/**
	*获取摘要
	*http://www.bgbk.org
*/
function Bing_excerpt( $length ){
	$GLOBALS['excerpt_length'] = $length;
	add_filter( 'excerpt_more', 'Bing_excerpt_more', 16 );
	add_filter( 'excerpt_length', 'Bing_excerpt_length', 16 );
		$excerpt = get_the_excerpt();
	remove_filter( 'excerpt_more', 'Bing_excerpt_more', 16 );
	remove_filter( 'excerpt_length', 'Bing_excerpt_length', 16 );
	return $excerpt;
}

/**
	*设置摘要字数
	*http://www.bgbk.org
*/
function Bing_excerpt_length(){
	global $excerpt_length;
	$length = $excerpt_length;
	unset( $excerpt_length );
	return $length;
}

/**
	*设置摘要更多标示
	*http://www.bgbk.org
*/
function Bing_excerpt_more(){
	return '...';
}

/**
	*获取网页描述
	*http://www.bgbk.org
*/
function Bing_site_description( $home ){
	if( is_home() || is_front_page() ) $description = $home;
	elseif( is_category() || is_tag() || is_tax() ) $description = term_description();
	elseif( is_singular() ) $description = wp_trim_words( get_post()->post_content, 120, '' );
	elseif( is_author() ) $description = get_the_author_meta( 'description' );
	elseif( is_date() ){
		if( is_day() ) $d = __( 'Y年m月d日', 'Bing' );
		elseif( is_month() ) $d = __( 'Y年m月', 'Bing' );
		else $d = __( 'Y年', 'Bing' );
		$description = sprintf( __( '%s发布的文章', 'Bing' ), get_the_date( $d ) );
	}
	else return '';
	return strip_tags( trim( $description ) );
}

/**
	*打印网页描述元标记
	*http://www.bgbk.org
*/
function Bing_add_site_description_meta(){
	if( $description = Bing_site_description( Bing_mpanel( 'site_description' ) ) ) echo '<meta name="description" content="' . esc_attr( $description ) . '" />';
}
add_action( 'wp_head', 'Bing_add_site_description_meta' );

/**
	*调用侧边栏
	*http://www.bgbk.org
*/
function Bing_sidebar(){
	if( Bing_mpanel( 'sidebar' ) ) add_action( 'get_footer', 'get_sidebar', 1, 0 );
}
add_action( 'init', 'Bing_sidebar' );

/**
	*移除侧边栏
	*http://www.bgbk.org
*/
function Bing_remove_sidebar(){
	remove_action( 'get_footer', 'get_sidebar', 1 );
}

/**
	*主题使用人数统计
	*http://www.bgbk.org
*/
function Bing_theme_install_statistics(){
	if( get_option( 'blog_public' ) != '0' ) wp_remote_post( THEME_API_URL . '/activate/', array( 'body' => array(
		'url'        => home_url(),
		'name'       => get_bloginfo( 'name' ),
		'version'    => THEME_VERSION,
		'wp_version' => $GLOBALS['wp_version'],
		'locale'     => get_locale()
	) ) );
}
add_action( 'start_theme', 'Bing_theme_install_statistics' );

/**
	*固定界面缩放
	*http://www.bgbk.org
*/
function Bing_responsive_meta(){
	if( !Bing_mpanel( 'responsive' ) ) return;
	$ui = Bing_mpanel( 'hide_safari_bar' ) ? ',minimal-ui' : '';
	echo '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0' . $ui . '" />';
	echo '<meta http-equiv="Cache-Control" content="no-siteapp" />';
}
add_action( 'wp_head', 'Bing_responsive_meta' );

/**
	*头像缓存
	*http://www.bgbk.org
*/
function Bing_avatar_cache( $url, $id_or_email, $args ){
	if( !Bing_mpanel( 'avatar_cache' ) || apply_filters( 'pre_option_show_avatars', false ) ) return $url;

	if( is_numeric( $id_or_email ) ) $user = get_user_by( 'id', absint( $id_or_email ) );
	elseif( is_string( $id_or_email ) ) strpos( $id_or_email, '@md5.gravatar.com' ) ? list( $email_hash ) = explode( '@', $id_or_email ) : $email = $id_or_email;
	elseif( $id_or_email instanceof WP_User ) $user = $id_or_email;
	elseif( $id_or_email instanceof WP_Post ) $user = get_user_by( 'id', (int) $id_or_email->post_author );
	elseif( is_object( $id_or_email ) && isset( $id_or_email->comment_ID ) ){
		if( !empty( $id_or_email->user_id ) ) $user = get_user_by( 'id', (int) $id_or_email->user_id );
		if( ( empty( $user ) || is_wp_error( $user ) ) && !empty( $id_or_email->comment_author_email ) ) $email = $id_or_email->comment_author_email;
	}
	if( empty( $email_hash ) ){
		if( !empty( $user ) ) $email = $user->user_email;
		$email_hash = md5( strtolower( trim( $email ) ) );
	}

	$file_path = WP_CONTENT_DIR . THEME_AVATAR_PATH . "/$email_hash.png";
	$cache_time = Bing_mpanel( 'avatar_cache_day' ) * DAY_IN_SECONDS;
	if( !is_file( $file_path ) || ( $cache_time > 0 && ( time() - filemtime( $file_path ) ) >= $cache_time ) ){
		remove_filter( 'get_avatar_url', 'Bing_avatar_cache', 16 );
			$args['size'] = 150;
			$avatar_cache_size = get_avatar_url( $id_or_email, $args );
		add_filter( 'get_avatar_url', 'Bing_avatar_cache', 16, 3 );

		$editor = wp_get_image_editor( $avatar_cache_size );
		if( is_wp_error( $editor ) ) return $url;

		Bing_build_empty_index( WP_CONTENT_DIR . THEME_AVATAR_PATH );

		if( is_wp_error( $editor->save( $file_path, 'image/png' ) ) ) return $url;
	}
	return content_url( THEME_AVATAR_PATH . "/$email_hash.png" );
}
add_filter( 'get_avatar_url', 'Bing_avatar_cache', 16, 3 );

/**
	*统一头像 Alt 标签
	*http://www.bgbk.org
*/
function Bing_avatar_alt( $args ){
	if( empty( $args['alt'] ) ) $args['alt'] = __( 'Gravatar 头像', 'Bing' );
	return $args;
}
add_filter( 'get_avatar_data', 'Bing_avatar_alt', 16 );

/**
	*搜索结果只限文章
	*http://www.bgbk.org
*/
function Bing_search_filter_post( $query ){
	if( Bing_mpanel( 'search_filter_post' ) && !is_admin() && $query->is_main_query() && $query->is_search() ) $query->set( 'post_type', 'post' );
	return $query;
}
add_filter( 'pre_get_posts', 'Bing_search_filter_post' );

/**
	*搜索结果只有一篇文章时自动跳转到该文章
	*http://www.bgbk.org
*/
function Bing_search_one_redirect(){
	if( !Bing_mpanel( 'search_one_redirect' ) || Bing_mpanel( 'ajax_load_page' ) ) return;
	global $wp_query;
	if( $wp_query->is_search() && $wp_query->found_posts == 1 ){
		wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
		die;
	}
}
add_action( 'template_redirect', 'Bing_search_one_redirect' );

/**
	*移除头部无用信息
	*http://www.bgbk.org
*/
function Bing_remove_head_refuse(){
	if( !Bing_mpanel( 'remove_head_refuse' ) ) return;
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'parent_post_rel_link', 10 );
	remove_action( 'wp_head', 'start_post_rel_link', 10 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );	
}
add_action( 'init', 'Bing_remove_head_refuse' );

/**
	*关闭离线编辑器接口
	*http://www.bgbk.org
*/
function Bing_remove_xmlrpc(){
	if( !Bing_mpanel( 'remove_xmlrpc' ) ) return;
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	add_filter( 'xmlrpc_enabled', '__return_false' );
}
add_action( 'init', 'Bing_remove_xmlrpc', 5 );

/**
	*WordPress 阻止站内文章互相 Pingback
	*http://www.endskin.com/noself-pingback/
*/
function Bing_no_self_pingback( &$links ){
	if( !Bing_mpanel( 'no_self_pingback' ) ) return;
	$home_url = home_url();
	foreach( $links as $key => $value ) if( strpos( $value, $home_url ) !== false ) unset( $links[$key] );
}
add_action( 'pre_ping', 'Bing_no_self_pingback' );

/**
	*文章内容链接全部在新窗口打开
	*http://www.bgbk.org
*/
function Bing_post_auto_blank( $content ){
	return Bing_mpanel( 'post_auto_blank' ) && is_single() ? str_replace( '<a', '<a target="_blank"', $content ) : $content;
}
add_filter( 'the_content', 'Bing_post_auto_blank', 13 );

/**
	*文章内容外链全部添加 nofollow 并在新窗口打开
	*http://www.bgbk.org
*/
function Bing_post_auto_nofollow_blank( $content ){
	if( !Bing_mpanel( 'post_auto_nofollow_blank' ) || !is_single() ) return $content;
	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
	if( preg_match_all( "/$regexp/siU", $content, $matches, PREG_SET_ORDER ) ){
		if( !empty( $matches ) ){
			$site_url = site_url();
			for( $i = 0; $i < count( $matches ); ++$i ){
				$tag = $matches[$i][0];
				$tag2 = $matches[$i][0];
				$url = $matches[$i][0];
				$noFollow = '';
				$pattern = '/target\s*=\s*"\s*_blank\s*"/';
				preg_match( $pattern, $tag2, $match, PREG_OFFSET_CAPTURE );
				if( count( $match ) < 1 ) $noFollow .= ' target="_blank" '; 
				$pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
				preg_match( $pattern, $tag2, $match, PREG_OFFSET_CAPTURE );
				if( count( $match ) < 1 ) $noFollow .= ' rel="nofollow" ';
				$pos = strpos( $url, $site_url );
				if( $pos === false ){
					$tag = rtrim( $tag, '>' );
					$tag .= $noFollow . '>';
					$content = str_replace( $tag2, $tag, $content );
				}
			}
		}
	}
	return $content;
}
add_filter( 'the_content', 'Bing_post_auto_nofollow_blank', 14 );

/**
	*文章编辑器功能扩展
	*http://www.bgbk.org
*/
function Bing_editor_add_functions( $buttons ){
	array_push( $buttons, 'fontselect', 'fontsizeselect', 'backcolor', 'underline', 'hr', 'sub', 'sup', 'cut', 'copy', 'paste', 'cleanup', 'wp_page', 'newdocument' );
	return $buttons;
}
add_filter( 'mce_buttons_3', 'Bing_editor_add_functions' );

/**
	*自定义头部代码
	*http://www.bgbk.org
*/
function Bing_custom_head_code(){
	echo Bing_mpanel( 'head_code' );
}
add_action( 'wp_head', 'Bing_custom_head_code', 14 );

/**
	*自定义底部代码
	*http://www.bgbk.org
*/
function Bing_custom_footer_code(){
	echo Bing_mpanel( 'footer_code' );
}
add_action( 'wp_footer', 'Bing_custom_footer_code', 14 );

/**
	*返回顶部
	*http://www.bgbk.org
*/
function Bing_return_top(){
	if( Bing_mpanel( 'return_top' ) ) echo '<a href="#" id="return-top" title="' . esc_attr__( '返回顶部', 'Bing' ) . '" data-no-ajax><span class="dashicons dashicons-arrow-up-alt"></span></a>';
}
add_action( 'wp_footer', 'Bing_return_top' );

//End of page.
