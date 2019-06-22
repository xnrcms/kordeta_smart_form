function tusi(txt,fun){
	$('.tusi').remove();
	var div = $('<div style="background: url('+PublicPath+'images/tusi.png);max-width: 85%;min-height: 77px;min-width: 270px;position: absolute;left: -1000px;top: -1000px;text-align: center;border-radius:10px;"><span style="color: #ffffff;line-height: 77px;font-size: 23px;">'+txt+'</span></div>');
	$('body').append(div);
	div.css('zIndex',9999999);
	div.css('left',parseInt(($(window).width()-div.width())/2));
	var top = parseInt($(window).scrollTop()+($(window).height()-div.height())/2);
	div.css('top',top);
	setTimeout(function(){
		div.remove();
    	if(fun){
    		fun();
    	}
	},2000);
}
function loading(txt){
	if(txt === false){
		mask(2);
		$('.qp_lodediv').remove();
	}else{
		mask(1);
		$('.qp_lodediv').remove();
	var div = $('<div class="qp_lodediv" style="background: url('+PublicPath+'images/loadb.png);width: 269px;height: 107px;position: absolute;left: -1000px;top: -1000px;text-align: center;"><span style="color: #ffffff;line-height: 107px;font-size: 23px; white-space: nowrap;">&nbsp;&nbsp;&nbsp;<img src="'+PublicPath+'images/load.gif" style="vertical-align: middle;"/>&nbsp;&nbsp;'+txt+'</span></div>');
		$('body').append(div);
		div.css('zIndex',9999999);
		div.css('left',parseInt(($(window).width()-div.width())/2)-20);
	var top = parseInt($(window).scrollTop()+($(window).height()-div.height())/2);
		div.css('top',top);
	}	
}
function mask(type)
{
	if(type == 2){
		$(".wincover").remove();
	}else{
		var dheight	= jQuery(document).height();//文档高度
			$("<div class='wincover' style='position:fixed;top:0px;opacity:0.4;filter:alpha(opacity=40);color:red;z-index:99999; top:expression(eval(document.documentElement.scrollTop));background:#000;left:0px;width:100%;height:"+dheight+"px;'></div>").prependTo("body");	
	}
}
var informMove = false ;
function _inform(text,fn,delay,speed1,speed2){
    if(informMove == true){ return false; } informMove = true;
    var informDiv = $('<div style="padding:20px 30px; background:rgba(0,0,0,0.5); color:#fff; font-size:20px; line-height:20px; position:fixed; left:50%; top:0%; z-index:9999; border-radius:10px; opacity:0; -webkit-transform:translateX(-50%); -moz-transform:translateX(-50%); -ms-transform:translateX(-50%); transform:translateX(-50%);">操作成功</div>');
    if(text)informDiv.text(text);
    if(!delay)delay     = 800;  //停留时间
    if(!speed1)speed1   = 300;  //出现时间
    if(!speed2)speed2   = 400;  //消失时间
    informDiv.appendTo($('body')).animate({"top":"20%","opacity":"1"},speed1).delay(delay).animate({"top":"0%","opacity":"0"},speed2,function(){
        informDiv.remove();
        if(fn)fn();
        informMove = false;
    });
}
//获取单选或复选被选中的值
function rcVal(obj,isnum){
	var vals = '';
	obj.each(function(i){
		if(this.checked) vals += this.value + ',';
	});
	
	vals	= vals.replace(/\,$/gi,"");
	vals	= !vals ? '' : vals;
	if(isnum == 1){
		vals	= !vals ? 0 : vals;
		return parseInt(vals);
	}
	return vals ? ','+ vals +',' : '';
}