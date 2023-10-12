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

if(!$decoded->data->report1)
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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>NTD~PHP 幫客人匯款記錄表</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/hierarchy-select.min.css" type="text/css">
    <link rel="stylesheet" href="css/vue-select.css" type="text/css">
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"> -->
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>

    <style>
        th {
            text-align: center;
        }

        td {
            text-align: center;
            vertical-align: middle !important;
            font-size: small;
        }

        .red {
            color: #ff0000;
        }

        .orange {
            color: #ffa500;
        }

        .green {
            color: #00B000;
        }

        .blue {
            color: #0000ff;
        }

        .hide {
            display: none;
        }

        .header {
            background-color: rgb(30, 107, 168);
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header button:focus {
            outline: none !important;
        }

        div.record_color {
            display: flex;
            align-items: center;
            height: 100%;
        }

        div.record_color > label {
            width: 18px;
            height: 18px;
            margin-bottom: 0;
            margin-left: 3px;
        }

        div.record_color > input:not(:first-child) {
            margin-left: 15px;
        }

        .custom-control-label::before {
            top: 0.75rem !important;
        }

        .custom-control-label::after {
            top: 0.75rem !important;
        }

        a.nav_link{
           color: #FFFFFF;
            font-weight: bold;
            padding: 0 20px;
            text-decoration: none;
            cursor: pointer;
            border-right: 2px solid #FFFFFF;
        }

        a.nav_link:last-of-type{
            border-right: none;
            margin-right: 20px;
        }

        .panel-body {
            border: 3px solid rgb(222, 226, 230);
            border-top: none;
            padding: 20px 20px 0;
        }

        .panel-body .tb_add_record {

        }

        .panel-body .tb_add_record > ul {
            list-style-type: none;
            padding-left: 0px;
        }

        .panel-body .tb_add_record > ul > li:nth-of-type(1) {
            display: table-cell;
            text-align: center;
            width: 230px;
            font-size: 13px;
            font-weight: 400;
            height: 38px;
        }

        .panel-body .tb_add_record > ul > li:nth-of-type(2) {
            display: table-cell;
            text-align: left;
            padding-left: 10px;
            height: 38px;
        }

        .panel-body .tb_add_record > ul > li:nth-of-type(2) input[type="date"],
        .panel-body .tb_add_record > ul > li:nth-of-type(2) input[type="text"],
        .panel-body .tb_add_record > ul > li:nth-of-type(2) input[type="number"],
        .panel-body .tb_add_record > ul > li:nth-of-type(2) select {
            width: 380px;
        }

        .tb_items {
            padding: 0 40px;
            width: 100%;
            margin-bottom: 30px;
        }

        .tb_items table {
            width: 100%;
        }

        .tb_items tr:nth-of-type(1) td {
            padding: 0 20px 10px;

        }

        .tb_items tr:nth-of-type(1) td input[type="text"] {
            border: none;
            border-bottom: 1px solid black;
            border-radius: 0;
            font-size: 14px;
            text-align: center;
        }

        .tb_items tr:nth-of-type(1) td input[type="text"], .tb_items tr:nth-of-type(1) td input[type="date"], .tb_items tr:nth-of-type(1) td select {
            border: none;
            border-bottom: 1px solid black;
            border-radius: 0;
            font-size: 14px;
        }

        .tb_items i {
            font-size: 24px;
            color: #206766;
            margin: 0 5px;
            cursor: pointer;
        }

        .tb_items tr:nth-of-type(2) th, .tb_items tr:nth-of-type(n+3) td {
            padding: 5px;
            border: 2px solid rgb(222, 225, 230);
        }

        .function_list {
            margin-top: 2vh;
            margin-bottom: 1vh;
        }

        .function_list > input, .function_list > select {
            height: 30px;
        }

        .function_list > button {
            width: 30px;
            height: 30px;
        }

        #panelchecked {
            overflow-x: auto;
        }

        .table > :not(:first-child) {
            border-top: none;
        }

        #panelchecked table tr.deleted td, #panelchecked table tr.deleted td > div > label {
            text-decoration: line-through;
            text-decoration-color: red;
        }

        #panelchecked thead:first-of-type tr th {
            font-size: 14px;
            background-color: #e9ecef;
        }

        #panelchecked thead:first-of-type tr th cht {
            display: block;
            font-size: 13px;
        }

        #panelchecked thead:first-of-type tr th:nth-last-of-type(2), #panelchecked thead:first-of-type tr th:nth-last-of-type(3) {
            background-color: rgba(250, 250, 210, 1);
        }

        #panelchecked thead tr th {
            min-width: 170px;
        }

        #panelchecked thead tr th:nth-of-type(9), #panelchecked thead tr th:nth-of-type(13) {
            min-width: 260px;
        }

        #panelchecked thead tr th:nth-of-type(3), #panelchecked thead tr th:nth-of-type(6), #panelchecked thead tr th:nth-of-type(8), #panelchecked thead tr th:nth-of-type(10), #panelchecked thead tr th:nth-of-type(12) {
            min-width: 140px;
        }

        #panelchecked thead tr th:nth-last-of-type(1) {
            min-width: 100px;
        }

        #panelchecked tbody tr td.yellow {
            background-color: rgba(250, 250, 210, 0.3);
        }

        #panelchecked tbody tr td button {
            height: 30px;
        }

        #panelchecked tbody tr td:nth-last-of-type(3) > input[type='date'] {
            width: 160px;
            height: 32.8px;
            border-radius: 5px;
            border: 1px solid rgb(153, 153, 153);
        }

        #panelchecked tbody tr td:nth-last-of-type(2) > input[type='text'] {
            width: 200px;
            height: 32.8px;
            border-radius: 5px;
            border: 1px solid rgb(153, 153, 153);
        }

        #panelchecked tfoot tr th {

            background-color: #e9ecef;
        }

        header {
            width: 100%;
            height: 70px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #1E6BA8;
            color: #FFF;
            padding: 10px;
            box-shadow: 2px 2px 2px rgb(0 0 0 / 40%);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header a.menu {
            margin-left: 25px;
            font-size: 25px;
            cursor: pointer;
        }

        header a.menu span {
            color: #FFFFFF;
        }

    </style>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/hierarchy-select.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>


</head>

<body>


<div id="app">

    <!-- header -->
    <header>
        <?php
            if($decoded->data->status_1 == 1 || $decoded->data->status_2 == 1)
            {
         ?>

            <a href="main.php" class="menu"><span>&#9776;</span></a>

        <?php
             }
         ?>


        <?php
            if($decoded->data->status_1 == 0 && $decoded->data->status_2 == 0)
            {
         ?>

            <a @click="logout()" class="menu"><span>&#9776;</span></a>

        <?php
             }
         ?>


        <div>
            <a class="nav_link" href="car_schedule_calendar.php">
                <eng>Car Schedule</eng>
            </a>

            <?php
                        if($decoded->data->status_1)
            {
            ?>
            <a class="nav_link" href="attendance_v2.php">
                <eng>Attendance</eng>
            </a>

            <a class="nav_link" href="staff_list.php">
                <eng>Staff List</eng>
            </a>

            <a class="nav_link" href="salary_recorder.php">
                <eng>Salary Recorder</eng>
            </a>

            <a class="nav_link" href="expense_recorder.php">
                <eng>Expense Recorder</eng>
            </a>

            <a class="nav_link" href="details_ntd_php.php">
                <eng>NTD~PHP</eng>
            </a>

            <?php
                        }
                    ?>
            <?php
                        if($decoded->data->status_2)
            {
            ?>
            <a class="nav_link" href="expense_recorder_v2.php">
                <eng>Expense Recorder2</eng>
            </a>
            <?php
                        }
             ?>

            <button data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true"
                    aria-controls="collapseOne" class=""
                    style="border: none; margin-right: 25px; font-weight: 700; font-size: x-large; background-color: rgb(30, 107, 168); color: rgb(255, 255, 255);">
                <i aria-hidden="true" class="fas fa-plus-square fa-lg"></i>
            </button>


        </div>
    </header>
    <!-- header end -->



    <div style="margin-top: 92px; margin-left:1.5vw; margin-bottom:3vh;">


        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="width:98.5%;">

            <div class="panel panel-default">

                <div class="panel-heading" role="tab" id="headingOne"
                     style="border: 3px solid rgb(222,226,230); padding:0.5% 0 0.2% 1%;">

                    <h4 class="panel-title">

                    <span
                            style="font-size: 18px;">Add & Edit Record</span>

                    </h4>
                </div>

                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne"
                     :ref="'collapseOne'">

                    <div class="panel-body">

                        <div class="tb_add_record">

                            <ul>
                                <li>
                                    <label>Client Name</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="record.client_name">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Payee Name</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="record.payee_name">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Amount in NTD</label>
                                </li>

                                <li>
                                    <input type="number" class="form-control" v-model="record.amount">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Currency Rate (Yahoo)</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="record.rate_yahoo">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Currency Rate</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="record.rate">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Amount in PHP</label>
                                </li>

                                <li>
                                    <input type="number" class="form-control" v-model="record.amount_php" @change="calculate_total()">
                                </li>
                            </ul>

                            <div class="tb_items">
                                <table>
                                    <tr>
                                        <td>
                                            <input type="date" class="form-control" style="width: 170px;" v-model="receive_date">
                                        </td>

                                        <td>
                                            <select class="form-control" style="width: 205px;" v-model="payment_method">
                                                <option value="">Choose Payment Method</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Deposit">Deposit</option>
                                                <option value="Check">Check</option>
                                            </select>
                                        </td>

                                        <td>
                                            <select class="form-control" style="width: 190px;" v-model="account_number">
                                                <option value="">Choose (Into) Account</option>
                                                <option value="0000028884751">0000028884751</option>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control" style="width: 250px;" v-model="check_details"
                                                   placeholder="Encode Check Details">
                                        </td>

                                        <td>
                                            <input type="number" class="form-control" style="width: 225px;" v-model="receive_amount"
                                                   placeholder="Encode Received Amount">
                                        </td>

                                        <td>
                                            <i class="fas fa-plus-circle" v-if="!editing" id="add_item"
                                               @click="add_plus_detail()"></i>
                                            <i class="fas fa-times-circle" v-if="editing" style="color: indianred;"
                                               @click="clear_item()"></i>
                                            <i class="fas fa-check-circle" v-if="editing" @click="save_item()"></i>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Received Date</th>
                                        <th>Payment Method</th>
                                        <th>(Into) Account</th>
                                        <th>Check Details</th>
                                        <th>Received Amount</th>
                                        <th>Actions</th>
                                    </tr>

                                    <tr v-for="(item, index) in record.details">
                                        <td>{{ item.receive_date }}</td>
                                        <td>{{ item.payment_method }}</td>
                                        <td>{{ item.account_number }}</td>
                                        <td>{{ item.check_details }}</td>
                                        <td>{{ item.receive_amount }}</td>
                                        <td>
                                            <i class="fas fa-edit" @click="edit_plus_detail(item.id)"></i>
                                            <i class="fas fa-trash-alt" @click="del_plus_detail(item.id)"></i>
                                        </td>
                                    </tr>

                                </table>
                            </div>

                            <ul>
                                <li>
                                    <label>Total Received Amount</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="record.total_receive" @change="calculate_total_amount()">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Overpayment</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="record.overpayment">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Remarks</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" style="width: calc( 100vw - 400px);"
                                           v-model="record.remark">
                                </li>
                            </ul>

                            <!--
                            <ul>
                                <li>
                                    <label>Record Color</label>
                                </li>

                                <li>
                                    <div class="record_color">
                                        <input type="radio" name="record_color" id="record_color_black" value="x"
                                               v-model="is_marked" checked="checked">
                                        <label for="record_color_black" style="background-color: black;"></label>

                                        <input type="radio" name="record_color" id="record_color_red" value="1"
                                               v-model="is_marked">
                                        <label for="record_color_red" style="background-color: red;"></label>

                                        <input type="radio" name="record_color" id="record_color_orange" value="2"
                                               v-model="is_marked">
                                        <label for="record_color_orange" style="background-color: orange;"></label>

                                        <input type="radio" name="record_color" id="record_color_green" value="3"
                                               v-model="is_marked">
                                        <label for="record_color_green" style="background-color: green;"></label>

                                        <input type="radio" name="record_color" id="record_color_blue" value="4"
                                               v-model="is_marked">
                                        <label for="record_color_blue" style="background-color: blue;"></label>

                                    </div>
                                </li>
                            </ul>
                            -->

                        </div>

                        <div style="margin-left:6vw; margin-top:2vh; margin-bottom:1.5vh;">

                            <button class="btn btn-secondary" style="width:10vw; font-weight:700" v-on:click="reset()">
                                Reset
                            </button>

                            <button class="btn btn-secondary" style="width:10vw; font-weight:700; margin-left:2vw;"
                                    data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne" v-on:click="reset()">Cancel
                            </button>

                            <button class="btn btn-primary" style="width:10vw; font-weight:700; margin-left:2vw;"
                                    data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne" v-on:click="apply()">Save
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="function_list">

            <input type="date" v-model="start_date">&nbsp; to &nbsp;<input type="date" v-model="end_date">

            <input type="text" v-model="keyword" style="width:15vw; margin-left:1vw;"
                   placeholder="Searching Keyword Here">

            <select class="hide" v-model="perPage" v-on:change="getRecords(this)">
                <option v-for="size in inventory" :value="size.id">{{size.name}}</option>
            </select>

            <button style="margin-left:1.5vw;" v-on:click="getRecords"><i class="fas fa-filter"></i></button>&ensp;
            <button v-on:click="printRecord"><i class="fas fa-file-export"></i></button>&ensp;


            <ul class="pagination pagination-sm hide" style="float:right; margin-right:1.5vw;">
                <li class="page-item" :disabled="page == 1" @click="page < 1 ? page = 1 : page--"
                    v-on:click="getRecords"><a class="page-link">Previous</a></li>

                <li class="page-item" v-for="pg in pages" @click="page=pg" :class="[page==pg ? 'active':'']"
                    v-on:click="getRecords"><a class="page-link">{{ pg }}</a></li>

                <li class="page-item" :disabled="page == pages.length" @click="page++" v-on:click="getRecords"><a
                        class="page-link">Next</a></li>
            </ul>

        </div>


        <div id="panelchecked">

            <table class="table table-sm table-bordered" style="width:97vw;">

                <thead class="thead-light">

                <tr>

                    <th class="text-nowrap">
                        <cht>客戶名</cht>
                        Customer
                    </th>

                    <th class="text-nowrap">
                        <cht>廠商名稱</cht>
                        Payee
                    </th>

                    <th class="text-nowrap">
                        <cht>台幣金額</cht>
                        Amount in NTD
                    </th>

                    <th class="text-nowrap">
                        <cht>匯率(雅虎)</cht>
                        Currency Rate (Yahoo)
                    </th>

                    <th class="text-nowrap">
                        <cht>匯率</cht>
                        Currency Rate
                    </th>

                    <th class="text-nowrap">
                        <cht>菲幣金額</cht>
                        Amount in PHP
                    </th>

                    <th class="text-nowrap">
                        <cht>收款日期</cht>
                        Received Date
                    </th>

                    <th class="text-nowrap">
                        <cht>付款方式</cht>
                        Payment Method
                    </th>

                    <th class="text-nowrap">
                        <cht>付款細節</cht>
                        Payment Details
                    </th>

                    <th class="text-nowrap">
                        <cht>收款金額</cht>
                        Received Amount
                    </th>

                    <th class="text-nowrap">
                        <cht>總收款金額</cht>
                        Total Received Amount
                    </th>

                    <th class="text-nowrap">
                        <cht>溢付金額</cht>
                        Overpayment
                    </th>

                    <th class="text-nowrap">
                        <cht>備註</cht>
                        Remarks
                    </th>

                    <th class="text-nowrap">
                        <cht>付款日期</cht>
                        Paid Date
                    </th>

                    <th class="text-nowrap">
                        <cht>廠商名稱</cht>
                        Payee
                    </th>

                    <th class="text-nowrap">
                        <cht>功能</cht>
                        Actions
                    </th>
                </tr>

                </thead>

                <tbody>

                    <template v-for="item in items">
                        <tr :class="[(item.status == '-1' ? 'deleted' : '')]">
                            <td :rowspan="item.details.length">{{ item.client_name }}</td>
                            <td :rowspan="item.details.length">{{ item.payee_name }}</td>
                            <td :rowspan="item.details.length">{{ item.amount }}</td>
                            <td :rowspan="item.details.length">{{ item.rate_yahoo }}</td>
                            <td :rowspan="item.details.length">{{ item.rate }}</td>
                            <td :rowspan="item.details.length">{{ item.amount_php }}</td>
                            <template v-for="(it, index) in item.details">
                                <td v-if="index == 0">{{ it.receive_date }}</td>
                                <td v-if="index == 0">{{ it.payment_method }}</td>
                                <td v-if="index == 0">{{ it.account_number }} / {{ it.check_details }}</td>
                                <td v-if="index == 0">{{ it.receive_amount }}</td>
                            </template>
                            <td :rowspan="item.details.length">{{ item.total_receive }}</td>
                            <td :rowspan="item.details.length">{{ item.overpayment }}</td>
                            <td :rowspan="item.details.length">{{ item.remark }}</td>
                            <td :rowspan="item.details.length" class="yellow">
                                <div v-show="item.is_edited == 1">
                                    <label>{{ item.pay_date }}</label>
                                </div>
                            <?php
                            if($decoded->data->status_1 != "1")
                            {
                            ?>
                                <input v-show="item.is_edited == 0" type="date" v-model="item.pay_date">
                            <?php
                            }
                            ?>
                            </td>
                            <td :rowspan="item.details.length" class="yellow">
                                <div v-show="item.is_edited == 1">
                                    <label>{{ item.payee }}</label>
                                </div>
                            <?php
                            if($decoded->data->status_1 != "1")
                            {
                            ?>
                                <input v-show="item.is_edited == 0" type="text" v-model="item.payee">
                            <?php
                            }
                            ?>
                            </td>
                            <td :rowspan="item.details.length" class="text-nowrap" v-show="item.status !== '-1'">
                            
                            <?php
                                if($decoded->data->status_1 == "1")
                                {
                            ?>
                            
                                <button v-if="item.is_edited == 1" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                        aria-expanded="true" aria-controls="collapseOne" v-on:click="edit(item)"><i
                                        class="fas fa-edit"></i>
                                </button>

                                <button v-if="item.is_edited == 1" v-on:click="deleteRecord(item.id)"><i
                                        class="fas fa-times"></i>
                                </button>
                            <?php
                                }
                            ?>

                            <?php
                                if($decoded->data->status_1 != "1")
                                {
                            ?>
                                <button v-show="item.is_edited == 1" @click="editRow(item)">修改</button>
                                <button v-show="item.is_edited == 0" @click="confirmRow(item)">確認</button>
                                <button v-show="item.is_edited == 0" @click="cancelRow(item)">取消</button>
                            <?php
                                }
                            ?>

                            </td>
                        </tr>

                        <tr v-if="index !== 0" v-for="(it, index) in item.details" :class="[(item.status == '-1' ? 'deleted' : '')]">
                            <td v-if="index !== 0">{{ it.receive_date }}</td>
                            <td v-if="index !== 0">{{ it.payment_method }}</td>
                            <td v-if="index !== 0">{{ it.account_number }} / {{ it.check_details }}</td>
                            <td v-if="index !== 0">{{ it.receive_amount !== undefined ? Number(it.receive_amount).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</td>
                        </tr>
                    </template>


                </tbody>
                <!--
                                <tbody>
                                <template v-for='(row, i) in items'>
                                    <tr v-for='(item, j) in row.payment' :class="[row.status == '-1'? 'deleted' : '']">
                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.sales_date }}</td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.sales_name }}</td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.customer_name }}</td>


                                        <td>{{ item.product_name }}</td>

                                        <td>{{ item.qty == "" ? "" : Number(item.qty).toLocaleString() }}</td>

                                        <td>{{ item.price == "" ? "" : Number(item.price).toLocaleString() }}</td>

                                        <td>{{ item.free != "" ? "FREE" : Number((item.price == "" ? 0 : item.price ) * (item.qty == ""
                                            ? 0 : item.qty )).toLocaleString() }}
                                        </td>


                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.total_amount == "" ? "" :
                                            Number(row.total_amount).toLocaleString() }}
                                        </td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.discount == "" ? "" :
                                            Number(row.discount).toLocaleString() }}
                                        </td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ Number((row.total_amount == "" ? 0 :
                                            row.total_amount) - (row.discount == "" ? 0 : row.discount)).toLocaleString() }}
                                        </td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.invoice }}</td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.payment_method }} <br/> {{ row.teminal }}
                                        </td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.remark }}</td>

                                        <td v-if="j == 0" :rowspan="row.payment.length">
                                            <button v-if="row.status != -1" @click="deleteRecord(row.id)"><i aria-hidden="true"
                                                                                                             class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>

                                </tbody>
                -->
                <tfoot class="thead-light">

                <tr>
                    <th colspan="2">Total</th>
                    <th style="text-align: right;">{{ amt_tw !== undefined ? Number(amt_tw).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</th>
                    <th colspan="2"></th>
                    <th style="text-align: right;">{{ amt_php !== undefined ? Number(amt_php).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</th>
                    <th colspan="4"></th>
                    <th style="text-align: right;">{{ amt_total !== undefined ? Number(amt_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</th>
                    <th style="text-align: right;">{{ amt_over !== undefined ? Number(amt_over).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</th>
                    <th colspan="4"></th>
                </tr>

                </tfoot>

            </table>

            <br><br>


        </div>


    </div>


</div>


</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<!-- <script src="https://cdn.bootcss.com/moment.js/2.21.0/moment.js"></script> -->
<script src="js/vue-select.js"></script>
<script src="js/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="js/a076d05399.js"></script>
<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<script>

    $(document).ready(function () {
        var today = new Date();
        var dd = ("0" + (today.getDate())).slice(-2);
        var mm = ("0" + (today.getMonth() + 1)).slice(-2);
        var yyyy = today.getFullYear();
        today = yyyy + '-' + mm + '-' + dd;
        $("#todays-date").attr("value", today);
        $("#todays_date").attr("value", today);
    });

</script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script defer src="js/details_ntd_php.js"></script>

</html>
