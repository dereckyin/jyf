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
    <meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

    <!-- CSS -->
    <link rel="stylesheet" href="js/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

    <style>
        a.nav_link {
            color: #FFFFFF;
            font-weight: bold;
            padding: 0 20px;
            text-decoration: none;
            cursor: pointer;
            border-right: 2px solid #FFFFFF;
            font-size: 16px;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        }

        a.nav_link:last-of-type {
            border-right: none;
            margin-right: 90px;
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

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script>
        //$(function () {
        //    $('header').load('include/header_admin.php');
        //})
    </script>

    <!-- Bootstrap  -->
    <script src="js/bootstrap/popper.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/npm/vue/dist/vue.js"></script>
    <script src="js/npm/sweetalert2@9.js"></script>
    <script src="js/axios.min.js"></script>
    <script type="text/javascript" src="js/staff_list_sea.js" defer></script>

</head>

<body>
<div id="contactor">
    <div class="bodybox">
        <!-- header -->
        <header>
        <a href="main.php" class="menu"><span>&#9776;</span></a>

        <div>
            <a class="nav_link" href="car_schedule_calendar.php">
                <eng>Car Schedule</eng>
            </a>

            <?php
                        if($decoded->data->sea_expense)
            {
            ?>
            <a class="nav_link" href="attendance_sea_v2.php">
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
                    <?php
                        if($decoded->data->gcash_expense_sea)
            {
            ?>
            <a class="nav_link" href="gcash_expense_recorder_sea.php">
                <eng>GCash Recorder</eng>
            </a>
            <?php
                        }
                    ?>
                    <?php
                        if($decoded->data->gcash_expense_sea_2)
            {
            ?>
            <a class="nav_link" href="gcash_expense_recorder_sea_2.php">
                <eng>GCash Recorder 2</eng>
            </a>
            <?php
                        }
                    ?>
        </div>
    </header>
        <!-- header end -->
        <div class="mainContent" style="padding-top: 70px;">
            <h6>Staff List</h6>
            <!-- add form -->
            <div class="block" v-if="!isEditing">
                <div class="tablebox d01">
                <ul>
                            <li>Name</li>
                            <li>
                                <input type="text" name="staff" v-model="staff" maxlength="256">
                                <span class="text-danger" v-if="error_staff" v-text="error_staff"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>Phone</li>
                            <li>
                                <input type="text" name="phone" v-model="phone" maxlength="64">
                                <span class="text-danger" v-if="error_phone" v-text="error_phone"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>E-mail</li>
                            <li>
                                <input type="text" name="email" v-model="email" maxlength="128">
                                <span class="text-danger" v-if="error_email" v-text="error_email"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>Address
                            </li>
                            <li>
                                <input type="text" name="address" v-model="address" maxlength="512">
                                <span class="text-danger" v-if="error_address" v-text="error_address"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>Punch
                            </li>
                            <li>
                                <input id="punch" type="checkbox" name="punch" :true-value="1" name="punch" v-model:checked="punch" @change="setPunch">
                                <label for="punch">Need Punch</label>
                              
                            </li>
                        </ul>
                </div>

                <div class="btnbox">
                    <a class="btn" @click="createReceiveRecord()">Save</a>
                </div>
            </div>

            <!-- eidt form -->
            <div class="block" v-else>
                <div class="tablebox d01">
                <ul>
                            <li>Name</li>
                            <li>
                                <input type="text" name="staff" v-model="record.staff" maxlength="256">
                                <span class="text-danger" v-if="error_staff" v-text="error_staff"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>Phone</li>
                            <li>
                                <input type="text" name="phone" v-model="record.phone" maxlength="64">
                                <span class="text-danger" v-if="error_phone" v-text="error_phone"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>E-mail</li>
                            <li>
                                <input type="text" name="email" v-model="record.email" maxlength="128">
                                <span class="text-danger" v-if="error_email" v-text="error_email"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>Address
                            </li>
                            <li>
                                <input type="text" name="address" v-model="record.address" maxlength="512">
                                <span class="text-danger" v-if="error_address" v-text="error_address"></span>
                            </li>
                        </ul>
                        <ul>
                            <li>Punch
                            </li>
                            <li>
                                <input id="record_punch" type="checkbox" name="record_punch" :true-value="1" name="record_punch" v-model:checked="record.punch" @change="updatePunch">
                                <label for="record_punch">Need Punch</label>
                            </li>
                        </ul>
                </div>

                <div class="btnbox">
                    <a class="btn" @click="cancelReceiveRecord($event)" style="color:white;">Cancel</a>
                    <a class="btn" @click="editReceiveRecord($event)" style="color:white;">Save</a>
                </div>
            </div>
            <div class="block record show">
                <h6>Staff List</h6>
                <!-- list -->
                <div class="mainlist">
                    <div class="listheader">
                        <div class="pageblock" style="float:right;"> Page Size:
                            <select v-model="perPage">
                                <option v-for="item in inventory" :value="item" :key="item.id"> {{ item.name }}</option>
                            </select>
                            Page:
                            <div class="pageblock"><a class="first micons" @click="page=1">first_page</a> <a
                                    class="prev micons" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">chevron_left</a>
                                <select v-model="page">
                                    <option v-for="pg in pages" :value="pg"> {{ pg }}</option>
                                </select>
                                <a class="next micons" :disabled="page == pages.length"
                                   @click="page++">chevron_right</a> <a class="last micons" @click="page=pages.length">last_page</a>
                            </div>
                        </div>
                        <div class="searchblock" style="float:left;">Search: <input type="text" v-model="keyword"></div>
                    </div>
                    <div class="tablebox d02">
                        <ul class="header">
                            <li>Check</li>
                            <li>Name</li>
                            <li>Phone</li>
                            <li>E-mail</li>
                            <li>Address</li>
                            <li>Punch</li>
                        </ul>
                        <ul v-for='(contactor, index) in displayedPosts'>
                        <li>
                                        <input type="checkbox" name="record_id" class="alone" :value="contactor.index" :true-value="1" v-model:checked="contactor.is_checked">
                                    </li>
                                    <li>{{ contactor.staff }}</li>
                                    <li>{{ contactor.phone }}</li>
                                    <li>{{ contactor.email }}</li>
                                    <li>{{ contactor.address }}</li>
                                    <li>{{ contactor.punch == 1 ? 'yes' : 'no' }}</li>
                        </ul>
                    </div>
                </div>
                <div class="btnbox">
                    <a class="btn small selbtn" style="color:white;" @click="toggleCheckbox();">Select / Deselect
                        All</a>
                    <a class="btn small" style="color:white;" @click="editRecord()">Edit</a>
                    <a class="btn small" style="color:white;" @click="deleteRecord()">Delete</a>
                    <a class="btn small" style="color:white;" v-bind:href="pageUrl">Export</a></div>
            </div>
        </div>
    </div>
</div>
<!-- The Modal -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">編輯PROJECT</h5>
            </div>
            <div class="modal-body"> content</div>
            <div class="modal-footer"><a class="btn" data-dismiss="modal">取消</a> <a class="btn">確認</a></div>
        </div>
    </div>
</div>
<!-- The Modal -->

</body>
</html>
