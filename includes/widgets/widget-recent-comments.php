<?php  
/**
	*小工具：评论
	*http://www.bgbk.org
*/
class Bing_widget_recent_comments extends WP_Widget{

	//默认设置
	public $default_instance = array();

	//初始化
	function __construct(){
		parent::__construct(
			'recent_comments',
			THEME_WIDGET_PREFIX . __( '评论', 'Bing' ),
			array( 'description' => __( '根据设置可以显示不同的评论', 'Bing' ) )
		);
		$this->default_instance = array(
			'title'                 => __( '评论', 'Bing' ),
			'number'                => 5,
			'exclude_users'         => array(),
			'exclude_administrator' => false,
			'exclude_posts'         => array(),
			'descending'            => true,
			'date_limit'            => 'unlimited'
		);
	}

	//小工具内容
	function widget( $args, $instance ){
		if( empty( $instance ) ) $instance = $this->default_instance;
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
			if( !empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
			$exclude_users = $instance['exclude_users'];
			if( $instance['exclude_administrator'] ){
				$administrator_ids = array_map( 'absint', get_users( 'fields=ids&role=administrator' ) );
				$exclude_users = array_unique( array_merge( $instance['exclude_users'], $administrator_ids ) );
			}
			$date_query = $instance['date_limit'] == 'unlimited' ? null : array( 'after' => $instance['date_limit'] );
			$comments = get_comments( array(
				'number'         => $instance['number'],
				'author__not_in' => $exclude_users,
				'post__not_in'   => $instance['exclude_posts'],
				'order'          => $instance['descending'] ? 'DESC' : 'ASC',
				'date_query'     => $date_query,
				'status'         => 'approve',
				'type'           => 'comment'
			) );
			if( empty( $comments ) ):
?>
				<div class="empty-recent-comments">
					<p><?php _e( '什么评论都没有，赶紧去发表你的意见吧！' ); ?></p>
				</div>
<?php
			else:
				$show_avatars = get_option( 'show_avatars' ) ? ' show-avatars' : '';
				echo '<ul class="sidebar-comments-list' . $show_avatars . '">';
					foreach( $comments as $comment ):
						$a_title = sprintf( __( '发表在《%s》', 'Bing' ), get_the_title( $comment->comment_post_ID ) );
?>
						<li>
							<a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>" title="<?php echo esc_attr( $a_title ); ?>">
								<?php echo get_avatar( $comment, 36 ); ?>
								<div class="right-box">
									<span class="author"><?php echo get_comment_author( $comment ); ?></span>
									<p class="comment-text"><?php echo htmlspecialchars( get_comment_text( $comment ) ); ?></p>
								</div>
							</a>
						</li>
<?php
					endforeach;
				echo '</ul>';
			endif;
		echo $args['after_widget'];
	}

	//保存设置选项
	function update( $new_instance ){
		$instance = array();
		
		//标题
		$instance['title'] = strip_tags( $new_instance['title'] );

		//评论数量
		$instance['number'] = absint( $new_instance['number'] );
		if( $instance['number'] === 0 ) $instance['number'] = 1;

		//排除用户
		$instance['exclude_users'] = array_map( 'absint', (array) $new_instance['exclude_users'] );
		if( !empty( $instance['exclude_users'] ) ){
			$instance['exclude_users'] = get_users( array(
				'include' => $instance['exclude_users'],
				'fields' => 'ids'
			) );
			$instance['exclude_users'] = array_map( 'absint', $instance['exclude_users'] );
		}

		//排除所有管理员
		$instance['exclude_administrator'] = !empty( $new_instance['exclude_administrator'] );

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

		//倒序排列
		$instance['descending'] = !empty( $new_instance['descending'] );

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

		return $instance;
	}

	//设置表单
	function form( $instance ){
		$instance = wp_parse_args( $instance, $this->default_instance );

		$users = get_users( array(
			'orderby' => 'registered',
			'fields'  => array( 'ID', 'display_name' )
		) );

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
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( '评论数量：', 'Bing' ); ?></label>
			<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo $instance['number']; ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_users' ) ); ?>"><?php _e( '排除用户：', 'Bing' ); ?></label>
			<select class="widefat" multiple="multiple" id="<?php echo esc_attr( $this->get_field_id( 'exclude_users' ) ); ?>[]" name="<?php echo esc_attr( $this->get_field_name( 'exclude_users' ) ); ?>[]">
				<?php foreach( $users as $user ): ?>
					<option value="<?php echo esc_attr( $user->ID ); ?>"<?php if( in_array( $user->ID, $instance['exclude_users'] ) ) echo ' selected="selected"'; ?>><?php echo $user->display_name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_administrator' ) ); ?>"><?php _e( '排除所有管理员：', 'Bing' ); ?></label>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'exclude_administrator' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_administrator' ) ); ?>" value="1"<?php checked( $instance['exclude_administrator'] ); ?> />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_posts' ) ); ?>"><?php _e( '排除文章：', 'Bing' ); ?></label>
			<select class="widefat" multiple="multiple" id="<?php echo esc_attr( $this->get_field_id( 'exclude_posts' ) ); ?>[]" name="<?php echo esc_attr( $this->get_field_name( 'exclude_posts' ) ); ?>[]">
				<?php foreach( get_posts( 'nopaging=1&post_status=publish,future' ) as $post ): ?>
					<option value="<?php echo esc_attr( $post->ID ); ?>"<?php if( in_array( $post->ID, $instance['exclude_posts'] ) ) echo ' selected="selected"'; ?>><?php echo get_the_title( $post ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'descending' ) ); ?>"><?php _e( '倒序排列：', 'Bing' ); ?></label>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'descending' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'descending' ) ); ?>" value="1"<?php checked( $instance['descending'] ); ?> />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'date_limit' ) ); ?>"><?php _e( '日期限制：', 'Bing' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'date_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'date_limit' ) ); ?>">
				<?php foreach( $date_limit as $date_code => $date_title ): ?>
					<option value="<?php echo $date_code; ?>"<?php selected( $instance['date_limit'], $date_code ); ?>><?php echo $date_title; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
<?php
	}

}
register_widget( 'Bing_widget_recent_comments' );

//End of page.
