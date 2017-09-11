<?php
/**
	*相关文章
	*http://www.bgbk.org
*/
function Bing_related_posts(){
	if( !$related_posts_ID = Bing_related_posts_ID() ) return;
	$query = new WP_Query( array(
		'nopaging'            => true,
		'post__in'            => $related_posts_ID,
		'orderby'             => 'rand',
		'ignore_sticky_posts' => true
	) );
	if( !$query->have_posts() ) return;
?>
	<div class="span12 related-posts-box mobile-hide">
		<div class="panel transparent no-padding related-posts-box-panel">
			<header class="panel-header">
				<h3>
					<span class="dashicons dashicons-pressthis"></span><?php _e( '相关文章', 'Bing' ); ?>
				</h3>
				<span class="right">
					<a href="javascript:;" class="refresh" data-post-id="<?php the_ID(); ?>" title="<?php esc_attr_e( '换一批新的相关文章', 'Bing' ); ?>">
						<span class="dashicons dashicons-image-rotate"></span><?php _e( '刷新', 'Bing' ); ?>
					</a>
				</span>
			</header>
			<ul class="related-posts row">
				<?php
				while( $query->have_posts() ):
					$query->the_post();
					Bing_related_posts_loop();
				endwhile;
				?>
			</ul>
		</div>
	</div>
<?php
	wp_reset_postdata();
}

/**
	*获取相关文章
	*http://www.bgbk.org
*/
function Bing_related_posts_ID( $post = null ){
	$post = get_post( $post );
	$number = Bing_mpanel( 'related_posts_number' );
	$posts = array();
	$exclude_ID = array( $post->ID );
	if( $tags = get_the_tags( $post ) ){
		$tags_ID = array();
		foreach( $tags as $tag ) $tags_ID[] = $tag->term_id;
		$posts = get_posts( array(
			'tag__in'     => $tags_ID,
			'exclude'     => $exclude_ID,
			'orderby'     => 'rand',
			'fields'      => 'ids',
			'numberposts' => $number
		) );
	}
	if( count( $posts ) < $number && $cats = get_the_category( $post ) ){
		$cats_ID = array();
		foreach( $cats as $cat ) $cats_ID[] = $cat->term_id;
		$_posts = get_posts( array(
			'category__in' => $cats_ID,
			'exclude'      => array_merge( $exclude_ID, $posts ),
			'orderby'      => 'rand',
			'fields'       => 'ids',
			'numberposts'  => $number - count( $posts )
		) );
		$posts = array_merge( $posts, $_posts );
	}
	if( count( $posts ) < $number ){
		$_posts = get_posts( array(
			'exclude'     => array_merge( $exclude_ID, $posts ),
			'orderby'     => 'rand',
			'fields'      => 'ids',
			'numberposts' => $number - count( $posts )
		) );
		$posts = array_merge( $posts, $_posts );
	}
	return $posts;
}

/**
	*相关文章样式
	*http://www.bgbk.org
*/
function Bing_related_posts_loop(){
?>
	<li <?php post_class( 'span4' ); ?>>
		<div class="panel transparent no-padding related-posts-panel">
			<a href="<?php the_permalink(); ?>" class="thumbnail-link" rel="bookmark" title="<?php the_title_attribute(); ?>">
				<?php echo Bing_thumbnail( 175, 80, false ); ?>
				<div class="excerpt"><?php echo Bing_excerpt( 50 ); ?></div>
			</a>
			<div class="bottom-box">
				<?php
				the_title( '<h4 class="post-title"><a href="' . esc_url( get_permalink() ) . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h4>' );
				Bing_post_meta( array( 'author', 'date-abb' ) );
				?>
			</div>
		</div>
	</li>
<?php
}

/**
	*相关文章刷新
	*http://www.bgbk.org
*/
function Bing_related_posts_refresh(){
	if( empty( $_REQUEST['post_id'] ) || get_post_status( $_REQUEST['post_id'] ) != 'publish' || !$related_posts_ID = Bing_related_posts_ID( $_REQUEST['post_id'] ) ) return;
	$query = new WP_Query( array(
		'nopaging'            => true,
		'post__in'            => $related_posts_ID,
		'orderby'             => 'rand',
		'ignore_sticky_posts' => true
	) );
	while( $query->have_posts() ):
		$query->the_post();
		Bing_related_posts_loop();
	endwhile;
	wp_reset_postdata();
	wp_die();
}
add_action( 'wp_ajax_related_posts_refresh', 'Bing_related_posts_refresh' );
add_action( 'wp_ajax_nopriv_related_posts_refresh', 'Bing_related_posts_refresh' );

//End of page.
