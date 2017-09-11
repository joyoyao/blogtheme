<aside id="sidebar" itemscope itemtype="http://schema.org/WPSideBar">
	<ul class="row">
		<?php if( !dynamic_sidebar( Bing_current_sidebar() ) ): ?>
			<li class="span12 empty-sidebar">
				<div class="panel">
					<header class="panel-header">
						<h3 class="empty-sidebar-title"><?php echo $GLOBALS['wp_registered_sidebars'][Bing_current_sidebar()]['name']; ?></h3>
					</header>
					<p class="warning"><?php _e( '该边栏还没有设置任何小工具', 'Bing' ); ?></p>
					<?php if( current_user_can( 'edit_theme_options' ) ): ?>
						<p class="set-widget">
							<a href="<?php echo esc_url( admin_url( 'widgets.php#' . Bing_current_sidebar() ) ); ?>"><?php _e( '点此设置' ); ?></a>
						</p>
					<?php endif; ?>
				</div>
			</li>
		<?php endif; ?>
	</ul>
</aside>