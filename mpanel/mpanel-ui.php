<?php
/**
	*后台选项
	*http://www.bgbk.org
*/

//广告
$banners = array(
	array( 'name' => __( '头部广告', 'Bing' ),     'id' => 'header' ),
	array( 'name' => __( '底部广告', 'Bing' ),     'id' => 'footer' ),
	array( 'name' => __( '文章底部广告', 'Bing' ), 'id' => 'post_bottom' )
);

//基本设置
$this->before( __( '基本设置', 'Bing' ) );

	//响应式布局
	$this->group( __( '响应式布局', 'Bing' ) );

		//开启响应式布局
		$responsive_show = array( 'hide_safari_bar', 'hot_searches' );
		foreach( $banners as $banner ) $responsive_show[] = 'banner_' . $banner['id'] . '_client';
		$this->item( array(
			'name'    => __( '开启响应式布局', 'Bing' ),
			'id'      => 'responsive',
			'type'    => 'checkbox',
			'show'    => $responsive_show,
			'shows'   => 'custom_responsive_css',
			'default' => true
		) );

		//屏蔽 Safari 底栏
		$this->item( array(
			'name' => __( '屏蔽 Safari 底栏', 'Bing' ),
			'id'   => 'hide_safari_bar',
			'type' => 'checkbox',
			'help' => __( '开启后 iPhone 和 iPad 上使用 Safari 浏览你的网站时会屏蔽底栏，留出更大阅读空间；仅在 iOS7 有效', 'Bing' )
		) );

	$this->end();

	//Logo
	$this->group( 'Logo' );

		//显示图片 Logo
		$this->item( array(
			'name'    => __( '显示图片 Logo', 'Bing' ),
			'id'      => 'logo',
			'type'    => 'checkbox',
			'show'    => 'logo_url',
			'default' => true
		) );

		//自定义 Logo
		$this->item( array(
			'name'    => __( '自定义 Logo', 'Bing' ),
			'id'      => 'logo_url',
			'type'    => 'upload',
			'explain' => __( 'Logo 图片显示大小为 120 × 50px；请尽量选择 240 × 100px 的图片，以兼容 Retina 屏幕的显示效果', 'Bing' )
		) );

	$this->end();

	//缩略图
	$this->group( __( '缩略图', 'Bing' ) );

		//显示缩略图
		$this->item( array(
			'name'    => __( '显示缩略图', 'Bing' ),
			'id'      => 'thumbnail',
			'type'    => 'checkbox',
			'default' => true
		) );

		//裁剪缩略图
		$this->item( array(
			'name'    => __( '裁剪缩略图图片', 'Bing' ),
			'id'      => 'crop_thumbnail',
			'type'    => 'checkbox',
			'help'    => sprintf( __( '使用 WordPress 自带函数自动裁剪文章的缩略图到需要的尺寸，生成的缩略图文件将统一存储到：%s', 'Bing' ), WP_CONTENT_DIR . THEME_THUMBNAIL_PATH ),
			'default' => true
		) );

	$this->end();

	//AJAX
	$this->group( 'AJAX' );

		//AJAX 加载页面
		$this->item( array(
			'name'    => __( 'AJAX 加载页面', 'Bing' ),
			'id'      => 'ajax_load_page',
			'type'    => 'checkbox',
			'shows'   => 'progress',
			'hide'    => 'search_one_redirect',
			'help'    => __( 'AJAX 加载页面，只会重新加载页面核心部分，提高加载速度' ),
			'default' => true
		) );

		//AJAX 提交评论
		$this->item( array(
			'name'    => __( 'AJAX 提交评论', 'Bing' ),
			'id'      => 'ajax_comment',
			'type'    => 'checkbox',
			'default' => true
		) );

	$this->end();

	//进度条
	$this->group( __( '进度条', 'Bing' ), 'progress' );

		//显示 AJAX 加载进度条
		$this->item( array(
			'name'    => __( '显示 AJAX 加载进度条', 'Bing' ),
			'id'      => 'progress',
			'type'    => 'checkbox',
			'help'    => __( 'AJAX 加载页面时在顶部显示进度条', 'Bing' ),
			'default' => true
		) );

	$this->end();

	//面包屑导航
	$this->group( __( '面包屑导航', 'Bing' ) );

		//显示面包屑导航
		$this->item( array(
			'name'    => __( '显示面包屑导航', 'Bing' ),
			'id'      => 'breadcrumbs',
			'type'    => 'checkbox',
			'default' => true
		) );

	$this->end();

	//网站描述
	$this->group( __( '网站描述', 'Bing' ) );

		//网站描述
		$this->item( array(
			'name'        => __( '网站描述', 'Bing' ),
			'id'          => 'site_description',
			'type'        => 'textarea',
			'placeholder' => __( '优化建议：不超过 140 个字', 'Bing' ),
			'help'        => __( '给搜索引擎使用的网站描述内容', 'Bing' )
		) );

	$this->end();

$this->after();

//优化配置
$this->before( __( '优化配置', 'Bing' ) );

	//文章
	$this->group( __( '文章', 'Bing' ) );

		//阻止站内文章互相 Pingback
		$this->item( array(
			'name'    => __( '阻止站内文章互相 Pingback', 'Bing' ),
			'id'      => 'no_self_pingback',
			'type'    => 'checkbox',
			'default' => true
		) );

		//首行缩进两格
		$this->item( array(
			'name' => __( '首行缩进两格', 'Bing' ),
			'id'   => 'first_line_indent',
			'type' => 'checkbox',
			'help' => __( '文章每个段落开头自动空两格', 'Bing' )
		) );

		//显示相关文章
		$this->item( array(
			'name'    => __( '显示相关文章', 'Bing' ),
			'id'      => 'related_posts',
			'type'    => 'checkbox',
			'show'    => 'related_posts_number',
			'default' => true
		) );

		//相关文章数量
		$this->item( array(
			'name'    => __( '相关文章数量', 'Bing' ),
			'id'      => 'related_posts_number',
			'type'    => 'number',
			'default' => 3
		) );

	$this->end();

	//头部元信息
	$this->group( __( '头部元信息', 'Bing' ) );

		//移除头部无用信息
		$this->item( array(
			'name'    => __( '移除头部无用信息', 'Bing' ),
			'id'      => 'remove_head_refuse',
			'type'    => 'checkbox',
			'default' => true,
			'help'    => __( 'WordPress 在头部输出了一些无用的信息，开启后会把他们移除', 'Bing' )
		) );

		//关闭离线编辑器接口
		$this->item( array(
			'name' => __( '关闭离线编辑器接口', 'Bing' ),
			'id'   => 'remove_xmlrpc',
			'type' => 'checkbox',
			'help' => __( '如果你只使用 WordPress 的后台编辑器开启此选项会更安全', 'Bing' )
		) );

	$this->end();

	//阅读
	$this->group( __( '阅读', 'Bing' ) );

		//文章内链接全部在新窗口打开
		$this->item( array(
			'name' => __( '文章链接全部在新窗口打开', 'Bing' ),
			'id'   => 'post_auto_blank',
			'type' => 'checkbox'
		) );

		//文章内容外链添加 nofollow 并在新窗口打开
		$this->item( array(
			'name'    => __( '文章外链添加 nofollow 并在新窗口打开', 'Bing' ),
			'id'      => 'post_auto_nofollow_blank',
			'type'    => 'checkbox',
			'default' => true
		) );

	$this->end();

	//搜索
	$this->group( __( '搜索', 'Bing' ) );

		//搜索结果只包括文章
		$this->item( array(
			'name' => __( '搜索结果只包括文章', 'Bing' ),
			'id'   => 'search_filter_post',
			'type' => 'checkbox',
			'help' => __( '排除页面和其它自定义文章类型', 'Bing' )
		) );			

		//搜索结果只有一篇文章时自动跳转到该文章
		$this->item( array(
			'name'    => __( '搜索结果只有一篇文章时自动跳转到该文章', 'Bing' ),
			'id'      => 'search_one_redirect',
			'type'    => 'checkbox',
			'default' => true
		) );

		//移动版搜索页面显示热门搜索
		$this->item( array(
			'name'    => __( '移动版搜索页面显示热门搜索', 'Bing' ),
			'id'      => 'hot_searches',
			'type'    => 'checkbox',
			'help'    => __( '移动版搜索页在还没有进行搜索的时候显示热门搜索' ),
			'default' => true
		) );

	$this->end();

$this->after();

//评论系统
$this->before( __( '评论系统', 'Bing' ) );

	//垃圾评论
	$this->group( __( '垃圾评论', 'Bing' ) );

		//垃圾评论拦截
		$this->item( array(
			'name' => __( '垃圾评论拦截', 'Bing' ),
			'id'   => 'comment_anti',
			'type' => 'checkbox',
			'help' => __( '拦截所有不是来自于本站评论表单的评论', 'Bing' )
		) );

		//拦截不包含中文的评论
		$this->item( array(
			'name' => __( '拦截不包含中文的评论', 'Bing' ),
			'id'   => 'comment_anti_chinese',
			'type' => 'checkbox'
		) );

		//禁止没有头像的用户评论
		$this->item( array(
			'name' => __( '禁止没有头像的用户评论', 'Bing' ),
			'id'   => 'validate_gravatar_comment',
			'type' => 'checkbox',
			'help' => __( '谨慎开启。开启之后如果评论用户的邮箱没有 Gravatar 头像将会被禁止', 'Bing' )
		) );

	$this->end();

	//评论邮件通知
	$this->group( __( '评论邮件通知', 'Bing' ) );

		//评论邮件通知
		$this->item( array(
			'name' => __( '评论邮件通知', 'Bing' ),
			'id'   => 'comment_email_notify',
			'type' => 'checkbox',
			'help' => __( '当用户的评论被回复之后自动发送一封邮件提醒', 'Bing' )
		) );

	$this->end();

	//回复评论加 @
	$this->group( __( '回复评论加 @', 'Bing' ) );

		//回复评论加 @
		$this->item( array(
			'name'    => __( '回复评论加 @', 'Bing' ),
			'id'      => 'comment_add_at',
			'type'    => 'checkbox',
			'default' => true,
			'help'    => __( '回复的评论会在前面加上 @ 回复的人，@ 不会被写入数据库；使用主题之前的评论也有效', 'Bing' )
		) );

	$this->end();

	//评论链接
	$this->group( __( '评论链接', 'Bing' ) );

		//评论作者链接新窗口打开
		$this->item( array(
			'name' => __( '评论作者链接新窗口打开', 'Bing' ),
			'id'   => 'comment_author_link_blank',
			'type' => 'checkbox',
			'help' => __( '评论作者的链接在新窗口打开，以免流失访客', 'Bing' )
		) );

	$this->end();

$this->after();

//底部内容
$this->before( __( '底部内容', 'Bing' ) );

	//页脚文本区域内容
	$this->group( __( '页脚文本区域内容', 'Bing' ) );

		//页脚文本区域内容（左）
		$this->item( array(
			'name'        => __( '页脚文本区域内容（左）', 'Bing' ),
			'id'          => 'footer_text_left',
			'type'        => 'textarea',
			'placeholder' => __( '显示在页脚左侧', 'Bing' ),
			'default'     => '© ' . current_time( 'Y' ) . ' <a href="' . esc_url( home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '">' . get_bloginfo( 'name', 'display' ) . '</a>',
			'help'        => __( '通常为网站版权信息；会在很多地方调用，请不要添加统计代码之类的内容', 'Bing' )
		) );

		//页脚文本区域内容（右）
		$this->item( array(
			'name'        => __( '页脚文本区域内容（右）', 'Bing' ),
			'id'          => 'footer_text_right',
			'type'        => 'textarea',
			'placeholder' => __( '显示在页脚右侧', 'Bing' ),
			'default'     => 'Power by <a href="http://cn.wordpress.org" rel="external" target="_blank">WordPress</a> | Theme <a href="http://www.bgbk.org" rel="external" target="_blank">' . THEME_NAME . '</a>',
			'help'        => __( '请不要去除版权链接', 'Bing' )
		) );

	$this->end();

	//返回顶部
	$this->group( __( '返回顶部', 'Bing' ) );

		//显示返回顶部按钮
		$this->item( array(
			'name'    => __( '显示返回顶部按钮', 'Bing' ),
			'id'      => 'return_top',
			'type'    => 'checkbox',
			'default' => true
		) );

	$this->end();

$this->after();

//边栏设置
$this->before( __( '边栏设置', 'Bing' ) );

	//侧边栏
	$this->group( __( '侧边栏', 'Bing' ) );

		//显示侧边栏
		$this->item( array(
			'name'    => __( '显示侧边栏', 'Bing' ),
			'id'      => 'sidebar',
			'type'    => 'checkbox',
			'shows'   => array( 'refresh_sidebar', 'sidebars_list', 'sidebars_location' ),
			'default' => true
		) );

	$this->end();

	//侧边栏列表
	$this->group( __( '侧边栏列表', 'Bing' ), 'sidebars_list' );

		//创建侧边栏
		$this->item( array(
			'name' => __( '创建侧边栏', 'Bing' ),
			'id'   => 'sidebars_list',
			'type' => 'list'
		) );

	$this->end();

	//侧边栏位置
	$this->group( __( '侧边栏位置', 'Bing' ), 'sidebars_location' );

		$sidebars = array( 'default' => __( '默认侧边栏', 'Bing' ) );
		foreach( array_filter( (array) $this->mpanel->get( 'sidebars_list' ) ) as $sidebar ) $sidebars[$sidebar] = $sidebar;

		$pages_location = array(
			'home'    => __( '首页', 'Bing' ),
			'post'    => __( '文章页', 'Bing' ),
			'archive' => __( '存档页', 'Bing' ),
			'search'  => __( '搜索页', 'Bing' )
		);
		foreach( $pages_location as $page_location => $page_location_name ) $this->item( array(
			'name'   => sprintf( __( '%s侧边栏', 'Bing' ), $page_location_name ),
			'id'     => 'sidebar_location_' . $page_location,
			'type'   => 'select',
			'option' => $sidebars
		) );

		foreach( get_categories( 'hide_empty=0' ) as $category_location ) $this->item( array(
			'name'   => sprintf( __( '分类[%s]侧边栏', 'Bing' ), $category_location->name ),
			'id'     => 'sidebar_location_category_' . $category_location->term_id,
			'type'   => 'select',
			'option' => $sidebars
		) );

		unset( $sidebar, $pages_location, $page_location, $page_location, $category_location, $sidebars );

?>
		<script type="text/javascript">
			jQuery( function( $ ){

				jQuery( document ).on( 'click', '#mpanel-item-sidebars_list .mpanel-list-add', function(){
					var text = jQuery( this ).next( 'ul' ).children( 'li:last' ).children( '.mpanel-list-li-name' ).html(),
						option = jQuery( '#mpanel-item-sidebar_location_home option:last' );
					if( text != option.text() ) jQuery( '<option>' + text + '</option>' ).val( text ).appendTo( '.sidebars_location select' );
				} );

				jQuery( document ).on( 'click', '#mpanel-item-sidebars_list .mpanel-list-li-delete', function(){
					var text = jQuery( this ).prev( '.mpanel-list-li-name' ).html();
					jQuery( '.sidebars_location select option' ).each( function(){
						var $this = jQuery( this );
						if( $this.val() == text ) $this.remove();
					} );
				} );

			} );
		</script>
<?php

	$this->end();

$this->after();

//样式定义
$this->before( __( '样式定义', 'Bing' ) );

	//主颜色
	$this->group( __( '主颜色', 'Bing' ) );

		//自定义主颜色
		$this->item( array(
			'name' => __( '自定义主颜色', 'Bing' ),
			'id'   => 'custom_main_color',
			'type' => 'checkbox',
			'show' => 'main_color'
		) );

		//主颜色
		$this->item( array(
			'name'    => __( '主颜色', 'Bing' ),
			'id'      => 'main_color',
			'type'    => 'color',
			'default' => '#2D6DCC'
		) );

	$this->end();

	//自定义全局 CSS
	$this->group( __( '自定义全局 CSS', 'Bing' ) );

		//自定义全局 CSS
		$this->item( array(
			'id'          => 'custom_css',
			'type'        => 'bigtext',
			'placeholder' => __( '前台全局 CSS 代码', 'Bing' )
		) );

	$this->end();

	//自定义响应式 CSS
	$this->group( __( '自定义响应式 CSS', 'Bing' ), 'custom_responsive_css' );

		//自定义响应式 CSS
		foreach( array( 1220, 1200, 1100, 1000, 900, 800, 700, 600, 500, 400 ) as $px ){
			$this->item( array(
				'name' => sprintf( __( '自定义响应式 CSS（%s px 以下）', 'Bing' ), $px ),
				'id'   => 'custom_responsive_css_' . $px,
				'type' => 'textarea',
				'help' => sprintf( __( '此 CSS 会在屏幕分辨率在 %s px 以下才会生效', 'Bing' ), $px )
			) );
		}

	$this->end();

$this->after();

//广告设置
$this->before( __( '广告设置', 'Bing' ) );

	//显示广告
	$this->group( __( '头部广告', 'Bing' ) );

		foreach( $banners as $banner ){
			$banner_id = 'banner_' . $banner['id'];
			$this->item( array(
				'name'  => sprintf( __( '显示%s', 'Bing' ), $banner['name'] ),
				'id'    => $banner_id . '_show',
				'shows' => $banner_id,
				'type'  => 'checkbox'
			) );
		}

	$this->end();

	//广告
	foreach( $banners as $banner ){

		$banner_id = 'banner_' . $banner['id'];
		$this->group( $banner['name'], $banner_id );

			//广告类型
			$this->item( array(
				'name'    => __( '广告类型', 'Bing' ),
				'id'      => $banner_id . '_type',
				'type'    => 'radio',
				'option'  => array(
					'img'  => __( '图片广告', 'Bing' ),
					'code' => __( '代码广告', 'Bing' )
				),
				'show'    => array(
					'img'  => array(
						$banner_id . '_img',
						$banner_id . '_alt',
						$banner_id . '_url',
						$banner_id . '_tab'
					),
					'code' => $banner_id . '_code'
				),
				'default' => 'img'
			) );

			//广告图片
			$this->item( array(
				'name' => __( '广告图片', 'Bing' ),
				'id'   => $banner_id . '_img',
				'type' => 'upload'
			) );

			//提示文本
			$this->item( array(
				'name'        => __( '提示文本', 'Bing' ),
				'id'          => $banner_id . '_alt',
				'type'        => 'text',
				'placeholder' => __( '留空则不显示提示文本', 'Bing' ),
				'help'        => __( '鼠标停留到广告图片时显示的文本', 'Bing' )
			) );

			//广告链接
			$this->item( array(
				'name'        => __( '广告链接', 'Bing' ),
				'id'          => $banner_id . '_url',
				'type'        => 'text',
				'placeholder' => __( '留空则不添加链接', 'Bing' ),
				'help'        => __( '广告的链接地址', 'Bing' )
			) );

			//在新窗口打开链接
			$this->item( array(
				'name'    => __( '在新窗口打开链接', 'Bing' ),
				'id'      => $banner_id . '_tab',
				'type'    => 'checkbox',
				'default' => true
			) );

			//自定义广告代码
			$this->item( array(
				'name' => __( '自定义广告代码', 'Bing' ),
				'id'   => $banner_id . '_code',
				'type' => 'textarea'
			) );

			//自动下架
			$this->item( array(
				'name'        => __( '自动下架', 'Bing' ),
				'id'          => $banner_id . '_hide_date',
				'type'        => 'date',
				'placeholder' => __( '留空则不自动下架', 'Bing' ),
				'help'        => __( '到此日期时自动不显示广告', 'Bing' )
			) );

			//适用平台
			$this->item( array(
				'name'    => __( '适用平台', 'Bing' ),
				'id'      => $banner_id . '_client',
				'type'    => 'multiple',
				'help'    => __( '移动端应该尽量避免使用加载速度较慢的广告', 'Bing' ),
				'explain' => __( '可以利用 Ctrl 键多选', 'Bing' ),
				'option'  => array(
					'pc'     => __( '在 PC 端显示', 'Bing' ),
					'mobile' => __( '在移动端显示', 'Bing' )
				),
				'default' => array( 'pc', 'mobile' )
			) );

		$this->end();

	}

$this->after();

//高级功能
$this->before( __( '高级功能', 'Bing' ) );

	//头像缓存
	$this->group( __( '头像缓存', 'Bing' ) );

		//头像缓存
		$this->item( array(
			'name' => __( '头像缓存', 'Bing' ),
			'id'   => 'avatar_cache',
			'type' => 'checkbox',
			'help' => sprintf( __( '将存储在美国服务器的 Gravatar 头像缓存到本地，提高加载速度；生成的缓存图片文件将统一存储到：%s', 'Bing' ), WP_CONTENT_DIR . THEME_AVATAR_PATH ),
			'show' => 'avatar_cache_day'
		) );

		//缓存天数
		$this->item( array(
			'name'    => __( '缓存天数', 'Bing' ),
			'id'      => 'avatar_cache_day',
			'type'    => 'number',
			'help'    => __( '设置为 0 则永久缓存', 'Bing' ),
			'default' => 15
		) );

	$this->end();

	//自定义头部代码
	$this->group( __( '自定义头部代码', 'Bing' ) );

		//自定义头部代码
		$this->item( array(
			'id' => 'head_code',
			'type' => 'bigtext'
		) );

		//头部代码帮助
		$this->item( array(
			'name' => __( '这段代码会被添加到前台的 &lt;head&gt; 标签里边，可以引入一些 CSS、放置统计代码。', 'Bing' ),
			'type' => 'help'
		) );

	$this->end();

	//自定义底部代码
	$this->group( __( '自定义底部代码', 'Bing' ) );

		//自定义底部代码
		$this->item( array(
			'id'   => 'footer_code',
			'type' => 'bigtext'
		) );

		//页脚代码帮助
		$this->item( array(
			'name' => __( '这段代码会被添加到前台的底部 &lt;/body&gt; 标签之前，可以放置统计代码、引入一些 JS。', 'Bing' ),
			'type' => 'help'
		) );

	$this->end();

	//导出设置
	$this->group( __( '导出设置', 'Bing' ) );

		//自定义导出设置头部代码
		$this->item( array(
			'id'   => 'export',
			'type' => 'bigtext'
		) );

		//导出设置帮助
		$this->item( array(
			'name' => __( '保存这个文本框的代码即可备份设置，把代码填入下边的文本框就能恢复设置。', 'Bing' ),
			'type' => 'help'
		) );

	$this->end();

	//导入设置
	$this->group( __( '导入设置', 'Bing' ) );

		//导入设置
		$this->item( array(
			'id'          => 'import',
			'type'        => 'bigtext',
			'placeholder' => __( '粘贴之前保存的设置导出字符串，点击保存就能导入', 'Bing' )
		) );

	$this->end();

$this->after();

//End of page.
