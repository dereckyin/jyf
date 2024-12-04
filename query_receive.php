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

.bodybox .mask {
    position: fixed;
    background: rgba(0, 0, 0, 0.5);
    width: 100%;
    height: 100%;
    top: 0;
    z-index: 1;
    display: none;
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
<div class="mask" style="display:none"
         onclick="(function(){ return false;})();return false;">
    </div>
  <!-- header -->
  <header> 
  </header>
  <!-- header end -->
  <div id='receive_record'>
    <div class="mainContent">
      <h6>收貨記錄查詢</h6>
      <p><eng>(Query For Receiving Records)&nbsp;</eng></p>
      <!-- add form -->
      <div class="block">
        <div class="tablebox V s01">
         
          <ul>
			<li class="header"></li>
            <li>收貨日期 <eng>Date Receive</eng></li>
            <li>
              <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
              <date-picker id="date_start"  @update-date="updateDate"  style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
				&nbsp; &nbsp; ~ &nbsp; &nbsp;
				<date-picker id="date_end"  @update-date="updateDate"  style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
            </li>
          </ul>
          <ul>
			  <li class="header"></li>
            <li>收件人 <eng>Company / Customer</eng></li>
            <li>
                  <input type="text" class="goods_num" id="customer" maxlength="256" name="customer" style="width: calc(80% - 40px);"> 
              <button type="button" class="btn btn-primary" id="create-customer"><i class="fas fa-address-card"></i></button>
            </li>
          </ul>
          <ul>
			  <li class="header"></li>
            <li>寄貨人 <eng>Supplier</eng></li>
            <li>
              <input type="text" class="goods_num" id="supplier" maxlength="256" name="supplier"  style="width: calc(80% - 40px);"> 
              <button type="button" class="btn btn-primary" id="create-supplier"><i class="fas fa-address-card"></i></button>
            </li>
          </ul>
          <ul>
			  <li class="header"></li>
            <li>貨品名稱 <eng>Description</eng></li>
            <li>
              <input type="text" class="goods_num" id="description" maxlength="256" name="description"  style="width: calc(80% - 40px);">
            </li>
          </ul>
          <ul>
			  <li class="header"></li>
            <li>備註 <eng>Remark</eng></li>
            <li>
              <input type="text" class="goods_num" id="remark" maxlength="256" name="remark"  style="width: calc(80% - 40px);">
            </li>
          </ul>
          <ul>
			  <li class="header"></li>
            <li>排序方式 <eng>Sort by</eng></li>
            <li>
              <select id="sort" style="width: calc(30% - 40px);">
                <option value="" selected>Oldest On Top 舊的放上面</option>
                <option value="d">Latest On Top 新的放上面</option>
              </select>
            </li>
          </ul>
        </div>

        <div class="btnbox"><a class="btn" @click="query()" style="color:white;">查詢 <eng>Query</eng></a><a class="btn orange" @click="print()" style="color:white;">匯出 <eng>Print</eng></a></div>
      </div>


      <div class="block record show">
        <h6>收貨紀錄 <eng>Receiving Records</eng></h6>
        <div class="mainlist" style="overflow: auto; max-height: calc(100vh - 150px);">

          <div class="listheader" style="position: sticky; top: 0; left: 0; background-color: white;">
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
            <ul class="header" style="position: sticky; top: 40px; left: 0px;">
              <li><eng>Date Receive</eng> 收件日期</li>
              <li><eng>Company/Customer</eng>收件人</li>
              <li><eng>Picture</eng>照片</li>
              <li><eng>Description</eng> 貨品名稱</li>
              <li><eng>Quantity</eng> 件數</li>
              <li><eng>Supplier</eng> 寄貨人</li>
              <li><eng>Remark</eng> 備註</li>


              <li><eng>Date Sent</eng>結關日期</li>
              <li><eng>ETA</eng></li>
              <li><eng>Date C/R</eng> 貨櫃到倉日期</li>
              <li><eng>DR</eng> 單號</li>
              <li><eng>Date Pickup</eng> 提貨日期</li>
              <li><eng>Date Paid</eng> 付款日期</li>
            </ul>
            <ul v-for='(receive_record, index) in displayedPosts'>
              <li>{{ receive_record.date_receive }}</li>
              <li>{{ receive_record.customer }}</li>
              <li><div><i aria-hidden="true" class="fas fa-image" v-if="receive_record.pic != ''" @click="zoom_rec(receive_record.id)"></i></div></li>
              <li>{{ receive_record.description }}</li>
              <li>{{ receive_record.quantity }}</li>
              <li>{{ receive_record.supplier }}</li>
              <li><p v-html="receive_record.remark.replace(/(?:\r\n|\r|\n)/g, '&nbsp')"></p></li>
         

              <li>{{ receive_record.date_sent }}</li>
              <li :style="[receive_record.eta_date_his.length > 10 ? {'color': 'red'} : {'color': 'black'}]">{{ receive_record.eta_date }}</li>
              <li :style="[receive_record.date_arrive_his.length > 10 ? {'color': 'red'} : {'color': 'black'}]">{{ receive_record.date_arrive }}</li>
              <li>{{ receive_record.dr }}</li>
              <li>{{ receive_record.real_pick_time }}</li>
              <li>{{ receive_record.real_payment_time }}</li>
            </ul>
          </div>
        </div>
       </div>
    </div>



  <!-- The Modal -->
  <div class="modal" id="supModal">

    <div class="modal-dialog modal-lg">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">寄貨人 / Supplier</h4>
        </div>
        <div>
          <input class="form-control" v-model="s_keyword" placeholder="Search for...">
        </div>
        <!-- Modal body -->
        <div class="modal-body">
            <table class="table table-hover table-striped table-sm table-bordered" id="showUser">
              <thead>
                <tr>
                  <th><p>Checked</p>
                    <p>選擇</p></th>
                  <th><p>Supplier</p>
                    <p>寄貨人</p></th>
                </tr>
              </thead>
              <tbody id="s_contact">
                <tr v-for="(item, index) in s_filter" >
                      <td onclick="data(this)">
                        <input type="checkbox" class="form-check-input" :value="item.name">
                        <label class="form-check-label">&nbsp</label>
                      </td>
                      <td> {{item.name}} </td>
                    </tr>
              </tbody>
            </table>
        </div>
            <div class="modal-footer">
              <button type="button" class="btn orange" style="background-color: lightgrey;" onclick="toggleCheckboxSupplier();">全選 / 全取消<br>Select All / Undo</button>
              <button type="button" class="btn btn-primary" onclick="getSupplier()">確認<br>Confirm</button>
            </div> 
          </div>

                        <!-- Modal footer -->
                        <div class="modal" id="imgModal">
            <div v-if="this.selectedImage" max-width="85vw">
                <!-- <img :src="this.selectedImage" alt="" width="100%" @click.stop="this.selectedImage = null"> -->
                <template v-for="(item, index) in pic_preview">
                    <img v-if="item.type == 'FILE'" name="img_pre" class="img-responsive postimg" :src="'img/' + item.gcp_name" alt="" width="100%">
                    <img v-if="item.type == 'RECEIVE'" name="img_pre" class="img-responsive postimg" :src="url_ip + item.gcp_name" alt="" width="100%">
                    <hr>
                </template>
            </div>
        </div>

      <div class="modal" id="cusModal">

    <div class="modal-dialog modal-lg">
      <div class="modal-content"> 
        
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">收件人 / Company/Customer</h4>
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
                  <th><p>Company/Customer</p>
                    <p>收件人</p></th>
                </tr>
              </thead>
              <tbody id="c_contact">
                  <tr v-for="(item, index) in c_filter" >
                      <td onclick="data(this)">
                        <input type="checkbox" class="form-check-input" :value="item.name">
                        <label class="form-check-label">&nbsp</label>
                      </td>
                      <td> {{item.name}} </td>
                    </tr>
                  </tr>
              </tbody>
            </table>
        </div>
            <div class="modal-footer">
              <button type="button" class="btn orange" style="background-color: lightgrey;" onclick="toggleCheckbox();">全選 / 全取消<br>Select All / Undo</button>
              <button type="button" class="btn btn-primary" onclick="getCustomer()">確認<br>Confirm</button>
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
<script type="text/javascript" src="js/queryreceive_new.js?rand=<?php echo uniqid(); ?>" defer></script> 
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

    function getSupplier()
    {   
      console.log('getSupplier');

      var containers = '';

      var checkboxes = document.querySelector("#s_contact").querySelectorAll('input');

      var count = 0;
      for (var i = 0, element; element = checkboxes[i]; i++) {
        if(element.checked)
        {
          containers += element.value + "||";
          count++;
        }
      }

      if(count > 50)
      {
        alert('請選擇50個以內的寄貨人，目前選了' + count + '個\nDue to system workload, please select 50 or below suppliers for query. Now you already selected ' + count + ' suppliers.');
        return;
      }


      document.getElementsByName('supplier')[0].value = containers;

      $( "#supModal" ).dialog('close');
    };

    function getCustomer()
    {   
      console.log('getCustomer');

      var containers = '';

      var checkboxes = document.querySelector("#c_contact").querySelectorAll('input');

      // do not over 50 checkboxes checked
      var count = 0;
      for (var i = 0, element; element = checkboxes[i]; i++) {
        if(element.checked)
        {
          containers += element.value + "||";
          count++;
        }
      }

      if(count > 50)
      {
        alert('請選擇50個以內的收件人，目前選了' + count + '個\nDue to system workload, please select 50 or below companies/customers for query. Now you already selected ' + count + ' companies/customers.');
        return;
      }

      document.getElementsByName('customer')[0].value = containers;

      $( "#cusModal" ).dialog('close');
    };

    function toggleCheckbox()
    {
        var checkboxes = document.querySelector("#c_contact").querySelectorAll('input');

        for( var i = 0, element; element = checkboxes[i]; i++) {
          element.checked = (element.checked == 1 ? 0 : 1);
        }

        //element.checked = (element.checked == 1 ? 0 : 1);
      //$(".alone").prop("checked", !this.clicked);
      //this.clicked = !this.clicked;
    };

    function toggleCheckboxSupplier()
    {
        var checkboxes = document.querySelector("#s_contact").querySelectorAll('input');

        for( var i = 0, element; element = checkboxes[i]; i++) {
          element.checked = (element.checked == 1 ? 0 : 1);
        }

        //element.checked = (element.checked == 1 ? 0 : 1);
      //$(".alone").prop("checked", !this.clicked);
      //this.clicked = !this.clicked;
    };

    </script> 

</body>
</html>
