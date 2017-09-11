<?php
/**
	*AJAX 提交评论
	*http://www.bgbk.org
*/
function Bing_ajax_comment(){
	if( !Bing_mpanel( 'ajax_comment' ) ) return;

	global $comment_post_ID, $post, $status, $status_obj, $comment_author, $comment_author_email, $comment_author_url, $comment_content, $user, $comment_type, $user_ID, $comment_parent, $commentdata, $comment_id, $comment;

	add_filter( 'wp_die_ajax_handler', 'Bing_add_ajax_comment_error', 16 );
	add_action( 'comment_flood_trigger', 'Bing_ajax_comment_flood_trigger', 16 );
	add_action( 'comment_duplicate_trigger', 'Bing_ajax_comment_duplicate_trigger', 16 );

	$comment_post_ID = isset( $_POST['comment_post_ID'] ) ? (int) $_POST['comment_post_ID'] : 0;

	$post = get_post( $comment_post_ID );

	$invalid_text = __( '检测为非法提交的评论', 'Bing' );

	if ( empty( $post->comment_status ) ) {
		do_action( 'comment_id_not_found', $comment_post_ID );
		wp_die( $invalid_text );
	}

	$status = get_post_status( $post );
	$status_obj = get_post_status_object( $status );

	if( !comments_open( $comment_post_ID ) ){
		do_action( 'comment_closed', $comment_post_ID );
		wp_die( __( '抱歉，该文章无法评论', 'Bing' ) );
	}elseif( 'trash' == $status ){
		do_action( 'comment_on_trash', $comment_post_ID );
		wp_die( $invalid_text );
	}elseif( !$status_obj->public && !$status_obj->private ){
		do_action( 'comment_on_draft', $comment_post_ID );
		wp_die( $invalid_text );
	}elseif( post_password_required( $comment_post_ID ) ) {
		do_action( 'comment_on_password_protected', $comment_post_ID );
		wp_die( __( '抱歉，输入密码之后才能评论', 'Bing' ) );
	}else do_action( 'pre_comment_on_post', $comment_post_ID );

	$comment_author       = ( isset( $_POST['author'] ) )  ? trim( strip_tags( $_POST['author'] ) ) : null;
	$comment_author_email = ( isset( $_POST['email'] ) )   ? trim( $_POST['email'] )                : null;
	$comment_author_url   = ( isset( $_POST['url'] ) )     ? trim( $_POST['url'] )                  : null;
	$comment_content      = ( isset( $_POST['comment'] ) ) ? trim( $_POST['comment'] )              : null;

	$user = wp_get_current_user();
	if( $user->exists() ){
		if( empty( $user->display_name ) ) $user->display_name = $user->user_login;
		$comment_author       = wp_slash( $user->display_name );
		$comment_author_email = wp_slash( $user->user_email );
		$comment_author_url   = wp_slash( $user->user_url );
		if( current_user_can( 'unfiltered_html' ) && !( isset( $_POST['_wp_unfiltered_html_comment'] ) && wp_verify_nonce( $_POST['_wp_unfiltered_html_comment'], 'unfiltered-html-comment_' . $comment_post_ID ) ) ){
			kses_remove_filters();
			kses_init_filters();
		}
	}elseif( get_option( 'comment_registration' ) || 'private' == $status ) wp_die( __( '抱歉，您必须登录才能评论' ) );

	$comment_type = '';

	if( get_option( 'require_name_email' ) && !$user->exists() ){
		if( 6 > strlen( $comment_author_email ) || '' == $comment_author ) wp_die( __( '请填写正确的昵称和邮箱' ) );
		elseif( ! is_email( $comment_author_email ) ) wp_die( __( '请使用一个有效的邮箱' ) );
	}

	$user = wp_get_current_user();
	if( $user->exists() ){
		if( empty( $user->display_name ) ) $user->display_name = $user->user_login;
		$comment_author = esc_sql( $user->display_name );
		$comment_author_email = esc_sql( $user->user_email );
		$comment_author_url = esc_sql( $user->user_url );
		$user_ID = esc_sql( $user->ID );
		if( current_user_can( 'unfiltered_html' ) ){
			if( wp_create_nonce( 'unfiltered-html-comment_' . $comment_post_ID ) != $_POST['_wp_unfiltered_html_comment'] ){
				kses_remove_filters();
				kses_init_filters();
			}
		}
	}elseif( get_option( 'comment_registration' ) || $status == 'private' ) wp_die( __( '您必须登录才能发表评论', 'Bing' ) );

	if( get_option( 'require_name_email' ) && !$user->exists() ){
		if( strlen( $comment_author_email ) < 6 || !$comment_author ) wp_die( __( '请填写正确的昵称和邮箱', 'Bing' ) );
        elseif( !is_email( $comment_author_email ) ) wp_die( __( '请使用一个有效的邮箱', 'Bing' ) );
	}

	if( empty( $comment_content ) ) wp_die( __( '评论不能为空', 'Bing' ) );

	$comment_parent = isset( $_POST['comment_parent'] ) ? absint( $_POST['comment_parent'] ) : 0;

	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

	if( !$comment_id = wp_new_comment( $commentdata ) ) wp_die( __( '无法保存评论，请稍后再试', 'Bing' ) );

	$comment = get_comment( $comment_id );

	do_action( 'set_comment_cookies', $comment, $user );

	Bing_comments_list_loop( $comment );
	Bing_comments_list_loop_end();

	remove_filter( 'wp_die_ajax_handler', 'Bing_add_ajax_comment_error', 16 );
	remove_action( 'comment_flood_trigger', 'Bing_ajax_comment_flood_trigger', 16 );
	remove_action( 'comment_duplicate_trigger', 'Bing_ajax_comment_duplicate_trigger', 16 );

	wp_die();
}
add_action( 'wp_ajax_submit_comment', 'Bing_ajax_comment' );
add_action( 'wp_ajax_nopriv_submit_comment', 'Bing_ajax_comment' );

/**
	*AJAX 添加评论报错改造
	*http://www.bgbk.org
*/
function Bing_add_ajax_comment_error(){
	return 'Bing_ajax_comment_error';
}

/**
	*AJAX 评论报错改造
	*http://www.bgbk.org
*/
function Bing_ajax_comment_error( $message ){
	header( 'HTTP/1.0 500 Internal Server Error' );
	header( 'Content-Type: text/plain;charset=UTF-8' );
	die( $message );
}

/**
	*AJAX 评论报错频率过快改造
	*http://www.bgbk.org
*/
function Bing_ajax_comment_flood_trigger(){
	wp_die( __( '您评论的速度太快了，休息一下吧', 'Bing' ) );
}

/**
	*AJAX 评论报错内容重复改造
	*http://www.bgbk.org
*/
function Bing_ajax_comment_duplicate_trigger(){
	wp_die( __( '您好像已经发表过这篇评论了', 'Bing' ) );
}

/**
	*把评论中的代码转换成实体字符
	*http://www.bgbk.org
*/
function Bing_comment_code_htmlspecialchars( $comment ){
	$comment['comment_content'] = preg_replace_callback( '|<pre.*>(.*)</pre|isU', 'Bing_comment_code_htmlspecialchars_callback', $comment['comment_content'] );
	$comment['comment_content'] = preg_replace_callback( '|<code.*>(.*)</code|isU', 'Bing_comment_code_htmlspecialchars_callback', $comment['comment_content'] );
	return $comment;
}
add_filter( 'preprocess_comment', 'Bing_comment_code_htmlspecialchars', 4 );

/**
	*把评论中的代码转换成实体字符（回调函数）
	*http://www.bgbk.org
*/
function Bing_comment_code_htmlspecialchars_callback( $matches ){
	return str_replace( $matches[1], htmlspecialchars( $matches[1] ), $matches[0] );
}

/**
	*回复的评论加 @
	*http://www.bgbk.org
*/
function Bing_comment_add_at( $comment_text, $comment = null ){
	return Bing_mpanel( 'comment_add_at' ) && isset( $comment->comment_parent ) && $comment->comment_parent > 0 ? '<a class="comment_at" href="' . esc_url( get_comment_link( $comment->comment_parent ) ) . '">@' . get_comment_author( $comment->comment_parent ) . '</a> ' . $comment_text : $comment_text;
}
add_filter( 'comment_text' , 'Bing_comment_add_at', 18, 2 );

/**
	*WordPress 代码阻止绝大多数垃圾评论
	*http://www.endskin.com/anti-spam/
*/
class Bing_anti_spam{

	//垃圾评论判断
	public $spam = false;

	//实例化
	static function init(){
		if( Bing_mpanel( 'comment_anti' ) && !is_user_logged_in() ) new self;
	}

	//初始化
	function __construct(){
		$this->check();
		add_filter( 'comment_form_field_comment', array( $this, 'change_form' ), 16 );
		add_filter( 'pre_comment_approved', array( $this, 'check_comment' ), 10, 2 );
	}

	//过滤数据
	function check(){
		if( !isset( $_POST['comment'] ) ) return;
		if( isset( $_POST['person-comment'] ) ){
			$_POST['comment'] = $_POST['person-comment'];
			unset( $_POST['person-comment'] );
			$_REQUEST = array_merge( $_GET, $_POST );
		}else $this->spam = true;
	}

	//修改评论表单
	function change_form( $comment_field ){
		$search = array(
			'name="comment"',
			'</textarea>'
		);
		$replace = array(
			'name="person-comment"',
			'</textarea><textarea name="comment" style="display:none"></textarea>'
		);
		return str_replace( $search, $replace, $comment_field );
	}

	//检测机器人评论
	function check_comment( $approved, $comment ){
		if( $this->spam ){
			if( in_array( $comment['comment_type'], array( 'pingback', 'trackback' ) ) ) return $comment;
			wp_die( __( '检测为机器人评论，请在网站评论表单处提交您的评论', 'Bing' ) );
		}
		return $approved;
	}

}
add_action( 'init', array( 'Bing_anti_spam', 'init' ) );

/**
	*WordPress 禁止不包含中文的评论
	*http://www.endskin.com/spam-chinese-comment/
*/
function Bing_spam_chinese_comment( $comment ){
	if( Bing_mpanel( 'comment_anti_chinese' ) && !preg_match( '/[一-龥]/u', $comment['comment_content'] ) ) wp_die( __( '请在评论里包含中文' , 'Bing' ) );
	return $comment;
}
add_filter( 'preprocess_comment', 'Bing_spam_chinese_comment' );

/**
	*禁止没有头像的用户评论
	*http://www.endskin.com/validate-gravatar-comment/
*/
function Bing_validate_gravatar_comment( $comment ){
	if( !Bing_mpanel( 'validate_gravatar_comment' ) ) return $comment;
	$headers = @get_headers( 'http://cn.gravatar.com/avatar/' . md5( strtolower( trim( $comment['comment_author_email'] ) ) ) . '?d=404' );
	if( strpos( $headers[0], '200' ) === false ) wp_die( __( '请使用有 Gravatar 头像的邮箱评论', 'Bing' ) );
	return $comment;
}
add_filter( 'preprocess_comment', 'Bing_validate_gravatar_comment' );

/**
	*评论邮件通知
	*http://www.bgbk.org
*/
function Bing_comment_mail_notify( $comment_ID ){
	$comment = get_comment( $comment_ID );
	if( !Bing_mpanel( 'comment_email_notify' ) || empty( $comment->comment_parent ) || $comment->comment_approved == 'spam' || get_comment_author_email( $comment ) == get_comment_author_email( $comment->comment_parent ) ) return;
	$subject = sprintf( __( '你在%s的评论有回复啦！', 'Bing' ), sprintf( __( '《%s》', 'Bing' ), get_the_title( $comment->comment_post_ID ) ) );
	$message_code = '
	<div>
		<div style="color: #555; font:12px/1.5 Hiragino Sans GB, Microsoft Yahei, SimSun, Helvetica, Arial, Sans-serif; width: 600px; margin: 50px auto; border: 1px solid #E9E9E9; border-top: none;">
			<table border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr valign="top" height="2">
						<td width="190" bgcolor="#0B9938"></td>
						<td width="120" bgcolor="#9FCE67"></td>
						<td width="85" bgcolor="#EDB113"></td>
						<td width="85" bgcolor="#FFCC02"></td>
						<td width="130" bgcolor="#5B1301" valign="top"></td>
					</tr>
				</tbody>
			</table>
			<div style="padding: 0 15px 8px;">
				<h2 style="border-bottom: 1px solid #E9E9E9; font-size: 14px; font-weight: normal; padding: 10px 0 10px;">
					<span style="color: #12ADDB;">&gt;</span>
					%s
				</h2>
				<div style="font-size: 12px; color: #777; padding: 0 10px; margin-top: 18px;">
					<p>%s</p>
					<p style="background: #F5F5F5; padding: 10px 15px; margin: 18px 0;">%s</p>
					<p>%s</p>
					<p style="background: #F5F5F5; padding: 10px 15px; margin: 18px 0;">%s</p>
					<p>%s</p>
				</div>
			</div>
			<div style="color: #888; padding: 10px; border-top: 1px solid #E9E9E9; background: #F5F5F5;">
				<p style="margin: 0; padding: 0;">%s</p>
			</div>
		</div>
	</div>';
	$message = sprintf(
		$message_code,
		sprintf( __( '您在%s的评论有回复啦！', 'Bing' ), '<a style="text-decoration: none; color: #12ADDB;" href="' . esc_attr( home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" target="_blank">' . get_bloginfo( 'name', 'display' ) . '</a>' ),
		sprintf( __( '%s，您曾在%s中发表评论：', 'Bing' ), get_comment_author( $comment->comment_parent ), '<a style="text-decoration: none; color: #12ADDB;" href="' . esc_url( get_permalink( $comment->comment_post_ID ) ) . '" title="' . the_title_attribute( array( 'echo' => false, 'post' => $comment->comment_post_ID ) ) . '">' . sprintf( __( '《%s》', 'Bing' ), get_the_title( $comment->comment_post_ID ) ) . '</a>' ),
		nl2br( htmlspecialchars( get_comment_text( $comment->comment_parent ) ) ),
		sprintf( __( '“%s”给您的回复如下：', 'Bing' ), get_comment_author( $comment ) ),
		nl2br( htmlspecialchars( get_comment_text( $comment ) ) ),
		sprintf( _x( '您可以%s，欢迎再次光临%s！', '第一个“%s”为“点击查看完整的回复内容”的链接', 'Bing' ), '<a style="text-decoration: none; color: #12ADDB;" href="' . esc_url( get_comment_link( $comment->comment_parent ) ) . '" title="' . esc_attr__( '点击查看完整的回复内容', 'Bing' ) . '" target="_blank">' . __( '点击查看完整的回复内容', 'Bing' ) . '</a>', '<a style="text-decoration: none; color: #12ADDB;" href="' . esc_url( home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" target="_blank">' . get_bloginfo( 'name', 'display' ) . '</a>' ),
		sprintf( __( '%s - 邮件自动生成，请勿直接回复！', 'Bing' ), '© ' . current_time( 'Y' ) . ' <a style="text-decoration: none; color: #12ADDB;" href="' . esc_url( home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" target="_blank">' . get_bloginfo( 'name', 'display' ) . '</a>' )
	);
	wp_mail( get_comment_author_email( $comment->comment_parent ), $subject, $message, 'Content-Type: text/html' );
}
add_action( 'comment_post', 'Bing_comment_mail_notify' );

/**
	*评论作者链接新窗口打开
	*http://www.endskin.com/comment-author-link-newtab/
*/
function Bing_comment_author_link( $link ){
	return Bing_mpanel( 'comment_author_link_blank' ) ? str_replace( "href=", 'target="_blank" href=', $link ) : $link;
}
add_filter( 'get_comment_author_link', 'Bing_comment_author_link', 2 );

/**
	*移除评论 HTML 使用权限判断验证随机数
	*http://www.bgbk.org
*/
function Bing_remove_comment_form_unfiltered_html_nonce(){
	remove_action( 'comment_form', 'wp_comment_form_unfiltered_html_nonce' );
}
add_action( 'after_setup_theme', 'Bing_remove_comment_form_unfiltered_html_nonce', 14 );

/**
	*获取插入表情按钮
	*http://www.bgbk.org
*/
function Bing_insert_smiley(){
	global $wpsmiliestrans;
	if( empty( $wpsmiliestrans ) || !is_array( $wpsmiliestrans ) ) return;
	$code = '';
	foreach( array_unique( $wpsmiliestrans ) as $key => $smiley ) $code .= '<a href="javascript:;" data-smiley="' . esc_attr( $key ) . '">' . translate_smiley( array( $key ) ) . '</a>';
	return $code;
}

//End of page.
