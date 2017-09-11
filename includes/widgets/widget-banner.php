<?php  
/**
	*小工具：广告
	*http://www.bgbk.org
*/
class Bing_widget_banner extends WP_Widget{

	//默认设置
	public $default_instance = array();

	//初始化
	function __construct(){
		parent::__construct(
			'banner',
			THEME_WIDGET_PREFIX . __( '广告', 'Bing' ),
			array( 'description' => __( '自定义广告代码', 'Bing' ) )
		);
		$this->default_instance = array(
			'title' => __( '广告', 'Bing' ),
			'code'  => ''
		);
	}

	//小工具内容
	function widget( $args, $instance ){
		if( empty( $instance ) ) $instance = $this->default_instance;
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
			if( !empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
			echo $instance['code'];
		echo $args['after_widget'];
	}

	//保存设置选项
	function update( $new_instance ){
		$instance = array();
		
		//标题
		$instance['title'] = strip_tags( $new_instance['title'] );

		//广告代码
		$instance['code'] = force_balance_tags( $new_instance['code'] );
		$instance['code'] = do_shortcode( $instance['code'] );

		return $instance;
	}

	//设置表单
	function form( $instance ){
		$instance = wp_parse_args( $instance, $this->default_instance );
?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( '标题：', 'Bing' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'code' ) ); ?>"><?php _e( '广告代码：', 'Bing' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'code' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'code' ) ); ?>" rows="10"><?php echo esc_attr( $instance['code'] ); ?></textarea>
		</p>
		<?php if( !empty( $instance['code'] ) ): ?>
			<p><?php _e( '广告预览：', 'Bing' ); ?></p>
			<div style="margin: 13px 0; overflow: hidden;">
				<?php echo $instance['code']; ?>
				<div class="clear: both;"></div>
			</div>
		<?php endif; ?>
<?php
	}

}
register_widget( 'Bing_widget_banner' );

//End of page.
