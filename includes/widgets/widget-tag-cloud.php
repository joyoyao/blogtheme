<?php  
/**
	*小工具：标签云
	*http://www.bgbk.org
*/
class Bing_widget_tag_cloud extends WP_Widget{

	//默认设置
	public $default_instance = array();

	//初始化
	function __construct(){
		parent::__construct(
			'tag_cloud',
			THEME_WIDGET_PREFIX . __( '标签云', 'Bing' ),
			array( 'description' => __( '显示任意分类法的条款', 'Bing' ) )
		);
		$this->default_instance = array(
			'title'      => __( '标签云', 'Bing' ),
			'number'     => 45,
			'taxonomy'   => 'post_tag',
			'orderby'    => 'name',
			'order'      => 'DESC'
		);
	}

	//小工具内容
	function widget( $args, $instance ){
		if( empty( $instance ) ) $instance = $this->default_instance;
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
			if( !empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
			ob_start();
				wp_tag_cloud( array(
					'taxonomy' => $this->_get_current_taxonomy( $instance ),
					'number'   => $instance['number'],
					'unit'     => 'px',
					'smallest' => 12,
					'largest'  => 12,
					'orderby'  => $instance['orderby'],
					'order'    => $instance['order']
				) );
			$tag_cloud_code = ob_get_clean();
			echo empty( $tag_cloud_code ) ? '<p>' . __( '什么标签都没有，赶紧去创建吧！' ) . '</p>' : '<div class="list-box">' . $tag_cloud_code . '</div>';
		echo $args['after_widget'];
	}

	//保存设置选项
	function update( $new_instance ){
		$instance = array();
		
		//标题
		$instance['title'] = strip_tags( $new_instance['title'] );

		//标签数量
		$instance['number'] = absint( $new_instance['number'] );

		//分类法
		$instance['taxonomy'] = $this->_get_current_taxonomy( $new_instance );

		//标签排序
		$all_orderby = array( 'name', 'count' );
		$instance['orderby'] = in_array( $new_instance['orderby'], $all_orderby ) ? $new_instance['orderby'] : 'name';

		//排序规则
		$all_order = array( 'ASC', 'DESC', 'RAND' );
		$instance['order'] = in_array( $new_instance['order'], $all_order ) ? $new_instance['order'] : 'DESC';

		return $instance;
	}

	//设置表单
	function form( $instance ){
		$instance = wp_parse_args( $instance, $this->default_instance );

		$taxonomy = $this->_get_current_taxonomy( $instance );

		$orderby = array(
			'name'   => __( '标签名称', 'Bing' ),
			'count'  => __( '文章数量', 'Bing' )
		);

		$order = array(
			'ASC'   => __( '正序排列', 'Bing' ),
			'DESC'  => __( '倒序排列', 'Bing' ),
			'RAND'  => __( '随机排列', 'Bing' )
		);
?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( '标题：', 'Bing' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p title="<?php esc_attr_e( '设置为 0 则全部显示', 'Bing' ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( '标签数量：', 'Bing' ); ?></label>
			<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo $instance['number']; ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php _e( '分类法：', 'Bing' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>">
				<?php
				foreach( get_taxonomies( array( 'show_tagcloud' => true ), false ) as $tax ):
					if ( empty( $tax->labels->name ) ) continue;
				?>
					<option value="<?php echo esc_attr( $tax->name ); ?>"<?php selected( $taxonomy, $tax->name ); ?>><?php echo $tax->labels->name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php _e( '标签排序：', 'Bing' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
				<?php foreach( $orderby as $orderby_name => $orderby_title ): ?>
					<option value="<?php echo $orderby_name; ?>"<?php selected( $instance['orderby'], $orderby_name ); ?>><?php echo $orderby_title; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php _e( '排序规则：', 'Bing' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<?php foreach( $order as $order_name => $order_title ): ?>
					<option value="<?php echo $order_name; ?>"<?php selected( $instance['order'], $order_name ); ?>><?php echo $order_title; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
<?php
	}

	//获取当前分类法
	function _get_current_taxonomy( $instance ){
		$taxonomy = stripslashes( $instance['taxonomy'] );
		return !empty( $taxonomy ) && taxonomy_exists( $taxonomy ) ? $taxonomy : 'post_tag';
	}

}
register_widget( 'Bing_widget_tag_cloud' );

//End of page.
