require('./Public/js/distpicker/index.js')
let tools = require('./Public/js/tools')
//let weui = require('./Public/js/weui/weui.min.js')
$(function(){
	let $distpicker = $( '#distpicker' ).distpicker()
	var $distpickerList = $('#distpicker select')
	if ($distpickerList.length > 0){
		if (province){
			$distpickerList.eq(0).val( province )
			$distpickerList.eq(0).trigger( 'change' )
		}
		if (city){
			$distpickerList.eq(1).val( city )
			$distpickerList.eq(1).trigger( 'change' )
		}
		if (district){
			$distpickerList.eq(2).val( district )
		}
	}
	
	// 表单验证
	$("#form").on('submit', function(){
		var $that = $(this)
		var validate = weui.form.validate('#form', function (error) {
			if (!error) {
				var loading = weui.loading('提交中...')
				var btnTitle = $that.html(),
					data = $that.serialize()
				$.post($that.attr('action'), data, function(json){
					loading.hide();
					if (typeof json == "string"){
						json = eval("(" + json + ")");
					}
					if (json.status == 0){
						toast(json.msg || '操作成功', {status: 0});
						setTimeout(function () {
							if (json.url){						
								top.location.href = json.url;
							} else {
								top.location.reload();
							}
						}, 1500);
					} else {
						setTimeout(function(){
							toast(json.info || json.msg || '操作失败', {status: 1});					
						}, 500)
						return false;
					}
					$that.html(btnTitle);
					$that.attr('disabled',false);
				}, 'json' )
			}
			// return true; // 当return true时，不会显示错误
		}, {
			regexp: {
				IDNUM: /(?:^\d{15}$)|(?:^\d{18}$)|^\d{17}[\dXx]$/,
				VCODE: /^.{4}$/
			}
		});
		return false;
	})
})

//切换横竖屏重新计算大小
function handleRotate(){
    if( !handleRotate.html ) return;
    handleRotate.html.className
        = window.innerWidth > window.innerHeight ? 'rotate' : '';
    if (window.innerWidth > window.innerHeight) {
    		$('.fix-bottom-box').addClass('no-fix')
    } else {
	    	$('.fix-bottom-box').removeClass('no-fix')
    }
    
//    if( $( 'body' ).height() > window.innerHeight ){
//    		$('.fix-bottom-box').addClass( 'no-fix' );
//    }
}
handleRotate.html = document.getElementsByTagName( 'html' );
handleRotate.html = handleRotate.html && handleRotate.html[ 0 ];
$( window ).on( 'resize', function(){
    clearTimeout( handleRotate.tid );
    handleRotate.tid = setTimeout( handleRotate, 300 );
} );
handleRotate();

