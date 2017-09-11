<?php get_header(); ?>
	<section id="container">
		<div class="row">
			<div class="span12 search-header">
				<article class="panel">
					<header class="panel-header">
						<h2 class="search-title">
							<span class="dashicons dashicons-search"></span><?php _e( '搜索', 'Bing' ); ?>
						</h2>
						<?php if( Bing_mpanel( 'breadcrumbs' ) ) Bing_breadcrumbs( '<span class="separator dashicons dashicons-arrow-right-alt2"></span>', '<span class="right breadcrumb"%s>', '</span>', '<span class="dashicons dashicons-admin-home"></span>' . __( '首页', 'Bing' ) ); ?>
					</header>
					<?php echo '<p class="search-number">' . ( get_search_query() ? sprintf( __( '关键词“%s”共有 %s 个搜索结果', 'Bing' ), get_search_query(), $GLOBALS['wp_query']->found_posts ) : __( '无关键词的搜索', 'Bing' ) ) . '</p>'; ?>
				</article>
			</div>
			<?php
			if( Bing_mpanel( 'hot_searches' ) && !get_search_query() ) Bing_mobile_menu_hot_searches();
			if( get_search_query() ) Bing_posts_list(); 
			?>
		</div>
		<a class="search-mobile-mask" href="javascript:;"></a>
	</section>
<?php get_footer(); ?>