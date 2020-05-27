// 瀏覽器版本測試 1.1
// jQuery最新僅支援到2.2.4
var isMobile = {
	 Android: function() { 	return navigator.userAgent.match(/Android/i) ? true : false; },
	 BlackBerry: function() { return navigator.userAgent.match(/BlackBerry/i) ? true : false; },
	 iOS: function() { 		return navigator.userAgent.match(/iPhone|iPod/i) ? true : false; },
	 iPad: function() { 	return navigator.userAgent.match(/iPad/i) ? true : false; },
	 Windows: function() { 	return navigator.userAgent.match(/IEMobile/i) ? true : false; },
	 any: function() { 		return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.iPad() || isMobile.Windows()); }
};
function getBrowserWidth() {
	if (/msie/.test(navigator.userAgent.toLowerCase())) {
		return document.compatMode == 'CSS1Compat' ? document.documentElement.clientWidth :
				 document.body.clientWidth;
	} else {
		return self.innerWidth;
	}
}
function messageadd(msg,txt){
   if(txt != ''){
	   if (msg != ''){ 
			msg +='<div style="padding:3px;"> * ';
			return msg + txt + '</div>'; 
	   } else {
		   txt='<div style="padding:3px;"> * '+txt+'</div>';
		   return txt;
	   };
   } else { return msg; };
};
function getIEVersion() {
  var rv = -1;
  if (navigator.appName == 'Microsoft Internet Explorer')
  {
    var ua = navigator.userAgent;
	
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  else if (navigator.appName == 'Netscape')
  {
    var ua = navigator.userAgent;
    var re  = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");  //for IE 11
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  return rv;
}
var ZoomChecked = function(){//瀏覽器是否被放大或縮小
	var temp = 0;
	var temp2 = 0; 
	if(navigator.userAgent.match("Chrome") == "Chrome" && navigator.userAgent.match("Safari") == "Safari"  ){// chrome
		temp =window.devicePixelRatio;
	}else if(navigator.userAgent.match("NET") == "NET" || navigator.userAgent.match("MSIE") == "MSIE"){
		temp = screen.deviceXDPI  / screen.logicalXDPI;
	}else if(navigator.userAgent.match("Firefox") == "Firefox"){
		temp = window.devicePixelRatio;
	}else if(navigator.userAgent.match("Mac") == "Mac"){//mac的safari
		temp = window.outerWidth / window.innerWidth;
	}else if(navigator.userAgent.match("Safari") == "Safari"){//window的 safari
		temp = (window.outerWidth-8) / window.innerWidth;
		temp2 = (window.outerWidth-16) / window.innerWidth;
	}	
	if(temp != 1 && temp2 != 1){
		return true;
	}else{
		return false;	
	}
}
function baseBrowserTest(support){
    if((support.line != undefined) && support.line){
        var line  = navigator.userAgent.match(/Line/i) ? true : false;
        if(line && (isMobile.iOS() || isMobile.iPad())){
            var img = $("<div/>").addClass("mbk");
            $('body').append(img);
        }
    }
   var msg='';
   var txt='';
   var errNum=0;
   if((support.pc == undefined)||(support.pc == false && support.mobile == false)){support.pc=true;};
   if(support.mobile == undefined){support.mobile=true;};
   if((support.ie == undefined)||(support.ie < 7)){support.ie=9;};
   
   if((support.ie != undefined) && getIEVersion()>0){
		 txt='您的IE版本太舊(最低需求IE'+support.ie+'以上)，如遇瀏覽上的困難，請用新版IE或是其他的瀏覽器(推薦使用Chrome,Firefox,Safari)來檢視,謝謝。';
		 if (getIEVersion()<support.ie){
			 if (getIEVersion()==6){
				 alert(txt);
			 } else {
			 	msg = messageadd(msg,txt);txt='';errNum++;
			 }
		 }
	   };
   if(!isMobile.any() && ZoomChecked()){
		  // 不支援mobile, 檢視縮放大小
		  txt="您的瀏灠器頁面檢視縮放並非為最佳的瀏覽比例，請調整到100%來做檢視！";
		  msg = messageadd(msg,txt);txt='';errNum++;
   };
   if(support.base_w != undefined && !isMobile.any()){
		  // 螢幕解析度
		  txt=(support.base_w > getBrowserWidth())?'本網頁最佳瀏覽解析度寬度為'+support.base_w+'px，但您目前視窗寬度為<span id="nowBrowserWidth">'+getBrowserWidth()+'</span>px，請將您的瀏覽視窗寬度拉大。':'';
		  msg = messageadd(msg,txt);txt='';errNum++;
	  };
   if(!support.pc){
		  // 不支援PC, 預設為支援
		  txt=(!isMobile.any())?'這個網頁設計並非專為個人電腦瀏覽設計，請用行動裝置瀏覽。':'';
		  msg = messageadd(msg,txt);txt='';errNum++;
	  };
   if(!support.mobile){
		  // 不支援行動裝置
		  txt=(isMobile.any())?'這個網頁設計並未替行動裝置做最佳瀏覽設計，請用個人電腦檢視來獲得最佳瀏覽體驗。':'';
		  msg = messageadd(msg,txt);txt='';errNum++;
	  };
   if (msg !='' && getCookie('ErrorMessage') != 1){ 
		$('body').prepend('<div class="messagebox alert"><div class="closemessagebox">X</div><div class="sorry" >很抱歉，必須提醒您...<div class="note">'+msg+'</div></div></div>');
		$('.messagebox').slideDown(200);
		$(window).bind('resize', function(){
			$('#nowBrowserWidth').text(getBrowserWidth());
			if (getBrowserWidth()>support.base_w && errNum <2){
				$('.messagebox').slideUp(100);
			};
		});
		$('.closemessagebox').click(function() {
            $('.messagebox').slideUp(100);
            setCookie('ErrorMessage', 1, 3)
        });
	};
};
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires +"; path=/";
}