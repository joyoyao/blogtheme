<?php  
/**
	*小工具：文章
	*http://www.bgbk.org
*/
class Bing_widget_posts_list extends WP_Widget{

	//默认设置
	public $default_instance = array();

	//初始化
	function __construct(){
		parent::__construct(
			'posts_list',
			THEME_WIDGET_PREFIX . __( '文章', 'Bing' ),
			array( 'description' => __( '根据设置可以显示不同的文章', 'Bing' ) )
		);
		$this->default_instance = array(
			'title'         => __( '文章', 'Bing' ),
			'orderby'       => 'date',
			'descending'    => true,
			'number'        => 5,
			'thumbnail'     => true,
			'date_limit'    => 'unlimited',
			'exclude_posts' => array(),
			'exclude_tax'   => array()
		);
	}

	//小工具内容
	function widget( $args, $instance ){
		if( empty( $instance ) ) $instance = $this->default_instance;
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
			if( !empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
			$query_args = array(
				'ignore_sticky_posts' => true,
				'orderby'             => $instance['orderby'],
				'order'               => $instance['descending'] ? 'DESC' : 'ASC',
				'posts_per_page'      => $instance['number'],
				'post__not_in'        => $instance['exclude_posts']
			);
			if( $query_args['orderby'] == 'views' ){
				$query_args['meta_key'] = 'views';
				$query_args['orderby'] = 'meta_value_num';
			}
			if( $instance['date_limit'] != 'unlimited' ) $query_args['date_query'] = array( 'after' => $instance['date_limit'] );
			if( !empty( $instance['exclude_tax'] ) ){
				$query_args['tax_query'] = array( 'relation' => 'AND' );
				foreach( $instance['exclude_tax'] as $tax => $term ) $query_args['tax_query'][] = array(
					'taxonomy' => $tax,
					'field'    => 'id',
					'terms'    => $term,
					'operator' => 'NOT IN'
				);
			}
			Bing_sidebar_posts_list( $query_args, $instance['thumbnail'] );
		echo $args['after_widget'];
	}

	//保存设置选项
	function update( $new_instance ){
		$instance = array();
		
		//标题
		$instance['title'] = strip_tags( $new_instance['title'] );

		//文章排序
		$all_orderby = array( 'date', 'comment_count', 'views', 'rand' );
		$instance['orderby'] = in_array( $new_instance['orderby'], $all_orderby ) ? $new_instance['orderby'] : 'date';

		//倒序排列
		$instance['descending'] = !empty( $new_instance['descending'] );

		//文章数量
		$instance['number'] = absint( $new_instance['number'] );
		if( $instance['number'] === 0 ) $instance['number'] = 1;

		//显示缩略图
		$instance['thumbnail'] = !empty( $new_instance['thumbnail'] );

		//日期限制
		$all_date_limit = array(
			'unlimited',
			'1 day ago',
			'3 day ago',
			'1 week ago',
			'1 month ago',
			'3 month ago',
			'6 month ago',
			'1 year ago',
			'2 year ago',
			'3 year ago'
		);
		$instance['date_limit'] = in_array( $new_instance['date_limit'], $all_date_limit ) ? $new_instance['date_limit'] : 'unlimited';

		//排除文章
		$instance['exclude_posts'] = array_map( 'absint', (array) $new_instance['exclude_posts'] );
		if( !empty( $instance['exclude_posts'] ) ) $instance['exclude_posts'] = get_posts( array(
			'post__in'               => $instance['exclude_posts'],
			'nopaging'               => true,
			'post_type'              => 'post',
			'post_status'            => array( 'publish', 'future' ),
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		) );

		//排除分类法
		$instance['exclude_tax'] = (array) $new_instance['exclude_tax'];
		foreach( $instance['exclude_tax'] as $tax_name => $tax ){
			if( !taxonomy_exists( $tax_name ) || !is_array( $tax ) || empty( $tax ) ){
				unset( $instance['exclude_tax'][$tax_name] );
				continue;
			}
			$instance['exclude_tax'][$tax_name] = (array) $instance['exclude_tax'][$tax_name];
			foreach( $instance['exclude_tax'][$tax_name] as $key => $term_id ){
				$instance['exclude_tax'][$tax_name][$key] = absint( $term_id );
				if( !term_exists( $instance['exclude_tax'][$tax_name][$key], $tax_name ) ) unset( $instance['exclude_tax'][$tax_name][$key] );
			}
			if( empty( $instance['exclude_tax'][$tax_name] ) ) unset( $instance['exclude_tax'][$tax_name] );
		}

		return $instance;
	}

	//设置表单
	function form( $instance ){
		$instance = wp_parse_args( $instance, $this->default_instance );

		$orderby = array(
			'date'          => __( '发布时间', 'Bing' ),
			'comment_count' => __( '评论数量', 'Bing' ),
			'views'         => __( '浏览次数', 'Bing' ),
			'rand'          => __( '随机排列', 'Bing' )
		);

		$date_limit = array(
			'unlimited'   => __( '无限制', 'Bing' ),
			'1 day ago'   => __( '一天之内', 'Bing' ),
			'3 day ago'   => __( '三天之内', 'Bing' ),
			'1 week ago'  => __( '一周之内', 'Bing' ),
			'1 month ago' => __( '一个月之内', 'Bing' ),
			'3 month ago' => __( '三个月之内', 'Bing' ),
			'6 month ago' => __( '半年之内', 'Bing' ),
			'1 year ago'  => __( '一年之内', 'Bing' ),
			'2 year ago'  => __( '两年之内', 'Bing' ),
			'3 year ago'  => __( '三年之内', 'Bing' )
		);
?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( '标题：', 'Bing' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php _e( '文章排序：', 'Bing' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
				<?php foreach( $orderby as $orderby_name => $orderby_title ): ?>
					<option value="<?php echo $orderby_name; ?>"<?php selected( $instance['orderby'], $orderby_name ); ?>><?php echo $orderby_title; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'descending' ) ); ?>"><?php _e( '倒序排列：', 'Bing' ); ?></label>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'descending' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'descending' ) ); ?>" value="1"<?php checked( $instance['descending'] ); ?> />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( '文章数量：', 'Bing' ); ?></label>
			<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo $instance['number']; ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>"><?php _e( '显示缩略图：', 'Bing' ); ?></label>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' ) ); ?>" value="1"<?php checked( $instance['thumbnail'] ); ?> />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'date_limit' ) ); ?>"><?php _e( '日期限制：', 'Bing' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'date_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'date_limit' ) ); ?>">
				<?php foreach( $date_limit as $date_code => $date_title ): ?>
					<option value="<?php echo $date_code; ?>"<?php selected( $instance['date_limit'], $date_code ); ?>><?php echo $date_title; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_posts' ) ); ?>"><?php _e( '排除文章：', 'Bing' ); ?></label>
			<select class="widefat" multiple="multiple" id="<?php echo esc_attr( $this->get_field_id( 'exclude_posts' ) ); ?>[]" name="<?php echo esc_attr( $this->get_field_name( 'exclude_posts' ) ); ?>[]">
				<?php foreach( get_posts( 'nopaging=1&post_status=publish,future' ) as $post ): ?>
					<option value="<?php echo esc_attr( $post->ID ); ?>"<?php if( in_array( $post->ID, $instance['exclude_posts'] ) ) echo ' selected="selected"'; ?>><?php echo get_the_title( $post ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
		foreach( get_taxonomies( array( 'show_tagcloud' => true ), false ) as $tax_name => $tax ):
			if ( empty( $tax->labels->name ) || !in_array( 'post', $tax->object_type ) ) continue;
			$trems = get_terms( $tax_name, 'hide_empty=0' );
			if( is_wp_error( $trems ) ) continue;
			if( empty( $instance['exclude_tax'][$tax_name] ) ) $instance['exclude_tax'][$tax_name] = array();
		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_tax' ) ) . "[$tax_name]"; ?>"><?php printf( __( '排除%s：', 'Bing' ), $tax->labels->name ); ?></label>
				<select class="widefat" multiple="multiple" id="<?php echo esc_attr( $this->get_field_id( 'exclude_tax' ) ) . "[$tax_name]"; ?>[]" name="<?php echo esc_attr( $this->get_field_name( 'exclude_tax' ) . "[$tax_name]" ); ?>[]">
					<?php foreach( $trems as $trem ): ?>
						<option value="<?php echo esc_attr( $trem->term_id ); ?>"<?php if( in_array( $trem->term_id, $instance['exclude_tax'][$tax_name] ) ) echo ' selected="selected"'; ?>><?php echo $trem->name; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php endforeach; ?>
<?php
	}

}
register_widget( 'Bing_widget_posts_list' );

//End of page.
