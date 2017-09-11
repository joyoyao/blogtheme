<form method="get" class="search-form" action="<?php echo esc_url( home_url() ); ?>">
	<input class="search-text" name="s" autocomplete="off" placeholder="<?php esc_attr_e( '输入关键词搜索...', 'Bing' ); ?>" type="text" value="<?php echo get_search_query(); ?>" required="required" />
	<button class="search-submit" alt="<?php esc_attr_e( '搜索', 'Bing' ); ?>" type="submit"><?php _e( '搜索', 'Bing' ); ?></button>
</form>