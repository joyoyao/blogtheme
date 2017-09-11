<?php get_header(); ?>
	<section id="container">
		<div class="row">
			<?php the_post(); ?>
			<article id="post-box" itemscope itemtype="http://schema.org/Article" <?php post_class( 'span12' ); ?>>
				<div class="panel">
					<header class="panel-header">
						<div class="post-meta-box">
							<?php if( Bing_mpanel( 'breadcrumbs' ) ) Bing_breadcrumbs( '<span class="separator dashicons dashicons-arrow-right-alt2"></span>', '<span class="breadcrumb"%s>', '</span>', '<span class="dashicons dashicons-admin-home"></span>' . __( '首页', 'Bing' ) ); ?>
							<?php Bing_post_meta( array( 'author', 'date-abb', 'comments', 'tags' ) ); ?>
						</div>
						<?php
						the_title( '<h2 class="post-title">', '</h2>' );
						edit_post_link( '<span class="dashicons dashicons-edit"></span>' . __( '编辑', 'Bing' ), '<span class="right">', '</span>' );
						Bing_mobile_tab_menu();
						?>
					</header>
					<section class="context">
						<?php
						the_content();
						wp_link_pages( array( 'before' => '<div class="page-links">' . __( '页码：', 'Bing' ), 'after' => '</div>' ) );
						?>
					</section>
				</div>
			</article>
			<?php
			Bing_banner_post_bottom();
			if( Bing_mpanel( 'related_posts' ) ) Bing_related_posts();
			if( !post_password_required() && ( comments_open() || get_comments_number() > 0 ) ) comments_template( '', true );
			?>
		</div>
	</section>
<?php get_footer(); ?>