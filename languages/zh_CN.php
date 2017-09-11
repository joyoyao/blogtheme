<?php
/**
	*Gravatar 头像使用中国服务器
	*http://www.bgbk.org
*/
function Bing_gravatar_cn( $url ){
	$gravatar_url = array(
		'0.gravatar.com',
		'1.gravatar.com',
		'2.gravatar.com'
	);
	return str_replace( $gravatar_url, 'cn.gravatar.com', $url );
}
add_filter( 'get_avatar_url', 'Bing_gravatar_cn', 4 );

/**
	*禁止半角符号自动转换
	*http://www.bgbk.org
*/
add_filter( 'run_wptexturize', '__return_false', 12 );

/**
	*替换 Google API 为 360 CDN
	*http://www.bgbk.org
*/
function Bing_google_apis_replace_useso( $src ){
	$google = array(
		'https://fonts.googleapis.com/',
		'https://ajax.googleapis.com/',
		'//fonts.googleapis.com/',
		'//ajax.googleapis.com/'
	);
	$useso = array(
		'http://fonts.useso.com/',
		'http://ajax.useso.com/',
		'//fonts.useso.com/',
		'//ajax.useso.com/'
	);
	return str_replace( $google, $useso, $src );
}
add_filter( 'style_loader_src', 'Bing_google_apis_replace_useso', 16 );
add_filter( 'script_loader_src', 'Bing_google_apis_replace_useso', 16 );

/**
	*WordPress Emoji 表情无法使用的问题
	*Emoji 使用 MaxCDN
	*http://www.endskin.com/emoji-error/
*/
function Bing_emoji_url_maxcdn(){
	return set_url_scheme( '//twemoji.maxcdn.com/72x72/' );
}
add_filter( 'emoji_url', 'Bing_emoji_url_maxcdn', 8 );

//End of page.
