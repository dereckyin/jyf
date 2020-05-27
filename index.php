<!DOCTYPE html>
<html>
<head>
<title>中亞菲國際貿易有限公司</title>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

<!-- jQuery和js載入 --> 
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> 
<script type="text/javascript" src="js/rm/realmediaScript.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script type="text/javascript" src="js/login.js" defer></script> 
<script src="js/axios.min.js"></script> 
<script src='https://www.google.com/recaptcha/api.js'></script>

<script>
$(function(){
//    $('header').load('Include/header.htm');
    toggleme($('a.btn.detail'),$('.block.record'),'show');
})
</script>
</head>

<body>
<div class="bodybox"> 
  <!-- header -->
  <header> 
    <!-- 主選單 -->
    <script>
    $(function(){
        $('header').load('include/header.htm');
    })
    </script>

    <script defer src="https://kit.fontawesome.com/a076d05399.js"></script> 
  </header>
  <!-- header end -->
  <div id='mainContent'>
    <div class="mainContent">
      <h6>登入</h6>
      <p>(Login )&nbsp;</p>
      <div class="block" style="width: 50%; margin: 0 auto;">
        <div class="tablebox s01">
          <ul>
            <li class="header"></li>
            <li>使用者
              <p>username:</p>
            </li>
            <li>
              <input type="text" v-model="logDetails.username" v-on:keyup="keymonitor">
            </li>
          </ul>
          <ul>
            <li class="header"></li>
            <li>密碼
              <p>password:</p>
            </li>
            <li>
              <input type="password" v-model="logDetails.password" v-on:keyup="keymonitor">
            </li>
          </ul>
          <ul>
            <li class="header"></li>
            <li>
              
            </li>
            <li>
              <div class="g-recaptcha" data-sitekey="6LdU3dUUAAAAAI6y3D6BQtE2wfWiOZJDQVX_O3m5" style="transform:scale(1.0);-webkit-transform:scale(1.0);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
              <div>
            </li>
          </ul>
        </div>
        
        <div class="alert alert-danger text-center" v-if="errorMessage" style="margin-left: 200px;">
            <button  type="button" class="close" @click="clearMessage();"><span aria-hidden="true">&times;</span></button>
            <span class="glyphicon glyphicon-alert"></span> {{ errorMessage }} </div>
          <div class="alert alert-success text-center" v-if="successMessage"  style="margin-left: 200px;">
            <button type="button" class="close" @click="clearMessage();"><span aria-hidden="true">&times;</span></button>
            <span class="glyphicon glyphicon-check"></span> {{ successMessage }} </div>
        
        <div class="btnbox"><a class="btn" @click="register();">新會員/Sign Up</a><a class="btn orange" @click="checkLogin();">登入/login</a></div>
      </div>
    </div>
  </div>
 </div>
</body>
</html>
