<?php include 'check.php';?>
<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
  header( 'location:index' );
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/config/database.php';


use \Firebase\JWT\JWT;

$taiwan_read = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

            $taiwan_read = $decoded->data->taiwan_read;
          

        }
        catch (Exception $e){

            header( 'location:index.php' );
        }

    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        header( 'location:index.php' );
    }

?>

<!DOCTYPE html>
<html>
<head>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

<!-- CSS -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="js/bootstrap/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>

<style>
img.ui-datepicker-trigger {
    padding-left: 10px;
    margin: -8px;
}

p {
  margin:0; 
  padding:0;
}


</style>

<script>
$(function(){
    $('header').load('include/header_admin.php');
})
</script>

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
        <header></header>
        <!-- header end -->
        <div id='receive_record'>
          <div class="mainContent">
            <h6>貨物裝櫃<eng>Loading Goods into Container</eng></h6>
             <div class="block">
                <div class="btnbox">
                    <?php
                    if($taiwan_read == "0")
                    {
                    ?>
                     <a class="btn small detail">新增貨櫃記錄<eng>New Container Record</eng></a>
                     <a class="btn small" href="loading_edit.php">修改貨櫃記錄<eng>Edit Container Record</eng></a>
                     <?php
                    }
                    ?>
                     <a class="btn small" href="loading_query.php">查詢貨櫃記錄<eng>Query Container Record</eng></a>
                     <a href="send_email.php" class="btn small">E-Mail功能
                        <eng>E-Mail Function</eng>
                    </a>
                 </div>
             </div>
             <div class="block record">
               <div class="block">
                 <div class="tablebox d01">
                     <ul>
                         <li>麥頭<eng>Shopping Mark</eng></li>
                         <li><input type="text" name="shipping_mark" v-model="shipping_mark"></li>
                         <li>櫃號<eng>Container Number</eng></li>
                         <li><input type="text" name="container_number" v-model="container_number"></li>
                     </ul>
                     <ul>
                         <li>空櫃重<eng>Empty Container Weight</eng></li>
                         <li><input type="text" name="estimate_weight" v-model="estimate_weight"></li>
                         <li>實際櫃重<eng>Actual Weight</eng></li>
                         <li><input type="text" name="actual_weight" v-model="actual_weight"></li>
                     </ul>
                     <ul>
                         <li>封條<eng>Seal</eng></li>
                         <li><input type="text" name="seal" v-model="seal"></li>
                         <li>S/O</li>
                         <li><input type="text" name="so" v-model="so"></li>
                     </ul>
                 </div>
                 <div class="tablebox d01">
                     <ul>
                         <li>船公司<eng>Shipping Line Company</eng></li>
                         <li><input type="text" name="ship_company" v-model="ship_company"></li>
                         <li>船名航次<eng>Shipping Line Boat</eng></li>
                         <li><input type="text" name="ship_boat" v-model="ship_boat"></li>
                     </ul>
                     <ul>
                         <!-- <li>領櫃<eng>Neck Cabinet</eng></li>
                         <li><input type="text" name="neck_cabinet" v-model="neck_cabinet"></li> -->
                         <li>出貨人<eng>Shipper</eng></li>
                         <li>
                            <select v-model="shipper">
                                <option value="0"></option>
                                <option value="1">盛盛</option>
                                <option value="2">中亞菲</option>
                                <option value="3">心心</option>
                            </select>
                        </li>
                         <li>領櫃人<eng>Broker</eng></li>
                         <li>
                             <select v-model="broker">
                                <option v-for="item in name" :value="item.name" :key="item.id">
                                {{ item.name }}
                                </option>
                            </select>
                         </li>
                     </ul>
                 </div>
                 <div class="tablebox lo01 withbtn">
                     <ul>
                         <li style="width: 5%">結關<eng>Date Sent</eng></li>
                         <li style="width: 14%"><date-picker id="date_sent"  @update-date="update_date_sent" v-model="date_sent" style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker><span class="text-danger" v-if="error_date_send" v-text="error_date_send"></span></li>
                         <li style="width: 5%">ETD</li>
                         <li style="width: 14%"><etd-date-picker id="etd_date"  @update-date="update_etd_date" v-model="etd_date" style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></etd-date-picker><span class="text-danger" v-if="error_etd_date" v-text="error_etd_date"></span></li>
                         <li style="width: 5%">O/B</li>
                         <li style="width: 14%"><ob-date-picker id="ob_date"  @update-date="update_ob_date" v-model="ob_date" style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></ob-date-picker><span class="text-danger" v-if="error_ob_date" v-text="error_ob_date"></span></li>
                         <li style="width: 5%">ETA</li>
                         <li style="width: 14%"><eta-date-picker id="eta_date"  @update-date="updat_eta_date" v-model="eta_date" style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></eta-date-picker><span class="text-danger" v-if="error_eta_date" v-text="error_eta_date"></span></li>
                         <li style="width: 5%">到倉日期<eng>Date C/R</eng></li>
                         <li style="width: 14%"><date-arrive-picker id="date_arrive"  @update-date="updat_date_arrive" v-model="date_arrive" style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-arrive-picker><span class="text-danger" v-if="error_date_arrive" v-text="error_date_arrive"></span></li>
                     </ul>
                 </div>  
                 <div class="tablebox d01">
                    <ul><!-- 配色底用 --></ul>
                     <ul>
                         <li>備註<eng>Remark</eng></li>
                         <li><input type="text" name="remark" v-model="remark"></li>
                     </ul>
                 </div>             
             </div>
             <div class="block">
                 <h6>選擇裝櫃貨物<eng>Select Goods to Load</eng></h6>
                 <!-- list -->
                 <div class="mainlist">
                     
                     <div class="tablebox d02">
                     <ul class="header">
                          <li>勾選<eng>Check</eng></li>
                          <li>收貨日期<eng>Date Receive</eng></li>
                          <li>收件人<eng>Company/Customer</eng></li>
                          <li>貨品名稱<eng>Description</eng></li>
                          <li>件數<eng>Quantity</eng></li>
                          <li>寄貨人<eng>Supplier</eng></li>
                          <li>重量<eng>Kilo</eng></li>
                          <li>材積<eng>Cuft</eng></li>
                          <li>台灣付<eng>Taiwan Pay</eng></li>
                          <li>代墊<eng>Courier/Payment</eng></li>
                          <li>備註<eng>Remark</eng></li>
                     </ul>
                     <ul v-for='(receive_record, index) in displayedPosts'>
                        <li>
                            <input type="checkbox" name="record_id" class="alone" @change="updateWeightAndCult" :value="receive_record.index" :true-value="1" v-model:checked="receive_record.is_checked">
                        </li>
                        <li>{{ receive_record.date_receive }}</li>
                        <li>{{ (receive_record.customer !== 'undefined' ) ? receive_record.customer.replace(/\\/g, '') : "" }}</li>
                        <li>{{ receive_record.description }}</li>
                        <li>{{ receive_record.quantity }}</li>
                        <li>{{ (receive_record.supplier !== 'undefined') ? receive_record.supplier.replace(/\\/g, '') : "" }}</li>
                        <li>{{ (receive_record.kilo == 0) ? "" : receive_record.kilo }}</li>
                        <li>{{ (receive_record.cuft == 0) ? "" : receive_record.cuft }}</li>
                        <li>{{ (receive_record.taiwan_pay == 1) ? "是 (yes)" : "否 (no)" }}</li>
                        <li>{{ (receive_record.courier_money == 0) ? "" : receive_record.courier_money }}</li>
                        <li><p v-html="receive_record.remark.replace(/(?:\r\n|\r|\n)/g, '&nbsp')"></p></li>
                     </ul>
                 </div>
                    
                 </div>
                 <!-- resume -->
                 <div class="tablebox s03">
                     <ul>
                         <li>已選擇</li>
                         <li>重量 <span>{{ Math.round((n_kilo + Number.EPSILON) * 100) / 100 }}</span>、材積 <span>{{ Math.round((n_cuft + Number.EPSILON) * 100) / 100 }}</span></li>
                         <li>Goods Selected</li>
                         <li>Kilo <span>{{ Math.round((n_kilo + Number.EPSILON) * 100) / 100 }}</span>, Cuft <span>{{ Math.round((n_cuft + Number.EPSILON) * 100) / 100 }}</span></li>
                     </ul>
                 </div>
                 
                 <div class="btnbox">
                     <a class="btn small" @click="toggleCheckbox();">全選 / 全取消<eng>All/Undo</eng></a>
                     <a class="btn small" @click="createLoadingRecord()" >儲存<eng>Save</eng></a>
                     <a class="btn small detail" @click="cancelReceiveRecord($event)">取消<eng>Cancel</eng></a>
                 </div>
             </div>
            </div>
          </div>
      </div>
    </div>
<!-- The Modal -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">編輯PROJECT</h5>
      </div>
      <div class="modal-body">
         content
      </div>
      <div class="modal-footer">
        <a class="btn" data-dismiss="modal">取消</a>
        <a class="btn">確認</a>
      </div>
    </div>
  </div>
</div>
<!-- The Modal -->
<!-- Bootstrap  -->
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="js/axios.min.js"></script> 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script type="text/javascript" src="js/loading.js" defer></script> 
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script> 
</body>
</html>
