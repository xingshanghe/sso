(function($){
	var passportUrl = 'http://passport.vboss.sobey.com';
	var lkey = __getCookie('lkey');
	var lvalue = __getCookie('lvalue');
	if(__getCookie('logining')==1){
		jQuery.ajax({
			url:passportUrl+'/hello?d='+window.location.host+'&lkey='+lkey+'&lvalue='+lvalue,
			dataType:'jsonp',
			contentType:'application/x-www-form-urlencoded; charset=UTF-8',
			success:function(data){
				//document.cookie="logining=0";
				if(data&&data.sso){
					$.each(data.sso,function(key,value){
						$.ajax({
							url:passportUrl+value,
							contentType:'application/x-www-form-urlencoded; charset=UTF-8',
							dataType:'jsonp',
							crossDomain:true
						});
					});
				}
			}
		});
	}
	
	if(__getCookie('logouting')==1){
		jQuery.ajax({
			url:passportUrl+'/bye?d='+window.location.host+'&lkey='+lkey+'&lvalue='+lvalue,
			dataType:'jsonp',
			contentType:'application/x-www-form-urlencoded; charset=UTF-8',
			success:function(data){
				//document.cookie="logouting=0";
				if(data&&data.sso){
					$.each(data.sso,function(key,value){
						$.ajax({
							contentType:'application/x-www-form-urlencoded; charset=UTF-8',
							url:value,
							dataType:'jsonp',
							crossDomain:true
						});
					});
				}
			}
		});
	}
	
	
})(jQuery);

function __getCookie(name) 
{ 
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg))
    	return decodeURI(arr[2]); 
    	//return unescape(arr[2]); 
    else 
        return null; 
} 