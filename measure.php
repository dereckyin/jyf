<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

<!-- CSS -->
<link rel="stylesheet" href="js/bootstrap/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<style type="text/css">
  hr {
    border: none;
    height: 1px;
    size: 1;

    /* Set the hr color */
    color: #333; /* old IE */
    background-color: #333; /* Modern Browsers */
}
</style>


<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>


<script>
$(function(){
    $('header').load('include/header_admin.php');
})
</script>
</head>

<body>
    <div class="bodybox">
        <!-- header -->
        <header></header>
        <!-- header end -->
        <div class="mainContent" id="measure">
          <h6>未領貨櫃記錄 <eng>On the Way Container Records</eng></h6>
          <div class="block">
                <div class="btnbox">
                     <a class="btn small detail" href="measure.php">丈量<eng>Measurement</eng></a>
                     <a class="btn small" href="payment.php">提貨與付款<eng>Pickup/Payment</eng></a>
                     <a class="btn small" href="taiwanpay.php">台灣付款<eng>Taiwan Pay</eng></a>
                 </div>
             </div>
			<div class="block record show">
               <h6>未領貨櫃記錄 <eng>On the Way Container Records</eng></h6>
               <!-- list -->
               <div class="mainlist">
                   
                   <div class="tablebox d02">
                   <ul class="header">
                        <li><eng>Check</eng>勾選</li>
                        <li><eng>Container Number</eng>櫃號</li>
                        <li>S/O</li>
                        <li><eng>Shipping Line Company</eng>船公司</li>
                        <li><eng>Date Send</eng>結關日期</li>
                        <li>ETA</li>
                   </ul>
                   <ul v-for='(record, index) in displayedLoading'>
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.id" :true-value="1" v-model:checked="record.is_checked" @change="preventAddRecord()"></li>
                        <li>{{ record.container_number }}</li>
                        <li>{{ record.so }}</li>
                        <li>{{ record.ship_company }}</li>
                        <li>{{ record.date_sent }}</li>
                        <li>{{ record.etd_date }}</li>
                   </ul>
                   
               </div>
                   
               </div>
               <div class="btnbox">
                   <a class="btn small" v-bind:href="pageUrl">貨物明細匯出<eng>Export to Excel, Pdf, Print</eng></a>
                   <a class="btn small" @click="addReceiveRecords()" v-if="!isAdding">新增丈量記錄<eng>Create New Measuremnt Record</eng></a>
                   <a class="btn small" @click="cancelReceiveRecords()" v-if="isAdding">取消丈量記錄<eng>Cancel New Measuremnt Record</eng></a>
               </div>
           </div>
			
			<div class="block record show">
               <h6>丈量記錄 <eng>Measurement Records</eng></h6>
               <!-- list -->
               <div class="mainlist">
                   
                   <div class="tablebox d02">
                   <ul class="header">
                        <li><eng>Check</eng>勾選</li>
                        <li><eng>Date Encoded</eng>丈量日期</li>
                        <li><eng>Date C/R</eng>貨櫃到倉日期</li>
                        <li><eng>quantity of Containers</eng>貨櫃數量</li>
                        <li><eng>Container Number(s)</eng>櫃號</li>
                        <li><eng>Remark</eng>備註</li>
                   </ul>
                   <ul v-for='(record, index) in displayedMeasure'>
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.id" :true-value="1" v-model:checked="record.is_checked"></li>
                        <li>
                          {{ record.date_encode }}
                        </li>
                        <li>
                          {{ record.date_arrive }}
                        </li>
                        <li>{{ record.qty }}</li>
                        <li>{{ record.container }}</li>
                        <li>{{ record.remark }}</li>
                   </ul>
               </div>
                  
               </div>
               <div class="btnbox">
                   <a class="btn small" @click="deleteMeasure()" v-if="!isAdding">刪除<eng>Delete</eng></a>
				            <a class="btn small" @click="editMeasureRecord()" v-if="!isAdding">修改<eng>Edit</eng></a>
               </div>
           </div>
			
			
           <div class="block">
               <div class="tablebox d01">
                   <ul>
                       <li>丈量日期<eng>Date Encoded</eng></li>
                       <li><date-encode id="date_encode"  @update-date="update_date_encode" v-model="date_encode" style="width: calc(40% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-encode>
                          <span class="text-danger" v-if="error_date_encode" v-text="error_date_encode"></span>
                        </li>
					   <li>貨櫃到倉日期<eng>Date C/R   (Or Date Container arrived Maila)</eng></li>
					   <li><date-cr id="date_cr"  @update-date="update_date_cr" v-model="date_cr" style="width: calc(40% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-cr>
                          <span class="text-danger" v-if="error_date_cr" v-text="error_date_cr"></span></li>
					   <li>匯率<eng>Currency Rate</eng></li>
                       <li><input type="text" name="currency_rate" v-model="currency_rate"></li>
                   </ul>
               </div>
               <div class="tablebox d01">
                   <ul>
                       <li>貨櫃數量<eng>quantity of Containers</eng></li>
                       <li><input type="text" name="measure_qty" v-model="measure_qty"></li>
                       <li>櫃號<eng>Containers number(s)</eng></li>
                       <li><input type="text" name="measure_container" v-model="measure_container"></li>
                   </ul>
                   <ul>
                       <li>備註<eng>Remark</eng></li>
                       <li><input type="text" name="remark" v-model="remark"></li>
                   </ul>
			   </div>
               <!-- <div class="btnbox"><a class="btn">儲存<eng>Save</eng></a><a class="btn" data-toggle="modal" data-target="#Modal"><eng>Modal Sample</eng></a></div> -->
           </div>
           <div class="block record show">
               <h6>貨物內容<eng>Container Content</eng></h6>
               <!-- list -->
               <div class="mainlist">
                   
                   <div class="tablebox d02">
                   <ul class="header">
                        <li><eng>Date Receive</eng>收貨日期</li>
                        <li><eng>Company/Customer</eng>收件人</li>
                        <li><eng>Description</eng>貨品名稱</li>
                        <li><eng>quantity</eng>件數</li>
                        <li><eng>Kilo</eng>重量</li>
                        <li><eng>Cuft</eng>才積</li>
                        <li><eng>Price per Kilo</eng>重量單價</li>
                        <li><eng>Price per Cuft</eng>才積單價</li>
					    <li><eng>Courier/Payment</eng>代墊</li>
					    <li><eng>Supplier</eng>寄貨人</li>
					    <li><eng>Remark</eng>備註</li>
                   </ul>
                   <ul v-for='(record, index) in displayedReceive'>
                        <li><p v-html="record.date_receive.split('<br>').join('<hr />').split('&nbsp').join('')"></p></li>
                        <li><p v-html="record.customer.split('<br>').join('<hr />').split('&nbsp').join('')"></p></li>
                        <li><p v-html="record.description.split('<br>').join('<hr />').split('&nbsp').join('')"></p></li>
                        <li><p v-html="record.quantity.split('<br>').join('<hr />').split('&nbsp').join('')"></p></li>
                        <li><input type="text" class="payment" name="kilo" v-model="record.kilo"></li>
                        <li><input type="text" class="payment" name="cuft" v-model="record.cuft"></li>
                        <li><input type="text" class="payment" name="price_kilo" v-model="record.price_kilo"></li>
					              <li><input type="text" class="payment" name="price_cuft" v-model="record.price_cuft"></li>
                        <li><p v-html="record.courier_money.split('<br>').join('<hr />').split('&nbsp').join('')"></p></li>
                        <li><p v-html="record.supplier.split('<br>').join('<hr />').split('&nbsp').join('')"></p></li>
                        <li><p style="width:80px; white-space:nowrap; text-overflow: ellipsis; overflow: hidden;" v-html="record.remark.split('<br>').join('<hr />').split('&nbsp').join('')"></p></li>
                   </ul>
               </div>
                   
               </div>
               <div class="btnbox">
                   <a class="btn small" @click="save_measurement()" v-if="!isEditing">儲存<eng>Save</eng></a>
                   <a class="btn small" @click="cancelReceiveRecords()" v-if="isAdding">取消<eng>Cancel</eng></a>

                   <a class="btn small" @click="updateMeasureRecord()" v-if="isEditing">修改<eng>Edit</eng></a>

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
<script src="js/bootstrap/popper.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js"></script> 
<script src="js/axios.min.js"></script> 
<script type="text/javascript" src="js/measure.js" defer></script> 

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 

</body>
</html>
