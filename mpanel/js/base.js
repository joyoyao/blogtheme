/**
	*mpanel JavaScript
	*http://www.bgbk.org
*/
(function( $, args ){

	//切换分页
	$( document ).on( 'click', '.mpanel-menu li', function(){
		$( '.mpanel-menu li' ).removeClass( 'current' );
		$( this ).addClass( 'current' );
		$( '.mpanel-mian-panel .mpanel-page' ).hide();
		$( '.mpanel-mian-panel .mpanel-page' ).eq( $( this ).index() ).fadeIn();
	} );

	//控制显示
	var panel_hide = new Array();

	$.fn.checkbox_show = function( control ){
		if( !$( this ).attr( 'checked' ) ) panel_hide.push( control );
		$( this ).click(function(){
			$( this ).attr( 'checked' ) ? $( control ).slideDown( 200 ) : $( control ).slideUp( 200 );
		} );
	}

	$.fn.checkbox_hide = function( control ){
		if( $( this ).attr( 'checked' ) ) panel_hide.push( control );
		$( this ).click(function(){
			$( this ).attr( 'checked' ) ? $( control ).slideUp( 200 ) : $( control ).slideDown( 200 );
		} );
	}

	$.fn.radio_show = function( control ){
		if( !$( this ).attr( 'checked' ) ) panel_hide.push( control );
		$( this ).data( 'control-show', control );
	}

	$.fn.radio_hide = function( control ){
		if( $( this ).attr( 'checked' ) ) panel_hide.push( control );
		$( this ).data( 'control-hide', control );
	}

	$( document ).on( 'click', '.mpanel-radio-box input[type=radio]', function(){
		$( this ).parent( 'label' ).parent( '.mpanel-radio-box' ).find( 'input[type=radio]' ).each(function(){
			var show = $( this ).data( 'control-show' ),
				hide = $( this ).data( 'control-hide' );
			if( $( this ).attr( 'checked' ) ){
				if( show ) $( show ).slideDown( 200 );
				if( hide ) $( hide ).slideUp( 200 );
			}else{
				if( show ) $( show ).slideUp( 200 );
				if( hide ) $( hide ).slideDown( 200 );
			}
		} );
	} );

	//自定义列表
	$( document ).on( 'click', '.mpanel-list-add', function(){
		var input = $( this ).prev( 'input' );
			value = $( '<div/>' ).text( input.val().replace( /(^\s*)|(\s*$)/g, '' ) ).html(),
			list  = $( this ).next( 'ul.mpanel-list' );
		if( $( this ).data( 'repeat' ) ){
			var repeat_error = false;
			list.find( 'li .mpanel-list-li-name' ).each( function(){
				if( $( this ).text() === value ) repeat_error = true;
			} );
			if( repeat_error ){
				alert( args.list_repeat_error );
				return;
			}
		}
		if( value ){
			list.append( '<li><span class="mpanel-list-li-name">' + value + '</span><a href="javascript:;" class="mpanel-list-li-delete"></a></li>' );
			$( '<input type="hidden" name="mpanel[' + $( this ).data( 'list-name' ) + '][]" class="mpanel-list-hidden-content">' ).val( value ).prependTo( list.children( 'li:last' ) );
			input.val( '' );
		}
	} );
	$( document ).on( 'click', '.mpanel-list .mpanel-list-li-delete', function(){
		$( this ).parent().slideUp( 200, function(){
			$( this ).remove();
		} );
	} );

	//提示框
	$( document ).on( 'mouseover', '.mpanel-help', function(){
		if( !this.title ) return;
		$( this ).data( 'tptitle', this.title );
		this.title = '';
		$( 'body' ).append( '<div id="mpanel-tooltip">' + $( this ).data( 'tptitle' ) + '</div>' );
		$( '#mpanel-tooltip' ).css({
			'left': $( this ).offset().left + $( this ).outerWidth( false ) / 2 - $( '#mpanel-tooltip' ).outerWidth( false ) / 2,
			'top': $( this ).offset().top - 8
		} );
		$( '#mpanel-tooltip' ).fadeIn();
	} );
	$( document ).on( 'mouseout', '.mpanel-help', function(){
		if( !$( this ).data( 'tptitle' ) ) return;
		$( '#mpanel-tooltip' ).remove();
		this.title = $( this ).data( 'tptitle' );
	} );

	//Ajax 表单
	$( document ).on( 'submit', '#mpanel-form', function(){
		var load = $( '#mpanel-load' ),
			save = $( '.mpanel-save, .mpanel-save2' );
		if( $( '#mpanel-item-import' ).val() ) return true;
		save.attr( 'disabled', true );
		load.fadeIn( 200 );
		$.post( args.admin_ajax, $( '#mpanel-form' ).serialize() + '&action=mpanel-save', function( data ){
			if( data === '1' ){
				load.addClass( 'mpanel-done' );
				setTimeout( function(){
					load.fadeOut( 200 );
					save.attr( 'disabled', false );
				}, 1200 );
				setTimeout( function(){
					load.removeClass( 'mpanel-done' );
				}, 1400 );
				return false;
			}
			load.addClass( 'mpanel-error' );
			setTimeout( function(){
				load.fadeOut( 200 );
				save.attr( 'disabled', false );
			}, 1200 );
			setTimeout( function(){
				load.removeClass( 'mpanel-done' );
			}, 1400 );
		} );
		return false;
	} );

	//文件上传
	$( document ).on( 'click', '.mpanel-upload-button', function(){
		var upload_frame,
			value = $( '#' + $( this ).data( 'value' ) ),
			top_parent = $( this ).parent( '.input-group-btn' ).parent( '.input-group' );
		if( upload_frame ){
			upload_frame.open();
			return;
		}
		upload_frame = wp.media( {
			title: 'Upload',
			button: {
				text: 'Upload',
			},
			multiple: false
		} );
		upload_frame.on( 'select', function(){
			attachment = upload_frame.state().get( 'selection' ).first().toJSON();
			var url = attachment.url,
				box = top_parent.next( '.upload-img-box' );
			box.length === 0 ? top_parent.after( '<div class="upload-img-box"><img src="' + url + '" /><span class="delete"></span></div>' ) : box.find( 'img' ).attr( 'src', url );
			value.val( url ).trigger( 'change' );
		} );
		upload_frame.open();
	} );

	$( document ).on( 'click', '.upload-img-box .delete', function(){
		var box = $( this ).parent( '.upload-img-box' );
		$( '#' + box.prev( '.input-group' ).find( '.mpanel-upload-button' ).data( 'value' ) ).val( '' );
		box.fadeOut( 100, function(){
			$( this ).remove();
		} );
	} );

	//Onload
	$(function(){

		//复选框
		$( '.mpanel-checkbox' ).each(function(){
			new Switchery( this );
		} );

		//隐藏开关控制的元素
		for( var i = 0; i < panel_hide.length; i++ ){
			$( panel_hide[i] ).hide();
		}
		
		//设置默认分页
		$( '.mpanel-menu li:first' ).addClass( 'current' );
		$( '.mpanel-mian-panel .mpanel-page:not(:first)' ).hide();

		//颜色选择器
		$( '.colorSelector' ).wpColorPicker();

		//日期选择器
		$( 'input.mpanel-cxcalendar' ).each( function(){
			$( this ).cxCalendar();
		} );

	} );

})( jQuery, mpanel_base_args );