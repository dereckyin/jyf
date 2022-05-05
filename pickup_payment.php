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
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">

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

        .tb_measure thead tr th:nth-of-type(2) {
            min-width: 120px;
        }

        .tb_measure thead tr th:nth-of-type(3) {
            max-width: 300px;
        }

        .tb_measure thead tr th:nth-of-type(6) {
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

        div.block > .listheader{
            width: 100%;
            padding: 5px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            Pickup and Payment
            <cht>提貨與付款</cht>
        </h6>

        <div class="block record show">
            <h6>
                Measurement Records
                <cht>丈量記錄</cht>
            </h6>
            <!-- list -->
            <div class="mainlist">

                <div class="tablebox d02">
                    <ul class="header">
                        <li>
                            <cht>勾選</cht>
                            Check
                        </li>
                        <!--
                        <li>
                            <cht>丈量日期</cht>
                            Date Encoded
                        </li>
                        -->
                        <li>
                            <cht>貨櫃到倉日期</cht>
                            Date C/R (Date Container arrived Manila)
                        </li>
                        <li>
                            <cht>貨櫃數量</cht>
                            Qty of Containers
                        </li>
                        <li>
                            <cht>櫃號</cht>
                            Containers Number
                        </li>
                        <li>
                            <cht>備註</cht>
                            Remark
                        </li>
                    </ul>

                    <ul v-for='(record, index) in displayedLoading'>
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.id" :true-value="1"
                                   v-model:checked="record.is_checked"></li>
                        <!-- <li>{{ record.date_encode }}</li> -->
                        <li>{{ record.date_arrive }}</li>
                        <li>{{ record.qty }}</li>
                        <li>{{ record.container }}</li>
                        <li>{{ record.remark }}</li>
                    </ul>

                </div>

            </div>
            <div class="btnbox">
                <?php
                if($phili_read == "0")
{
    ?>
                <a class="btn small" @click="pickup()">Generate Pickup / Payment Record
                    <cht>打單</cht>
                </a>
                <?php
}
?>
            </div>
        </div>


        <div class="block record show">
            <h6>Pickup / Payment Records
                <cht>提貨與付款記錄</cht>
            </h6>

            <div class="listheader">

                <select style="text-align: left; width: 610px;" @change="getMeasures()"
                        v-model="filter">
                    <option value="F">List "All Except For All Completed" (全部列出，除了已提貨且已付款)</option>
                    <option value="N">List "Not Yet Pickup" (僅列出未提貨)</option>
                    <option value="A">List "Already Pickup Not Yet Paid" (僅列出已提貨但未付款)</option>
                    <option value="D">List "Already Pickup And Paid" (僅列出已提貨且已付款)</option>
                    <option value="">List "All" (全部列出)</option>
                </select>

                <div v-show="filter == '' || filter == 'D'">
                    <input type="text" v-model="search" placeholder="Only Search for DR" style="width: 200px; margin-right: 10px;">
                    <button @click="getMeasures('search')">Search</button>
                </div>

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
                            <cht>勾選</cht>
                            Check
                        </th>
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
                            <td v-if="j == 0" :rowspan="row.measure.length">
                                <input type="checkbox" name="record_id" true-value="1" class="alone" value=""
                                       v-model="row.is_checked">
                            </td>
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
                                        @click="item_payment(row.payments, row.ar, row.measure_detail_id, row.measure)">Encode
                                </button>
                                <?php
}
?>
                                <button data-toggle="modal" data-target="#payment_modal_detail"
                                        v-if="item.payment_status != ''"
                                        @click="item_payment(row.payments, row.ar, row.measure_detail_id, row.measure)">Detail
                                </button>
                            </td>
                        </tr>
                    </template>

                    </tbody>
                </table>

                <div class="btnbox" style="border: none; margin-top: 10px;">
                    <?php
                if($phili_read == "0")
{
    ?>
                    <a class="btn small" @click="merge_item()">
                        Merge Items
                        <cht>合併項目</cht>
                    </a>
                    <a class="btn small" @click="decompose_item()">
                        Decompose Item
                        <cht>拆分項目</cht>
                    </a>
                    <a class="btn small" @click="edit_measurement()">
                        Edit Measurement Data
                        <cht>修改丈量資料</cht>
                    </a>

                    <a class="btn small" @click="seperate_record()">
                        Decompose Measurement Data
                        <cht>拆分丈量資料</cht>
                    </a>

                    <a class="btn small" @click="archive_record()" v-if="filter == 'D'">
                        Archive
                        <cht>歸檔</cht>
                    </a>

                    <?php
}
?>
                </div>
            </div>

        </div>

    </div>


    <!-- The Modal -->
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

    <!-- The Modal -->
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


    <!-- The Modal -->
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

    <!-- The Modal -->
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


    <!-- The Modal -->
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


    <!-- The Modal -->
    <div class="modal" id="edit_record_modal">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1200px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Edit Measurement Data
                        <cht>修改丈量資料</cht>
                    </h5>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="tablebox s02">
                        <ul class="header">
                            <li>
                                SOLD TO
                            </li>
                            <li>
                                <cht>重量</cht>
                                Kilo
                            </li>
                            <li>
                                <cht>才積</cht>
                                Cuft
                            </li>
                            <li>
                                <cht>重量單價</cht>
                                Price per Kilo
                            </li>
                            <li>
                                <cht>才積單價</cht>
                                Price per Cuft
                            </li>
                            <li>
                                <cht>收費金額</cht>
                                Amount
                            </li>
                        </ul>

                        <ul>
                            <li><input type="text" v-model="measure_to_edit.customer"></li>
                            <li><input type="number" min="0" v-model="measure_to_edit.kilo" @change="change_A()"></li>
                            <li><input type="number" min="0" v-model="measure_to_edit.cuft" @change="change_B()"></li>
                            <li><input type="number" min="0" v-model="measure_to_edit.kilo_price" @change="change_C()"></li>
                            <li><input type="number" min="0" v-model="measure_to_edit.cuft_price" @change="change_D()"></li>
                            <li><input type="number" min="0" v-model="measure_to_edit.charge"></li>
                        </ul>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning" @click="edit_measurement_cancel()">Cancel
                        <cht>取消</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary" @click="save_measurement_data()">Save
                        <cht>儲存</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>


    <!-- The Modal -->
    <div class="modal" id="seperate_record_modal">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1400px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Decompose Measurement Data
                        <cht>拆分丈量資料</cht>
                    </h5>
                </div>

                <!-- Modal body -->
                <div class="modal-body" style="max-height: calc(100vh - 320px); overflow-y: auto;">
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
                            <li style="min-width: 150px;">
                                <cht>拆分至</cht>
                                Decompose To
                            </li>
                        </ul>

                        <ul v-for="(item, j) in measure_to_seperate.record">
                            <li>{{ item.date_receive }}</li>
                            <li>{{ item.customer }}</li>
                            <li>{{ item.description }}</li>
                            <li>{{ item.quantity }}</li>
                            <li>{{ item.supplier }}</li>
                            <li>{{ item.remark }}</li>
                            <li>
                                <select v-model="item.group">
                                    <option value='A'>Group A</option>
                                    <option value='B'>Group B</option>
                                </select>
                            </li>
                        </ul>

                    </div>

                    <hr style="margin: 20px 0;">

                    <div class="tablebox s02">
                        <ul class="header">
                            <li></li>
                            <li>
                                SOLD TO
                            </li>
                            <li>
                                <cht>重量</cht>
                                Kilo
                            </li>
                            <li>
                                <cht>才積</cht>
                                Cuft
                            </li>
                            <li>
                                <cht>重量單價</cht>
                                Price per Kilo
                            </li>
                            <li>
                                <cht>才積單價</cht>
                                Price per Cuft
                            </li>
                            <li>
                                <cht>收費金額</cht>
                                Amount
                            </li>
                        </ul>

                        <ul>
                            <li>Group A</li>
                            <li><input type="text" v-model="group_a.customer"></li>
                            <li><input type="number" min="0" v-model="group_a.kilo" @change="A_change_A()"></li>
                            <li><input type="number" min="0" v-model="group_a.cuft" @change="A_change_B()"></li>
                            <li><input type="number" min="0" v-model="group_a.kilo_price" @change="A_change_C()"></li>
                            <li><input type="number" min="0" v-model="group_a.cuft_price" @change="A_change_D()"></li>
                            <li><input type="number" min="0" v-model="group_a.charge"></li>
                        </ul>

                        <ul>
                            <li>Group B</li>
                            <li><input type="text" v-model="group_b.customer"></li>
                            <li><input type="number" min="0" v-model="group_b.kilo" @change="B_change_A()"></li>
                            <li><input type="number" min="0" v-model="group_b.cuft" @change="B_change_B()"></li>
                            <li><input type="number" min="0" v-model="group_b.kilo_price" @change="B_change_C()"></li>
                            <li><input type="number" min="0" v-model="group_b.cuft_price" @change="B_change_D()"></li>
                            <li><input type="number" min="0" v-model="group_b.charge"></li>
                        </ul>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning" @click="seperate_record_cancel()">Cancel
                        <cht>取消</cht>
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary" @click="save_seperate_data()">Save
                        <cht>儲存</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>

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
<script type="text/javascript" src="js/pickup_payment_v2.js" defer></script>

</body>
</html>
