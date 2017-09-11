<?php
/**
	*设置选项
	*http://www.bgbk.org
*/
class Bing_mpanel{

	//验证随机数 ID
	public $nonce;

	//表单选项样式
	public $panel;

	//数据库字段名
	public $name;

	//初始化
	function __construct(){
		$this->name = THEME_MPANEL_NAME;
		$this->panel = new Bing_mpanel_panel( $this );
		$this->nonce = THEME_SLUG . '_mpanel_nonce';
		if( get_option( $this->name ) === false ) $this->restore();
		add_action( 'mpanel_content', array( $this, 'save' ) );
		add_action( 'wp_ajax_mpanel-save', array( $this, 'save' ) );
		add_action( 'mpanel_content', array( $this, 'register_scripts' ), 9 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 100 );
		add_filter( 'gettext', array( $this, 'update_content_help' ), 16, 3 );
	}

	//初始化设置
	function restore(){
		update_option( $this->name, $this->panel->get_default() );
	}

	//保存设置
	function save(){
		if( !(
				( isset( $_GET['page'] ) && $_GET['page'] == 'mpanel' ) ||
				( isset( $_POST['action'] ) && $_POST['action'] == 'mpanel-save' )
			) ||
			!current_user_can( 'edit_theme_options' ) ||
			!isset( $_POST['mpanel_noncename'] ) ||
			!wp_verify_nonce( $_POST['mpanel_noncename'], $this->nonce )
		) return;

		if( !empty( $_POST['mpanel-restore-settings'] ) ){
			$this->restore();
			return;
		}

		if( !empty( $_POST['mpanel-import'] ) ){
			$import = maybe_unserialize( base64_decode( $_POST['mpanel-import'] ) );
			if( !empty( $import ) ){
				update_option( $this->name, $import );
				return;
			}
		}

		if( isset( $_POST['mpanel'] ) ){
			$content = $_POST['mpanel'];
			unset( $content['mpanel-export'] );
			update_option( $this->name, $content );
		}

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) wp_die( 1 );
	}

	//创建页面
	function admin_menu(){
		add_menu_page(
			sprintf( __( '%s 设置', 'Bing' ), THEME_NAME ),
			THEME_NAME,
			'edit_theme_options',
			'mpanel',
			array( $this, 'ui' ),
			'dashicons-awards'
		);
		add_submenu_page(
			'mpanel',
			sprintf( __( '%s 设置', 'Bing' ), THEME_NAME ),
			__( '设置', 'Bing' ),
			'edit_theme_options',
			'mpanel',
			array( $this, 'ui' )
		);
		add_submenu_page(
			'mpanel',
			sprintf( __( '%s 使用帮助', 'Bing' ), THEME_NAME ),
			__( '使用帮助', 'Bing' ),
			'edit_theme_options',
			'mpanel-help',
			array( $this, 'help' )
		);
		if( Bing_update_content() ) add_submenu_page(
			'mpanel',
			sprintf( __( '%s 更新内容', 'Bing' ), THEME_NAME ),
			__( '更新', 'Bing' ),
			'update_themes',
			'mpanel-update',
			array( $this, 'update_content' )
		);
	}

	//在 Admin Bar 创建菜单
	function admin_bar( $wp_admin_bar ){
		$actions = array();

		if( current_user_can( 'edit_theme_options' ) ){
			$actions['admin.php?page=mpanel'] = array( 'id' => 'options', 'title' => __( '设置', 'Bing' ) );
			$actions['admin.php?page=mpanel-help'] = array( 'id' => 'help', 'title' => __( '使用帮助', 'Bing' ) );
		}

		if( current_user_can( 'update_themes' ) && Bing_update_content() ) $actions['admin.php?page=mpanel-update'] = array( 'id' => 'update', 'title' => __( '更新', 'Bing' ) );

		if( empty( $actions ) ) return;
		$wp_admin_bar->add_menu( array(
			'id'    => 'mpanel',
			'title' => THEME_NAME,
			'href'  => current( array_keys( $actions ) )
		));

		foreach( $actions as $link => $action ) $wp_admin_bar->add_menu( array(
			'parent' => 'mpanel',
			'id'     => 'mpanel-' . $action['id'],
			'title'  => $action['title'],
			'href'   => admin_url( $link )
		) );
	}

	//挂载脚本
	function register_scripts(){
		$url = get_template_directory_uri() . '/mpanel';

		//Switchery
		wp_register_style( 'mpanel-switchery', $url . '/css/switchery.css' );
		wp_register_script( 'mpanel-switchery', $url . '/js/switchery.js' );

		//jQuery UI
		wp_register_style( 'mpanel-jquery-ui', $url . '/css/jquery-ui-1.9.2.custom.css' );
		wp_register_script( 'mpanel-jquery-ui', $url . '/js/jquery-ui-1.9.2.custom.js' );

		//cxCalendar
		wp_register_style( 'mpanel-cxcalendar', $url . '/css/cxcalendar.css', array( 'mpanel-jquery-ui' ) );
		wp_register_script( 'mpanel-cxcalendar', $url . '/js/cxcalendar.js', array( 'mpanel-jquery-ui' ) );

		//Style
		wp_enqueue_style( 'mpanel-style', $url . '/css/style.css', array( 'wp-color-picker', 'mpanel-switchery', 'mpanel-cxcalendar' ), THEME_VERSION );

		//Base
		wp_enqueue_script( 'mpanel-base', $url . '/js/base.js', array( 'jquery', 'wp-color-picker', 'mpanel-switchery', 'mpanel-cxcalendar' ), THEME_VERSION, true );
		wp_localize_script( 'mpanel-base', 'mpanel_base_args', array( 'admin_ajax' => admin_url( 'admin-ajax.php' ), 'list_repeat_error' => __( '列表不能重复', 'Bing' ) ) );

		//Media
		wp_enqueue_media();
	}

	//获取设置
	function get( $option ){
		$content = get_option( $this->name );
		if( empty( $option ) || !isset( $content[$option] ) ) return false;
		return wp_unslash( $content[$option] );
	}

	//更新设置
	function update( $option, $value ){
		if( empty( $option ) ) return false;
		$content = get_option( $this->name );
		$content[$option] = $value;
		return update_option( $this->name, $content );
	}

	//删除设置
	function delete( $option ){
		if( empty( $option ) ) return false;
		$content = get_option( $this->name );
		if( !isset( $content[$option] ) ) return false;
		unset( $content[$option] );
		return update_option( $this->name, $content );
	}

	//设置界面
	function ui(){
		do_action( 'mpanel_content' );
		$panel = $this->panel->get_from();
?>
		<div id="mpanel-wrap">
			<form method="post" action="" id="mpanel-form">
				<div class="mpanel-menu-box">
					<h1 class="mpanel-logo"></h1>
					<ul class="mpanel-menu">
						<?php
						$i = 0;
						foreach( $this->panel->menu as $name ){
							++$i;
							echo "<li class='list-$i'>$name</li>";
						}
						?>
					</ul>
				</div>
				<?php wp_nonce_field( $this->nonce, 'mpanel_noncename' ); ?>
				<input name="mpanel-save" type="submit" class="mpanel-submit mpanel-save" value="<?php esc_attr_e( '保存设置', 'Bing' ); ?>" />
				<div class="mpanel-mian-panel"><?php echo $panel; ?></div>
				<input name="mpanel-save" type="submit" class="mpanel-submit mpanel-save2" value="<?php esc_attr_e( '保存设置', 'Bing' ); ?>" />
			</form>
			<form method="post" action="" id="mpanel-form-restore-settings">
				<input type="submit" name="mpanel-restore-settings" title="<?php esc_attr_e( '恢复初始设置', 'Bing' ); ?>" onclick="return confirm( '<?php echo esc_js( __( '所有目前的设置都会被恢复到初始状态，无法恢复。你确定？', 'Bing' ) ); ?>' ) ? true : false;" value="<?php esc_attr_e( '恢复初始设置', 'Bing' ); ?>" class="mpanel-submit mpanel-restore-settings">
				<?php wp_nonce_field( $this->nonce, 'mpanel_noncename' ); ?>
			</form>
		</div>
		<div id="mpanel-load"></div>
<?php
	}

	//使用帮助
	function help(){
?>
		<div class="wrap">
			<h1><?php printf( __( '%s 使用帮助', 'Bing' ), THEME_NAME ); ?></h1>
			<p><?php _e( '使用帮助正在制作中...', 'Bing' ); ?></p>
			<p><?php printf( __( '临时帮助：%s' ), '<a href="http://www.bgbk.org/wp-theme-beginning-help/" target="_blank">http://www.bgbk.org/wp-theme-beginning-help/</a>' ); ?></p>
		</div>
<?php
	}

	//更新内容
	function update_content(){
?>
		<div class="wrap">
			<h1><?php printf( __( '%s 更新内容', 'Bing' ), THEME_NAME ); ?></h1>
			<?php echo Bing_update_content(); ?>
		</div>
<?php
	}

	//更新页面添加更新内容查看提示
	function update_content_help( $translations, $text, $domain ){
		if( is_admin() && $domain == 'default' && $text == 'The following themes have new versions available. Check the ones you want to update and then click &#8220;Update Themes&#8221;.' && Bing_update_content() ){
			remove_filter( 'gettext', array( $this, 'update_content_help' ), 16 );
				$help = sprintf( __( '你可以<a href="%s">点击此处</a>来查看 %s 主题的更新内容。' ), admin_url( 'admin.php?page=mpanel-update' ), THEME_NAME );
			add_filter( 'gettext', array( $this, 'update_content_help' ), 16, 3 );
			$translations = $translations . '</p><p>' . $help;
		}
		return $translations;
	}

}

//End of page.
