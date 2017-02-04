/**
 * 统计
 */
var cookiepre = "sat5_";
var WebSiteCookie={
	cookieName:"",
	cookieValue:"",
	expires:new Date((new Date()).getTime()+3600000000),
	domain:"",
	path:"/",
	set:function(){
		var cookies = this.cookieName + "=" + escape(this.cookieValue) + ";path=" + this.path + ";expires=" + (this.expires).toGMTString();
		cookies += this.domain=="" ? "" : ";domain=" + this.domain;
		document.cookie = cookies;
		if(this.get() == '' && this.cookieValue != ''){/*判断cookie写入是否成功*/
			/*alert("您的浏览器安全设置过高,不支持Cookie,请重新设置浏览器的。");*/
		}
	},
	get:function(){
		var cookie = document.cookie;
		var index = cookie.indexOf(this.cookieName + "=");
		if(index < 0){
			return '';
		}
		if(cookie.indexOf(";",index) > 0){
			return unescape(cookie.substring(index + this.cookieName.length + 1,cookie.indexOf(";",index)));
		}else{
			return unescape(cookie.substring(index + this.cookieName.length + 1));
		}
	},
	clear:function(){
		if(this.get() != null){
			var cookies = this.cookieName + "=;path=" + this.path + ";expires=" + (new Date()).toGMTString();
			cookies += this.domain == "" ? "" : ";domain=" + this.domain;
			document.cookie = cookies
		}else{
			/*alert('cookie值不存在');*/
		}
	}
}
function zzSetCookie(name,value){
	WebSiteCookie.cookieName=cookiepre + name;
	WebSiteCookie.cookieValue=value;
	WebSiteCookie.set();
}
function zzGetCookie(name){
	WebSiteCookie.cookieName=cookiepre + name;
	return WebSiteCookie.get()
}
function zzClearCookie(name){
	WebSiteCookie.cookieName=cookiepre + name;
	WebSiteCookie.clear();
}
function zzGetInfo(){
	var msg = '';

	msg += "appCodeName=" + navigator.appCodeName + "&";
	msg += "appName=" + navigator.appName + "&";
	msg += "appVersion=" + navigator.appVersion + "&";
	msg += "cookieEnabled=" + navigator.cookieEnabled + "&";
	//msg += "mimeTypes.length=" + navigator.mimeTypes.length + "&";
	//msg += "plugins.length=" + navigator.plugins.length + "&";
	msg += "platform=" + navigator.platform + "&";
	msg += "userAgent=" + navigator.userAgent + "&";
	//msg += "language=" + navigator.language + "&";

	msg += "appMinorVersion=" + navigator.appMinorVersion + "&";
	msg += "cpuClass=" + navigator.cpuClass + "&";
	//msg += "browserLanguage=" + navigator.browserLanguage + "&";
	//msg += "userLanguage=" + navigator.userLanguage + "&";
	msg += "systemLanguage=" + navigator.systemLanguage + "&";
	//msg += "onLine=" + navigator.onLine + "&";
	//msg += "userProfile=" + navigator.userProfile + "&";

	//msg += "screenLeft=" + window.screenLeft + "&";
	//msg += "screenTop=" + window.screenTop + "&";
	msg += "width=" + window.screen.width + "&";
	msg += "height=" + window.screen.height + "&";
	//msg += "availHeight=" + window.screen.availHeight + "&";
	//msg += "availWidth=" + window.screen.availWidth + "&";
	msg += "colorDepth=" + window.screen.colorDepth + "&";
	msg += "deviceXDPI=" + window.screen.deviceXDPI;// + "&";
	return msg;
}

var ZBase64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(input){var output="";var chr1,chr2,chr3,enc1,enc2,enc3,enc4;var i=0;input=ZBase64._utf8_encode(input);while(i<input.length){chr1=input.charCodeAt(i++);chr2=input.charCodeAt(i++);chr3=input.charCodeAt(i++);enc1=chr1>>2;enc2=((chr1&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64;} else if(isNaN(chr3)){enc4=64;}output=output+this._keyStr.charAt(enc1)+this._keyStr.charAt(enc2)+this._keyStr.charAt(enc3)+this._keyStr.charAt(enc4);}return output;},decode:function(input){var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;input=input.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<input.length){enc1=this._keyStr.indexOf(input.charAt(i++));enc2=this._keyStr.indexOf(input.charAt(i++));enc3=this._keyStr.indexOf(input.charAt(i++));enc4=this._keyStr.indexOf(input.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3 != 64){output=output+String.fromCharCode(chr2);}if(enc4 != 64){output=output+String.fromCharCode(chr3);}}output=ZBase64._utf8_decode(output);return output;},_utf8_encode:function(string){string=string.replace(/\r\n/g,"\n");var utftext="";for(var n=0; n<string.length; n++){var c=string.charCodeAt(n);if(c<128){utftext+=String.fromCharCode(c);}else if((c>127) &&(c<2048)){utftext+=String.fromCharCode((c>>6)|192);utftext+=String.fromCharCode((c&63)|128);}else{utftext+=String.fromCharCode((c>>12)|224);utftext+=String.fromCharCode(((c>>6)&63)|128);utftext+=String.fromCharCode((c&63)|128);}}return utftext;},_utf8_decode:function(utftext){var string="";var i=0;var c=c1=c2=0;while(i<utftext.length){c=utftext.charCodeAt(i);if(c<128){string+=String.fromCharCode(c);i++;}else if((c>191) &&(c<224)){c2=utftext.charCodeAt(i+1);string+=String.fromCharCode(((c&31)<<6)|(c2&63));i+=2;}else{c2=utftext.charCodeAt(i+1);c3=utftext.charCodeAt(i+2);string+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));i+=3;}}return string;}}
var st_ref='';
if(document.referrer.length>0){st_ref=document.referrer;}
try{if(st_ref.length==0&&opener.location.href.length>0){st_ref=opener.location.href;}}catch(e){}

var sid = zzGetCookie('sid');
var s = ZBase64.encode(st_ref);
var n = ZBase64.encode(window.location.href);
var info = '';//ZBase64.encode(zzGetInfo());

document.write("<scr" + "ipt language=javascript src=\"http://st.chofn.com/?sid=" + sid + "&s=" + s + "&n=" + n + "&info=" + info + "\"></script>");