$(function(){
	
	// 搜索
    var $searchBar = $('#searchBar'),
        $searchResult = $('#searchResult'),
        $searchText = $('#searchText'),
        $searchInput = $('#searchInput'),
        $searchClear = $('#searchClear'),
        $searchCancel = $('#searchCancel');

    function hideSearchResult(){
        $searchResult.hide();
        $searchInput.val('');
    }
    function cancelSearch(){
        hideSearchResult();
        $searchBar.removeClass('weui-search-bar_focusing');
        $searchText.show();
    }

    $searchText.on('click', function(){
        $searchBar.addClass('weui-search-bar_focusing');
        $searchInput.focus();
    });
    $searchInput
        .on('blur', function () {
            if(!this.value.length) cancelSearch();
        })
        .on('input', function(){
            if(this.value.length) {
                $searchResult.show();
            } else {
                $searchResult.hide();
            }
        })
    ;
    $searchClear.on('click', function(){
        hideSearchResult();
        $searchInput.focus();
    });
    $searchCancel.on('click', function(){
        cancelSearch();
        $searchInput.blur();
    });
    
    $('.jump_url').on('click', function(){location.href=$(this).data('url')})
	
    $('.ajax_do').on('click', function(){
    		var url = $(this).data('url')
    		$.post(url, {}, function(json){
    			toast(json.msg, {status: json.status, callback: function(){
    				location.reload()
    			}})
    		}, 'json')
    })
    
    $('.number').on('keyup', function(){
		$(this).val($(this).val().replace(/\D/g, ''))
	})
	
	$('.float').on('keyup', function(){
		$(this).val($(this).val().replace(/[^\d.]/g, ''))
	})

	$('.nospecial').on('keyup', function(){
		$(this).val($(this).val().replace(/[^a-z\d\u4e00-\u9fa5]/ig, ''))
	})
    
    // 专属导购
    $('.zsdg').on('click', function(){
    		var phone = $(this).data('phone') || '',
    			kf = $(this).data('kf') || ''
		weui.dialog({
		    title: '',
		    content: '<p>您的账号是</p><p class="num">'+phone+'</p><p>请告知客服</p>',
		    className: 'custom-classname',
		    buttons: [
		        {
			        label: '确定',
			        type: 'default'
		        },
		    		{
			        label: '<a class="weui-dialog__btn weui-dialog__btn_primary" href="tel://'+kf+'">拨打客服电话</a>',
			        type: 'primary'
		    		}
		    ]
		});
		$btn = $('.weui-dialog__btn_primary')
		$btn.each(function(){
			if (!$(this).html()) $(this).remove()
		})
	})
})

function op(title, url, callback){
	if (!callback){
		callback = function(){location.reload()}
	}
	weui.confirm(title, function(){
		if (url){
			$.post(url, {}, function(json){
				toast(json.msg, {status: json.status, callback:callback})
			}, 'json')
		}
	})
}

function toast(title, {status, duration, callback, className}){
	status = status || 0
	className = className || ''
	duration = duration || 1500
	callback = callback || function(){}
	title = title ? title : ( status == 0 ? '操作成功' : '操作失败' )
	//if (status != 0) className += ' error'
	if (status == 0){
		weui.toast(title, {
			duration,
			className,
			callback
		})
	} else {
		var loading = weui.loading(title, {
			className
		})
		setTimeout(function () {
		    loading.hide();
		}, duration);
	}
}

















