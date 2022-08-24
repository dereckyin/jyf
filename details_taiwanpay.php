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

$test_manager = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;


// 可以存取Expense Recorder的人員名單如下：Dennis Lin(2), Glendon Wendell Co(4), Kristel Tan(6), Kuan(3), Mary Jude Jeng Articulo(9), Thalassa Wren Benzon(41), Stefanie Mika C. Santos(99)
// 為了測試先加上testmanager(87) by BB
// if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 41 || $user_id == 3 || $user_id == 9 || $user_id == 87 || $user_id == 99)
// {
//     $access3 = true;
// }
// else
// {
//     header( 'location:index' );
// }

}
catch (Exception $e){

header( 'location:index.php' );
}


//if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
//    header( 'location:index.php' );
}
// if decode fails, it means jwt is invalid
catch (Exception $e){

header( 'location:index.php' );
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>中亞菲國際貿易有限公司</title>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

    <!-- CSS -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
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
            margin: 0;
            padding: 0;
        }

        .mainlist {
            overflow: auto;
            height: calc(100vh - 420px);
        }

        .mainlist div.tablebox > ul > li {
            font-size: 14px;
        }

        .mainlist div.tablebox > ul.header > li:nth-of-type(1) {
            min-width: 100px;
        }

        .mainlist div.tablebox > ul.header > li:nth-of-type(2), .mainlist div.tablebox > ul.header > li:nth-of-type(3), .mainlist div.tablebox > ul.header > li:nth-of-type(7)  {
            min-width: 230px;
        }

        .mainlist div.tablebox > ul.header > li:nth-of-type(4) {
            min-width: 80px;
        }

        .mainlist div.tablebox > ul.header > li:nth-of-type(5), .mainlist div.tablebox > ul.header > li:nth-of-type(6), .mainlist div.tablebox > ul.header > li:nth-of-type(16)  {
            min-width: 60px;
        }

        .mainlist div.tablebox > ul.header > li:nth-of-type(8), .mainlist div.tablebox > ul.header > li:nth-of-type(15) {
            min-width: 260px;
        }

        .mainlist div.tablebox > ul.header > li:nth-of-type(9), .mainlist div.tablebox > ul.header > li:nth-of-type(10), .mainlist div.tablebox > ul.header > li:nth-of-type(11), .mainlist div.tablebox > ul.header > li:nth-of-type(12), .mainlist div.tablebox > ul.header > li:nth-of-type(13), .mainlist div.tablebox > ul.header > li:nth-of-type(14) {
            min-width: 150px;
        }

        .mainlist div.tablebox > ul > li > div > label
        {
            margin-bottom: 0;
        }

        div.tablebox > ul > li > input[type='date']{
            width: 160px;
            height: 32.8px;
            border-radius: 5px;
            border: 1px solid rgb(153,153,153);
        }

        .mainlist .listheader {
            position: sticky;
            top: 0;
            left: 0;
            background-color: white;
        }

        .listheader > .pageblock select {
            background-image: url(../images/ui/icon_form_select_arrow.svg);
        }


    </style>

    <script>
        $(function () {
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
            <h6>台灣付明細<eng>Details of Taiwan Pay</eng></h6>
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
                            <date-picker id="date_start" @update-date="updateDate" v-model="date_start"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                            &nbsp; &nbsp; ~ &nbsp; &nbsp;
                            <date-picker id="date_end" @update-date="updateDate" v-model="date_end"
                                         style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
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
                        <li>特定貨櫃
                            <eng>Container Number</eng>
                        </li>
                        <li>
                            <input type="text" class="goods_num" id="container_number" name="container_number"
                                   v-model="container_number" style="width: calc(80% - 40px);">
                            <button type="button" class="btn btn-primary" id="create-supplier"><i
                                    class="fas fa-address-card"></i></button>
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
                <h6>收貨紀錄
                    <eng>Receiving Records</eng>
                </h6>
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

                                <a class="next micons" :disabled="page == pages.length"
                                   @click="page++">chevron_right</a>
                                <a class="last micons" @click="page=pages.length">last_page</a>
                            </div>
                        </div>
                        <!-- <div class="searchblock" style="float:left;">搜尋<input type="text"></div> -->
                    </div>

                    <div class="tablebox s02">
                        <!-- <table class="table table-hover table-striped table-sm table-bordered" id="showUser1" ref="showUser1"> -->
                        <ul class="header" style="position: sticky; top: 40px; left: 0;">
                            <li>
                                <eng>Date Received</eng>
                                收件日期
                            </li>
                            <li>
                                <eng>Company/Customer</eng>
                                收件人
                            </li>
                            <li>
                                <eng>Description</eng>
                                貨品名稱
                            </li>
                            <li>
                                <eng>Quantity</eng>
                                件數
                            </li>
                            <li>
                                <eng>Kilo</eng>
                                重量
                            </li>
                            <li>
                                <eng>Cuft</eng>
                                才積
                            </li>
                            <li>
                                <eng>Supplier</eng>
                                寄貨人
                            </li>
                            <li>
                                <eng>Remark</eng>
                                備註
                            </li>
                            <li><eng>Date C/R</eng>貨櫃到倉日期</li>
                            <li>
                                <eng>A/R (PHP)</eng>
                                請款金額(菲幣)
                            </li>
                            <li>
                                <eng>A/R (TWD)</eng>
                                請款金額(台幣)
                            </li>
                            <li>
                                <eng>Paid Amount</eng>
                                付款金額
                            </li>
                            <li>
                                <eng>Paid Date</eng>
                                付款日期
                            </li>
                            <li><eng>Currency Rate</eng>匯率</li>
                            <li>
                                <eng>Notes</eng>
                                補充說明
                            </li>
                            <li>
                                <eng>Action</eng>
                                功能
                            </li>
                        </ul>
                        <ul v-for='(receive_record, index) in displayedPosts'>
                            <li>{{ receive_record.date_receive }}</li>
                            <li>{{ receive_record.customer }}</li>
                            <li>{{ receive_record.description }}</li>
                            <li>{{ receive_record.quantity }}</li>
                            <li>{{ receive_record.kilo }}</li>
                            <li>{{ receive_record.cuft }}</li>
                            <li>{{ receive_record.supplier }}</li>
                            <li><p v-html="receive_record.remark.replace(/(?:\r\n|\r|\n)/g, '&nbsp')"></p></li>
                            <li>{{ receive_record.date_arrive }}</li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.ar_php}}</label>
                                </div>
                                <input type="number" name="ar_php" v-show="receive_record.is_edited == 0" v-model="receive_record.ar_php">
                            </li>

                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.ar}}</label>
                                </div>
                                <input type="number" name="ar" v-show="receive_record.is_edited == 0" v-model="receive_record.ar">
                            </li>

                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.amount}}</label>
                                </div>
                                <input type="number" name="amount" v-show="receive_record.is_edited == 0" v-model="receive_record.amount">
                            </li>

                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{ receive_record.payment_date}}</label>
                                </div>
                                <input type="date" name="payment_date" v-show="receive_record.is_edited == 0" v-model="receive_record.payment_date">
                            </li>

                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{ receive_record.rate}}</label>
                                </div>
                                <input type="text" name="rate" v-show="receive_record.is_edited == 0" v-model="receive_record.rate">
                            </li>

                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.note}}</label>
                                </div>
                                <input type="text" name="note" v-show="receive_record.is_edited == 0" v-model="receive_record.note" maxlength="512">
                            </li>


                            <li>
                                <?php
                                    if($decoded->data->taiwan_read != "1")
                                    {
                                ?>
                                <button v-show="receive_record.is_edited == 1 && receive_record.status == ''" @click="editRow(receive_record)">修改</button>
                                <button v-show="receive_record.is_edited == 0" @click="confirmRow(receive_record)">確認</button>
                                <button v-show="receive_record.is_edited == 0" @click="cancelRow(receive_record)">取消</button>
                                <button v-show="receive_record.is_edited == 1 && receive_record.status == ''" @click="completeRow(receive_record)">完成</button>
                                <?php
                                    }
                                ?>
                            </li>
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
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script type="text/javascript" src="js/details_taiwanpay.js?rand=<?php echo uniqid(); ?>" defer></script>
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

    function data(e) {
        e.querySelectorAll('input')[0].checked = !e.querySelectorAll('input')[0].checked;
    };

    function getContainer() {
        console.log('getContainer');

        var containers = '';

        var checkboxes = document.querySelector("#contact").querySelectorAll('input');

        for (var i = 0, element; element = checkboxes[i]; i++) {
            if (element.checked)
                containers += element.value + ",";
            //work with element
        }

        document.getElementsByName('container_number')[0].value = containers;

        $("#myModal").dialog('close');
    };

</script>

</body>
</html>
