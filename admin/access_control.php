<?php include 'check.php';?>
<!doctype html>
<html class="easy-sidebar-active">
<head>

    <meta charset="utf-8">
    <title>後台管理(使用者)</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/easy-sidebar.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="../css/vue-select.css" type="text/css">
    <link rel="stylesheet" href="../css/fontawesome/v5.7.0/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">



    <style>
        .table th, td {
            text-align: center;
        }

        .panel-default{
            border: none;
        }

        .panel-body{
            border-radius: 4px;
            border: 1px solid #ddd;

        }

        .navbar-inverse{
            background-color: #4D576C;
        }

        .navbar-toggle{
            border-color: #4D576C!important;
        }

        .block ul{
            list-style-type: none;
            display: flex;
            align-items: center;
            padding: 0;
        }

        .block ul:nth-of-type(1){
            margin-top: 15px;
        }

        .block ul li{
            padding: 3px 20px;
        }

        .block ul>li:nth-of-type(1){
            width: 25%;
            text-align: right;
        }

        .block ul>li:nth-of-type(2){
            width: 40%;
        }

        .block .brn-box{
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .block .brn-box button{
            margin: 0 10px;
            width: 80px;
        }
    </style>


    <script src="../js/rm/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>



</head>

<body>

<?php

include 'menu.php';

?>


<div class="container " style="width: 100vw; height: 60px; padding-left: 1vw">

    <div class="row" style="background-color: #4D576C">

        <a class="btn easy-sidebar-toggle navbar-brand" ><span style="color: white;">&#9776;</span></a>

    </div>



    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

        <div id="mainContent" class="panel panel-default">

            <div role="tab" id="headingOne" class="panel-heading" style="background-color: lightskyblue; margin-top: 15px;">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion"
                       href="https://webmatrix.myvnc.com/admin/user.php#collapseOne"
                       aria-expanded="true" aria-controls="collapseOne"
                       style="font-size: 25px; text-decoration: none; color: white; font-weight: bold;">權限設定</a>
                </h4>
            </div>

            <div class="container-fluid" style="border: 1px solid #ddd; margin-bottom: 50px; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px;" id="app">
                <div class="box-content">
                    <ul>
                        <li><b>Car Approval 1</b></li>
                        <br>
                        <li>
                            <div>
                            <v-select v-model="car_access1"
                                                :options="payees"
                                                attach
                                                chips
                                                label="payeeName"
                                                multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(1)">Cancel</a>
                        <a class="btn" @click="save(1)">Save</a>
                    </div>
                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Car Approval 2</b></li>
                        <br>
                        <li>
                            <div>
                                
                                <v-select v-model="car_access2"
                                                :options="payees"
                                                attach
                                                chips
                                                label="payeeName"
                                                multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(2)">Cancel</a>
                        <a class="btn" @click="save(2)">Save</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/npm/vue/dist/vue.js"></script> 
<script src="../js/axios.min.js"></script> 
<script defer src="../admin/js/access_control.js"></script>
<script src="../js/vue-select.js"></script>
<script defer src="../js/npm/sweetalert2@9.js"></script>



<script>
    //easy-sidebar-toggle-right
    $('.easy-sidebar-toggle').click(function(e) {
        e.preventDefault();
        //$('body').toggleClass('toggled-right');
        $('body').toggleClass('toggled');
        //$('.navbar.easy-sidebar-right').removeClass('toggled-right');
        $('.navbar.easy-sidebar').removeClass('toggled');
    });

    $('.dropdownmenu_button').click(function(e) {

        $('.dropdownmenu').toggle();
    });

</script>


</body>
</html>
