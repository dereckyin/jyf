<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
<title>中亞菲國際貿易有限公司</title>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

<!-- CSS -->
<link rel="stylesheet" href="css/jquery-ui/1.12.1/jquery-ui.css">
<link rel="stylesheet" href="css/bootstrap/4.3.1/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/datatables/jquery.dataTables.min.css"/>
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
      <h6>台灣付款</h6>
      <p><eng>(Taiwan Pay)&nbsp;</eng></p>
      <!-- add form -->
      <div class="block">
        <div class="tablebox V s01">
          <ul>
            <li class="header"></li>
            <li>
              收貨記錄篩選種類
            </li>
            <li><select name="taiwan_pay" id="taiwan_pay">
                <option value="1">台灣付 (Taiwan Pay)</option>
                <option value="0">代墊 (Courier Money)</option>
              </select></li>
            <li></li>
            <li></li>
          </ul>
          <ul>
            <li class="header"></li>
            <li>收貨日期 <eng>Date Receive</eng></li>
            <li>
              <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
              <date-picker id="date_start"  @update-date="updateDate" v-model="date_start" style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
				&nbsp; &nbsp; ~ &nbsp; &nbsp;
				<date-picker id="date_end"  @update-date="updateDate" v-model="date_end" style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
            </li>
            <li>
              
            </li>
            <li class="right"></li>
          </ul>
        </div>
        <div class="tablebox V s01">
          <ul>
            <!-- 留空 -->
          </ul>
          <ul>
            <li class="header"></li>
            <li>特定貨櫃 <eng>Container Number</eng></li>
            <li>
              <input type="text" class="goods_num" id="container_number"  name="container_number" v-model="container_number" style="width: calc(80% - 40px);"> 
              <button type="button" class="btn btn-primary" id="create-supplier"><i class="fas fa-address-card"></i></button>
            </li>
          </ul>
        </div>
        <div class="btnbox"><a class="btn" @click="query()" style="color:white;">查詢 <eng>Query</eng></a><a class="btn orange" @click="print()" style="color:white;">匯出 <eng>Print</eng></a></div>
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
              <li><eng>Date Receive</eng> 收件日期</li>
              <li><eng>Company/Customer</eng>收件人</li>
              <li><eng>Description</eng> 貨品名稱</li>
              <li><eng>Quantity</eng> 件數</li>
              <li><eng>Supplier</eng> 寄貨人</li>
              <li v-if="courier_query"><eng>Courior Money</eng> 代墊</li>
              <li><eng>Remark</eng> 備註</li>
            </ul>
            <ul v-for='(receive_record, index) in displayedPosts'>
              <li>{{ receive_record.date_receive }}</li>
              <li>{{ receive_record.customer }}</li>
              <li>{{ receive_record.description }}</li>
              <li>{{ receive_record.quantity }}</li>
              <li>{{ receive_record.supplier }}</li>
              <li v-if="courier_query">{{ receive_record.courier_money }}</li>
              <li><p v-html="receive_record.remark.replace(/(?:\r\n|\r|\n)/g, '&nbsp')"></p></li>
            </ul>
          </div>
        </div>
       </div>
    </div>



  <!-- The Modal -->
  <div class="modal" id="myModal">

    <div class="modal-dialog modal-lg">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">貨櫃名稱 (Container Number)</h4>
        </div>
        <div>
          <input class="form-control" v-model="c_keyword" placeholder="Search for...">
        </div>
        <!-- Modal body -->
        <div class="modal-body">
            <table class="table table-hover table-striped table-sm table-bordered" id="showUser">
              <thead>
                <tr>
                  <th><p>Checked</p>
                    <p>選擇</p></th>
                  <th><p>Container Number</p>
                    <p>櫃號</p></th>
                </tr>
              </thead>
              <tbody id="contact">
              </tbody>
            </table>
        </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="getContainer()">Confirm / 確認</button>
            </div> 
          </div>

                        <!-- Modal footer -->
      
        </div>
      </div>
    </div>
  </div>
  </div>




</div>

</div>
<script src="js/npm/vue/dist/vue.js"></script> 
<script src="js/axios/0.19.2/axios.js"></script> 
<script src="js/jquery/1.12.4/jquery-1.12.4.js"></script> 
<script src="js/jquery/ui/1.12.1/jquery-ui.js"></script> 
<script type="text/javascript" src="js/datatables/datatables.min.js"></script> 
<script src="js/npm/sweetalert2@9.js"></script> 
<script src="js/jquery/validate/jquery.validate.js"></script>
<script type="text/javascript" src="js/taiwanpay.js" defer></script> 
<script defer src="js/a076d05399.js"></script> 

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
      e.querySelectorAll('input')[0].checked = !e.querySelectorAll('input')[0].checked;
    };

    function getContainer()
    {   
      console.log('getContainer');

      var containers = '';

      var checkboxes = document.querySelector("#contact").querySelectorAll('input');

      for (var i = 0, element; element = checkboxes[i]; i++) {
        if(element.checked)
          containers += element.value + ",";
          //work with element
      }

      document.getElementsByName('container_number')[0].value = containers;

      $( "#myModal" ).dialog('close');
    };

    </script> 

</body>
</html>
