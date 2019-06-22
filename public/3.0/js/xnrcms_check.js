var CheckJs={
		//必填
		required: function( value ) {
			if (typeof value === 'undefined') {
				return false;
			}
			return value.length > 0;
		},
		//邮箱验证
		email: function( value ) {
			return /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test( value );
		},
		mobile:function( value ){
			return /^1([3-9][0-9])\d{8}$/.test( value );
		},
		//URL合法验证
		url: function( value ) {
			return /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test( value );
		},
		//合法的日期验证
		date: function( value ) {
			return !/Invalid|NaN/.test( new Date( value ).toString() );
		},
		//合法的日期 (ISO)验证
		dateISO: function( value ) {
			return this.optional( element ) || /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test( value );
		},
		//数字验证
		number: function( value ) {
			return /^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test( value );
		},
		//只能输入整数
		digits: function( value ) {
			return /^\d+$/.test( value );
		},
		//合法的信用卡号验证
		creditcard: function( value, element ) {
			if ( /[^0-9 \-]+/.test( value ) ) { return false;}
			var nCheck = 0,nDigit = 0,bEven = false,n, cDigit;
				value = value.replace( /\D/g, "" );
			if ( value.length < 13 || value.length > 19 ) { return false;}
			for ( n = value.length - 1; n >= 0; n--) {
				cDigit = value.charAt( n );
				nDigit = parseInt( cDigit, 10 );
				if ( bEven ) {
					if ( ( nDigit *= 2 ) > 9 ) {
						nDigit -= 9;
					}
				}
				nCheck += nDigit;
				bEven = !bEven;
			}
			return ( nCheck % 10 ) === 0;
		},
		minlength: function( value,param ) {
			var length = value.length;
			return length >= param;
		},
		maxlength: function( value, param ) {
			var length = value.length;
			return length <= param;
		},
		rangelength: function( value, param ) {
			var length = value.length;
			return ( length >= param[ 0 ] && length <= param[ 1 ] );
		},
		min: function( value, param ) {
			return value >= param;
		},
		max: function( value, param ) {
			return value <= param;
		},
		range: function( value, param ) {
			return ( value >= param[ 0 ] && value <= param[ 1 ] );
		},
		//相等比较
		equalTo: function( value,param ) { return value === param;},
		//中文验证
		chinese:function(value){return /^[\u4e00-\u9fa5]+$/.test(value);},
		//判断整数value是否等于0
		isIntEqZero:function(value){return parseInt(value) == 0;},
	    //判断整数value是否大于0
		isIntGtZero:function(value){return parseInt(value) > 0;},
	    //判断整数value是否大于或等于0
		isIntGteZero:function(value){return parseInt(value) >= 0;},
	    //判断整数value是否不等于0
		isIntNEqZero:function(value){return parseInt(value) != 0;},
	    //判断整数value是否小于0
		isIntLtZero:function(value){return parseInt(value) < 0;}, 
	    //判断整数value是否小于或等于0
		isIntLteZero:function(value){return parseInt(value) <= 0;},
	    //判断浮点数value是否等于0
		isFloatEqZero:function(value){return parseFloat(value) == 0;},
	    //判断浮点数value是否大于0
		isFloatGtZero:function(value){return parseFloat(value) > 0;},
	    //判断浮点数value是否大于或等于0
		isFloatGteZero:function(value){return parseFloat(value) >= 0;},
	    //判断浮点数value是否不等于0
		isFloatNEqZero:function(value){return parseFloat(value) != 0;},
	    //判断浮点数value是否小于0
		isFloatLtZero:function(value){return parseFloat(value) < 0;},
	    //判断浮点数value是否小于或等于0
		isFloatLteZero:function(value){return parseFloat(value) <= 0;},
	    //判断浮点型 
		isFloat:function(value){return /^[-\+]?\d+(\.\d+)?$/.test(value); },
	    //匹配integer
		isInteger:function(value){return (/^[-\+]?\d+$/.test(value) && parseInt(value)>=0);},
	    //判断数值类型，包括整数和浮点数
		isNumber:function(value){return /^[-\+]?\d+$/.test(value) || /^[-\+]?\d+(\.\d+)?$/.test(value);},
	    //只能输入[0-9]数字
		isDigits:function(value){return /^\d+$/.test(value);},
	    //判断英文字符
		isEnglish:function(value){return /^[A-Za-z]+$/.test(value);},
	     //手机号码验证
		isMobile:function(value){return value.length == 11 && /^(((13[0-9]{1})|(15[0-35-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/.test(value);},
	    //电话号码验证
		isPhone:function(value){var tel = /^(\d{3,4}-?)?\d{7,9}$/g;return (tel.test(value));},
	    //联系电话(手机/电话皆可)验证
		isTel:function(value){
			var length = value.length;   
	        var mobile = /^(((13[0-9]{1})|(15[0-35-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
	        var tel = /^(\d{3,4}-?)?\d{7,9}$/g;       
	        return tel.test(value) || (length==11 && mobile.test(value));
		},
	     //匹配qq
		isQq:function(value){return /^[1-9]\d{4,12}$/.test(value);},
	     //邮政编码验证
		isZipCode:function(value){return (/^[0-9]{6}$/.test(value));},
	    //匹配密码，以字母开头，长度在6-12之间，只能包含字符、数字和下划线。
		isPwd:function(value){return /^[a-zA-Z]\\w{6,12}$/.test(value);},
	    //身份证号码验证
	    isIdCardNo:function(value){return isIdCardNo(value);},
	    //IP地址验证
	    isIp:function(value){return /^(([1-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))\.)(([1-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))\.){2}([1-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))$/.test(value);},
	    //字符验证，只能包含中文、英文、数字、下划线等字符
	    stringCheck:function(value){return /^[a-zA-Z0-9\u4e00-\u9fa5-_]+$/.test(value);},
	    //判断中文字符
		isChinese:function(value){return /^[\u0391-\uFFE5]+$/.test(value);},
	    //匹配汉字 
		isChinese2:function(value){return /^[\u4e00-\u9fa5]+$/.test(value);},   
	    //匹配中文(包括汉字和字符)
		isChineseChar:function(value){return /^[\u0391-\uFFE5]+$/.test(value);},
	    //判断是否为合法字符(a-zA-Z0-9-_)
		isRightfulString:function(value){return /^[A-Za-z0-9_-]+$/.test(value);},
	    //判断是否包含中英文特殊字符，除英文"-_"字符外
		isContainsSpecialChar:function(value){
			var reg = RegExp(/[(\ )(\`)(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\+)(\=)(\|)(\{)(\})(\')(\:)(\;)(\')(',)(\[)(\])(\.)(\<)(\>)(\/)(\?)(\~)(\！)(\@)(\#)(\￥)(\%)(\…)(\&)(\*)(\（)(\）)(\—)(\+)(\|)(\{)(\})(\【)(\】)(\‘)(\；)(\：)(\”)(\“)(\’)(\。)(\，)(\、)(\？)]+/);   
	         	return !reg.test(value);
		},
};
//身份证号码的验证规则
function isIdCardNo(num){ 
　   //if (isNaN(num)) {alert("输入的不是数字！"); return false;} 
　　 var len = num.length, re; 
　　 if (len == 15) 
　　 re = new RegExp(/^(\d{6})()?(\d{2})(\d{2})(\d{2})(\d{2})(\w)$/); 
　　 else if (len == 18) 
　　 re = new RegExp(/^(\d{6})()?(\d{4})(\d{2})(\d{2})(\d{3})(\w)$/); 
　　 else {
		//alert("输入的数字位数不对。"); 
		return false;
	} 
　　 var a = num.match(re); 
　　 if (a != null) 
　　 { 
　　 if (len==15) 
　　 { 
　　 var D = new Date("19"+a[3]+"/"+a[4]+"/"+a[5]); 
　　 var B = D.getYear()==a[3]&&(D.getMonth()+1)==a[4]&&D.getDate()==a[5]; 
　　 } 
　　 else 
　　 { 
　　 var D = new Date(a[3]+"/"+a[4]+"/"+a[5]); 
　　 var B = D.getFullYear()==a[3]&&(D.getMonth()+1)==a[4]&&D.getDate()==a[5]; 
　　 } 
　　 if (!B) {
		//alert("输入的身份证号 "+ a[0] +" 里出生日期不对。"); 
		return false;
	} 
　　 } 
　　 if(!re.test(num)){
		//alert("身份证最后一位只能是数字和字母。");
		return false;
	}
　　 return true; 
} 
//车牌号校验
function isPlateNo(plateNo){
    var re = /^[\u4e00-\u9fa5]{1}[A-Z]{1}[A-Z_0-9]{5}$/;
    if(re.test(plateNo)){
        return true;
    }
    return false;
}