<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
<title>中亞菲國際貿易有限公司</title>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

<!-- CSS -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

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
});


</script>

</head>

<body>
<div class="bodybox"> 
  <!-- header -->
  <header> 
  </header>
  <!-- header end -->
  <div id='receive_record'>
    <div class="mainContent">
      <h6>階段 - 收貨</h6>
      <p><eng>(Receiving Record )&nbsp;</eng></p>
      <!-- add form -->
      <div class="block" v-if="!isEditing">
        <div class="tablebox V s01">
          <ul>
            <li class="header">送件資訊</li>
            <li>收貨日期 <eng>Date Receive</eng></li>
            <li>
              <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
              <date-picker id="adddate"  @update-date="updateDate" v-model="date_receive" style="width: calc(40% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
              <span class="text-danger" v-if="error_date_receive" v-text="error_date_receive"></span>
            </li>
            <li></li>
            <li class="right"><a class="btn small before-micons detail" style="color:white;">收貨紀錄</a></li>
          </ul>
          <ul>
            <li class="header"></li>
            <li>收件人 <eng>Company/customer</eng></li>
            <li>
              <input type="text" name="customer" v-model="customer" maxlength="256" style="width: calc(65% - 40px);">
              <button type="button" class="btn btn-primary" id="create-user"><i class="fas fa-address-card"></i></button>
            </li>
            <li>E-Mail</li>
            <li>
              <input type="text" name="email" v-model="email">
              <span class="text-danger" v-if="error_email" v-text="error_email"></span>
            </li>
          </ul>
        </div>
        <div class="tablebox V s01">
          <ul>
            <li class="header"></li>
            <li>貨品名稱 <eng>Description</eng></li>
            <li class="g01">
              <input type="text" class="goods_name"  name="description" v-model="description" style="width: calc(70% - 40px);">
              <span>件數 <eng>Quantity</eng></span>
              <input type="text" class="goods_num"  name="quantity" v-model="quantity">
              <span class="text-danger" v-if="error_quantity" v-text="error_quantity"></span>
            </li>
          </ul>
        </div>
        <div class="tablebox V s01">
          <ul>
            <li class="header">寄件人資訊</li>
            <li>寄件人 <eng>Supplier</eng></li>
            <li>
              <input type="text" name="supplier" v-model="supplier" maxlength="256" style="width: calc(80% - 40px);">
              <button type="button" class="btn btn-primary" id="create-supplier"><i class="fas fa-address-card"></i></button>
              <span class="text-danger" v-if="error_customer" v-text="error_customer"></span>
            </li>
            <li>照片</li>
            <li>
              <input type="file" accept="image/*" id="file" ref="file" style="color: black;" v-on:change="onChangeFileUpload()">
            </li>
          </ul>
          <ul>
            <li class="header"></li>
            <li>重量 <eng>Kilo</eng></li>
            <li>
              <input type="text" name="kilo" v-model="kilo">
              <span class="text-danger" v-if="error_kilo" v-text="error_kilo"></span>
            </li>
            <li>材積 <eng>Cuft</eng></li>
            <li>
              <input type="text" name="cuft" v-model="cuft">
              <span class="text-danger" v-if="error_cuft" v-text="error_cuft"></span>
            </li>
          </ul>
          <ul>
            <li class="header"></li>
            <li></li>
            <li>
              <input type="checkbox" id="A" :true-value="1"  name="taiwan_pay" v-model:checked="taiwan_pay" @change="updateTaiwanPay">
              <label for="A">&nbsp;台灣付運費 <eng>Taiwan Pay</eng></label>
            </li>
            <li>代墊 <eng>Courier/payment</eng></li>
            <li>
              <input type="text" class="payment" name="courier_money" v-model="courier_money">
              元(NT.)</li>
              <span class="text-danger" v-if="error_courier_money" v-text="error_courier_money"></span>
          </ul>
        </div>
        <div class="tablebox V s01">
          <ul>
            <!-- 留空 -->
          </ul>
          <ul>
            <li class="header"></li>
            <li>備註 <eng>Remark</eng></li>
            <li>
              <textarea name="" id="" name="remark" v-model="remark">
              </textarea>
            </li>
          </ul>
        </div>
        <div class="btnbox"><a class="btn" @click="createReceiveRecord()" style="color:white;">儲存 <eng>Save</eng></a><a class="btn orange" @click="createReceiveRecordMail()" style="color:white;">儲存 <eng>Save + </eng><i class="before-micons mail"></i></a></div>
      </div>


      <!-- edit form -->
      <div class="block" v-else>
        <div class="tablebox V s01">
          <ul>
            <li class="header">送件資訊</li>
            <li>收貨日期 <eng>Date Receive</eng></li>
            <li>
              <edit-date-picker id="adddate1" @update-date="updateDate"  v-model="record.date_receive"  style="width: calc(40% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></edit-date-picker>
              <span class="text-danger" v-if="error_date_receive" v-text="error_date_receive"></span>
            </li>
            <li></li>
            <li class="right"><a class="btn small before-micons detail" style="color:white;">收貨紀錄</a></li>
          </ul>
          <ul>
            <li class="header"></li>
            <li>收件人 <eng>Company/customer</eng></li>
            <li>
              <input type="text" name="customer" maxlength="256" v-model="record.customer" style="width: calc(65% - 40px);">
              <button type="button" class="btn btn-primary" id="create-user1"><i class="fas fa-address-card"></i></button>
            </li>
            <li>E-Mail</li>
            <li>
              <input type="text" name="email"  v-model="record.email">
            </li>
          </ul>
        </div>
        <div class="tablebox V s01">
          <ul>
            <li class="header"></li>
            <li>貨品名稱 <eng>Description</eng></li>
            <li class="g01">
              <input type="text" class="goods_name"  name="description" v-model="record.description" style="width: calc(70% - 40px);">
              <span class="text-danger" v-if="error_email" v-text="error_email"></span>
              <span>件數 <eng>Quantity</eng></span>
              <input type="text" class="goods_num"  name="quantity" v-model="record.quantity">
              <span class="text-danger" v-if="error_quantity" v-text="error_quantity"></span>
            </li>
          </ul>
        </div>
        <div class="tablebox V s01">
          <ul>
            <li class="header">寄件人資訊</li>
            <li>寄件人 <eng>Supplier</eng></li>
            <li>
              <input type="text" name="supplier" maxlength="256" v-model="record.supplier" style="width: calc(80% - 40px);">
              <button type="button" class="btn btn-primary" id="create-supplier1"><i class="fas fa-address-card"></i></button>
              <span class="text-danger" v-if="error_customer" v-text="error_customer"></span>
            </li>
            <li>照片</li>
            <li>
              <div><i class="fas fa-image" v-if="record.picname" @click="zoom(record.picname)"></i><input type="file" accept="image/*" id="file" ref="file" style="color: black;" v-on:change="onChangeFileUpload()"></div>
            </li>
          </ul>
          <ul>
            <li class="header"></li>
            <li>重量 <eng>Kilo</eng></li>
            <li>
              <input type="text" name="kilo" v-model="record.kilo">
              <span class="text-danger" v-if="error_kilo" v-text="error_kilo"></span>
            </li>
            <li>材積 <eng>Cuft</eng></li>
            <li>
              <input type="text" name="cuft" v-model="record.cuft">
              <span class="text-danger" v-if="error_cuft" v-text="error_cuft"></span>
            </li>
          </ul>
          <ul>
            <li class="header"></li>
            <li></li>
            <li>
              <input type="checkbox" id="B" :true-value="1"  v-model:checked="record.taiwan_pay" @change="updateEditTaiwanPay" name="taiwan_pay" >
              <label for="B">&nbsp;台灣付運費 <eng>Taiwan Pay</eng></label>
            </li>
            <li>代墊 <eng>Courier/payment</eng></li>
            <li>
              <input type="text" class="payment" name="courier_money" v-model="record.courier_money">
              元(NT.)</li>
              <span class="text-danger" v-if="error_courier_money" v-text="error_courier_money"></span>
          </ul>
        </div>
        <div class="tablebox V s01">
          <ul>
            <!-- 留空 -->
          </ul>
          <ul>
            <li class="header"></li>
            <li>備註 <eng>Remark</eng></li>
            <li>
              <textarea name="" name="remark" v-model="record.remark">
                            </textarea>
            </li>
          </ul>
        </div>

        <div class="btnbox"><a class="btn" @click="cancelReceiveRecord($event)" style="color:white;">取消 <eng>Cancel</eng></a><a class="btn" @click="editReceiveRecord($event)" style="color:white;">儲存 <eng>Save</eng></a><a class="btn orange" @click="editReceiveRecordMail($event)" style="color:white;">儲存 <eng>Save + </eng><i class="before-micons mail"></i></a></div>
      </div>



      <div class="block record show">
        <h6>收貨紀錄 <eng>Receiving Records</eng></h6>
        <div class="mainlist">

          <div class="listheader">
            <div class="pageblock" style="float:right;"> Page Size: 
              <select v-model="perPage">
                <option v-for="item in inventory" :value="item" :key="item.id">
                  {{ item.name }}
                </option>
              </select> Page: 
              <div class="pageblock">
                <a class="first micons" @click="page=1">first_page</a>
                <a class="prev micons" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">chevron_left</a>
                <select v-model="page">
                  <option v-for="pg in pages" :value="pg">
                    {{ pg }}
                  </option>
                </select>

                <a class="next micons" :disabled="page == pages.length" @click="page++">chevron_right</a>
                <a class="last micons" @click="page=pages.length">last_page</a>
              </div>
            </div>
              <!-- <div class="searchblock" style="float:left;">搜尋<input type="text"></div> -->
          </div>

          <div class="tablebox s02">
            <!-- <table class="table table-hover table-striped table-sm table-bordered" id="showUser1" ref="showUser1"> -->
            <ul class="header">
              <li>Check 勾選</li>
              <li><eng>Date Receive</eng> 收件日期</li>
              <li><eng>Company/Customer</eng>收件人</li>
              <li><eng>Picture</eng> 照片</li>
              <li><eng>Description</eng> 貨品名稱</li>
              <li><eng>Quantity</eng> 件數</li>
              <li><eng>Kilo</eng> 重量</li>
              <li><eng>Cuft</eng> 材積</li>
              <li><eng>Supplier</eng> 寄貨人</li>
              <li><eng>Taiwan Pay</eng> 台灣付</li>
              <li><eng>Courier/Payment</eng> 代墊</li>
              <li><eng>Remark</eng> 備註</li>
            </ul>
            <ul v-for='(receive_record, index) in displayedPosts'>
              <li>
                <input type="checkbox" name="record_id" class="alone" :value="receive_record.index" :true-value="1" v-model:checked="receive_record.is_checked">
              </li>
              <li>{{ receive_record.date_receive }}</li>
              <li>{{ (receive_record.customer !== 'undefined' ) ? receive_record.customer.replace(/\\/g, '') : "" }}</li>
              <li><i class="fas fa-image" v-if="receive_record.picname" @click="zoom(receive_record.picname)"></i></li>
              <li>{{ receive_record.description }}</li>
              <li>{{ receive_record.quantity }}</li>
              <li>{{ (receive_record.kilo == 0) ? "" : receive_record.kilo }}</li>
              <li>{{ (receive_record.cuft == 0) ? "" : receive_record.cuft }}</li>
              <li>{{ (receive_record.supplier !== 'undefined') ? receive_record.supplier.replace(/\\/g, '') : "" }}</li>
              <li>{{ (receive_record.taiwan_pay == 1) ? "是 (yes)" : "否 (no)" }}</li>
              <li>{{ (receive_record.courier_money == 0) ? "" : receive_record.courier_money }}</li>
              <li><p v-html="(receive_record.remark !== 'undefined') ? receive_record.remark.replace(/(?:\r\n|\r|\n)/g, '&nbsp') : '' "></p></li>
            </ul>

          </div>
          <div class="tablebox s03">
            <ul>
              <li>已收</li>
              <li>重量 <span>{{ Math.round((r_kilo + Number.EPSILON) * 100) / 100 }}</span>, 材積 <span>{{ Math.round((r_cuft + Number.EPSILON) * 100) / 100 }}</span></li>
              <li>未收</li>
              <li>重量 <span>{{ Math.round((n_kilo + Number.EPSILON) * 100) / 100 }}</span>, 材積 <span>{{ Math.round((n_cuft + Number.EPSILON) * 100) / 100 }}</span> </li>
              <li>總和</li>
              <li>重量<span>{{ Math.round((n_kilo + r_kilo + Number.EPSILON) * 100) / 100 }}</span>, 材積<span>{{ Math.round((n_cuft + r_cuft + Number.EPSILON) * 100) / 100 }}</span></li>
            </ul>
        <!--  </div>
          <div class="tablebox s03"> -->
            <ul>
              <li>Goods Received</li>
              <li>Kilo <span>{{ Math.round((r_kilo + Number.EPSILON) * 100) / 100 }}</span>, Cuft <span>{{ Math.round((r_cuft + Number.EPSILON) * 100) / 100 }}</li>
              <li>Goods Yet Received</li>
              <li>Kilo <span>{{ Math.round((n_kilo + Number.EPSILON) * 100) / 100 }}</span>, Cuft <span>{{ Math.round((n_cuft + Number.EPSILON) * 100) / 100 }}</span></li>
              <li>Goods Total</li>
              <li>Kilo <span>{{ Math.round((n_kilo + r_kilo + Number.EPSILON) * 100) / 100 }}</span>, Cuft <span>{{ Math.round((n_cuft + r_cuft + Number.EPSILON) * 100) / 100 }}</span></li>
            </ul>
          </div>
          <div class="btnbox"> <a class="btn small selbtn" style="color:white;" @click="toggleCheckbox();">全選 / 全取消
            <p>All/Undo</p>
            </a> <a class="btn small" style="color:white;" @click="editRecord()">修改
            <p>Edit</p>
            </a> <a class="btn small" style="color:white;" @click="deleteRecord()">刪除
            <p>Delete</p>
            </a> <a class="btn small" style="color:white;" v-bind:href="pageUrl">匯出
            <p>Export</p>
            </a> </div>
        </div>
       </div>
    </div>



  <!-- The Modal -->
  <div class="modal" id="myModal">

    <div class="modal-dialog modal-lg">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">通訊錄</h4>
        </div>
        <div>
          <input class="form-control" v-model="c_keyword" placeholder="Search for...">
        </div>

        <div class="form-check" style="padding: 10px;">
          <input type="checkbox" class="form-check-input" id="c_mark">
          <label class="form-check-label" for="c_mark">Add Mark/加入麥頭</label>

          <input type="checkbox" class="form-check-input" id="c_tel">
          <label class="form-check-label" for="c_tel">Add Tel/加入電話</label>
        </div>
        <!-- Modal body -->
            <table class="table table-hover table-striped table-sm table-bordered" id="showUser">
              <thead>
                <tr>
                  <th><p>Mark</p>
                    <p>麥頭</p></th>
                  <th><p>Company/Customer</p>
                    <p>收件人</p></th>
                  <th><p>Phone</p>
                    <p>電話</p></th>
                  <th><p>Fax</p>
                    <p>傳真</p></th>
                  <th><p>E-mail</p>
                    <p>E-mail</p></th>
                </tr>
              </thead>
              <tbody id="contact">
              </tbody>
            </table>
          </div>
  
        </div>
      </div>
      
      <!-- Modal footer -->
      <!--<div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">select</button>
      </div> -->


    <!-- The Modal -->
  <div class="modal" id="supModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">通訊錄</h4>
        </div>
        <div>
          <input class="form-control" v-model="s_keyword" placeholder="Search for...">
        </div>
        <div class="form-check" style="padding: 10px;">

          <input type="checkbox" class="form-check-input" id="s_tel">
          <label class="form-check-label" for="s_tel">Add Tel/加入電話</label>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-sm table-bordered" id="showUser">
              <thead>
                <tr>
                  <th><p>Supplier</p>
                    <p>寄件人</p></th>
                  <th><p>Phone</p>
                    <p>電話</p></th>
                  <th><p>Fax</p>
                    <p>傳真</p></th>
                  <th><p>Company Title</p><p>抬頭</p>
                    </th>
                </tr>
              </thead>
              <tbody id="supplier">
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Modal footer -->
      <!--<div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">select</button>
      </div> -->
    </div>
  </div>
  <!-- The Modal -->

      
    </div>
  </div>
  <!-- The Modal -->






  </div>

  <div class="modal" id="imgModal">
    <div v-if="this.selectedImage" max-width="85vw">
     <!-- <img :src="this.selectedImage" alt="" width="100%" @click.stop="this.selectedImage = null"> -->
     <img name="img_pre" class="img-responsive postimg" id="img_pre" alt="" width="100%">
      <hr>
    </div>
  </div>


</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script> 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script type="text/javascript" src="js/main.js" defer></script> 
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script> 

<!-- jQuery和js載入 --> 
<script>
  /*
    $( function() {
      $("#datepicker").datepicker({ 
        dateFormat: "yy/mm/dd",
        showOn: "button",
          buttonImage: "images/calendar.png",
          buttonImageOnly: true,
          buttonText: "" }).val()

    } );
*/

    function data(e)
      {
        is_mark_checked = document.querySelector("#c_mark").checked;
        is_phone_checked = document.querySelector("#c_tel").checked;

        c_string = '';

        if(is_mark_checked)
          c_string = e.querySelectorAll('td')[0].textContent + ' ';

        c_string = c_string + e.querySelectorAll('td')[1].textContent + ' ';

        if(is_phone_checked)
          c_string = c_string + e.querySelectorAll('td')[2].textContent + ' ';

        if(!mainState.isEditing)
        {
          //document.querySelector("input[name=customer]").value=e.querySelectorAll('td')[0].textContent;
          mainState.customer = c_string.trim();
          //document.querySelector("input[name=email]").value=e.querySelectorAll('td')[3].textContent;
          mainState.email = e.querySelectorAll('td')[3].textContent;
        }
        else
        {
          mainState.record.customer = c_string.trim();
          mainState.record.email = e.querySelectorAll('td')[3].textContent;
        }

        mainState.s_keyword = '';
        mainState.c_keyword = '';

        $( "#myModal" ).dialog('close');
      };

      function data1(e)
      {
        is_phone_checked = document.querySelector("#s_tel").checked;

        s_string = e.querySelectorAll('td')[0].textContent + ' ';

        if(is_phone_checked)
          s_string = s_string + e.querySelectorAll('td')[1].textContent + ' ';

        if(!mainState.isEditing)
        {
          //document.querySelector("input[name=customer]").value=e.querySelectorAll('td')[0].textContent;
          mainState.supplier = s_string.trim();
          //document.querySelector("input[name=email]").value=e.querySelectorAll('td')[3].textContent;
          //mainState.email = e.querySelectorAll('td')[3].textContent;
        }
        else
        {
          mainState.record.supplier = s_string.trim();
          //mainState.record.email = e.querySelectorAll('td')[3].textContent;
        }

        mainState.s_keyword = '';
        mainState.c_keyword = '';

        $( "#supModal" ).dialog('close');
      };


    </script> 

</body>
</html>
