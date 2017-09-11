<?php
/**
	*表单选项样式
	*http://www.bgbk.org
*/
class Bing_mpanel_panel{

	//主类对象
	public $mpanel;

	//页面 ID
	public $page = 0;

	//选项组合 ID
	public $group = 0;

	//菜单
	public $menu = array();

	//选项默认值
	public $default = array();

	//获取默认值
	public $get_default = false;

	//表单 Html
	public $from;

	//初始化
	function __construct( $mpanel ){
		$this->mpanel = $mpanel;
	}

	//选项样式
	function item( $args ){
		$args = wp_parse_args( $args, array(
			'type' => 'text',
			'id'   => false,
			'name' => false
		) );
		if( $this->get_default ){
			if( isset( $args['default'] ) ) $this->default[$args['id']] = $args['default'];
			return;
		}
		if( $args['type'] == 'help' ){
			$id = $args['id'] ? ' id="' . 'mpanel-item-' . esc_attr( $args['id'] ) . '"' : '';
			echo '<div class="mpanel-form-table mpanel-form-table-help"' . $id . '>' . $args['name'] . '</div>';
			return;
		}
		$option_value = $this->mpanel->get( $args['id'] );
		if( $args['id'] ) $htmlid = esc_attr( 'mpanel-item-' . $args['id'] );
?>
		<div class="mpanel-form-table mpanel-form-table-<?php echo $args['type']; ?>" id="<?php echo esc_attr( 'mpanel-panel-box-' . $args['id'] ); ?>">
			<?php
			if( !empty( $args['help'] ) ) echo '<div class="mpanel-help" title="' . esc_attr( $args['help'] ) . '"></div>';
			if( $args['name'] && $args['type'] != 'bigtext' ) echo '<span class="mpanel-panel-name">' . $args['name'] . '</span>';
			echo '<div class="mpanel-panel-box">';
				switch( $args['type'] ){
					case 'text':
					case 'number':
					case 'password':
					$placeholder = $args['type'] == 'text' && !empty( $args['placeholder'] ) ? ' placeholder="' . esc_attr( $args['placeholder'] ) . '"' : '';
				?>
						<input name="mpanel[<?php echo $args['id']; ?>]" type="<?php echo esc_attr( $args['type'] ); ?>" id="<?php echo $htmlid; ?>"<?php echo $placeholder; ?> value="<?php echo esc_attr( $option_value ); ?>" />
				<?php
					break;
					case 'slider':
				?>
						<div id="<?php echo $htmlid; ?>-slider"></div>
						<input name="mpanel[<?php echo $args['id']; ?>]" type="number" id="<?php echo $htmlid; ?>" value="<?php echo esc_attr( $option_value ); ?>" />
						<?php if( !empty( $args['unit'] ) ) echo '<span class="unit">' . $args['unit'] . '</span>'; ?>
						<script type="text/javascript">
							jQuery(function($){
								var slider = $('#<?php echo esc_js( $htmlid . '-slider' ); ?>').slider({
									range: 'min',
									<?php if( !empty( $option_value ) ) echo "value: $option_value,"; ?>
									min: <?php echo $args['min']; ?>,
									max: <?php echo $args['max']; ?>,
									slide: function( e, ui ){
										$('#<?php echo esc_js( $htmlid ); ?>').val( ui.value );
									}
								});
								$('#<?php echo esc_js( $htmlid ); ?>').focus(function(){
									$(this).keyup(function(){
										$(this).prev( 'div' ).slider( 'value', $(this).val() );
									});
								});
								$('#<?php echo esc_js( $htmlid ); ?>').change(function(){
									$(this).prev( 'div' ).slider( 'value', $(this).val() );
								});
							});
						</script>
				<?php
					break;
					case 'upload':
				?>
						<div class="input-group">
							<input class="text" name="mpanel[<?php echo $args['id']; ?>]" type="text" id="<?php echo $htmlid; ?>" value="<?php echo esc_attr( $option_value ); ?>" />
							<span class="input-group-btn"><button class="mpanel-upload-button btn" data-value="<?php echo $htmlid; ?>"><?php _e( '上传', 'Bing' ); ?></button></span>
						</div>
						<?php if( !empty( $option_value ) ) echo '<div class="upload-img-box"><img src="' . esc_url( $option_value ) . '" /><span class="delete"></span></div>'; ?>
				<?php
					break;
					case 'checkbox':
						$checked = $option_value ? ' checked="checked"' : '';
				?>
						<input class="mpanel-checkbox" name="mpanel[<?php echo $args['id']; ?>]" type="<?php echo esc_attr( $args['type'] ); ?>" id="<?php echo $htmlid; ?>" value="1"<?php echo $checked; ?> />
				<?php
						foreach( array( 'show', 'hide', 'shows', 'hides' ) as $type ):
							if( empty( $args[$type] ) ) continue;
							$args[$type] = (array) $args[$type];
							$i = count( $args[$type] );
							$controlhtmlid = '';
							$id_before = $type == 'show' || $type == 'hide' ? '#mpanel-panel-box-' : '.mpnael-group.';
							foreach( $args[$type] as $id_after ){
								$controlhtmlid .= $id_before . $id_after;
								if( $i !== 1 ) $controlhtmlid .= ',';
								--$i;
							}
				?>
							<script type="text/javascript">
								jQuery(function($){
									$('<?php echo esc_js( '#' . $htmlid ); ?>').checkbox_<?php echo $type == 'show' || $type == 'shows' ? 'show' : 'hide'; ?>( '<?php echo esc_js( $controlhtmlid ); ?>' );
								});
							</script>
				<?php
						endforeach;
					break;
					case 'select':
					case 'multiple':
						if( !$args['option'] ) return;
						if( $args['type'] == 'multiple' ){
							$multiple = ' multiple="multiple"';
							$multiplearray = '[]';
						}else{
							$multiple = '';
							$multiplearray = '';					
						}
				?>
						<select name="mpanel[<?php echo $args['id']; ?>]<?php echo $multiplearray; ?>"<?php echo $multiple; ?> id="<?php echo $htmlid; ?>">
							<?php
							$multipletype = $args['type'] == 'multiple';
							$selecttype = $args['type'] == 'select';
							foreach( $args['option'] as $key => $value ){
								$selected = ( is_array( $option_value ) && in_array( $key, $option_value ) && $multipletype ) || ( $option_value === $key && $selecttype ) ? ' selected="selected"' : '';
								echo '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . $value . '</option>';
								$selected = '';
							}
							?>
						</select>
				<?php
					break;
					case 'textarea':
						$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
				?>
						<textarea id="<?php echo $htmlid; ?>" name="mpanel[<?php echo $args['id']; ?>]"<?php echo $placeholder; ?>><?php echo $option_value; ?></textarea>
				<?php
					break;
					case 'color':
				?>
						<input class="colorSelector" name="mpanel[<?php echo $args['id']; ?>]" type="text" id="<?php echo $htmlid; ?>" maxlength="6" value="<?php echo esc_attr( $option_value ); ?>" />
				<?php
					break;
					case 'bigtext':
						$readonly = empty( $args['readonly'] ) ? '' : ' readonly="readonly"';
						if( $args['id'] == 'export' ){
							$readonly = ' readonly="readonly"';
							$option_value = base64_encode( maybe_serialize( get_option( $this->mpanel->name ) ) );
						}
						$bigname = $args['id'] == 'export' || $args['id'] == 'import' ? 'mpanel-' . $args['id'] : 'mpanel[' . $args['id'] . ']';
						$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
				?>
						<textarea id="<?php echo $htmlid; ?>" name="<?php echo $bigname; ?>"<?php echo $readonly . $placeholder; ?>><?php echo $option_value; ?></textarea>
				<?php
					break;
					case 'list':
						if( !isset( $args['repeat'] ) ) $args['repeat'] = true;
				?>
						<div id="<?php echo $htmlid; ?>" class="custom-list">
							<input name="mpanel-list-enter" class="mpanel-list-enter" type="text" id="<?php echo $htmlid; ?>" />
							<a href="javascript:;" class="mpanel-list-add" data-list-name="<?php echo esc_attr( $args['id'] ); ?>" data-repeat=<?php echo esc_attr( $args['repeat'] ); ?>><?php _e( '添加', 'Bing' ); ?></a>
							<ul class="mpanel-list">
								<?php
								if( !empty( $option_value ) ):
									foreach( (array) $option_value as $name ):
								?>
										<li>
											<input type="hidden" name="mpanel[<?php echo $args['id']; ?>][]" class="mpanel-list-hidden-content" value="<?php echo esc_attr( $name ); ?>">
											<span class="mpanel-list-li-name"><?php echo $name; ?></span>
											<a href="javascript:;" class="mpanel-list-li-delete"></a>
										</li>
								<?php
									endforeach;
								endif;
								?>
							</ul>
						</div>
				<?php
					break;
					case 'date':
						$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
				?>
						<input class="mpanel-cxcalendar" name="mpanel[<?php echo $args['id']; ?>]" type="text" id="<?php echo $htmlid; ?>"<?php echo $placeholder; ?> value="<?php echo esc_attr( $option_value ); ?>" />
				<?php
					break;
					case 'radio':
						echo '<div class="mpanel-radio-box">';
							$args_id = esc_attr( $args['id'] );
							$args_type = esc_attr( $args['type'] );
							foreach( $args['option'] as $key => $name ){
								$id = esc_attr( $htmlid . '-' . $key );
								printf( '<label for="%s"><input id="%s" name="mpanel[%s]" type="%s" value="%s"%s />%s</label>', $id, $id, $args_id, $args_type, esc_attr( $key ), $option_value == $key ? ' checked="checked"' : '', $name );
							}
						echo '</div>';
						foreach( array( 'show', 'hide', 'shows', 'hides' ) as $type ):
							if( empty( $args[$type] ) ) continue;
							$control_keys = array_intersect_key( $args[$type], $args['option'] );
							$id_before = $type == 'show' || $type == 'hide' ? '#mpanel-panel-box-' : '.mpnael-group.';
							foreach( $control_keys as $control_key => $control_value ){
								if( empty( $control_value ) ) continue;
								$i = count( $control_value );
								$controlhtmlid = '';
								foreach( (array) $control_value as $id_after ){
									$controlhtmlid .= $id_before . $id_after;
									if( $i !== 1 ) $controlhtmlid .= ',';
									--$i;
								}
								?>
								<script type="text/javascript">
									jQuery(function($){
										$('#<?php echo esc_js( $htmlid . '-' . $control_key ); ?>').radio_<?php echo $type == 'show' || $type == 'shows' ? 'show' : 'hide'; ?>( '<?php echo esc_js( $controlhtmlid ); ?>' );
									});
								</script>
								<?php
							}
						endforeach;
					break;
				}
			echo '</div>';
			if( !empty( $args['explain'] ) ) echo '<p class="mpanel-explain">' . $args['explain'] . '</p>';
			?>
		</div>
<?php
	}

	//初始化
	function init(){
		$this->menu = $this->default = array();
		$this->page = $this->group = 0;
		$this->get_default = false;
		unset( $this->from );
	}

	//页面开始
	function before( $name, $class = '' ){
		$this->menu[] = $name;
		++$this->page;
		$classes = empty( $class ) ? '' : ' ' . $class;
?>
		<section id="mpanel-page-<?php echo $this->page; ?>" class="mpanel-page<?php echo esc_attr( $classes ); ?>">
			<h2 class="title"><?php echo $name; ?></h2>
			<div class="mpanel-page-box">
<?php
	}

	//页面结束
	function after(){
		echo '</div></section>';
	}

	//选项组合开始
	function group( $title, $class = '' ){
		++$this->group;
		$classes = empty( $class ) ? '' : ' ' . $class;
?>
		<div class="mpnael-group<?php echo esc_attr( $classes ); ?>" id="mpnael-group-id-<?php echo $this->group; ?>">
			<h3 class="title"><?php echo $title; ?></h3>
			<div class="mpnael-group-box">
<?php
	}

	//选项组合结束
	function end(){
		echo '</div></div>';
	}

	//选项界面
	function get_from( $reset = false ){
		if( !isset( $this->from ) || $reset ){
			global $mpanel;
			$categories = array();
			foreach( get_categories( 'hide_empty=0' ) as $cat ) $categories[$cat->cat_ID] = $cat->cat_name;
			ob_start();
				include( dirname( __FILE__ ) . '/mpanel-ui.php' );
			$this->from = ob_get_clean();
		}
		return $this->from;
	}

	//获取默认设置
	function get_default(){
		$this->get_default = true;
		$this->get_from( true );
		$default = $this->default;
		$this->init();
		return $default;
	}

}

//End of page.
