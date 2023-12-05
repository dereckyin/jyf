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

if(!$decoded->data->sea_expense)
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
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Attendance Detail</title>
    <!--
<meta name="keywords" content="FELIIX">
<meta name="Description" content="FELIIX">
<meta name="robots" content="all" />
<meta name="author" content="FELIIX" />
-->

    <!-- Open Graph protocol -->
    <!--
<meta property="og:site_name" content="FELIIX" />
<meta property="og:url" content="分享網址" />
<meta property="og:type" content="website" />
<meta property="og:description" content="FELIIX" />
<!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>


</head>

<style type="text/css">

    * {
        -webkit-text-size-adjust: none;
        -webkit-font-smoothing: antialiased;
        margin: 0;
        padding: 0;
    }

    *, *::before, *::after {
        box-sizing: border-box;
    }

    ul, li, dl, dd, dt {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    a, a:link, a:visited, a:active, a:hover, area {
        text-decoration: none;
        cursor: pointer;
    }

    a, a:link {
        color: #000;
        display: inline-block;
    }

    a.btn {
        padding: 12px 24px 8px;
        background-color: #2F9A57;
        font-size: 18px;
        color: #FFF;
        font-weight: 700;
        transition: .5s;
        border-radius: 10px;
        vertical-align: middle;
        height: 44px;
    }

    a.btn:hover {
        background-color: #A9E5BF;
    }

    input[type=range], input[type=text], input[type=password], input[type=file], input[type=date], input[type=number], input[type=url], input[type=email], input[type=tel], input[list], input[type=button], input[type=submit], button, textarea, select, output {
        box-sizing: border-box;
        border: 2px solid #1E6BA8;
        background-color: transparent;
        padding: 8px;
        vertical-align: middle;
        font-size: 18px;
        height: 44px;
        width: 70%
    }

    textarea {
        resize: none;
    }

    input, select {
        font-size: 18px;
        font-family: Lato, Arial, Helvetica, 'Noto Sans TC', 'LiHei Pro', "微軟正黑體", "新細明體", 'Microsoft JhengHei', sans-serif;
        font-weight: 500;
        display: inline-block;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        outline: 0 none;
    }

    select {
        background-image: url(images/ui/icon_form_select_arrow_blue.svg);
        background-size: auto 100%;
        background-position: 100% center;
        background-repeat: no-repeat;
        padding-right: 35px;
        padding-left: 15px;
        height: 44px;
        width: 70%;
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

    body {
        background-color: #F0F0F0;
        font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
        font-weight: 300;
    }

    #app {
        color: #000000;
    }

    .mainContent {
        padding: 110px 12px 30px;
        width: 100%;
        min-height: calc(100vh - 100px);
    }

    .mainContent > .block {
        display: none;
        width: 100%;
        border: 2px solid #1E6BA8;
    }

    .mainContent > .block.focus {
        display: block;
        margin-bottom: 40px;
    }

    .block h6 {
        font-size: 36px;
        font-weight: 700;
        color: #1E6BA8;
        border-bottom: 2px solid #1E6BA8;
        padding: 10px 20px;
    }

    .block .box-content {
        padding: 20px 40px 30px;
    }

    .block .formbox2 {
        width: 100%;
    }

    .block .formbox2 ul li {
        font-size: 18px;
        padding: 10px 0;
        font-weight: 500;
        text-align: left;
        border-bottom: 1px solid #707070;
        vertical-align: middle;
    }

    .block .formbox2 ul li.head {
        font-weight: 700;
        border-bottom: none;
    }

    .block .formbox2 ul li img {
        max-width: 100%;
        max-height: 300px;
    }

    .block .formbox2 ul li span {
        display: block;
        margin-bottom: 5px;
    }

    a.nav_link {
        color: #FFFFFF;
        font-weight: bold;
        padding: 0 20px;
        text-decoration: none;
        cursor: pointer;
        border-right: 2px solid #FFFFFF;
        font-size: 16px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    }

    a.nav_link:last-of-type {
        border-right: none;
        margin-right: 90px;
    }

</style>

<body class="second">

<div class="bodybox">
    <!-- header -->
    <header>
        <a href="main.php" class="menu"><span>&#9776;</span></a>

        <div>
            <?php
                        if($decoded->data->sea_expense)
            {
            ?>
            <a class="nav_link" href="attendance_sea.php">
                <eng>Attendance</eng>
            </a>

            <a class="nav_link" href="staff_list_sea.php">
                <eng>Staff List</eng>
            </a>

            <a class="nav_link" href="salary_recorder_sea.php">
                <eng>Salary Recorder</eng>
            </a>

            <a class="nav_link" href="expense_recorder_sea.php">
                <eng>Expense Recorder</eng>
            </a>
            <?php
                        }
                    ?>
            <?php
                        if($decoded->data->sea_expense_v2)
            {
            ?>
            <a class="nav_link" href="expense_recorder_sea_v2.php">
                <eng>Expense Recorder2</eng>
            </a>
            <?php
                        }
                    ?>
        </div>
    </header>
    <!-- header end -->
    <div id='app' class="mainContent">
        <!-- Blocks -->

        <div class="block A focus">
            <h6>{{ username }}</h6>
            <div class="box-content">
                <!-- 表單樣式2 -->
                <div class="formbox2">
                    <div v-for='(record, index) in displayedRecord'>
                        <ul v-if="record.duty_type === 'A'">
                            <li class="head">Time-In Time</li>
                            <li>{{ record.duty_date }}  {{ record.duty_time }}</li>
                        </ul>
                        <ul v-else>
                            <li class="head">Time-Out Time</li>
                            <li>{{ record.duty_date }}  {{ record.duty_time }}</li>
                        </ul>

                        <ul v-if="record.remark !== ''">
                            <li class="head">Remark</li>
                            <li>{{ record.remark }}</li>
                        </ul>

                        <ul>
                            <li class="head">
                                <span>Photo</span>
                                <img :src="'img/' + record.pic_url" v-if="record.pic_url !== ''"></li>
                            </li>
                        </ul>

                        

                        <br />
                        <br />
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/attendance_detail.js"></script>
</html>
