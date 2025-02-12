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

if(!$decoded->data->report2)
header( 'location:index.php' );

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

        div.tablebox.s02 {
            width: 99%;
            margin: auto;
            margin-top: 10px;
        }

        div.tablebox > ul:nth-of-type(2n+1) {
            background-color: #fff;
        }

        div.tablebox > ul:hover:nth-of-type(2n+1) {
            background-color: var(--orange01);
        }

        div.tablebox > ul > li {
            border-right: 1px solid #94BABB;
            border-bottom: 1px solid #94BABB;
        }

        div.tablebox > ul > li:nth-of-type(1) {
            border-left: 2px solid #94BABB;
        }

        div.tablebox > ul > li:nth-of-type(9) {
            border-right: 2px solid #94BABB;
        }

        div.tablebox > ul.header {
            background-color: #DFEAEA;
        }

        div.tablebox > ul.header > li {
            border-top: 2px solid #94BABB;
            border-right: 1px solid #94BABB;
            border-bottom: 1px solid #94BABB;
        }

        div.tablebox > ul.header > li:nth-of-type(1) {
            border-left: 2px solid #94BABB;
            border-top-left-radius: 9px;
        }

        div.tablebox > ul.header > li:nth-of-type(9) {
            border-right: 2px solid #94BABB;
            border-top-right-radius: 9px;
        }

        div.tablebox > ul.total > li {
            border-bottom: 2px solid #94BABB;
        }

        div.tablebox > ul.total > li:nth-of-type(1) {
            border-left: 2px solid #94BABB;
            border-bottom-left-radius: 9px;
        }

        div.tablebox > ul.total > li:nth-of-type(9) {
            border-right: 2px solid #94BABB;
            border-bottom-right-radius: 9px;
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


        div.tablebox > table {
            width: 100%;
            border-collapse: separate;
        }

        div.tablebox > table thead th {
            border-top: 2px solid #94BABB;
            border-right: 1px solid #94BABB;
            border-bottom: 1px solid #94BABB;
            background-color: #BBB;
            padding: 8px;
            text-align: center;
            font-size: 16px;
            transition: .3s;
            color: #333;
            min-width: 50px;
            vertical-align: middle;
            font-weight: 500;
        }

        div.tablebox > table thead th:nth-of-type(1) {
            border-top-left-radius: 9px;
            border-left: 2px solid #94BABB;
        }

        div.tablebox > table thead th:nth-of-type(10) {
            border-top-right-radius: 9px;
            border-right: 2px solid #94BABB;
        }

        div.tablebox > table thead th eng {
            display: block;
            font-size: 12px;
            margin-left: 5px;
        }

        div.tablebox > table tbody td {
            border-right: 1px solid #94BABB;
            border-bottom: 1px solid #94BABB;
            padding: 8px;
            text-align: center;
            font-size: 16px;
            transition: .3s;
            color: #333;
            min-width: 50px;
            vertical-align: middle;
            font-weight: 500;
        }

        div.tablebox > table tbody td:nth-of-type(10) {
            border-right: 2px solid #94BABB;
        }

        div.tablebox > table tbody tr:nth-of-type(4n+3) > td,
        div.tablebox > table tbody tr:nth-of-type(4n) > td {
            background-color: #F5F5F5;
        }

        div.tablebox > table tbody td:nth-of-type(10) {
            border-right: 2px solid #94BABB;
        }

        div.tablebox > table tfoot td {
            border-right: 1px solid #94BABB;
            border-bottom: 1px solid #94BABB;
            padding: 8px;
            text-align: center;
            font-size: 16px;
            transition: .3s;
            color: #333;
            min-width: 50px;
            vertical-align: middle;
            font-weight: 500;
            background-color: #DDD;
        }

        div.tablebox > table tfoot td:nth-of-type(10) {
            border-bottom-right-radius: 9px;
            border-right: 2px solid #94BABB;
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
        $(function () {
            $('header').load('include/header_admin.php');
        });


    </script>

</head>

<body>
<div class="bodybox">
<div class="mask" style="display:none">
    </div>
    <!-- header -->
    <header>
    </header>
    <!-- header end -->
    <div id='receive_record'>
        <div class="mainContent">
            <h6>貨櫃帳款報表
                <eng>A/R Report of Containers</eng>
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
                            <select v-model="fil_category">
                                <option value="1">Date Sent</option>
                                <option selected value="2">Date C/R</option>
                            </select>

                            <input style="margin-left: 10px;" type="date" id="start" v-model="date_start"> ~ <input type="date" id="end" v-model="date_end">

                            <button style="margin-left: 20px;" @click="query('')"><i aria-hidden="true" class="fas fa-filter"></i></button>
                            <button @click="print()"><i aria-hidden="true" class="fas fa-file-export"></i></button>

                           
                        </div>

                        <div class="month_btns">
                                <button class="btn btn-success" @click="getSpace('s')">空白</button>
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

                    <!-- 新表格 -->
                    <div class="tablebox s02">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>
                                    <eng>Date Sent</eng>
                                    結關日期
                                </th>
                                <th>
                                    <eng>Date C/R</eng>
                                    到倉日期
                                </th>
                                <th>
                                    <eng>Container Number</eng>
                                    櫃號
                                </th>
                                <th>
                                    <eng>A/R (By Kilo)</eng>
                                    應收帳款(根據重量)
                                </th>
                                <th>
                                    <eng>A/R (By Cuft)</eng>
                                    應收帳款(根據材積)
                                </th>
                                <th>
                                    <eng>A/R</eng>
                                    應收帳款
                                </th>
                                <th>
                                    <eng>A/R</eng>
                                    PH Pay 菲律賓付<br>
                                    TW Pay 台灣付
                                </th>
                                <th>
                                    <eng>Amount Received</eng>
                                    已收金額
                                </th>
                                <th>
                                    <eng>Remaining A/R</eng>
                                    未收金額
                                </th>
                                <th>
                                    <eng>Remarks</eng>
                                    備註
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                                <template v-for='(item, index) in displayedPosts'>
                                    <tr>
                                        <td rowspan="2" style="border-left: 2px solid #94BABB;"><p v-for='(it, index) in item.loading'>{{it.date_sent}}</p></td>
                                        <td rowspan="2"><p v-for='(it, index) in item.loading'>{{it.date_arrive}}</p></td>
                                        <td rowspan="2"><p v-for='(it, index) in item.loading'>{{it.container_number}}</p></td>
                                        <td rowspan="2">₱ {{ item.charge_kilo !== undefined ? Number(item.charge_kilo).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                        <td rowspan="2">₱ {{ item.charge_cuft !== undefined ? Number(item.charge_cuft).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                        <td rowspan="2">₱ {{ Number(item.charge_kilo) + Number(item.charge_cuft) !== undefined ? Number(Number(item.charge_kilo) + Number(item.charge_cuft)).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>

                                        <!-- PH Pay 菲律賓付 的應收總金額、已收金額、未收金額 -->
                                        <td>₱ {{ item.philippine_charge_kilo + item.philippine_charge_cuft !== undefined ? Number(item.philippine_charge_kilo + item.philippine_charge_cuft).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                        <td>₱ {{ item.philippine_complete_charge !== undefined ? Number(item.philippine_complete_charge).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                        <td>₱ {{ item.philippine_ar !== undefined ? Number(item.philippine_ar).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>

                                        <td rowspan="2">
                                            <div class="remarks">{{item.notes}}</div>
                                            <i class="fas fa-edit" aria-hidden="true" @click="update_remark(item)"></i>
                                        </td>
                                    </tr>

                                    <tr>
                                        <!-- TW Pay 台灣付 的應收總金額、已收金額、未收金額 -->
                                        <td>₱ {{ item.taiwan_charge !== undefined ? Number(item.taiwan_charge).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                        <td>NTD {{ item.taiwan_complete_charge !== undefined ? Number(item.taiwan_complete_charge).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}<br/>(Cost: ₱ {{ item.taiwan_courier !== undefined ? Number(item.taiwan_courier).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }})</td>
                                        <td>₱ {{ item.taiwan_charge - item.taiwan_complete_charge !== undefined ? Number(item.taiwan_charge - item.taiwan_complete_charge).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                    </tr>
                                </template>
                            </tbody>

                            <tfoot>
                            <tr class="total">
                                <td rowspan="2" style="border-bottom-left-radius: 9px; border-left: 2px solid #94BABB;">Total</td>
                                <td rowspan="2"></td>
                                <td rowspan="2">{{ container_total }}</td>
                                <td rowspan="2"></td>
                                <td rowspan="2"></td>
                                <td rowspan="2">₱ {{ total_total !== undefined ? Number(total_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                <td>₱ {{ philippines_ar_total != undefined ? Number(philippines_ar_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                <td>₱ {{ philippines_charge_total !== undefined ? Number(philippines_charge_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                <td>₱ {{ philippines_total_total !== undefined ? Number(philippines_total_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                <td rowspan="2"></td>
                            </tr>

                            <tr>
                                <td>₱ {{ taiwan_ar_total != undefined ? Number(taiwan_ar_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                <td>₱ {{ taiwan_charge_total !== undefined ? Number(taiwan_charge_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                                <td>₱ {{ taiwan_total_total !== undefined ? Number(taiwan_total_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                            </tr>
                            </tfoot>
                            
                        </table>
                    </div>

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
<script type="text/javascript" src="js/report_container_ac.js" defer></script>

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
