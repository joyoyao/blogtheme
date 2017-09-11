<?php
/**
	*移动版头部
	*http://www.bgbk.org
*/
function Bing_mobile_header(){
	$show_class = is_singular() && get_post_type() != 'page' ? ' show' : '';
?>
	<div id="mobile-header">
		<a href="javascript:history.go( -1 );" class="mobile-return<?php echo $show_class; ?>">
			<span class="dashicons dashicons-arrow-left-alt2"></span><?php _e( '返回', 'Bing' ); ?>
		</a>
		<div class="mobile-title"><?php echo Bing_mobile_title(); ?></div>
	</div>
<?php
}

/**
	*移动版菜单
	*http://www.bgbk.org
*/
function Bing_mobile_menu(){
	$current = Bing_mobile_menu_current();
?>
	<ul id="mobile-menu">
		<li class="home<?php if( $current == 'home' || empty( $current ) ) echo ' current'; ?>" data-name="home">
			<a href="<?php echo esc_url( home_url() ); ?>">
				<span class="dashicons dashicons-admin-home"></span>
				<?php _e( '首页', 'Bing' ); ?>
			</a>
		</li>
		<li class="category<?php if( $current == 'category' ) echo ' current'; ?>" data-name="category">
			<a href="<?php echo esc_url( add_query_arg( 'mobile_page', 'category', home_url() ) ); ?>">
				<span class="dashicons dashicons-category"></span>
				<?php _e( '分类', 'Bing' ); ?>
			</a>
		</li>
		<li class="search<?php if( $current == 'search' ) echo ' current'; ?>" data-name="search">
			<a href="<?php echo esc_url( home_url( '?s' ) ); ?>">
				<span class="dashicons dashicons-search"></span>
				<?php _e( '搜索', 'Bing' ); ?>
			</a>
		</li>
		<li class="about<?php if( $current == 'about' ) echo ' current'; ?>" data-name="about">
			<a href="<?php echo esc_url( add_query_arg( 'mobile_page', 'about', home_url() ) ); ?>">
				<span class="dashicons dashicons-info"></span>
				<?php _e( '关于', 'Bing' ); ?>
			</a>
		</li>
	</ul>
<?php
}

/**
	*移动版 tab 菜单
	*http://www.bgbk.org
*/
function Bing_mobile_tab_menu(){
	$tabs = array(
		'context'  => array( 'name' => __( '内容' ), 'current' => true ),
		'comments' => array( 'name' => __( '评论' ), 'disable' => post_password_required() || ( !comments_open() && get_comments_number() <= 0 ) ),
		'related'  => array( 'name' => __( '相关' ), 'disable' => !Bing_mpanel( 'related_posts' ) )
	);
?>
	<ul id="mobile-tab-menu" class="no-js-hide">
		<?php
		foreach( $tabs as $ID => $tab ):
			$classes = array();
			if( isset( $tab['current'] ) && $tab['current'] ) $classes[] = 'current';
			if( isset( $tab['disable'] ) && $tab['disable'] ) $classes[] = 'disable';
			?>
				<li class="<?php echo implode( $classes, ' ' ); ?>" data-tab="<?php echo esc_attr( $ID ); ?>"><?php echo $tab['name']; ?></li>
		<?php endforeach; ?>
	</ul>
<?php
}

/**
	*移动版热门搜索
	*http://www.bgbk.org
*/
function Bing_mobile_menu_hot_searches(){
	if( !$tags = get_tags( 'orderby=count&order=DESC&number=10' ) ) return;
?>
	<div class="span12 hot-searches">
		<article class="panel center">
			<header class="panel-header">
				<h3 class="hot-searches-title"><?php _e( '热门搜索', 'Bing' ); ?></h3>
			</header>
			<ul class="hot-searches-list">
				<?php foreach( $tags as $tag ): ?>
					<li>
						<a href="<?php echo esc_url( get_search_link( $tag->name ) ); ?>"><?php echo $tag->name; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</article>
	</div>
<?php
}

/**
	*获取移动版菜单当前页
	*http://www.bgbk.org
*/
function Bing_mobile_menu_current(){
	if( is_search() ) return 'search';
	elseif( is_archive() || Bing_is_mobile_page_category() ) return 'category';
	elseif( Bing_is_mobile_page_about() ) return 'about';
	elseif( is_home() ) return 'home';
	else return '';
}

/**
	*获取移动版标题
	*http://www.bgbk.org
*/
function Bing_mobile_title(){
	if( is_category() || is_tag() || is_tax() ) return single_term_title( '', false );
	if( is_search() ) return get_search_form( false );
	if( is_author() ) return $GLOBALS['authordata']->display_name;
	if( is_date() ){
		if( is_day() ) $format = __( 'Y年m月d日', 'Bing' );
		elseif( is_month() ) $format = __( 'Y年m月', 'Bing' );
		else $format = __( 'Y年', 'Bing' );
		return get_the_date( $format );
	}
	if( is_404() ) return '404';
	if( is_page() ) return get_the_title();
	if( Bing_is_mobile_page_category() ) return __( '分类', 'Bing' );
	if( Bing_is_mobile_page_about() ) return __( '关于', 'Bing' );
	return get_bloginfo( 'name', 'display' );
}

/**
	*移动版分页导航
	*http://www.bgbk.org
*/
function Bing_mobile_page_navi(){
	if( !Bing_mpanel( 'ajax_load_page' ) ) return Bing_mobile_page_navi_button();
?>
	<div class="span12 posts-list-mobile-page-navi" data-next-link="<?php echo esc_url( get_next_posts_page_link() ); ?>">
		<div class="panel">
			<?php if( get_next_posts_link() ): ?>
				<p class="help-text"><?php _e( '更多...', 'Bing' ); ?></p>
				<p class="loading">
					<span class="dashicons dashicons-image-rotate"></span>
					<?php _e( '加载中...', 'Bing' ); ?>
				</p>
			<?php else: ?>
				<p class="help-text"><?php _e( '全部加载完成', 'Bing' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
<?php
}

/**
	*移动版分页导航（按钮翻页）
	*http://www.bgbk.org
*/
function Bing_mobile_page_navi_button(){
	if( get_previous_posts_link() ):
?>
		<a href="<?php echo esc_url( get_previous_posts_page_link() ); ?>" class="span12 posts-list-mobile-page-navi">
			<div class="panel">
				<p class="help-text"><?php _e( '上一页', 'Bing' ); ?></p>
			</div>
		</a>
<?php
	endif;
	if( get_next_posts_link() ):
?>
		<a href="<?php echo esc_url( get_next_posts_page_link() ); ?>" class="span12 posts-list-mobile-page-navi">
			<div class="panel">
				<p class="help-text"><?php _e( '下一页', 'Bing' ); ?></p>
			</div>
		</a>
<?php
	endif;
}

/**
	*判断移动版分类列表页
	*http://www.bgbk.org
*/
function Bing_is_mobile_page_category(){
	return is_home() && isset( $_GET['mobile_page'] ) && $_GET['mobile_page'] == 'category';
}

/**
	*使用移动版分类列表页
	*http://www.bgbk.org
*/
function Bing_mobile_page_category( $template ){
	if( !Bing_is_mobile_page_category() ) return $template;
	$categories = get_categories( 'orderby=count&order=DESC' );
	get_header();
?>
		<section id="container">
			<div class="row">
				<?php if( empty( $categories ) ): ?>
					<div class="empty-categories-list span12">
						<article class="panel">
							<p><?php _e( '没有任何存在文章的分类', 'Bing' ); ?></p>
						</article>
					</div>
				<?php else: ?>
					<ul class="categories-list">
						<?php foreach( $categories as $category ): ?>
							<li class="span12">
								<article class="panel">
									<a href="<?php echo esc_url( get_category_link( $category ) ); ?>">
										<header class="panel-header">
											<h3 class="category-name"><span class="dashicons dashicons-category"></span><?php echo get_cat_name( $category->term_id ); ?></h3>
										</header>
										<?php echo category_description( $category->term_id ); ?>
									</a>
								</article>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</section>
<?php
	get_footer();
	return false;
}
add_filter( 'template_include', 'Bing_mobile_page_category', 16 );

/**
	*判断移动版关于页
	*http://www.bgbk.org
*/
function Bing_is_mobile_page_about(){
	return is_home() && isset( $_GET['mobile_page'] ) && $_GET['mobile_page'] == 'about';
}

/**
	*使用移动版分类列表页
	*http://www.bgbk.org
*/
function Bing_mobile_page_about( $template ){
	if( !Bing_is_mobile_page_about() ) return $template;
	get_header();
?>
		<section id="container">
			<div class="row">
				<div class="span12 mobile-about">
					<article class="panel">
						<header class="panel-header">
							<h3 class="about"><span class="dashicons dashicons-info"></span><?php _e( '关于', 'Bing' ); ?></h3>
						</header>
						<p><?php echo Bing_mpanel( 'footer_text_left' ); ?></p>
						<p><?php echo Bing_mpanel( 'footer_text_right' ); ?></p>
					</article>
				</div>
			</div>
		</section>
<?php
	get_footer();
	return false;
}
add_filter( 'template_include', 'Bing_mobile_page_about', 16 );

/**
	*AJAX 加载页面添加移动版信息
	*http://www.bgbk.org
*/
function Bing_ajax_load_page_data_mobile( $data ){
	$data = array_merge( $data, array(
		'mobile_menu_current' => Bing_mobile_menu_current(),
		'mobile_title'        => Bing_mobile_title(),
		'mobile_return_show'  => is_singular() && get_post_type() != 'page'
	) );
	return $data;
}
add_filter( 'ajax_load_page_data', 'Bing_ajax_load_page_data_mobile' );

//End of page.
