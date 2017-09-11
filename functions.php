<?php
/**
	*@author admin@bgbk.org
	*@link http://www.bgbk.org
*/

//Config
require( get_template_directory() . '/theme-config.php' );

//Option
require( get_template_directory() . '/mpanel/mpanel-functions.php' );
require( get_template_directory() . '/mpanel/mpanel-panel.php' );

//Functions
require( get_template_directory() . '/functions/theme-functions.php' );
require( get_template_directory() . '/functions/update.php' );
require( get_template_directory() . '/functions/nav-menu.php' );
require( get_template_directory() . '/functions/statistics.php' );
require( get_template_directory() . '/functions/thumbnail.php' );
require( get_template_directory() . '/functions/widgets.php' );
require( get_template_directory() . '/functions/comment.php' );
require( get_template_directory() . '/functions/style.php' );
require( get_template_directory() . '/functions/ajax-load.php' );
require( get_template_directory() . '/functions/breadcrumbs.php' );
require( get_template_directory() . '/functions/page-navi.php' );

//Languages
@include( get_template_directory() . '/languages/' . get_locale() . '.php' );

//Content
include( get_template_directory() . '/includes/posts-list.php' );
include( get_template_directory() . '/includes/archive-header.php' );
include( get_template_directory() . '/includes/related-posts.php' );
include( get_template_directory() . '/includes/comment.php' );
include( get_template_directory() . '/includes/mobile.php' );
include( get_template_directory() . '/includes/banner.php' );

//End of page.
