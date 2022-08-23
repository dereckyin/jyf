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

            $airship = $decoded->data->airship;
            $airship_read = $decoded->data->airship_read;

            if(!($airship == "1" || $airship_read == "1"))
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
    <title>Air-Shipment 空運記錄</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/hierarchy-select.min.css" type="text/css">
    <link rel="stylesheet" href="css/vue-select.css" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
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

    </style>

    <style>
        #header {
            background: #1E6BA8;
            padding: 0.5vh;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #header > a {
            margin-left: 25px;
            font-size: 25px;
            cursor: pointer;
        }

        #header > a span {
            color: #FFFFFF;
        }

        #header button {
            border: none;
            margin-right: 25px;
            font-weight: 700;
            font-size: x-large;
            background-color: #1E6BA8;
            color: #FFFFFF;
        }

        #container{
            margin-top:2.5vh;
            margin-left:1.5vw;
            margin-bottom:3vh;
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

        a.nav_link {
            color: #FFFFFF;
            font-weight: bold;
            padding: 0 20px;
            text-decoration: none;
            cursor: pointer;
            border-right: 2px solid #FFFFFF;
        }

        a.nav_link:last-of-type {
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
            width: 260px;
            font-size: 13px;
            font-weight: 400;
            height: 38px;
            vertical-align: middle;
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
        .panel-body .tb_add_record > ul > li:nth-of-type(2) input[type="datetime-local"],
        .panel-body .tb_add_record > ul > li:nth-of-type(2) select {
            width: 380px;
        }

        .panel-body .tb_add_record > ul > li.long {
            width: calc(100vw - 400px);
        }

        li.long > input[type="text"] {
            width: 100%;
        }

        li.long > input[type="text"], li.long > textarea {
            width: 100%!important;
            resize: none;
        }

        li.two_input > * {
            display: inline-block;
        }

        li.two_input > *:first-child {
            margin-right: 20px;
        }

        div.tb_items {
            padding: 20px 40px 5px;
            width: 100%;
            border: 1px solid #969696;
            border-bottom: none;
        }

        div.tb_items:last-of-type {
            border-bottom: 1px solid #969696;
        }

        .tb_items table {
            width: 100%;
            margin-bottom: 20px;
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
            border-bottom: 1px solid rgb(222, 225, 230);
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

        .tb_items > ul {
            list-style-type: none;
            padding-left: 0;
        }

        .tb_items > ul > li:nth-of-type(1) {
            display: table-cell;
            text-align: center;
            width: 220px;
            font-size: 13px;
            height: 38px;
            vertical-align: middle;
            font-weight: bolder;
        }

        .tb_items > ul > li:nth-of-type(2) {
            display: table-cell;
            text-align: left;
            padding-left: 10px;
            height: 38px;
        }

        .tb_items > ul > li:nth-of-type(2) input[type="number"], .tb_items > ul > li:nth-of-type(2) input[type="file"] {
            width: 380px;
        }

        #container_records{
            margin-right: 1.5vw;
        }

        .function_list {
            margin: 2vh 0 1vh;
            display: flex;
            justify-content: space-between;
        }

        .function_list div.function_filter > input, .function_list div.function_filter > select {
            height: 30px;
        }

        .function_list div.function_filter > button {
            width: 30px;
            height: 30px;
        }

        .function_list div.month_btns {
            margin-left: 20px;
        }

        .function_list div.month_btns button.btn-success {
            width: 48.5px;
            height: 30px;
            padding: 0 7px;
            vertical-align: 0;
            text-align: center;
            margin: 0 2px;
        }

        .function_list div.function_page ul {
            margin-bottom: 0;
        }

        #panelchecked {
            overflow-x: auto;
        }

        #panelchecked table{
            width: 100%;
        }

        #panelchecked th, #panelchecked td {
            padding: 8px;
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

        #panelchecked thead tr th {
            min-width: 170px;
        }

        #panelchecked thead tr > th:nth-of-type(3), #panelchecked thead tr > th:nth-of-type(4), #panelchecked thead tr > th:nth-of-type(16) {
            min-width: 260px;
        }

        #panelchecked thead tr th:nth-of-type(2), #panelchecked thead tr th:nth-of-type(7) {
            min-width: 200px;
        }

        #panelchecked thead tr th:nth-last-of-type(1) {
            min-width: 100px;
        }

        #panelchecked tbody tr td i {
            font-size: 20px;
            margin: 8px;
            cursor: pointer;
        }

        #panelchecked tfoot tr th {
            background-color: #e9ecef;
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

        div.tablebox {
            display: table;
            width: 100%;
            line-height: 18px;
        }

        div.tablebox > ul {
            display: table-row;
            transition: .3s;
            background-color: #FFF;
        }

        div.tablebox > ul:nth-of-type(2n+1) {
            background-color: #DDD;
        }

        div.tablebox > ul > li {
            width: auto;
            display: table-cell;
            padding: 8px;
            text-align: center;
            font-size: 16px;
            transition: .3s;
            color: #333;
            min-width: 50px;
            vertical-align: middle;
        }

        div.tablebox > ul > li > eng {
            font-size: 12px;
            margin-left: 5px;
        }

        div.tablebox > ul > li.right {
            text-align: right !important;
        }

        div.tablebox.V > ul > li.header,
        div.tablebox > ul.header {
            background-color: #BBB;
            pointer-events: none;
            font-weight: 500;
            vertical-align: middle;
        }

        div.tablebox > .header eng {
            display: block;
        }

        div.tablebox > ul:hover {
            background-color: #EA631A;
        }

        div.tablebox > ul:hover li.header {
            background-color: #333;
        }

        div.tablebox > ul:hover > li {
            color: #FFF;
        }

        div.tablebox > ul:hover li .btn {
            background-color: #EA631A;
            border: 1px solid #FFF;
        }

        div.tablebox > ul:hover li .btn:hover {
            background-color: #A81B04;
        }

        div.tablebox > ul > li > input,
        div.tablebox > ul > li > textarea,
        div.tablebox > ul > li > select {
            width: 100%;
        }

        div.tablebox > ul > li > textarea {
            height: 150px;
        }

        div.tablebox.withbtn li > input {
            width: calc(100% - 40px);
        }

        div.tablebox .btngroup {
            float: right;
            margin: 0;
        }

        button.quick_move {
            position: fixed;
            width: 50px;
            height: 50px;
            border: 1px solid #999;
            border-radius: 25px;
            font-size: 25px;
            font-weight: 700;
            background-color: rgba(7, 220, 237, 0.5);
            z-index: 999;
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

    <div id="header">

        <a href="main.php"><span>&#9776;</span></a>

        <div>
        <?php
                                                    if($airship == 1)
                                                    {
                                            ?>
            <button :class="[is_viewer == '1'? 'hide' : '']" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseOne" @click="reset()"
                    aria-expanded="true" aria-controls="collapseOne"><i class="fas fa-plus-square fa-lg"></i></button>
                    <?php
                                                    }
                                            ?>
        </div>

    </div>


    <div id="container">

        <button class="quick_move" style="left: 5px; top: calc(50vh - 30px)" onclick="location.href='#header'">↑
        </button>
        <button class="quick_move" style="left: 5px; top: calc(50vh + 30px)" onclick="move_left();">←</button>
        <button class="quick_move" style="right: 5px; top: calc(50vh - 30px)" onclick="location.href='#flag_total'">↓
        </button>
        <button class="quick_move" style="right: 5px; top: calc(50vh + 30px)" onclick="move_right();">→</button>


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

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>收件日期</cht>
                                        Date Received</label>
                                </li>

                                <li>
                                    <input type="date" v-model="date_receive" class="form-control">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>
                                        <cht>模式</cht>
                                        Mode</label>
                                </li>

                                <li class="two_input">
                                    <select class="form-control" v-model="mode">
                                        <option value="">空運</option>
                                        <option value="exp">快遞</option>
                                    </select>
                                </li>
                            </ul>

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>客戶名</cht>
                                        Customer</label>
                                </li>

                                <li>
                                    <input type="text" v-model="customer" class="form-control">
                                </li>
                            </ul>

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>地址</cht>
                                        Address</label>
                                </li>

                                <li class="long">
                                    <input type="text" class="form-control" v-model="address" maxlength="512">
                                </li>
                            </ul>

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>貨品名稱</cht>
                                        Description</label>
                                </li>

                                <li class="long">
                                    <input type="text" class="form-control" v-model="description" maxlength="512">
                                </li>
                            </ul>

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>件數</cht>
                                        Quantity</label>
                                </li>

                                <li>
                                    <input type="text" v-model="quantity" class="form-control" maxlength="512">
                                </li>
                            </ul>

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>重量</cht>
                                        Kilo</label>
                                </li>

                                <li>
                                    <input type="number" v-model.lazy="kilo" class="form-control" min="0">
                                </li>
                            </ul>

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>寄貨人</cht>
                                        Supplier</label>
                                </li>

                                <li>
                                    <input type="text" v-model="supplier" class="form-control">
                                </li>
                            </ul>

                            <ul class="tw">
                                <li>
                                    <label>
                                        <cht>班機與日期</cht>
                                        Flight and Date</label>
                                </li>

                                <li class="two_input">
                                    <input type="text" class="form-control" v-model="flight" placeholder="Ex. BR 271">
                                    <input type="date" class="form-control" v-model="flight_date">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>
                                        <cht>收費金額</cht>
                                        Amount</label>
                                </li>

                                <li class="two_input">
                                    <select class="form-control" v-model="currency">
                                        <option></option>
                                        <option value="NTD">台幣(NTD)</option>
                                        <option value="PHP">菲幣(PHP)</option>
                                    </select>

                                    <input type="number" class="form-control" v-model="total">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>
                                        <cht>付款日期</cht>
                                        Date Paid</label>
                                </li>

                                <li>
                                    <input type="date" class="form-control" v-model="pay_date">
                                </li>
                            </ul>

                            <ul style="margin-bottom: 30px;">
                                <li>
                                    <label>
                                        <cht>付款狀態</cht>
                                        Payment Status</label>
                                </li>

                                <li>
                                    <select class="form-control" v-model="pay_status">
                                        <option></option>
                                        <option value="t">台灣付 Taiwan Paid</option>
                                        <option value="p">菲律賓付 Philippines Paid</option>
                                    </select>
                                </li>
                            </ul>

                            <div class="tb_items tw">
                                <table>
                                    <tr>
                                        <td style="width: 500px;">
                                            <input type="text" class="form-control" v-model="title_ntd">
                                        </td>

                                        <td style="width: 150px;">
                                            <input type="number" class="form-control" v-model="qty_ntd">
                                        </td>

                                        <td style="width: 150px;">
                                            <input type="number" class="form-control" v-model="price_ntd">
                                        </td>

                                        <td style="width: 150px;">
                                            <i class="fas fa-plus-circle" v-if="!editing" id="add_item"
                                               @click="add_plus_detail()"></i>
                                            <i class="fas fa-times-circle" v-if="editing" style="color: indianred;"
                                               @click="clear_item()"></i>
                                            <i class="fas fa-check-circle" v-if="editing" @click="save_item()"></i>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>
                                            <cht>名目</cht>
                                            Title
                                        </th>
                                        <th>
                                            <cht>數量</cht>
                                            Qty
                                        </th>
                                        <th>
                                            <cht>單價</cht>
                                            Unit Price
                                        </th>
                                        <th>Actions</th>
                                    </tr>


                                    <tr v-for="(item, index) in details">
                                        <td>{{ item.title }}</td>
                                        <td>{{ item.qty }}</td>
                                        <td>{{ item.price }}</td>
                                        <td>
                                            
                                            <i class="fas fa-edit" @click="edit_plus_detail(item)"></i>
                                            <i class="fas fa-trash-alt" @click="del_plus_detail(item)"></i>
                                            

                                        </td>
                                    </tr>

                                </table>

                                <ul>
                                    <li>
                                        <label>
                                            <cht>台幣金額</cht>
                                            Amount in NTD</label>
                                    </li>

                                    <li>
                                        <input type="number" v-model="amount" class="form-control">
                                    </li>
                                </ul>

                            </div>


                            <div class="tb_items ph">
                                <table>
                                    <tr>
                                        <td style="width: 500px;">
                                            <input type="text" class="form-control" v-model="title_php">
                                        </td>

                                        <td style="width: 150px;">
                                            <input type="number" class="form-control" v-model="qty_php">
                                        </td>

                                        <td style="width: 150px;">
                                            <input type="number" class="form-control" v-model="price_php">
                                        </td>

                                        <td style="width: 150px;">
                                            <i class="fas fa-plus-circle" v-if="!editing_php" id="add_item"
                                               @click="add_plus_detail_php()"></i>
                                            <i class="fas fa-times-circle" v-if="editing_php" style="color: indianred;"
                                               @click="clear_item_php()"></i>
                                            <i class="fas fa-check-circle" v-if="editing_php" @click="save_item_php()"></i>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>
                                            <cht>名目</cht>
                                            Title
                                        </th>
                                        <th>
                                            <cht>數量</cht>
                                            Qty
                                        </th>
                                        <th>
                                            <cht>單價</cht>
                                            Unit Price
                                        </th>
                                        <th>Actions</th>
                                    </tr>

                                    <tr v-for="(item, index) in details_php">
                                        <td>{{ item.title }}</td>
                                        <td>{{ item.qty }}</td>
                                        <td>{{ item.price }}</td>
                                        <td>
                                            <i class="fas fa-edit" @click="edit_plus_detail_php(item)"></i>
                                            <i class="fas fa-trash-alt" @click="del_plus_detail_php(item)"></i>
                                        </td>
                                    </tr>

                                </table>

                                <ul>
                                    <li>
                                        <label>
                                            <cht>菲幣金額</cht>
                                            Amount in PHP</label>
                                    </li>

                                    <li>
                                        <input type="number" v-model="amount_php" class="form-control">
                                    </li>
                                </ul>

                            </div>


                            <ul class="ph" style="margin-top: 30px;">
                                <li>
                                    <label>
                                        <cht>抵達客人住址時間</cht>
                                        Time Delivery Arrived</label>
                                </li>

                                <li>
                                    <input type="datetime-local" v-model="date_arrive" class="form-control">
                                </li>
                            </ul>

                            <ul class="ph">
                                <li>
                                    <label>
                                        <cht>簽收人</cht>
                                        Person Receive Delivery</label>
                                </li>

                                <li>
                                    <input type="text" v-model="receiver" class="form-control">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>
                                        <cht>補充說明</cht>
                                        Notes</label>
                                </li>

                                <li class="long">
                                    <textarea rows="2" v-model="remark" class="form-control"></textarea>
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

        <div id="container_records">

            <div class="function_list">

                <div class="function_filter">
                <select style="width: 200px; margin-right: 10px;" v-model="date_type">
                    <option value="">抵達客人住址時間 Time Delivery Arrived</option>
                    <option value="r">收件日期 Date Received</option>
                    <option value="p">付款日期 Date Paid</option>
                </select>
                    <input type="date" v-model="start_date">&nbsp; to &nbsp;<input type="date" v-model="end_date">

                    <input class="hide" type="text" v-model="keyword" style="width:15vw; margin-left:1vw;"
                           placeholder="Searching Keyword Here">

                    <select class="hide" v-model="perPage" v-on:change="getRecords(this)">
                        <option v-for="size in inventory" :value="size.id">{{size.name}}</option>
                    </select>

                    <button style="margin-left:1.5vw;" v-on:click="getRecords"><i class="fas fa-filter"></i></button>&ensp;
                    <button v-on:click="printRecord"><i class="fas fa-file-export"></i></button>
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

                <div class="function_page">
                    <ul class="pagination pagination-sm">
                        <li class="page-item" :disabled="page == 1" @click="page < 1 ? page = 1 : page--"
                            v-on:click="getRecords"><a class="page-link">Previous</a></li>

                        <li class="page-item" v-for="pg in pages" @click="page=pg" :class="[page==pg ? 'active':'']"
                            v-on:click="getRecords"><a class="page-link">{{ pg }}</a></li>

                        <li class="page-item" :disabled="page == pages.length" @click="page++" v-on:click="getRecords">
                            <a
                                    class="page-link">Next</a></li>
                    </ul>
                </div>

            </div>


            <div id="panelchecked">

                <table class="table table-sm table-bordered">

                    <thead class="thead-light">

                    <tr>

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
                            <cht>功能</cht>
                            Actions
                        </th>
                    </tr>

                    </thead>

                    <tbody>

                    <tr v-for="(item, index) in items" :class="[(item.status == '-1' ? 'deleted' : '')]">

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
                        <?php
                                                    if($airship == 1)
                                                    {
                                            ?>
                            <i class="fas fa-edit fa-lg" @click="edit(item)" aria-hidden="true" v-if="item.status != -1"></i>
                            <i class="fas fa-trash-alt fa-lg" @click="deleteRecord(item)" aria-hidden="true" v-if="item.status != -1"></i>
                            <?php
                                                    }
                                            ?>
                        </td>
                    </tr>

                    </tbody>


                    <tfoot class="thead-light" id="flag_total">

                    <tr>
                        <th colspan="6">Total</th>
                        <th style="text-align: right;">
                            
                            {{ rec_kilo !== undefined ?
                            Number(rec_kilo).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} 
                            
                        </th>
                        <th colspan="2"></th>
                        <th style="text-align: right;">
                            
                            {{ rec_ntd !== undefined ?
                            Number(rec_ntd).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} NTD
                            {{ rec_php !== undefined ?
                            Number(rec_php).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} PHP
                        </th>
                        <th colspan="2"></th>
                        <th style="text-align: right;">
                            
                            {{ amount !== undefined ?
                            Number(rec_amount).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}
                            
                        </th>
                        <th style="text-align: right;">
                            
                            {{ amount_php !== undefined ?
                                Number(rec_amount_php).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}
                            
                        </th>
                        <th></th>
                        <th colspan="4"></th>
                    </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>


    <!-- The Modal -->
    <div class="modal fade" id="details_NTD">
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Cancel
                        <cht>取消</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>


    <!-- The Modal -->
    <div class="modal fade" id="details_PHP">
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Cancel
                        <cht>取消</cht>
                    </button>
                </div>

            </div>
        </div>
    </div>


</div>


</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<!-- <script src="https://cdn.bootcss.com/moment.js/2.21.0/moment.js"></script>-->
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


    function move_left() {
        const step = document.getElementById('container_records').clientWidth - 50;
        document.getElementById('panelchecked').scrollLeft -= step;
    };

    function move_right() {
        const step = document.getElementById('container_records').clientWidth - 50;
        document.getElementById('panelchecked').scrollLeft += step;
    };

</script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script defer src="js/airship_records.js"></script>

</html>
