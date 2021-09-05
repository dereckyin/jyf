
<!-- 主選單 -->
<a href="" class="logo"><i class='fab fa-docker'></i>海運系統</a>
<a class="mobilemenu"><span>行動裝置選單</span><b></b></a>
<nav style="display: flex; align-items: flex-start;">

<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);



include_once '../api/config/core.php';
include_once '../api/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../api/libs/php-jwt-master/src/ExpiredException.php';
include_once '../api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

if ( isset( $jwt ) ) {

	try {
	        // decode jwt
	        $decoded = JWT::decode($jwt, $key, array('HS256'));

	        if($decoded->data->is_admin)
	            echo "<a href='admin/main.php'>後台管理<eng>administration</eng></a>";

	        //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
	        //    header( 'location:index.php' );
	    }
	    // if decode fails, it means jwt is invalid
	    catch (Exception $e){
	    
	        
	    }
	}

?>

    <a href="directory.php">通訊錄<eng>Contactor</eng></a>
    <dl class="sub">
     <dt><a href="" class="after-micons">收貨記錄<eng>Receive Goods</eng></a></dt>
     <dd><a href="main.php">收貨記錄<eng>Receive Goods</eng></a></dd>
     <dd><a href="sea_take_photo.php">手機照相<eng>Take Photo</eng></a></dd>
    </dl>
 <a href="loading.php">貨物裝櫃<eng>Loading Goods into Container</eng></a>
 <a href="measure.php">到貨丈量、打單<eng>Measurement, Pickup/Payment</eng></a>
 <dl class="sub">
     <dt><a href="" class="after-micons">查詢<eng>Inquire</eng></a></dt>
     <dd><a href="taiwanpay.php">台灣付與代墊<eng>Taiwan Pay/ Courier Money</eng></a></dd>
     <dd><a href="query_receive.php">收貨記錄查詢<eng>Query For Receiving Records</eng></a></dd>
     <dd><a href="contact_us.php">聯絡我們<eng>Contact Us</eng></a></dd>
 </dl>
 <?php
 	if($decoded->data->sea_expense)
 		echo "<a href='expense_recorder_sea.php'>支出記錄表<eng>Expense Recorder</eng></a>";
 ?>
 <?php
 	if($decoded->data->sea_expense_v2)
 		echo "<a href='expense_recorder_sea_v2.php'>支出記錄表2<eng>Expense Recorder2</eng></a>";
 ?>
</nav>	
<!-- 主選單end -->

<script>
    $(function(){
        toggleme($('a.mobilemenu'),$('body'),'MobileMenuOn');
    });
</script>
<script defer src="js/a076d05399.js"></script>