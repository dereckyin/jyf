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

$taiwan_read = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

$taiwan_read = $decoded->data->taiwan_read;


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
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="js/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/webcam.js"></script>

    <script language="JavaScript">
        function take_snapshot() {

            app.snap_me = true;

            var real_width = document.getElementsByTagName("video")[0].srcObject.getVideoTracks()[0].getSettings().width;
            var real_height = document.getElementsByTagName("video")[0].srcObject.getVideoTracks()[0].getSettings().height;

            var scalex = real_width / 800;
            var scaley = real_height / 800;

            if (scalex <= 1 && scaley <= 1) {

                Webcam.set({
                    dest_width: real_width,
                    dest_height: real_height
                });

            } else {

                if (scalex >= scaley) {

                    Webcam.set({
                        dest_width: real_width / scalex,
                        dest_height: real_height / scalex
                    });

                } else {

                    Webcam.set({
                        dest_width: real_width / scaley,
                        dest_height: real_height / scaley
                    });

                }

            }

            Webcam.snap(function (data_uri) {
                document.getElementById('results').innerHTML = '<img id="base64image" src="' + data_uri + '"/>';
            });
        }

        var HideCam = function () {
            Webcam.reset('#my_camera');
        }

        var ShowCam = function () {

            Webcam.set({
                width: 600,
                height: 600,
                image_format: 'jpeg',
                jpeg_quality: 100,
                constraints: {
                    width: 800,
                    height: 600,
                    facingMode: "environment"
                }
            });

            Webcam.attach('#my_camera');
        }

        function uploadcomplete(event) {
            document.getElementById("loading").innerHTML = "";
            var image_return = event.target.responseText;
            var showup = document.getElementById("uploaded").src = image_return;
        }

        // window.onload = ShowCam;
    </script>

    <style>
        img.ui-datepicker-trigger {
            padding-left: 10px;
            margin: -8px;
        }

        p {
            margin: 0;
            padding: 0;
        }

        #photoModal .modal-dialog.modal-lg {
            max-width: 100%;
        }

        #webcam .modal-dialog.modal-lg {
            max-width: 100%;
        }

        #showPhoto tr td img {
            width: initial;
            max-width: 250px;
            max-height: 250px;
        }

        #showPhoto tr th, #showPhoto tr td {
            vertical-align: middle;
        }

        #showPhoto tr th:nth-of-type(1),
        #showPhoto tr td:nth-of-type(1) {
            width: 50px;
            text-align: center;
        }

        #showPhoto tr th:nth-of-type(2),
        #showPhoto tr td:nth-of-type(2) {
            width: 270px;
            text-align: center;
        }

        #showPhoto tr th:last-of-type,
        #showPhoto tr td:last-of-type {
            width: 40px;
            text-align: center;
        }

        #showPhoto tr td:last-of-type > button {
            width: 36px;
            height: 36px;
        }

        .tablebox.d01 .photobox {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }

        .tablebox.d01 .photobox img {
            max-width: 300px;
            max-height: 300px;
            border: 1px solid #999;
            margin: 0 10px 5px 0;
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

        .block .camerabox {
            border-radius: 0.38rem;
            border: 1px solid rgb(112, 112, 112);
            margin: 5px 10px 15px;
        }

        .block .camerabox #results > img {
            max-width: 600px;
            max-height: 600px;
        }

        .block .camerabox .photobox {
            display: flex;
            align-items: center;
        }

        .block .camerabox .photobox input.alone[type=checkbox]::before {
            font-size: 40px;
        }

        .block .camerabox .photobox img {
            max-width: 600px;
            max-height: 600px;
            margin: 10px 20px;
        }

        .block .camerabox .photobox button {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .tablebox button.ui-button.ui-corner-all + button.ui-button.ui-corner-all {
            margin-left: 10px;
        }

        #get_file, #get_file_1 {
            position: relative;
        }

        #get_file > input[type="file"], #get_file_1 > input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
            width: 100%;
            opacity: 0;
        }

        div.tablebox.d02 > ul.group1{
            background-color: var(--yellow);
        }

        div.tablebox.d02 > ul.group2{
            background-color: var(--teal);
        }
    </style>

    <script>
        $(function () {
            $('header').load('include/header_admin.php');
        })
    </script>

    <script>
        $(function () {
//    $('header').load('Include/header.htm');
            toggleme($('a.btn.detail'), $('.block.record'), 'show');
        })
    </script>

</head>

<body>
<div class="bodybox">
    <div class="mask" style="display:none"
         onclick="(function(){ $('.mask').toggle(); $('#webcam').toggle(); HideCam(); $('#photoModal').toggle(); return false;})();return false;">
    </div>
    <!-- header -->
    <header></header>
    <!-- header end -->
    <div id='receive_record'>
        <div class="mainContent">
            <h6>貨物裝櫃
                <eng>Loading Goods into Container</eng>
            </h6>
            <div class="block">
                <div class="btnbox">
                    <?php
                    if($taiwan_read == "0")
                    {
                    ?>
                    <a class="btn small detail">新增貨櫃記錄
                        <eng>New Container Record</eng>
                    </a>
                    <a class="btn small" href="loading_edit.php">修改貨櫃記錄
                        <eng>Edit Container Record</eng>
                    </a>
                    <?php
                    }
                    ?>
                    <a class="btn small" href="loading_query.php">查詢貨櫃記錄
                        <eng>Query Container Record</eng>
                    </a>
                    <a href="send_email.php" class="btn small">E-Mail功能
                        <eng>E-Mail Function</eng>
                    </a>
                </div>
            </div>
            <div class="block record">
                <div class="block">
                    <div class="tablebox d01">
                        <ul>
                            <li>麥頭
                                <eng>Shopping Mark</eng>
                            </li>
                            <li><input type="text" name="shipping_mark" v-model="shipping_mark"></li>
                            <li>櫃號
                                <eng>Container Number</eng>
                            </li>
                            <li><input type="text" name="container_number" v-model="container_number"></li>
                        </ul>
                        <ul>
                            <li>空櫃重
                                <eng>Empty Container Weight</eng>
                            </li>
                            <li><input type="text" name="estimate_weight" v-model="estimate_weight"></li>
                            <li>實際櫃重
                                <eng>Actual Weight</eng>
                            </li>
                            <li><input type="text" name="actual_weight" v-model="actual_weight"></li>
                        </ul>
                        <ul>
                            <li>封條
                                <eng>Seal</eng>
                            </li>
                            <li><input type="text" name="seal" v-model="seal"></li>
                            <li>S/O</li>
                            <li><input type="text" name="so" v-model="so"></li>
                        </ul>
                    </div>
                    <div class="tablebox d01">
                        <ul>
                            <li>船公司
                                <eng>Shipping Line Company</eng>
                            </li>
                            <li><input type="text" name="ship_company" v-model="ship_company"></li>
                            <li>船名航次
                                <eng>Shipping Line Boat</eng>
                            </li>
                            <li><input type="text" name="ship_boat" v-model="ship_boat"></li>
                        </ul>
                        <ul>
                            <!-- <li>領櫃<eng>Neck Cabinet</eng></li>
                            <li><input type="text" name="neck_cabinet" v-model="neck_cabinet"></li> -->
                            <li>出貨人
                                <eng>Shipper</eng>
                            </li>
                            <li>
                                <select v-model="shipper">
                                    <option value="0"></option>
                                    <option value="1">盛盛</option>
                                    <option value="2">中亞菲</option>
                                    <option value="3">心心</option>
                                </select>
                            </li>
                            <li>領櫃人
                                <eng>Broker</eng>
                            </li>
                            <li>
                                <select v-model="broker">
                                    <option v-for="item in name" :value="item.name" :key="item.id">
                                        {{ item.name }}
                                    </option>
                                </select>
                            </li>
                        </ul>
                    </div>
                    <div class="tablebox lo01 withbtn">
                        <ul>
                            <li style="width: 5%">結關
                                <eng>Date Sent</eng>
                            </li>
                            <li style="width: 14%">
                                <date-picker id="date_sent" @update-date="update_date_sent" v-model="date_sent"
                                             style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-picker>
                                <span class="text-danger" v-if="error_date_send" v-text="error_date_send"></span></li>
                            <li style="width: 5%">ETD</li>
                            <li style="width: 14%">
                                <etd-date-picker id="etd_date" @update-date="update_etd_date" v-model="etd_date"
                                                 style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></etd-date-picker>
                                <span class="text-danger" v-if="error_etd_date" v-text="error_etd_date"></span></li>
                            <li style="width: 5%">O/B</li>
                            <li style="width: 14%">
                                <ob-date-picker id="ob_date" @update-date="update_ob_date" v-model="ob_date"
                                                style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></ob-date-picker>
                                <span class="text-danger" v-if="error_ob_date" v-text="error_ob_date"></span></li>
                            <li style="width: 5%">ETA</li>
                            <li style="width: 14%">
                                <eta-date-picker id="eta_date" @update-date="updat_eta_date" v-model="eta_date"
                                                 style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></eta-date-picker>
                                <span class="text-danger" v-if="error_eta_date" v-text="error_eta_date"></span></li>
                            <li style="width: 5%">到倉日期
                                <eng>Date C/R</eng>
                            </li>
                            <li style="width: 14%">
                                <date-arrive-picker id="date_arrive" @update-date="updat_date_arrive"
                                                    v-model="date_arrive"
                                                    style="width: calc(60% - 10px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-arrive-picker>
                                <span class="text-danger" v-if="error_date_arrive" v-text="error_date_arrive"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="tablebox d01">
                        <ul><!-- 配色底用 --></ul>
                        <ul>
                            <li>備註
                                <eng>Remark</eng>
                            </li>
                            <li><input type="text" name="remark" v-model="remark"></li>
                        </ul>
                        <ul>
                            <li>貨櫃照片
                                <eng>Container Photo</eng>
                            </li>
                            <li style="display: flex; align-items: center; flex-wrap: wrap;">
                                <div class="photobox" v-for="(item, index) in pic_receive">
                                    <img v-if="item.gcp_name" :src="url_ip + item.gcp_name">
                                    <input type="checkbox" class="alone" :value="item.is_checked"
                                           v-model="item.is_checked">
                                </div>

                                <div class="photobox" v-for="(item, index) in cam_receive">
                                    <img :src="item.url">
                                    <input type="checkbox" class="alone" :value="item.check" v-model="item.check">
                                </div>

                                <div class="photobox" v-for="(item, index) in  file_receive">
                                    <img :src="item.url">
                                    <input type="checkbox" class="alone" :value="item.check" v-model="item.check">
                                </div>

                                <button id="get_photo_library">圖片庫</button>
                                <button id="web_cam">照相</button>
                                <button id="get_file" class="ui-button ui-corner-all ui-widget">選取檔案
                                    <input type="file" accept="image/*" @change="onFileChange($event)">
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="block">
                    <h6>選擇裝櫃貨物
                        <eng>Select Goods to Load</eng>
                    </h6>
                    <!-- list -->
                    <div class="mainlist" style="overflow-x: auto;">

                        <div class="tablebox d02">
                            <ul class="header">
                                <li>勾選
                                    <eng>Check</eng>
                                </li>
                                <li>收貨日期
                                    <eng>Date Receive</eng>
                                </li>
                                <li>收件人
                                    <eng>Company/Customer</eng>
                                </li>
                                <li>照片
                                    <eng>Photo</eng>
                                </li>
                                <li>貨品名稱
                                    <eng>Description</eng>
                                </li>
                                <li>件數
                                    <eng>Quantity</eng>
                                </li>
                                <li>寄貨人
                                    <eng>Supplier</eng>
                                </li>
                                <li>重量
                                    <eng>Kilo</eng>
                                </li>
                                <li>材積
                                    <eng>Cuft</eng>
                                </li>
                                <li>台灣付
                                    <eng>Taiwan Pay</eng>
                                </li>
                                <li>代墊
                                    <eng>Courier / Payment</eng>
                                </li>
                                <li>備註
                                    <eng>Remark</eng>
                                </li>
                            </ul>
                            <ul v-for='(receive_record, index) in displayedPosts' :class="[receive_record.flag=='1' ? 'group1': (receive_record.flag=='2' ? 'group2': '')]">
                                <li>
                                    <input type="checkbox" name="record_id" class="alone" @change="updateWeightAndCult"
                                           :value="receive_record.index" :true-value="1"
                                           v-model:checked="receive_record.is_checked">
                                </li>
                                <li>{{ receive_record.date_receive }}</li>
                                <li>{{ (receive_record.customer !== 'undefined' ) ?
                                    receive_record.customer.replace(/\\/g, '') : "" }}
                                </li>
                                <li>
                                <div>
                                        <i class="fas fa-image"  v-if="receive_record.pic != ''" @click="zoom_rec(receive_record.id)"></i> 
                                    </div>
                                    <i class="fas fa-image"  @click="" v-if="receive_record.pic != '' && receive_record.is_edited == 0" @click="zoom_rec(receive_record.id)"></i> 
                                </li>
                                <li>{{ receive_record.description }}</li>
                                <li>{{ receive_record.quantity }}</li>
                                <li>{{ (receive_record.supplier !== 'undefined') ?
                                    receive_record.supplier.replace(/\\/g, '') : "" }}
                                </li>
                                <li>{{ (receive_record.kilo == 0) ? "" : receive_record.kilo }}</li>
                                <li>{{ (receive_record.cuft == 0) ? "" : receive_record.cuft }}</li>
                                <li>{{ (receive_record.taiwan_pay == 1) ? "是 (yes)" : "否 (no)" }}</li>
                                <li>{{ (receive_record.courier_money == 0) ? "" : receive_record.courier_money }}</li>
                                <li><p v-html="receive_record.remark.replace(/(?:\r\n|\r|\n)/g, '&nbsp')"></p></li>
                            </ul>
                        </div>

                    </div>
                    <!-- resume -->
                    <div class="tablebox s03">
                        <ul>
                            <li>已選擇</li>
                            <li>重量 <span>{{ Math.round((n_kilo + Number.EPSILON) * 100) / 100 }}</span>、材積 <span>{{ Math.round((n_cuft + Number.EPSILON) * 100) / 100 }}</span>
                            </li>
                            <li>Goods Selected</li>
                            <li>Kilo <span>{{ Math.round((n_kilo + Number.EPSILON) * 100) / 100 }}</span>, Cuft <span>{{ Math.round((n_cuft + Number.EPSILON) * 100) / 100 }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="btnbox">
                        <a class="btn small" @click="toggleCheckbox();">全選 / 全取消
                            <eng>All/Undo</eng>
                        </a>
                        <a class="btn small" @click="createLoadingRecord()">儲存
                            <eng>Save</eng>
                        </a>
                        <a class="btn small detail" @click="cancelReceiveRecord($event)">取消
                            <eng>Cancel</eng>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Modal Begin-->
        <div class="modal" id="photoModal">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">圖片庫</h4>
                    </div>
                    <div>
                        <input class="form-control" placeholder="Search for...">
                    </div>

                    <!-- Modal body -->
                    <table class="table table-hover table-striped table-sm table-bordered" id="showPhoto">
                        <thead>
                        <tr>
                            <th><input class="alone" type="checkbox" @click="bulk_toggle_library()"
                                       id="bulk_select_all_library"></th>
                            <th>
                                <p>Photo</p>
                                <p>照片</p>
                            </th>
                            <th>
                                <p>Date Receive</p>
                                <p>收貨日期</p>
                            </th>
                            <th>
                                <p>Quantity</p>
                                <p>件數</p>
                            </th>
                            <th>
                                <p>Supplier</p>
                                <p>寄件人</p>
                            </th>
                            <th>
                                <p>Company/Customer</p>
                                <p>收件人</p>
                            </th>
                            <th>
                                <p>Remark</p>
                                <p>備註</p>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="">
                        <tr v-for="(item, index) in pic_lib">
                            <td>
                                <input class="alone" type="checkbox" :value="item.is_checked" v-model="item.is_checked">
                            </td>
                            <td><a :href="url_ip + item.gcp_name" target="_blank"><img width="50%" v-if="item.gcp_name"
                                                                                       :src="url_ip + item.gcp_name"></a>
                            </td>
                            <td>{{ item.date_receive }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.supplier }}</td>
                            <td>{{ item.customer }}</td>
                            <td>{{ item.remark }}</td>
                            <!--
                            <td>
                                <a :href="url_ip + item.gcp_name" download="library"><button type="button" data-dismiss="modal" ><i class="fas fa-file-download"></i></button></a>
                            </td> -->
                        </tr>
                        </tbody>
                    </table>

                    <!-- Modal footer -->
                    <div class="modal-footer">

                        <?php
if($taiwan_read == "0")
{
?>
                        <button type="button" class="btn btn-warning" data-dismiss="modal" @click="delete_library()">刪除
                            Delete
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="choose_library()">
                            選取 Select
                        </button>
                        <?php
}
?>
                    </div>

                </div>

            </div>
        </div>
        <!-- Photo Modal End-->


        <!-- Webcam Modal Begin-->
        <div class="modal" id="webcam">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="block">
                        <div class="camerabox" style="border-color: transparent;">
                            <div id="Cam" class="container"
                                 style="display:flex; flex-direction: column; align-items: center;">
                                <b>Camera Preview</b>
                                <div id="my_camera"></div>
                                <form>
                                    <input type="button" value="Take Photo" onclick="take_snapshot()"
                                           style="border-radius: 0.38rem; border: 0.06rem solid rgb(112, 112, 112); font-size: 15px; margin: 0.38rem 0rem 0.48rem 0rem;">
                                </form>
                            </div>
                            <div class="container" id="Prev">
                                <div id="results"
                                     style="height: 480px; display:flex; justify-content: center; align-items: center;"></div>
                            </div>
                            <div class="container" id="Saved">
                                <span id="loading"></span><img id="uploaded" src=""/>
                            </div>

                            <div class="container" style="display:flex; flex-direction: column; align-items: center;">
                                <input type="button" value="Photo is ok" @click="append_pic()"
                                       style="border-radius: 0.38rem; border: 0.06rem solid rgb(112, 112, 112); font-size: 15px; margin: 0.38rem 0rem 0.48rem 0rem;">

                                <div v-for="(item, index) in pic_list" class="photobox">
                                    <input type="checkbox" class="alone" :value="item.check" v-model="item.check">
                                    <img :id="'hello_kitty_' + index" :src="item.url">
                                    <button type="button" data-dismiss="modal" @click="download_pic(index)"><i
                                            class="fas fa-file-download"></i></button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="choose_picture()">
                            選取 Select
                        </button>
                    </div>

                </div>

            </div>
        </div>
        <!-- Webcam Modal End-->

        <div class="modal" id="imgModal">
            <div v-if="this.selectedImage" max-width="85vw">
                <!-- <img :src="this.selectedImage" alt="" width="100%" @click.stop="this.selectedImage = null"> -->
                <template v-for="(item, index) in pic_preview">
                    <img v-if="item.type == 'FILE'" name="img_pre" class="img-responsive postimg" :src="'img/' + item.gcp_name" alt="" width="100%">
                    <img v-if="item.type == 'RECEIVE'" name="img_pre" class="img-responsive postimg" :src="url_ip + item.gcp_name" alt="" width="100%">
                    <hr>
                </template>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap  -->
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script type="text/javascript" src="js/loading.js" defer></script>
<script defer src="js/a076d05399.js"></script>
</body>
</html>
