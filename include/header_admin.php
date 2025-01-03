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

$taiwan_read = "0";
$phili_read = "0";


if ( isset( $jwt ) ) {

	try {
	        // decode jwt
	        $decoded = JWT::decode($jwt, $key, array('HS256'));

            $taiwan_read = $decoded->data->taiwan_read;
            $phili_read = $decoded->data->phili_read;

            $report1 = $decoded->data->report1;
            $report2 = $decoded->data->report2;
            $airship = $decoded->data->airship;
            $airship_read = $decoded->data->airship_read;

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
        <?php
            if($report2 == "1")
            {
        ?>

        <a href="report_container_ac.php">貨櫃帳款報表
                    <eng>A/R Report of Containers</eng>
        </a>
        <?php
            }
            ?>

        
<?php
    if($report1 == "1")
    {
?>
<a href="details_ntd_php.php">幫客人匯款記錄表
            <eng>NTD~PHP</eng>
</a>
<?php
            }
            ?>
            
<?php
            if($airship == "1" || $airship_read == "1")
            {
        ?>
<a href="airship_records.php">空運記錄 <eng>Airship Record</eng></a>

<?php
    }
    ?>
        <a href="directory.php">通訊錄
            <eng>Contactor</eng>
        </a>

        <a href="main.php">收貨記錄
                <eng>Receive Goods</eng></a>

                <?php
                if($taiwan_read == "0")
                {
                    ?>
        <a href="sea_take_photo.php">手機照相
                <eng>Take Photo</eng></a>
<?php
                }
                ?>
        <a href="loading.php">貨物裝櫃
            <eng>Loading Goods into Container</eng>
        </a>
        
        <a href="details_taiwanpay.php">台灣付明細
            <eng>Details of Taiwan Pay</eng>
        </a>

        <a href="query_receive.php">收貨記錄查詢
            <eng>Query For Receiving Records</eng>
        </a>

        <a href="query_airship_records.php">空運記錄查詢
            <eng>Query For Airship Records</eng>
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
        {
        echo "
        <a class='after-micons' href='create_measurement_v2.php'>Measurement <cht>丈量</cht></a>
        <a class='after-micons' href='pickup_payment.php'>Pickup and Payment <cht>提貨與付款</cht></a>
        <a class='after-micons' href='query_pickup_payment.php'>Query For Archived Pickup and Payment Records <cht>已歸檔提貨與付款記錄查詢</cht></a>
        <a class='after-micons' href='edit_soldto_dr.php'>Edit Sold To and DR <cht>修改 Sold To 和單號</cht></a>
        <a class='after-micons' href='report_daily_pickup.php'>Daily Pickup Report <cht>每日提貨報表</cht></a>
        <dl class='sub'>
            <dt>
                <a class='after-micons'>Directory <cht>通訊錄</cht></a>
            </dt>
            <dd>
                <a href='directory_ph.php'>Customer Directory <cht>客戶通訊錄</cht></a>
            </dd>
            <dd>
                <a href='possible_directory_ph.php'>Possible Customer Directory <cht>潛在客戶通訊錄</cht></a>
            </dd>
        </dl>";
        }
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
        <?php
 	if($decoded->data->gcash_expense_sea)
        echo "<a href='gcash_expense_recorder_sea.php'>GCash Recorder
        <cht>GCash 支出記錄表</cht>
    </a>";
        ?>
        
        <?php
 	if($decoded->data->gcash_expense_sea_2)
        echo "<a href='gcash_expense_recorder_sea_2.php'>GCash Recorder 2
        <cht>GCash 支出記錄表 2</cht>
    </a>";
        ?>

    <a href="car_schedule_calendar.php">Car Schedule
        <cht>車輛行程</cht>
    </a>
        
    </nav>
</div>
<!-- 主選單end -->
