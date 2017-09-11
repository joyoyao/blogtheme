<?php
/**
	*获取广告
	*http://www.bgbk.org
*/
function Bing_banner( $banner ){
	if( ( $hide_date = Bing_mpanel( $banner . '_hide_date' ) ) && strtotime( $hide_date ) > current_time( 'timestamp' ) ) return;
	if( Bing_mpanel( $banner . '_type' ) == 'code' ) return Bing_mpanel( $banner . '_code' );
	$url = esc_url( Bing_mpanel( $banner . '_img' ) );
	$alt = esc_attr( Bing_mpanel( $banner . '_alt' ) );
	$code = "<img src='$url' title='$alt' alt='$alt' />";
	if( $url = Bing_mpanel( $banner . '_url' ) ){
		$url = esc_url( $url );
		$tab = Bing_mpanel( $banner . '_tab' ) ? ' target="_blank"' : '';
		$code = "<a href='$url'$tab>$code</a>";
	}
	return $code;
}

/**
	*获取宽屏广告
	*http://www.bgbk.org
*/
function Bing_banner_span12( $banner ){
	$banner_id = 'banner_' . $banner;
	if( !( $code = Bing_banner( $banner_id ) ) || !Bing_mpanel( $banner_id . '_show' ) ) return;
	$classes = array(
		'span12',
		'banner',
		'banner-type-' . Bing_mpanel( $banner_id . '_type' ),
		$banner_id
	);
	if( Bing_mpanel( 'responsive' ) ){
		$client = (array) Bing_mpanel( $banner_id . '_client' );
		if( !in_array( 'pc', $client ) ) $classes[] = 'pc-hide';
		if( !in_array( 'mobile', $client ) ) $classes[] = 'mobile-hide';
	}
?>
	<div class="row">
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="panel no-padding"><?php echo $code; ?></div>
		</div>
	</div>
<?php
}

/**
	*获取文章底部
	*http://www.bgbk.org
*/
function Bing_banner_post_bottom(){
	$banner_id = 'banner_post_bottom';
	if( !( $code = Bing_banner( $banner_id ) ) || !Bing_mpanel( $banner_id . '_show' ) ) return;
	$classes = array(
		'span12',
		'banner',
		'banner-type-' . Bing_mpanel( $banner_id . '_type' ),
		$banner_id
	);
	if( Bing_mpanel( 'responsive' ) ){
		$client = (array) Bing_mpanel( $banner_id . '_client' );
		if( !in_array( 'pc', $client ) ) $classes[] = 'pc-hide';
		if( !in_array( 'mobile', $client ) ) $classes[] = 'mobile-hide';
	}
?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<div class="panel no-padding"><?php echo $code; ?></div>
	</div>
<?php
}

//End of page.
