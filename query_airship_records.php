<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
    <title>中亞菲國際貿易有限公司</title>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

    <!-- CSS -->
    <link rel="stylesheet" href="css/jquery-ui/1.12.1/jquery-ui.css">
    <link rel="stylesheet" href="css/bootstrap/4.3.1/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>


    <link rel="stylesheet" type="text/css" href="css/case.css"/>


    <link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
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
            
        table.tb_airship {
            width: 100%;
        }

        table.tb_airship th, table.tb_airship td {
            padding: 8px;
        }

        .table > :not(:first-child) {
            border-top: none;
        }

        table.tb_airship tr.deleted td, table.tb_airship tr.deleted td > div > label {
            text-decoration: line-through;
            text-decoration-color: red;
        }

        table.tb_airship thead:first-of-type tr th {
            font-size: 14px;
            background-color: #e9ecef;
        }

        table.tb_airship thead:first-of-type tr th cht {
            display: block;
            font-size: 13px;
        }

        table.tb_airship thead tr th {
            min-width: 170px;
        }

        table.tb_airship thead tr > th:nth-of-type(4), table.tb_airship thead tr > th:nth-of-type(5), table.tb_airship thead tr > th:nth-of-type(17) {
            min-width: 260px;
        }

        table.tb_airship thead tr th:nth-of-type(3), table.tb_airship thead tr th:nth-of-type(8) {
            min-width: 200px;
        }

        table.tb_airship thead tr th:nth-of-type(1) {
            min-width: 100px;
        }

        table.tb_airship tbody tr td i {
            font-size: 20px;
            margin: 8px;
            cursor: pointer;
            display: block;
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
            <h6>空運記錄查詢</h6>
            <p>
                <eng>(Query For Airship Records)&nbsp;</eng>
            </p>
            <!-- add form -->
            <div class="block">
                <div class="tablebox V s01">

                    <ul>
                        <li class="header"></li>
                        <li>收件日期
                            <eng>Date Received</eng>
                        </li>
                        <li>
                            <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
                            <date-picker id="date_start" @update-date="date_start"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                            &nbsp; &nbsp; ~ &nbsp; &nbsp;
                            <date-picker id="date_end" @update-date="date_end"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                        </li>
                    </ul>

                    <ul>
                        <li class="header"></li>
                        <li>付款日期
                            <eng>Date Paid</eng>
                        </li>
                        <li>
                            <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
                            <date-picker id="pay_start" @update-date="pay_start"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                            &nbsp; &nbsp; ~ &nbsp; &nbsp;
                            <date-picker id="pay_end" @update-date="pay_end"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                        </li>
                    </ul>

                    <ul>
                        <li class="header"></li>
                        <li>班機日期
                            <eng>Flight Date</eng>
                        </li>
                        <li>
                            <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
                            <date-picker id="flight_start" @update-date="flight_start"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                            &nbsp; &nbsp; ~ &nbsp; &nbsp;
                            <date-picker id="flight_end" @update-date="flight_end"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                        </li>
                    </ul>

                    <ul>
                        <li class="header"></li>
                        <li>抵達客人住址時間
                            <eng>Time Delivery Arrived
                        </li>
                        <li>
                            <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
                            <date-picker id="arrive_start" @update-date="arrive_start"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                            &nbsp; &nbsp; ~ &nbsp; &nbsp;
                            <date-picker id="arrive_end" @update-date="arrive_end"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                        </li>
                    </ul>

                    <ul>
                        <li class="header"></li>
                        <li>客戶名
                            <eng>Customer</eng>
                        </li>
                        <li>
                            <input type="text" class="goods_num" id="customer" maxlength="256" name="customer"
                                   style="width: calc(80% - 40px);">
                            <button type="button" class="btn btn-primary" id="create-customer"><i
                                    class="fas fa-address-card"></i></button>
                        </li>
                    </ul>
                    <ul>
                        <li class="header"></li>
                        <li>寄貨人
                            <eng>Supplier</eng>
                        </li>
                        <li>
                            <input type="text" class="goods_num" id="supplier" maxlength="256" name="supplier"
                                   style="width: calc(80% - 40px);">
                            <button type="button" class="btn btn-primary" id="create-supplier"><i
                                    class="fas fa-address-card"></i></button>
                        </li>
                    </ul>
                    <ul>
                        <li class="header"></li>
                        <li>貨品名稱
                            <eng>Description</eng>
                        </li>
                        <li>
                            <input type="text" class="goods_num" id="description" maxlength="256" name="description"
                                   style="width: calc(80% - 40px);">
                        </li>
                    </ul>

                    <ul>
                        <li class="header"></li>
                        <li>排序方式
                            <eng>Sort by</eng>
                        </li>
                        <!-- 舊的判斷標準是使用 收件日期 -->
                        <li>
                            <select id="sort" style="width: calc(30% - 40px);">
                                <option value="" selected>Oldest On Top 舊的放上面</option>
                                <option value="d">Latest On Top 新的放上面</option>
                            </select>
                        </li>
                    </ul>
                </div>

                <div class="btnbox"><a class="btn" @click="query()" style="color:white;">查詢
                    <eng>Query</eng>
                </a><a class="btn orange" @click="print()" style="color:white;">匯出
                    <eng>Print</eng>
                </a></div>
            </div>


            <div class="block record show">
                <h6>空運記錄
                    <eng>Airship Records</eng>
                </h6>
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

                                <a class="next micons" :disabled="page == pages.length"
                                   @click="page++">chevron_right</a>
                                <a class="last micons" @click="page=pages.length">last_page</a>
                            </div>
                        </div>

                    </div>


                    <table class="table table-sm table-bordered tb_airship">

                        <thead class="thead-light">

                        <tr>

                            <th class="text-nowrap">
                                <cht>編號</cht>
                                #
                            </th>

                            <th class="text-nowrap">
                                <cht>收件日期</cht>
                                Date Received
                            </th>

                            <th class="text-nowrap">
                                <cht>模式</cht>
                                Mode
                            </th>

                            <th class="text-nowrap">
                                <cht>客戶名</cht>
                                Customer
                            </th>

                            <th class="text-nowrap">
                                <cht>地址</cht>
                                Address
                            </th>

                            <th class="text-nowrap">
                                <cht>貨品名稱</cht>
                                Description
                            </th>

                            <th class="text-nowrap">
                                <cht>件數</cht>
                                Quantity
                            </th>

                            <th class="text-nowrap">
                                <cht>重量</cht>
                                Kilo
                            </th>

                            <th class="text-nowrap">
                                <cht>寄貨人</cht>
                                Supplier
                            </th>

                            <th class="text-nowrap">
                                <cht>班機與日期</cht>
                                Flight and Date
                            </th>

                            <th class="text-nowrap">
                                <cht>收費金額</cht>
                                Amount
                            </th>

                            <th class="text-nowrap">
                                <cht>付款日期</cht>
                                Date Paid
                            </th>

                            <th class="text-nowrap">
                                <cht>付款狀態</cht>
                                Payment Status
                            </th>

                            <th class="text-nowrap">
                                <cht>台幣金額</cht>
                                Amount in NTD
                            </th>

                            <th class="text-nowrap">
                                <cht>菲幣金額</cht>
                                Amount in PHP
                            </th>

                            <th class="text-nowrap">
                                <cht>抵達客人住址時間</cht>
                                Time Delivery Arrived
                            </th>

                            <th class="text-nowrap">
                                <cht>簽收人</cht>
                                Person Receive Delivery
                            </th>

                            <th class="text-nowrap">
                                <cht>補充說明</cht>
                                Notes
                            </th>

                            <th class="text-nowrap">
                                <cht>資料庫編號</cht>
                                DB Number
                            </th>

                        </tr>

                        </thead>

                        <tbody>

                        <tr v-for="(item, index) in displayedPosts" :class="[(item.status == '-1' ? 'deleted' : '')]">

                            <td>{{item.sn}}</td>

                            <td>{{ item.date_receive }}</td>

                            <td>{{ item.mode == 'exp' ? '快遞' : '空運' }}</td>

                            <td>{{ item.customer }}</td>

                            <td>{{ item.address }}</td>

                            <td>{{ item.description }}</td>

                            <td>{{ item.quantity }}</td>

                            <td>{{ item.kilo }}</td>

                            <td>{{ item.supplier }}</td>

                            <td>{{ item.flight }}<br>{{ item.flight_date }}</td>

                            <td>
                                {{ item.total !== null ? Number(item.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '' }} {{ item.currency }}
                            </td>

                            <td>
                                {{ item.pay_date }}
                            </td>

                            <td>
                                {{ item.pay_status == 't' ? 'Taiwan Paid' : ( item.pay_status == 'p' ? 'Philippines Paid' : '') }}
                            </td>

                            <td>
                                <span>{{ item.amount !== null ? Number(item.amount).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '' }}</span>
                                <i class="fas fa-info-circle fa-lg" aria-hidden="true" @click="show_ntd(item)"></i>
                            </td>

                            <td>
                                <span>{{ item.amount_php !== null ? Number(item.amount_php).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '' }}</span>
                                <i class="fas fa-info-circle fa-lg" aria-hidden="true" @click="show_php(item)"></i>
                            </td>

                            <td>
                                {{ item.date_arrive.replace('T', ' ') }}
                            </td>

                            <td>
                                {{ item.receiver }}
                            </td>

                            <td>
                                {{ item.remark }}
                            </td>

                            <td>
                                {{ item.id }}
                            </td>

                        </tr>

                        </tbody>

                    </table>


                </div>
            </div>
        </div>


        <!-- The Modal for Supplier -->
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
                            <tr v-for="(item, index) in s_filter">
                                <td onclick="data(this)">
                                    <input type="checkbox" class="form-check-input" :value="item.name">
                                    <label class="form-check-label">&nbsp</label>
                                </td>
                                <td> {{item.name}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn orange" style="background-color: lightgrey;"
                                onclick="toggleCheckboxSupplier();">全選 / 全取消<br>Select All / Undo
                        </button>
                        <button type="button" class="btn btn-primary" onclick="getSupplier()">確認<br>Confirm</button>
                    </div>
                </div>

            </div>
        </div>


        <!-- The Modal for Customer -->
        <div class="modal" id="cusModal">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">客戶名 / Customer</h4>
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
                                <th><p>Customer</p>
                                    <p>客戶名</p></th>
                            </tr>
                            </thead>
                            <tbody id="c_contact">
                            <tr v-for="(item, index) in c_filter">
                                <td onclick="data(this)">
                                    <input type="checkbox" class="form-check-input" :value="item.name">
                                    <label class="form-check-label">&nbsp</label>
                                </td>
                                <td> {{item.name}}</td>
                            </tr>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn orange" style="background-color: lightgrey;"
                                onclick="toggleCheckbox();">全選 / 全取消<br>Select All / Undo
                        </button>
                        <button type="button" class="btn btn-primary" onclick="getCustomer()">確認<br>Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- The Modal for NTD Details -->
        <div class="modal" id="details_NTD">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">Details of Amount in NTD
                            <cht>台幣金額明細</cht>
                        </h5>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="tablebox s02">
                            <ul class="header">
                                <li>
                                    <cht>名目</cht>
                                    Title
                                </li>
                                <li>
                                    <cht>數量</cht>
                                    Qty
                                </li>
                                <li>
                                    <cht>單價</cht>
                                    Unit Price
                                </li>
                                <li>
                                    <cht>金額</cht>
                                    Amount
                                </li>
                            </ul>

                            <ul v-for="(item, j) in record">

                                <li>{{ item.title }}</li>
                                <li>{{ item.qty }}</li>
                                <li>{{ item.price }}</li>
                                <li>{{ item.qty * item.price }}</li>
                            </ul>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-warning" onclick="close_ntd()">Cancel
                            <cht>取消</cht>
                        </button>
                    </div>

                </div>
            </div>
        </div>


        <!-- The Modal for PHP Details -->
        <div class="modal" id="details_PHP">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">Details of Amount in PHP
                            <cht>菲幣金額明細</cht>
                        </h5>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="tablebox s02">
                            <ul class="header">
                                <li>
                                    <cht>名目</cht>
                                    Title
                                </li>
                                <li>
                                    <cht>數量</cht>
                                    Qty
                                </li>
                                <li>
                                    <cht>單價</cht>
                                    Unit Price
                                </li>
                                <li>
                                    <cht>金額</cht>
                                    Amount
                                </li>
                            </ul>

                            <ul v-for="(item, j) in record">

                                <li>{{ item.title }}</li>
                                <li>{{ item.qty }}</li>
                                <li>{{ item.price }}</li>
                                <li>{{ item.qty * item.price }}</li>
                            </ul>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-warning" onclick="close_php()">Cancel
                            <cht>取消</cht>
                        </button>
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
<script type="text/javascript" src="js/query_airship_records.js" defer></script>
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

      for (var i = 0, element; element = checkboxes[i]; i++) {
        if(element.checked)
          containers += element.value + "||";
          //work with element
      }

      document.getElementsByName('supplier')[0].value = containers;

      $( "#supModal" ).dialog('close');
    };

    function getCustomer()
    {   
      console.log('getCustomer');

      var containers = '';

      var checkboxes = document.querySelector("#c_contact").querySelectorAll('input');

      for (var i = 0, element; element = checkboxes[i]; i++) {
        if(element.checked)
          containers += element.value + "||";
          //work with element
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

    function close_php() {
            $("#details_PHP").dialog("close");
          };

          function       close_ntd() {
                $("#details_NTD").dialog("close");
            };




</script>

</body>
</html>
