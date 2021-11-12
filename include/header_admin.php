<link rel="stylesheet" href="case.css">
<style>
    .side-menu {
        position: absolute;
        width: 300px;
        height: 100vh;
        background-color: var(--blue01);
        top: 0;
        left: 0;
        transform: translateX(-100%);
        overflow-y: auto;
    }

    .side-menu-logo {
        height: 60px;
        background-color: var(--blue01);
        padding: 10px;
    }

    .side-menu nav {
        width: 100%;
        padding: 10px 20px 0;
    }

    .side-menu nav > a, .side-menu nav > dl.sub dt {
        border-left: none;
    }

    .side-menu nav a {
        color: #FFF;
    }

    .side-menu nav a:hover {
        color: var(--orange01);
    }

    .side-menu nav > * {
        border-top: 1px solid #FFF;
        padding: 5px;

    }

    .side-menu nav > *:first-child {
        border-top: none;

    }

    .side-menu nav > dl.sub dt {
        margin-bottom: 0;
    }

    .side-menu nav > dl.sub dt a {
        padding: 0;
    }

    .side-menu nav > dl.sub {
        margin-bottom: 0;
    }

    .side-menu nav > dl.sub dd {
        border-bottom: none;
        border-top: 1px solid var(--blue02);
        box-shadow: none;
    }

    .side-menu nav > dl.sub dd a {
        padding: 0 10px 0 15px;
    }

    #side-menu-switch:checked + .side-menu {
        transform: translateX(0);
    }

</style>


<!-- 主選單 -->
<label for="side-menu-switch"
       style="font-size: 22px; font-weight: 500; letter-spacing: 5px; margin-left: 10px; cursor: pointer;"><i
        class="fab fa-docker" style="font-size: 36px;" aria-hidden="true"></i>海運系統</label>
<input type=checkbox id="side-menu-switch">


<div class="side-menu">
    <div class="side-menu-logo">
        <label for="side-menu-switch"
               style="font-size: 22px; font-weight: 500; letter-spacing: 5px; margin-left: 10px; cursor: pointer;"><i
                class="fab fa-docker" style="font-size: 36px;" aria-hidden="true"></i>海運系統</label>
    </div>

    <nav style="display: flex; flex-direction: column;">

        <?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);



include_once '../api/config/core.php';
include_once '../api/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../api/libs/php-jwt-master/src/ExpiredException.php';
include_once '../api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

if ( isset( $jwt ) ) {

	try {
	        // decode jwt
	        $decoded = JWT::decode($jwt, $key, array('HS256'));

	        if($decoded->data->is_admin)
        echo "<a href='admin/main.php'>後台管理
        <eng>administration</eng>
    </a>";

        //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
        // header( 'location:index.php' );
        }
        // if decode fails, it means jwt is invalid
        catch (Exception $e){


        }
        }

        ?>

        <a href="directory.php">通訊錄
            <eng>Contactor</eng>
        </a>

        <a href="main.php">收貨記錄
                <eng>Receive Goods</eng></a>
        <a href="sea_take_photo.php">手機照相
                <eng>Take Photo</eng></a>

        <a href="loading.php">貨物裝櫃
            <eng>Loading Goods into Container</eng>
        </a>

        <a href="measure.php">到貨丈量、打單
            <eng>Measurement, Pickup/Payment</eng>
        </a>

        <a href="query_receive.php">收貨記錄查詢
            <eng>Query For Receiving Records</eng>
        </a>
 
        <dl class="sub">
            <dt><a class="after-micons">查詢
                <eng>Inquire</eng>
            </a></dt>
            <dd><a href="taiwanpay.php">台灣付與代墊
                <eng>Taiwan Pay/ Courier Money</eng>
            </a></dd>
            <dd><a href="contact_us.php">聯絡我們
                <eng>Contact Us</eng>
            </a></dd>
        </dl>

        <?php
        if($decoded->data->phili)
        echo "<a href='directory_ph.php'>Directory
        <cht>客戶通訊錄</cht>
    </a>";
        ?>
        <?php
 	if($decoded->data->sea_expense)
        {
        echo "<a href='salary_recorder_sea.php'>Salary Recorder
        <cht>薪資記錄表</cht>
    </a>";
        echo "<a href='expense_recorder_sea.php'>Expense Recorder
        <cht>支出記錄表</cht>
    </a>";
        }
        ?>
        <?php
 	if($decoded->data->sea_expense_v2)
        echo "<a href='expense_recorder_sea_v2.php'>Expense Recorder2
        <cht>支出記錄表2</cht>
    </a>";
        ?>
    </nav>
</div>
<!-- 主選單end -->
