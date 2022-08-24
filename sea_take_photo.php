<?php include 'check.php'; ?>
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
            
            if($taiwan_read == "1")
            {
                header( 'location:index.php' );
            }

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
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- CSS -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/default.css">
    <link rel="stylesheet" type="text/css" href="css/case.css">
    <link rel="stylesheet" type="text/css" href="css/ui.css">

    <!-- JS -->
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
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


        function ShowCam() {

            Webcam.set({
                width: 480,
                height: 480,
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

        window.onload = ShowCam;
    </script>

    <style>
        img.ui-datepicker-trigger {
            padding-left: 10px;
            margin: -8px;
        }

        .bodybox header > a{
            color: #FFF;
        }

        .block ul > li:nth-of-type(1) {
            min-width: 200px;
            font-size: 20px;
        }

        .block ul > li:nth-of-type(2) {
            min-width: 380px;
            text-align: left;
        }

        .block ul > li:nth-of-type(2) > img {
            width: 33px;
            margin-left: 10px;
            box-sizing: border-box;
        }

        .block ul > li:nth-of-type(2) > button {
            width: 45px;
            margin-left: 10px;
            box-sizing: border-box;
        }

        .block .camerabox {
            border-radius: 0.38rem;
            border: 1px solid rgb(112, 112, 112);
            margin: 5px 10px 15px;
        }

        .block .camerabox #results > img{
            max-width: 480px;
            max-height: 480px;
        }

        .block .camerabox .photobox{
            display:flex;
            align-items: center;
        }

        .block .camerabox .photobox input.alone[type=checkbox]::before{
            font-size: 40px;
        }

        .block .camerabox .photobox img{
            max-width: 480px;
            max-height: 480px;
            margin: 10px 0;
        }

    </style>
    

</head>

<body>
<div class="bodybox">
    <!-- header -->
    <header>
        <!-- 主選單 -->
        <a href="https://webmatrix.myvnc.com/main.php" class="logo"><i class="fab fa-docker" aria-hidden="true"></i>海運系統</a>
    </header>
    <!-- header end -->

    <div id='receive_record'>
        <div id='app' class="mainContent">
            <h6 style="text-align: center;">收貨
                <eng>(Receiving Goods)</eng>
            </h6>

            <div class="block">

                <div class="camerabox">
                    <div id="Cam" class="container" style="display:flex; flex-direction: column; align-items: center;">
                        <b>Camera Preview</b>
                        <div id="my_camera"></div>
                        <form>
                            <input type="button" value="Take Photo" onclick="take_snapshot()"
                                   style="border-radius: 0.38rem; border: 0.06rem solid rgb(112, 112, 112); font-size: 15px; margin: 0.38rem 0rem 0.48rem 0rem;">
                        </form>
                    </div>
                    <div class="container" id="Prev">
                        <div id="results" style="height: 480px; display:flex; justify-content: center; align-items: center;"></div>
                    </div>
                    <div class="container" id="Saved">
                        <span id="loading"></span><img id="uploaded" src=""/>
                    </div>

                    <div class="container" style="display:flex; flex-direction: column; align-items: center;">
                        <input type="button" value="Photo is ok" @click="append_pic()" style="border-radius: 0.38rem; border: 0.06rem solid rgb(112, 112, 112); font-size: 15px; margin: 0.38rem 0rem 0.48rem 0rem;">

                        <div v-for="(item, index) in pic_list" class="photobox" >
                            <input type="checkbox" class="alone" :value="item.check" v-model="item.check" >
                            <img :src="item.url">
                        </div>

                    </div>
                </div>



                <div class="tablebox">
                    <ul>
                        <li>收貨日期
                            <eng>Date Receive</eng>
                        </li>
                        <li>
                        <input type="date" id="adddate" value="<?php echo date("Y-m-d");?>" class="hasDatepicker"
                                   style="width: calc(100% - 70px); border: 1px solid rgb(153, 153, 153); border-radius: 5px; background-color: rgb(255, 255, 255); padding: 5px;">
                            <!--<img class="ui-datepicker-trigger" src="./images/calendar.png" alt=""
                                 title=""> -->
                        </li>
                    </ul>

                    <ul>
                        <li>件數
                            <eng>Quantity</eng>
                        </li>
                        <li>
                            <input type="text" name="quantity" class="goods_num" v-model="quantity">
                        </li>
                    </ul>

                    <ul>
                        <li>寄件人
                            <eng>Supplier</eng>
                        </li>
                        <li>
                            <input type="text" name="supplier" v-model="supplier" maxlength="256">
                        </li>
                    </ul>

                    <ul>
                        <li>收件人
                            <eng>Company/Customer</eng>
                        </li>
                        <li>
                            <input type="text" name="customer" v-model="customer" maxlength="256">
                        </li>
                    </ul>

                    <ul>
                        <li>備註
                            <eng>Remark</eng>
                        </li>
                        <li>
                            <textarea name="" id="" v-model="remark"></textarea>
                        </li>
                    </ul>
                </div>

                <div class="btnbox">
                    <a class="btn orange" style="color: white;" @click=resetForm()>
                        清空
                        <eng>Reset</eng>
                    </a>
                    <a class="btn" @click="createLibraryRecord()" style="color: white;">
                        圖片庫
                        <eng>Storage</eng>
                    </a>
                    <a class="btn" @click="createReceiveRecord()" style="color: white;">
                        儲存
                        <eng>Save</eng>
                    </a>
                </div>
            </div>
        </div>


    </div>
</div>
<!-- The Modal -->


</div>

<div class="modal" id="imgModal">
    <div v-if="this.selectedImage" max-width="85vw">
        <!-- <img :src="this.selectedImage" alt="" width="100%" @click.stop="this.selectedImage = null"> -->
        <img name="img_pre" class="img-responsive postimg" id="img_pre" alt="" width="100%">
        <hr>
    </div>
</div>


</div>

</div>

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
        is_mark_checked = document.querySelector("#c_mark").checked;
        is_phone_checked = document.querySelector("#c_tel").checked;

        c_string = '';

        if (is_mark_checked)
            c_string = e.querySelectorAll('td')[0].textContent + ' ';

        c_string = c_string + e.querySelectorAll('td')[1].textContent + ' ';

        if (is_phone_checked)
            c_string = c_string + e.querySelectorAll('td')[2].textContent + ' ';

        if (!mainState.isEditing) {
            //document.querySelector("input[name=customer]").value=e.querySelectorAll('td')[0].textContent;
            mainState.customer = c_string.trim();
            //document.querySelector("input[name=email]").value=e.querySelectorAll('td')[3].textContent;
            mainState.email = e.querySelectorAll('td')[3].textContent;
        } else {
            mainState.record.customer = c_string.trim();
            mainState.record.email = e.querySelectorAll('td')[3].textContent;
        }

        mainState.s_keyword = '';
        mainState.c_keyword = '';

        $("#myModal").dialog('close');
    };

    function data1(e) {
        is_phone_checked = document.querySelector("#s_tel").checked;

        s_string = e.querySelectorAll('td')[0].textContent + ' ';

        if (is_phone_checked)
            s_string = s_string + e.querySelectorAll('td')[1].textContent + ' ';

        if (!mainState.isEditing) {
            //document.querySelector("input[name=customer]").value=e.querySelectorAll('td')[0].textContent;
            mainState.supplier = s_string.trim();
            //document.querySelector("input[name=email]").value=e.querySelectorAll('td')[3].textContent;
            //mainState.email = e.querySelectorAll('td')[3].textContent;
        } else {
            mainState.record.supplier = s_string.trim();
            //mainState.record.email = e.querySelectorAll('td')[3].textContent;
        }

        mainState.s_keyword = '';
        mainState.c_keyword = '';

        $("#supModal").dialog('close');
    };


</script>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/sea_take_photo.js"></script>
</body>
</html>
