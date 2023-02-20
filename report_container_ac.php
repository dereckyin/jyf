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

                            <button style="margin-left: 20px;" @click="query()"><i aria-hidden="true" class="fas fa-filter"></i></button>
                            <button @click="print()"><i aria-hidden="true" class="fas fa-file-export"></i></button>
                        </div>

                        <!-- <div class="searchblock" style="float:left;">搜尋<input type="text"></div> -->
                    </div>

                    <div class="tablebox s02">
                        <!-- <table class="table table-hover table-striped table-sm table-bordered" id="showUser1" ref="showUser1"> -->
                        <ul class="header">
                            <li>
                                <eng>Date Sent</eng>
                                結關日期
                            </li>
                            <li>
                                <eng>Date C/R</eng>
                                到倉日期
                            </li>
                            <li>
                                <eng>Container Number</eng>
                                櫃號
                            </li>
                            <li>
                                <eng>A/R (By Kilo)</eng>
                                應收帳款(根據重量)
                            </li>
                            <li>
                                <eng>A/R (By Cuft)</eng>
                                應收帳款(根據材積)
                            </li>
                            <li>
                                <eng>A/R</eng>
                                應收帳款
                            </li>
                            <li>
                                <eng>Amount Received</eng>
                                已收金額
                            </li>
                            <li>
                                <eng>Remaining A/R</eng>
                                未收金額
                            </li>
                            <li>
                                <eng>Remarks</eng>
                                備註
                            </li>
                        </ul>
                        <ul v-for='(item, index) in displayedPosts'>
                            <li><p v-for='(it, index) in item.loading'>{{it.date_sent}}</template></li>
                            <li><p v-for='(it, index) in item.loading'>{{it.date_arrive}}</template></li>
                            <li><p v-for='(it, index) in item.loading'>{{it.container_number}}</template></li>
                            <li>₱ {{ item.charge_kilo !== undefined ? Number(item.charge_kilo).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li>₱ {{ item.charge_cuft !== undefined ? Number(item.charge_cuft).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li>₱ {{ Number(item.charge_kilo) + Number(item.charge_cuft) !== undefined ? Number(Number(item.charge_kilo) + Number(item.charge_cuft)).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li>₱ {{ item.charge !== undefined ? Number(item.charge).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li>₱ {{ item.ar !== undefined ? Number(item.ar).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li>
                                <div class="remarks">{{item.remark}}</div>
                                <i class="fas fa-edit" aria-hidden="true" @click="update_remark(item)"></i>
                            </li>
                        </ul>
                     
                        <ul class="total">
                            <li>Total</li>
                            <li></li>
                            <li>{{ container_total }}</li>
                            <li></li>
                            <li></li>
                            <li>₱ {{ total_total !== undefined ? Number(total_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li>₱ {{ charge_total !== undefined ? Number(charge_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li>₱ {{ ar_total !== undefined ? Number(ar_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</li>
                            <li></li>
                        </ul>
                    </div>
                </div>
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
<script type="text/javascript" src="js/report_container_ac.js?random=<?php echo uniqid(); ?>" defer></script>

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
