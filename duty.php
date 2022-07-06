<?php
    date_default_timezone_set('Asia/Taipei');
    $date = date("Y-m-d");
    $show0 = false;

    if($date < '2022-02-01')
        $show0 = true;
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
    <title>Time In and Out</title>
    <!--
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all"/>
    <meta name="author" content="FELIIX"/>
    -->

    <!-- Open Graph protocol -->
    <!--
    <meta property="og:site_name" content="FELIIX"/>
    <meta property="og:url" content="分享網址" />
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="FELIIX"/>
    -->
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- css -->
    <!--
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    -->
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

    <script type="text/javascript" src="js/webcam.js"></script>
    <script language="JavaScript">

        function take_snapshot() {

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

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    if (document.getElementById('photo_gps') !== null)
                        document.getElementById('photo_gps').value = position.coords.latitude + ',' + position.coords.longitude;
                });
            }

            if (document.getElementById('photo_time') !== null)
                document.getElementById('photo_time').value = getToday() + ' ' + getTimeNow();

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

        function getToday() {

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            return yyyy + '/' + mm + '/' + dd;

            //setInterval(self.getToday, 1000 * 60)
        }

        function getTimeNow() {

            var today = new Date();
            var hh = String(today.getHours()).padStart(2, '0');
            var mm = String(today.getMinutes()).padStart(2, '0');
            var ss = String(today.getSeconds()).padStart(2, '0');

            return hh + ':' + mm + ':' + ss;

            //setInterval(self.getTimeNow, 1000)
        }

        window.onload = ShowCam;
    </script>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>


</head>

<style>
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
        padding: 8px 24px;
        background-color: #F0502F;
        font-size: 18px;
        color: #FFF;
        font-weight: 700;
        transition: .5s;
        border-radius: 10px;
        vertical-align: middle;
    }

    a.btn:hover {
        background-color: #F37058;
    }

    input[type=range], input[type=text], input[type=password], input[type=file], input[type=date], input[type=number], input[type=url], input[type=email], input[type=tel], input[list], input[type=button], input[type=submit], button, textarea, select, output {
        box-sizing: border-box;
        border: 2px solid #1E6BA8;
        background-color: transparent;
        padding: 8px;
        vertical-align: middle;
        font-size: 18px;
    }

    textarea {
        font-family: Lato, Arial, Helvetica, 'Noto Sans TC', 'LiHei Pro', "微軟正黑體", "新細明體", 'Microsoft JhengHei', sans-serif;
        resize: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        outline: 0 none;
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
        background-image: url(../images/ui/icon_form_select_arrow_lightblue.svg);
        background-size: auto 100%;
        background-position: 100% center;
        background-repeat: no-repeat;
        padding-right: 35px;
        padding-left: 15px;
        background-image: none \9;
        padding: 6px 10px 6px 10px \9;
    }

    header {
        display: block;
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
    }

    body {
        background-color: #F0F0F0;
        font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
        font-weight: 300;
    }

    #app {
        text-align: center;
        color:  #000000;
        margin-top: 60px;
    }

    .mainContent {
        padding: 50px 12px 30px;
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

    .block .title {
        margin: 5px 0;
    }

    .block .formbox {
        border: 2px solid #1E6BA8;
        padding: 20px;
        width: 100%;
    }

    .block .formbox dl, .block .formbox dt, .block .formbox dd {
        width: 100%;
        display: block;
    }

    .block .formbox dt {
        font-size: 17px;
        font-weight: 700;
        margin-top: 12px;
        padding-bottom: 8px;
    }

    .block .formbox dd {
        border: 2px solid #1E6BA8;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .block .container #results > img {
        max-width: 480px;
        max-height: 480px;
    }

    .block .formbox dd input, .block .formbox dd textarea, .block .formbox dd select, .block .formbox2 input, .block .formbox2 textarea, .block .formbox2 select {
        width: 100%;
        border: none;
    }

    .btnbox {
        width: 100%;
        display: inline-block;
        text-align: center;
        padding: 20px;
    }

    .btnbox .btn {
        margin: 0 8px;
    }

    #video {
        background-color: #000000;
    }

    #canvas {
        display: none;
    }

    li {
        display: inline;
        padding: 5px;
    }
</style>

<body class="primary">

<div class="bodybox">
    <!-- header -->
    <header></header>
    <!-- header end -->
    <div id='app' class="mainContent">
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Time In and Out</h6>
            <div class="box-content">
                <!-- 表單樣式 -->
                <div class="title">
                </div>
                <div class="formbox">
                    <dl>
                        <dt>Employee Name</dt>
                        <dd>
                            <select v-model="name">
                                <option v-for='(record, index) in username' :value="record.username">{{ record.username }}</option>
                               
                            </select>
                        </dd>
                        <dt>Date</dt>
                        <dd>
                            <input id="myDate" type="date" v-model="today" :min="yesterday" :max="calendar_today">
                        </dd>
                        <dt>Type</dt>
                        <dd>
                            <select name="" id="mobiscroll" v-model="type">
                                <option value="A">Time In</option>
                                <option value="B">Time Out</option>
                            </select>
                        </dd>

                        <dt>Remarks</dt>
                        <dd><textarea placeholder="" v-model="remark"></textarea></dd>

                        <dt>Photo</dt>
                        <!-- <dd v-if="showPhoto"><input type="file" id="file" ref="file" v-on:change="onChangeFileUpload()" accept="image/*" capture="camera"></dd> -->
                        <dd>
                            <div id="Cam" class="container"
                                 style="display:flex; flex-direction: column; align-items: center;"><b style="margin-top: 15px;">Camera
                                Preview</b>
                                <div id="my_camera"></div>
                                <form>
                                    <input type="button" value="Take Photo" onclick="take_snapshot()"
                                           style="border-radius: 0.38rem; border: 0.06rem solid rgb(112, 112, 112); font-size: 15px; margin: 0.38rem 0rem 0.48rem 0rem;">
                                </form>
                            </div>
                            <div class="container" id="Prev">
                                <div id="results"></div>
                            </div>
                            <div class="container" id="Saved">
                                <span id="loading"></span><img id="uploaded" src=""/>
                            </div>
                        </dd>

                        <!--   <hr>
                           <dt>Time In</dt>
                           <dd><input type="text" placeholder="" v-model="time" :readonly="true"></dd>  -->

                        <dt v-if="showPhoto" style="display: none;">Photo Taken Time</dt>
                        <dd v-if="showPhoto" style="display: none;"><input type="text" id="photo_time" placeholder="" :readonly="true"></dd>
                        <dt v-if="showPhoto" style="display: none;">Photo Taken GPS</dt>
                        <dd v-if="showPhoto" style="display: none;"><input type="text" id="photo_gps" placeholder="" :readonly="true"></dd>
                        <p id="map-link" style="font-size: 20px; font-weight: 500;" v-if="showPhoto"></p>
                    </dl>
                    <div class="btnbox">
                        <a class="btn" @click="reset">Reset</a>
                        <a class="btn" @click="upload" :disabled="submit == true">Submit</a>
                    </div>
                </div>
                <!-- 表單樣式 -->
            </div>
        </div>


    </div>
</div>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/device-uuid.min.js"></script>
<script defer src="js/on_duty.js"></script>
<script>
    $(document).ready(function() {
    var date = new Date();

    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();

    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    var today = year + "-" + month + "-" + day +"T00:00";       
    $("#myDate").attr("value", today);
});
</script>

</html>
