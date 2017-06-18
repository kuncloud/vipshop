function pay(url, id){
	$.post(url, {id}, function(json){
		if (json.status == -1){
			weui.alert(json.msg);
		} else {
			wx.ready(function(){
				wx.chooseWXPay({
				    timestamp: json["timeStamp"], // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
				    nonceStr: json["nonceStr"], // 支付签名随机串，不长于 32 位
				    package: json["package"], // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
				    signType: json["signType"], // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
				    paySign: json["paySign"], // 支付签名
				    success: function (res) {
				        // 支付成功后的回调函数
						payed(id)
				    }
				});
			});
		}
	}, 'json')
}

function countdown($obj){
	var remain = $obj.data('time')
	if (remain <= 0) location.reload()
	remain --
	$obj.data('time', remain)
	
	var html = '', d=h=i=s=0
	if (remain > 3600){	// 剩余时间大于1小时
		d = parseInt(remain / 86400)
		remain %= 86400
		h = parseInt(remain / 3600)
		remain %= 3600
		html += d + '天' + h + '时'
	}
	i = parseInt(remain / 60)
	s = parseInt(remain % 60)
	html += i + '分' + s +'秒'
	
	$obj.html(html)
	setTimeout(function(){countdown($obj)}, 1000)
}