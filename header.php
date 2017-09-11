<?php if( Bing_is_ajax_load_page() ) return; ?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php echo esc_attr( get_bloginfo( 'pingback_url' ) ); ?>" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div id="wrapper">
			<header id="header">
				<div class="box-md">
					<h1 class="logo">
						<a href="<?php echo esc_url( home_url() ) ?>" title="<?php echo esc_attr( sprintf( '%s | %s', get_bloginfo( 'name', 'display' ), get_bloginfo( 'description', 'display' ) ) ); ?>"><?php bloginfo( 'name' ); ?></a>
					</h1>
					<nav class="menu header-menu-box" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
						<?php echo Bing_nav_menu( 'header_menu' ); ?>
					</nav>
					<ul class="control">
						<li class="previous">
							<a class="dashicons dashicons-arrow-left-alt" href="javascript:history.go( -1 );" title="<?php esc_attr_e( '后退', 'Bing' ); ?>"></a>
						</li>
						<li class="next">
							<a class="dashicons dashicons-arrow-right-alt" href="javascript:history.go( 1 );" title="<?php esc_attr_e( '前进', 'Bing' ); ?>"></a>
						</li>
						<li class="refresh">
							<a class="dashicons dashicons-image-rotate" href="javascript:location.reload();" title="<?php esc_attr_e( '刷新', 'Bing' ); ?>"></a>
						</li>
					</ul>
					<?php get_search_form(); ?>
				</div>
				<?php Bing_mobile_header(); ?>
			</header>
			<div class="box-md">
				<?php Bing_banner_span12( 'header' ); ?>
				<div class="wrapper-table-box">
