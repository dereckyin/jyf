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

$phili_read = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

$phili_read = $decoded->data->phili_read;


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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-select.min.css"/>
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

        select {
            background-image: url(../images/ui/icon_form_select_arrow.svg);
        }

        .mainContent > h6 {
            letter-spacing: 0;
        }

        .mainContent > h6 > cht {
            font-size: 24px;
            margin-left: 8px;
            letter-spacing: 5px;
            opacity: 0.5;
        }

        .modal h5 {
            letter-spacing: 0;
            font-size: 28px;
        }

        .modal h5 > cht {
            font-size: 20px;
            margin-left: 8px;
            letter-spacing: 5px;
            opacity: 0.5;
        }

        .btnbox a.btn {
            letter-spacing: 0;
        }

        .btnbox a.btn cht {
            font-size: 12px;
            letter-spacing: 3px;
            margin-left: 4px;
        }

        .modal-footer button.btn {
            letter-spacing: 0;
        }

        .modal-footer button.btn cht {
            font-size: 12px;
            letter-spacing: 3px;
            margin-left: 4px;
        }

        div.tablebox > .header cht {
            display: block;
            font-weight: 400;
        }

        div.tablebox > ul > li > cht {
            font-size: 12px;
        }

        .tb_measure {
            width: 100%;
        }

        .tb_measure thead tr th, .tb_measure tbody tr td {
            font-size: 16px;
            padding: 12px 8px;
            text-align: center;
            color: #333;
            vertical-align: middle;
            min-width: 50px;
            width: auto;
            font-weight: 500;
        }

        .tb_measure thead tr th {
            background-color: #bbb;
        }

        .tb_measure thead tr th:nth-of-type(1) {
            min-width: 120px;
        }

        .tb_measure thead tr th:nth-of-type(2) {
            max-width: 300px;
        }

        .tb_measure thead tr th:nth-of-type(5) {
            max-width: 150px;
        }

        .tb_measure tbody tr td {
            font-weight: 300;
        }

        .tb_measure thead tr th cht {
            display: block;
            font-size: 12px;
        }

        .tb_measure {
            border: 0.5px solid #999;
            border-bottom: none;
        }

        .tb_measure tbody tr td {
            border: 0.5px solid #999;
        }

        .tb_measure tbody td input[type="number"] {
            width: 110px;
            text-align: center;
        }

        .tb_measure tbody td select {
            width: 100%;
        }

        .tb_measure tbody td div {
            margin-bottom: 5px;
        }

        .tb_measure tbody td div.ar {
            color: red;
            font-weight: 600;
        }

        .tablebox input[type="date"], .tablebox.payment select {
            border: 1px solid #999;
            border-radius: 5px;
            background-color: #fff;
            padding: 5px;
            vertical-align: middle;
            height: 33px;
        }

        .tablebox.payment ul > li:nth-of-type(2) input[type="date"] {
            width: 170px;
            min-width: 170px;
        }

        .tablebox.payment ul > li:nth-of-type(3) input[type="date"] {
            width: 170px;
            min-width: 170px;
        }

        .tablebox.payment ul > li:nth-of-type(4) {
            width: 130px;
            min-width: 130px;
        }

        .tablebox.payment ul > li:nth-of-type(5) {
            width: 130px;
            min-width: 130px;
        }

        .tablebox.payment ul > li:nth-of-type(6) {
            width: 130px;
            min-width: 130px;
        }

        .tablebox.payment ul > li:nth-of-type(7) {
            width: 300px;
            min-width: 300px;
        }

        .tablebox.payment ul > li:nth-of-type(8) {
            width: 40px;
            min-width: 40px;
        }

        .tablebox.payment ul > li:nth-of-type(8) span {
            display: block;
            color: white;
            font-size: 18px;
            font-weight: 700;
            width: 28px;
            height: 28px;
            border-radius: 14px;
            line-height: 24px;
            background-color: rgb(205, 92, 92);
            text-align: center;
            cursor: pointer;
        }

        .tablebox.payment ul.add_row, .tablebox.payment ul.add_row:hover {
            background-color: white;
        }

        .tablebox.payment ul.add_row > li:nth-of-type(7) i {
            color: rgb(32, 103, 102);
            font-size: 28px;
            cursor: pointer;
        }

        .tb_measure tbody tr td span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            border-radius: 5px;
            padding: 0 7px;
            margin: 0 5px;
        }

        div.block > .listheader {
            width: 100%;
            padding: 5px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        div.block > .tablebox li eng {
            display: block;
        }

        img.ui-datepicker-trigger {
            padding-left: 10px;
            margin: -8px;
        }

    </style>


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>


    <script>
        $(function () {
            $('header').load('include/header_admin.php');
        })
    </script>
</head>

<body>
<div class="bodybox" id="measure">
    <!-- header -->
    <header></header>
    <!-- header end -->
    <div class="mainContent">

        <h6>
            Query For Archived Pickup and Payment Records
            <cht>已歸檔提貨與付款記錄查詢</cht>
        </h6>

        <div class="block">
            <div class="tablebox V s01">
                <ul>
                    <li class="header"></li>
                    <li>Date C/R
                        <eng>貨櫃到倉日期</eng>
                    </li>
                    <li>
                        <!--<input type="text" id="datepicker" name="datepicker" style="width: calc(40% - 40px);" > -->
                        <date-picker id="date_start" @update-date="StartDate"
                                     style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                        &nbsp; &nbsp; ~ &nbsp; &nbsp;
                        <date-picker id="date_end" @update-date="EndDate"
                                     style="width: calc(30% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                    </li>
                </ul>
                <ul>
                    <li class="header"></li>
                    <li>SOLD TO
                        <eng></eng>
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
                    <li>Containers Number
                        <eng>櫃號</eng>
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
                    <li>DR
                        <eng>單號</eng>
                    </li>
                    <li>
                        <input type="text" class="goods_num" id="goods_num" maxlength="256" name="goods_num"
                               style="width: calc(80% - 40px);">
                    </li>
                </ul>
            </div>

            <div class="btnbox"><a class="btn" @click="query()" style="color:white;">查詢
                <eng>Query</eng>
            </a><a class="btn orange" @click="print()" style="color:white; display: none;">匯出
                <eng>Print</eng>
            </a></div>
        </div>

        <div class="block record show">
            <h6>Archived Pickup / Payment Records
                <cht>已歸檔提貨與付款記錄</cht>
            </h6>

            <div class="listheader">

                <div></div>

                <div class="pageblock" v-show="filter == '' || filter == 'D'"> <!--Page Size:
                    <select v-model="perPage">
                        <option v-for="item in inventory" :value="item" :key="item.id">
                            {{ item.name }}
                        </option>
                    </select> --> Page:
                    <div class="pageblock" style="display: inline-block;">
                        <a class="first micons" @click="page=1">first_page</a>
                        <a class="prev micons" :disabled="page == 1"
                           @click="page < 1 ? page = 1 : page--">chevron_left</a>
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


            <div class="mainlist">
                <table class="tb_measure">
                    <thead>
                    <tr>
                        <th>
                            <cht>提貨狀態</cht>
                            Pickup Status
                        </th>
                        <th>
                            SOLD TO
                        </th>

                        <th>
                            <cht>貨櫃到倉日期</cht>
                            Date C/R
                        </th>
                        <th>
                            <cht>櫃號</cht>
                            Containers Number
                        </th>
                        <th>
                            <cht>收貨記錄筆數</cht>
                            Number of Goods Records
                        </th>
                        <th>
                            <cht>重量</cht>
                            Kilo
                        </th>
                        <th>
                            <cht>才積</cht>
                            Cuft
                        </th>
                        <th>
                            <cht>收費金額</cht>
                            Amount
                        </th>
                        <th>
                            <cht>單號</cht>
                            DR
                        </th>
                        <th>
                            <cht>付款狀態</cht>
                            Payment Status
                        </th>
                    </tr>
                    </thead>

                    <tbody>


                    <template v-for='(row, i) in receive_records'>
                        <tr v-for='(item, j) in row.measure'>
                            <td>
                                <div v-for='(rs, k) in item.record'>{{rs.pick_date}}</div>
                                <?php
                if($phili_read == "0")
{
    ?>
                                <button @click="item_record(item.record)" data-toggle="modal"
                                        data-target="#record_modal" v-if="item.pickup_status == ''">Encode
                                </button>
                                <?php
}
?>
                                <button @click="item_record(item.record)" data-toggle="modal"
                                        data-target="#record_modal_detail" v-if="item.pickup_status != ''">Detail
                                </button>
                            </td>
                            <td>
                                <span v-for='(cust, j) in item.record_cust'>{{ cust }}</span>

                            </td>
                            <td>
                                {{ item.date_arrive }}
                            </td>
                            <td>
                                {{ item.container_number }}
                            </td>
                            <td>{{ item.record.length }}</td>
                            <td>{{ item.kilo }}{{ item.kilo == '' ? '' : '@' + (item.kilo_price) }}</td>
                            <td>{{ item.cuft }}{{ item.cuft == '' ? '' : '@' + (item.cuft_price) }}</td>
                            <td>{{ item.charge }}</td>
                            <td>
                                <div>{{ item.encode }}</div>
                                <?php
                if($phili_read == "0")
{
    ?>
                                <button data-toggle="modal" data-target="#encode_modal" v-if="item.encode_status == ''"
                                        @click="item_encode(item)">Encode
                                </button>
                                <?php
}
?>

                            </td>

                            <td v-if="j == 0" :rowspan="row.measure.length">
                                <div class="ar">A/R: {{ row.ar_amount }}</div>
                                <div v-for='(rs, l) in row.payments'>{{rs.payment_date}}, {{ rs.amount }}</div>
                                <?php
                if($phili_read == "0")
{
    ?>
                                <button data-toggle="modal" data-target="#payment_modal"
                                        v-if="item.payment_status == ''"
                                        @click="item_payment(row.payments, row.ar, row.measure_detail_id)">Encode
                                </button>
                                <?php
}
?>
                                <button data-toggle="modal" data-target="#payment_modal_detail"
                                        v-if="item.payment_status != ''"
                                        @click="item_payment(row.payments, row.ar, row.measure_detail_id)">Detail
                                </button>
                            </td>
                        </tr>
                    </template>

                    </tbody>
                </table>


            </div>

        </div>

    </div>


    <!-- Modal Begins -->
    <div class="modal" id="supModal">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">SOLD TO</h4>
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
                            <th style="vertical-align: middle;"><p>SOLD TO</p>
                                </th>
                        </tr>
                        </thead>
                        <tbody id="s_contact">
                        <tr v-for="(item, index) in s_filter">
                            <td onclick="data(this)">
                                <input type="checkbox" class="form-check-input" :value="item.cust">
                                <label class="form-check-label">&nbsp</label>
                            </td>
                            <td> {{item.cust}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn orange" style="background-color: lightgrey;"
                            onclick="toggleCheckboxSupplier();">全選 / 全取消<br>Select All / Undo
                    </button>
                    <button type="button" class="btn btn-primary" onclick="getSupplier()">確認<br>Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Ends -->

    <!-- Modal Begins -->
    <div class="modal" id="cusModal">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Containers Number 櫃號</h4>
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
                            <th><p>Containers Number</p>
                                <p>櫃號</p></th>
                        </tr>
                        </thead>
                        <tbody id="c_contact">
                        <tr v-for="(item, index) in c_filter">
                            <td onclick="data(this)">
                                <input type="checkbox" class="form-check-input" :value="item.container_number">
                                <label class="form-check-label">&nbsp</label>
                            </td>
                            <td> {{item.container_number}}</td>
                        </tr>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn orange" style="background-color: lightgrey;"
                            onclick="toggleCheckbox();">全選 / 全取消<br>Select All / Undo
                    </button>
                    <button type="button" class="btn btn-primary" onclick="getCustomer()">確認<br>Confirm</button>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal Ends -->

    <!-- Modal Begins -->
    <div class="modal" id="encode_modal">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">OR
                        <cht>單號</cht>
                    </h5>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <input type="text" style="width: 100%;" v-model="item.encode">
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Cancel
                        <cht>取消</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary" @click="encode_save()">Save
                        <cht>儲存</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary"
                            @click="encode_save_complete()">Complete
                        <cht>完成</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Ends -->

    <!-- Modal Begins -->
    <div class="modal fade" id="record_modal">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1400px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Details of Goods
                        <cht>貨品內容</cht>
                    </h5>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="tablebox s02">
                        <ul class="header">
                            <li>
                                <cht>收貨日期</cht>
                                Date Receive
                            </li>
                            <li>
                                <cht>收件人</cht>
                                Company/Customer
                            </li>
                            <li>
                                <cht>貨品名稱</cht>
                                Description
                            </li>
                            <li>
                                <cht>件數</cht>
                                Quantity
                            </li>
                            <li>
                                <cht>寄貨人</cht>
                                Supplier
                            </li>
                            <li>
                                <cht>備註</cht>
                                Remark
                            </li>
                            <li>
                                <cht>提貨日期</cht>
                                Date Pickup
                            </li>
                            <li>
                                <cht>提貨人</cht>
                                Pickup Person
                            </li>
                            <li>
                                <cht>補充說明</cht>
                                Notes
                            </li>
                        </ul>

                        <ul v-for="(item, j) in record">

                            <li>{{ item.date_receive }}</li>
                            <li>{{ item.customer }}</li>
                            <li>{{ item.description }}</li>
                            <li>{{ item.quantity }}</li>
                            <li>{{ item.supplier }}</li>
                            <li>{{ item.remark }}</li>
                            <li><input type="date" v-model="item.org_pick_date"></li>
                            <li><input type="text" v-model="item.pick_person"></li>
                            <li><input type="text" v-model="item.pick_note"></li>
                        </ul>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning" @click="record_cancel()">Cancel
                        <cht>取消</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary" @click="record_save()">Save
                        <cht>儲存</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary"
                            @click="record_save_complete()">Complete All Pickup
                        <cht>完成所有提貨</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Ends -->

    <!-- Modal Begins -->
    <div class="modal fade" id="record_modal_detail">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1400px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Details of Goods
                        <cht>貨品內容</cht>
                    </h5>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="tablebox s02">
                        <ul class="header">
                            <li>
                                <cht>收貨日期</cht>
                                Date Receive
                            </li>
                            <li>
                                <cht>收件人</cht>
                                Company/Customer
                            </li>
                            <li>
                                <cht>貨品名稱</cht>
                                Description
                            </li>
                            <li>
                                <cht>件數</cht>
                                Quantity
                            </li>
                            <li>
                                <cht>寄貨人</cht>
                                Supplier
                            </li>
                            <li>
                                <cht>備註</cht>
                                Remark
                            </li>
                            <li>
                                <cht>提貨日期</cht>
                                Date Pickup
                            </li>
                            <li>
                                <cht>提貨人</cht>
                                Pickup Person
                            </li>
                            <li>
                                <cht>補充說明</cht>
                                Notes
                            </li>
                        </ul>

                        <ul v-for="(item, j) in record">

                            <li>{{ item.date_receive }}</li>
                            <li>{{ item.customer }}</li>
                            <li>{{ item.description }}</li>
                            <li>{{ item.quantity }}</li>
                            <li>{{ item.supplier }}</li>
                            <li>{{ item.remark }}</li>
                            <li>{{ item.org_pick_date }}</li>
                            <li>{{ item.pick_person }}</li>
                            <li>{{ item.pick_note }}</li>
                        </ul>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Cancel
                        <cht>取消</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Ends -->

    <!-- Modal Begins -->
    <div class="modal" id="payment_modal">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1400px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Payment Status
                        <cht>付款狀態</cht>
                    </h5>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="tablebox s02 payment">
                        <ul class="header">
                            <li>
                                <cht>支付方式</cht>
                                Payment Method
                            </li>
                            <li>
                                <cht>開立日期</cht>
                                Issue Date
                            </li>
                            <li>
                                <cht>收到日期</cht>
                                Receive Date
                            </li>
                            <li>
                                <cht>金額</cht>
                                Amount
                            </li>
                            <li>
                                <cht>零錢</cht>
                                Change
                            </li>
                            <li>
                                <cht>代墊</cht>
                                Courier/payment
                            </li>
                            <li>
                                <cht>備註</cht>
                                Remark
                            </li>
                            <li></li>
                        </ul>

                        <ul v-for="(item, j) in payment">
                            <li>
                                <select v-model="item.type">
                                    <option value="1">Cash 現金</option>
                                    <option value="2">Deposit 存款</option>
                                    <option value="3">Check 支票</option>
                                    <option value="4">Taiwan Pay 台灣付款</option>
                                    <option value="5">Advance Payment 預付款</option>
                                </select>

                            </li>
                            <li>
                                <input type="date" v-model="item.issue_date">
                            </li>
                            <li>
                                <input type="date" v-model="item.payment_date">
                            </li>
                            <li>
                                <input type="number" min="0" v-model="item.amount" @change="chang_remark(item)">
                            </li>
                            <li>
                                <input type="number" min="0" v-model="item.change">
                            </li>
                            <li>
                                <input type="number" min="0" v-model="item.courier" @change="chang_remark(item)">
                            </li>
                            <li>
                                <input type="text" v-model="item.remark">
                            </li>
                            <li><span @click="del_plus_detail(item.id)">x</span></li>
                        </ul>

                        <ul class="add_row">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_plus_detail()></i></li>
                            <li></li>
                        </ul>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Cancel
                        <cht>取消</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary" @click=payment_save()>Save
                        <cht>儲存</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary" @click=payment_save_complete()>
                        Complete All Payment
                        <cht>完成所有付款</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Ends -->

    <!-- Modal Begins -->
    <div class="modal" id="payment_modal_detail">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1400px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Payment Status
                        <cht>付款狀態</cht>
                    </h5>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="tablebox s02 payment">
                        <ul class="header">
                            <li>
                                <cht>支付方式</cht>
                                Payment Method
                            </li>
                            <li>
                                <cht>開立日期</cht>
                                Issue Date
                            </li>
                            <li>
                                <cht>收到日期</cht>
                                Receive Date
                            </li>
                            <li>
                                <cht>金額</cht>
                                Amount
                            </li>
                            <li>
                                <cht>零錢</cht>
                                Change
                            </li>
                            <li>
                                <cht>代墊</cht>
                                Courier/payment
                            </li>
                            <li>
                                <cht>備註</cht>
                                Remark
                            </li>

                        </ul>

                        <ul v-for="(item, j) in payment">
                            <li>
                                {{ item.type == 1 ? "Cash 現金" : "" }}
                                {{ item.type == 2 ? "Deposit 存款" : "" }}
                                {{ item.type == 3 ? "Check 支票" : "" }}
                                {{ item.type == 4 ? "Taiwan Pay 台灣付款" : "" }}
                                {{ item.type == 5 ? "Advance Payment 預付款" : "" }}
                            </li>
                            <li>
                                {{ item.issue_date }}
                            </li>
                            <li>
                                {{ item.payment_date }}
                            </li>
                            <li>
                                {{ item.amount }}
                            </li>
                            <li>
                                {{ item.change }}
                            </li>
                            <li>
                                {{ item.courier }}
                            </li>
                            <li>
                                {{ item.remark }}
                            </li>

                        </ul>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Cancel
                        <cht>取消</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal Ends -->


</div>

<!-- Bootstrap  -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/vue.js"></script>
<script src="js/a076d05399.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript" src="js/query_pickup_payment.js" defer></script>


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

    </script> 
    
</body>
</html>
