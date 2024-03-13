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

// if(!$decoded->data->report2)
// header( 'location:index.php' );

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
    <link rel="stylesheet" href="css/jquery-ui/1.12.1/jquery-ui.css">
    <link rel="stylesheet" href="css/bootstrap/4.3.1/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/datatables/jquery.dataTables.min.css"/>
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

        .listheader > .pageblock {
            float: right;
            display: flex;
            align-items: center;
        }

        .listheader > .pageblock select {
            height: 29px;
            font-size: 14px;
            background-image: url(../images/ui/icon_form_select_arrow.svg);
        }

        .listheader > .pageblock select:first-of-type {
            margin: 0 7px 0 3px;
        }

        .listheader > .left_function {
            float: left;
            margin: 3px 20px 0 0;
            display: flex;
            align-items: center;
        }

        .listheader > .left_function > select {
            font-size: 14px;
            width: 120px;
            height: 29px;
            background-image: url(../images/ui/icon_form_select_arrow.svg);
        }

        .listheader > .left_function > input[type='date'] {
            height: 29px;
            font-size: 14px;
            margin: 0 5px;
        }

        .listheader > .left_function > button {
            height: 29px;
            width: 29px;
            padding: 2px;
            margin: 0 5px;
        }

        .listheader > .month_btns {
            float: left;
        }

        .listheader > .month_btns button.btn-success {
            width: 38px;
            height: 30px;
            padding: 0 5px;
            vertical-align: 0;
            text-align: center;
            margin: 0 2px;
            font-size: 12px;
        }

        .mainlist {
            border-bottom: none;
        }

        table.report_table {
            width: 99%;
            margin: auto;
            margin-top: 10px;
            line-height: 18px;
        }

        table.report_table thead tr td {
            background-color: #BBB;
            pointer-events: none;
            font-weight: 500;
            border-top: 1px solid #BBB;
            border-right: 1px solid #BBB;
            border-bottom: 1px solid #BBB;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 16px;
            color: #333;
            min-width: 50px;
        }

        table.report_table thead tr td:first-of-type {
            border-left: 1px solid #BBB;
            border-top-left-radius: 9px;
        }

        table.report_table thead tr td:last-of-type {
            border-top-right-radius: 9px;
        }

        table.report_table tbody tr td {
            background-color: #FFF;
            pointer-events: none;
            border-right: 1px solid #BBB;
            border-bottom: 1px solid #BBB;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 16px;
            color: #333;
            min-width: 50px;
        }

        table.report_table tbody tr td:first-of-type {
            border-left: 1px solid #BBB;
        }

        table.report_table tbody tr:last-of-type td {
            border-bottom: 1px solid #BBB;
        }

        table.report_table thead tr td cht {
            display: block;
            font-size: 12px;
        }

        div.tablebox > ul > li:nth-of-type(9) > div.remarks {
            max-width: 150px;
            text-align: left;
            font-size: 13px;
            margin-bottom: 3px;
        }

        div.tablebox > ul > li:nth-of-type(9) > i.fa-edit {
            cursor: pointer;
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
            <h6>每日提貨報表
                <eng>Daily Pickup Report</eng>
            </h6>

            <div class="block record show">
                <div class="mainlist">

                    <div class="listheader">
                        <div class="pageblock"> Page Size:
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
                        <div class="left_function">
                            <input style="margin-left: 10px;" type="date" id="start" v-model="date_start"> ~ <input type="date" id="end" v-model="date_end">

                            <button style="margin-left: 20px;" @click="query('')"><i aria-hidden="true" class="fas fa-filter"></i></button>
                            <button @click="print()"><i aria-hidden="true" class="fas fa-file-export"></i></button>

                           
                        </div>

                        <div class="month_btns">
                                <button class="btn btn-success" @click="getPeriod('01')">Jan</button>
                                <button class="btn btn-success" @click="getPeriod('02')">Feb</button>
                                <button class="btn btn-success" @click="getPeriod('03')">Mar</button>
                                <button class="btn btn-success" @click="getPeriod('04')">Apr</button>
                                <button class="btn btn-success" @click="getPeriod('05')">May</button>
                                <button class="btn btn-success" @click="getPeriod('06')">Jun</button>
                                <button class="btn btn-success" @click="getPeriod('07')">Jul</button>
                                <button class="btn btn-success" @click="getPeriod('08')">Aug</button>
                                <button class="btn btn-success" @click="getPeriod('09')">Sep</button>
                                <button class="btn btn-success" @click="getPeriod('10')">Oct</button>
                                <button class="btn btn-success" @click="getPeriod('11')">Nov</button>
                                <button class="btn btn-success" @click="getPeriod('12')">Dec</button>
                            </div>

                        <!-- <div class="searchblock" style="float:left;">搜尋<input type="text"></div> -->
                    </div>


                    <table class="report_table">
                        <thead>
                            <tr>
                                <td>
                                    <cht>日期</cht>
                                    Date
                                </td>

                                <td>
                                    <cht>客戶名稱</cht>
                                    Name of Customer
                                </td>

                                <td>
                                    <cht>單號</cht>
                                    DR #
                                </td>
                            </tr>
                        </thead>

                        <tbody>
                                <tr v-for="(item, index) in receive_records">
                                    <td :rowspan="item.encode_count" v-if="item.encode_count != '0'">{{ item.pick_date }}</td>
                                    <td :rowspan="item.customer_count" v-if="item.customer_count != '0'">{{ item.customer }}</td>
                                    <td>{{ item.encode }}</td>
                                </tr>
                        </tbody>

                    </table>

                </div>
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
<script type="text/javascript" src="js/report_daily_pickup.js" defer></script>

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
