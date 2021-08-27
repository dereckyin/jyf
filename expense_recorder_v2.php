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

            if(!$decoded->data->status_2)
                header( 'location:parts_index.php' );
            
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

            header( 'location:parts_index.php' );
        }


        //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
        //    header( 'location:index.php' );
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        header( 'location:parts_index.php' );
    }

?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Daily Expense</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/hierarchy-select.min.css" type="text/css">
    <link rel="stylesheet" href="css/vue-select.css" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />

    <style>
        th {
            text-align: center;
        }

        td {
            text-align: center;
            vertical-align: middle !important;
            font-size: small;
        }
        .red{
            color: #ff0000;
        }

        .orange{
            color: #ffa500;
        }

        .green{
            color: #00B000;
        }

        .blue{
            color: #0000ff;
        }
        .hide{
            display:none;
        }

    </style>

    <style>
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
            top: 0.75rem!important;
        }

        .custom-control-label::after {
            top: 0.75rem!important;
        }

        a.nav_link{
           color: #FFFFFF;
            font-weight: bold;
            padding: 0 20px;
            text-decoration: none;
            cursor: pointer;
            border-right: 2px solid #FFFFFF;
        }

        a.nav_link:last-of-type{
            border-right: none;
            margin-right: 20px;
        }

        tr.deleted{
            position: relative;
        }

        tr.deleted> td:first-of-type::before{
            content: "";
            width: 99%;
            height: 1px;
            background-color: red;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
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
<div style="background: rgb(30, 107, 168); padding: 0.5vh; height: 70px; display: flex; align-items: center; justify-content: space-between;">
        <a @click="logout()" style="margin-left: 25px; font-size: 25px;"><span style="color: rgb(255, 255, 255); cursor: pointer">☰</span></a>

        <div>
            <?php
                if($decoded->data->status_1)
                {
            ?>
            <a class="nav_link" href="expense_recorder.php">
                <eng>Expense Recorder</eng>
            </a>
            <?php
                }
            ?>

            <?php
                if($decoded->data->status_2)
                {
            ?>
            <a class="nav_link" href="expense_recorder_v2.php">
                <eng>Expense Recorder2</eng>
            </a>
            <?php
                }
            ?>

            <button data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true"
                    aria-controls="collapseOne" class=""
                    style="border: none; margin-right: 25px; font-weight: 700; font-size: x-large; background-color: rgb(30, 107, 168); color: rgb(255, 255, 255);">
                <i aria-hidden="true" class="fas fa-plus-square fa-lg"></i></button>
        </div>
    </div>




<div style="margin-top:2.5vh; margin-left:1.5vw; margin-bottom:3vh;">



<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="width:98.5%;">

        <div class="panel panel-default">

            <div class="panel-heading" role="tab" id="headingOne"
                 style="border: 3px solid rgb(222,226,230); padding:0.5% 0 0.2% 1%;">

                <h4 class="panel-title">

                    <span
                       style="font-size: 18px;">Add & Edit Record</span>

                </h4>
            </div>

            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" :ref="'collapseOne'">

                <div class="panel-body" style="border: 3px solid rgb(222,226,230); border-top:none;">


                    <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                        <div style="margin-bottom: -1.5vh;">&nbsp</div>

                        <tr>
                            <td style="width:15vw;">
                                <label>Date</label>
                            </td>

                            <td style="text-align: left;"><input type="date" class="form-control custom-control-inline"
                                                                 style="width:15vw;" id="todays-date" readonly></td>

                        </tr>

<!-- 
                        <tr>
                            <td>
                                <label>Account</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:15vw;" v-model="account">
                                    
                                    <option value="1">Office Petty Cash</option>
                                    <option value="3">Online Transactions</option>
                                    <option value="2">Security Bank</option>
                                </select>
                            </td>

                        </tr> -->

                        <tr>
                            <td>
                                <label>Operation Type</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:15vw;" v-model="operation_type">
                                    
                                    <option value="1">Cash In</option>
                                    <option value="2">Cash Out</option>
                                </select>
                            </td>

                        </tr>


                        <tr>
                            <td>
                                <label>Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;" v-model="category">
                                    <option>Accounting and govt payments</option>
                                    <option>Bills</option>
                                    <option>Client Refunds</option>
                                    <option>Consignment</option>
                                    <option>Credit Card</option>
                                    <option>Marketing</option>
                                    <option>Misc</option>
                                    <option>NTD to PHP</option>
                                    <option>Office Needs</option>
                                    <option>Others</option>
                                    <option>Payment for Container</option>
                                    <option>Petty Cash</option>
                                    <option>Projects</option>
                                    <option>Rental</option>
                                    <option>Salary</option>
                                    <option>Sales Petty Cash</option>
                                    <option>Store</option>
                                    <option>Transportation Petty Cash</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="category == 'Marketing' || category == 'Office Needs' || category == 'Others' || category ==  'Projects' || category == 'Store'">
                            <td>
                                <label >Sub Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;"v-model="sub_category">
                                    <option>Allowance</option>
                                    <option>Commission</option>
                                    <option>Delivery</option>
                                    <option>Maintenance</option>
                                    <option>Meals</option>
                                    <option>Misc</option>
                                    <option>Others</option>
                                    <option>Outsource</option>
                                    <option>Petty cash</option>
                                    <option>Products</option>
                                    <option>Supplies</option>
                                    <option>Tools and Materials</option>
                                    <option>Transportation</option>
                                </select>
                            </td>

                        </tr>
<!-- 
                        <tr v-if="category == 'Projects'">
                            <td style="width: 15vw;">
                                <label>Project Name</label>
                            </td>
                            <td style="text-align: left;">
                                <input type="text" class="form-control custom-control-inline" style="width: 25vw;" v-model="project_name">
                            </td>
                        </tr> -->

<!-- 
                        <tr id="relatedaccount">
                            <td>
                                <label>Related Account</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:15vw;" v-model="related_account">
                                    <option value="None">None</option>
                                    <option>Office Petty Cash</option>
                                    <option>Online Transactions</option>
                                    <option>Security Bank</option>
                                </select>
                            </td>

                        </tr> -->

                        <tr id="payee">
                            <td>
                                <label>Payee</label>
                            </td>

                            <td style="text-align: left;">

                                <div class="" style="width:15vw;">
                                    <!-- <v-select v-model="payee"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select> -->
                                              <input type="text" class="form-control" style="width:25vw;" 
                                   v-model="payee">
                                </div>

                            </td>

                        </tr>


                        <tr>
                            <td style="width:15vw;">
                                <label>Paid/Received Date</label>
                            </td>

                            <td style="text-align: left;"><input type="date" class="form-control custom-control-inline"
                                                                 style="width:15vw;" v-model="paid_date" id="todaysdate"></td>

                        </tr>


                        <tr>
                            <td style="width:15vw;">
                                <label>Amount</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control custom-control-inline"
                                                                 style="width:15vw;" v-model="amount"></td>

                        </tr>

                        <tr>
                            <td>
                                <label>Details</label>
                            </td>

                            <td style="text-align: left; width:70vw;"><textarea class="form-control" rows="2" :ref="'detail'"
                                                                               style="width:77vw;" v-html="details.replaceAll('&lt;br&gt;', '\n')"></textarea></td>

                        </tr>



                        <tr>
                            <td>
                                <label>Remarks</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control" style="width:77vw;" v-model="remarks">
                            </td>

                        </tr>

                        <tr>
                            <td>
                                <label>Photos</label>
                            </td>
 
                            <td style="text-align: left;"><input type="file" ref="file0" @change="onChangeFileUpload($event,0)" multiple>
                            </td>
                            
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <div id="sc_product_files" style="display: flex; flex-direction:column; text-align: left;">
                                        <div class="custom-control custom-checkbox" v-for="(item,index) in pic_url_array" :key="index">
                                            <div class="custom-control custom-checkbox" style="padding-top: 1%;" >
                                                    <input type="checkbox" class="custom-control-input" :id="item.id" v-model="item.is_checked" name="file_elements" :value="item.pic_url">
                                                    <label class="custom-control-label" :for="item.id">
                                                        <a :href="baseURL + item.pic_url" target="_blank">{{item.pic_url}}</a>
                                                    </label>
                                            </div>
                                        </div>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <label>Record Color</label>
                            </td>

                            <td style="text-align: left;">
                                <div class="record_color">
                                    <input type="radio" name="record_color" id="record_color_black" value="x" v-model="is_marked" checked="checked">
                                    <label for="record_color_black" style="background-color: black;"></label>

                                    <input type="radio" name="record_color" id="record_color_red" value="1" v-model="is_marked">
                                    <label for="record_color_red" style="background-color: red;"></label>

                                    <input type="radio" name="record_color" id="record_color_orange" value="2" v-model="is_marked">
                                    <label for="record_color_orange" style="background-color: orange;"></label>

                                    <input type="radio" name="record_color" id="record_color_green" value="3" v-model="is_marked">
                                    <label for="record_color_green" style="background-color: green;"></label>

                                    <input type="radio" name="record_color" id="record_color_blue" value="4" v-model="is_marked">
                                    <label for="record_color_blue" style="background-color: blue;"></label>

                                </div>
                            </td>

                        </tr>



                    </table>

                    <div style="margin-left:6vw; margin-top:2vh; margin-bottom:1.5vh;">

                        <button class="btn btn-secondary" style="width:10vw; font-weight:700" v-on:click="reset()">Reset
                        </button>

                        <button class="btn btn-secondary" style="width:10vw; font-weight:700; margin-left:2vw;" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                       aria-expanded="true" aria-controls="collapseOne" v-on:click="reset()">Cancel
                        </button>

                        <button class="btn btn-primary" style="width:10vw; font-weight:700; margin-left:2vw;" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                       aria-expanded="true" aria-controls="collapseOne" v-on:click="add(1,edd)" >Save
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>




    <div style="margin-top:2vh; margin-bottom:1vh;">

        <input type="date" v-model="start_date">&nbsp; to &nbsp;<input type="date" v-model="end_date">
        
        <select style="width:10vw; margin-left:1vw;" v-model="select_date_type">
            <option value="0" seleted>Date</option>
            <option value="1">Paid/Received Date</option>
        </select>
<!--         
        <select style="width:10vw; margin-left:1vw;" v-model="account">
            <option value="0" seleted>All</option>
            <option value="1">Office Petty Cash</option>
            <option value="3">Online Transactions</option>
            <option value="2">Security Bank</option>
        </select> -->

        <select style="width:10vw; margin-left:1vw;" v-model="select_category">
            <option value="" seleted>All</option>
            <option>Accounting and govt payments</option>
            <option>Bills</option>
            <option>Client Refunds</option>
            <option>Consignment</option>
            <option>Credit Card</option>
            <option>Marketing</option>
            <option>Misc</option>
            <option>NTD to PHP</option>
            <option>Office Needs</option>
            <option>Others</option>
            <option>Payment for Container</option>
            <option>Petty Cash</option>
            <option>Projects</option>
            <option>Rental</option>
            <option>Salary</option>
            <option>Sales Petty Cash</option>
            <option>Store</option>
            <option>Transportation Petty Cash</option>
        </select>

        <select style="width:10vw; margin-left:1vw;" v-if="select_category == 'Marketing' || select_category == 'Office Needs' || select_category == 'Others' || select_category ==  'Projects' || select_category == 'Store'" v-model="select_sub_category">
            <option>Allowance</option>
            <option>Commission</option>
            <option>Delivery</option>
            <option>Maintenance</option>
            <option>Meals</option>
            <option>Misc</option>
            <option>Others</option>
            <option>Outsource</option>
            <option>Petty cash</option>
            <option>Products</option>
            <option>Supplies</option>
            <option>Tools and Materials</option>
            <option>Transportation</option>
        </select>
        
        <input type="text" v-model="keyword" style="width:15vw; margin-left:1vw;" placeholder="Searching Keyword Here">

        <select class="hide" v-model="perPage" v-on:change="getRecords(this)">
            <option v-for="size in inventory" :value="size.id">{{size.name}}</option>
        </select>

        <button style="margin-left:1.5vw;" v-on:click="getRecords"><i class="fas fa-filter"></i></button>&ensp;
        <button v-on:click="printRecord"><i class="fas fa-file-export"></i></button>&ensp;


        <ul class="pagination pagination-sm hide" style="float:right; margin-right:1.5vw;">
                <li class="page-item" :disabled="page == 1"  @click="page < 1 ? page = 1 : page--" v-on:click="getRecords"><a class="page-link">Previous</a></li>

                <li class="page-item" v-for="pg in pages" @click="page=pg" :class="[page==pg ? 'active':'']" v-on:click="getRecords"><a class="page-link" >{{ pg }}</a></li>

                <li class="page-item" :disabled="page == pages.length" @click="page++" v-on:click="getRecords"><a class="page-link" >Next</a></li>
            </ul>

    </div>


    <div id="panelchecked">

        <table class="table table-sm table-bordered table-hover" style="width:97vw;">

            <thead class="thead-light">


            <tr>


                <th class="text-nowrap" style="width:6vw;">Date</th>

                <th class="text-nowrap" style="width:7vw;">Category</th>

                <!-- <th class="text-nowrap" style="width:7vw;">Project Name</th> -->

                <th class="text-nowrap" style="width:20vw;">Details</th>

                <th class="text-nowrap" style="width:4vw;">Photos</th>

                <th class="text-nowrap" style="width:10vw;">Payee</th>

                <th style="width:8vw;">Paid / Received Date</th>

                <th class="text-nowrap" style="width:5vw;">Cash In</th>

                <th class="text-nowrap" style="width:5vw;">Cash Out</th>

                <th class="text-nowrap" style="width:12vw;">Remarks</th>

                <th class="text-nowrap" style="width:6vw;">Actions</th>


            </tr>

            </thead>

            <tbody >
             <tr v-for='item in items' v-if="item.account == 1" :class="[(item.is_enabled == '0' ? 'deleted' : ''), (item.is_marked == '1' ? 'red' : ''), (item.is_marked == '2' ? 'orange' : ''), (item.is_marked == '3' ? 'green' : ''), (item.is_marked == '4' ? 'blue' : '')]">
                <td>{{item.created_at | dateString('YYYY-MM-DD')}}</td>

                <td>{{item.category}}<span v-if="item.sub_category != ''">>>{{item.sub_category}}</span></td>

                <!-- <td><span v-if="item.category == 'Projects'">{{item.project_name}}</span></td> -->

                <td style="text-align: left;"><span v-html="item.details.replace('&lt;br&gt;', '<br />')"></span></td>

                <td v-if="item.pic_url != ''" >
                    <a v-for="pic in item.pic_url" :href="`${mail_ip}${pic}`" target="_blank">
                        <i v-if="pic.endsWith('.jpg') || pic.endsWith('.png') || pic.endsWith('.jpeg')" class="fas fa-image fa-lg" style="display:block; margin: 0.5em;">
                        </i>
                        <i v-else="pic.endsWith('.jpg')" class="fas fa-file fa-lg" style="display:block; margin: 0.5em;" >
                        </i>
                    </a>
                </td>
                
                <td v-else>
                 </td>
                
                <td>{{item.payee}}</td>

                <td>{{item.paid_date}}</td>

                <td style="text-align: right;">{{ item.cash_in.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') }}</td>

                <td style="text-align: right;">{{item.cash_out.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</td>


                <td style="text-align: left;">{{item.remarks}}</td>


                <td class="text-nowrap" v-if="is_viewer == '1'">
                    <button v-if="1==0"><i class="fas fa-lock" :class="[item.is_locked == '1'? 'red' : '']" v-on:click="lockRecord(item.id)"></i></button>
                </td>
                <td class="text-nowrap" v-else-if="item.is_locked == '0'">
                    <button v-if="1==0" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne" v-on:click="edit(item.id)"><i class="fas fa-edit"></i>
                    </button>

<!-- 
                    <button data-toggle="modal"
                            data-target="#exampleModalScrollable" v-on:click="edit(item.id)"><i class="fas fa-project-diagram"></i>
                    </button> -->



                    <button v-if="item.is_enabled == 1" v-on:click="deleteRecord(item.id)"><i class="fas fa-times" ></i></button>

                </td>
                <td class="text-nowrap" v-else>
                </td>

            </tr>
            </tbody>

            <thead class="thead-light">

            <tr>
                <th colspan="4">Total</th>
                <th style="text-align: center;" colspan="2"><!--Beginning Balance: 0.00--></th>
                <th style="text-align: right;">{{accountOneCashIn.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: right;">{{accountOneCashOut.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: center;" colspan="2">
                Net Cash Flow: {{accountOneBalance.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
            </tr>

            </thead>

        </table>
<!-- 
        <br><br>

        <table class="table table-sm table-bordered table-hover"  style="width:97vw;">

            <thead class="thead-light">



            <tr>

                <th colspan="11" style="font-size:larger; font-weight:700;">Online Transactions</th>
            </tr>

            <tr>


                <th class="text-nowrap" style="width:6vw;">Date</th>

                <th class="text-nowrap" style="width:7vw;">Category</th>

                 <th class="text-nowrap" style="width:7vw;">Project Name</th> 

                <th class="text-nowrap" style="width:20vw;">Details</th>

                <th class="text-nowrap" style="width:4vw;">Photos</th>

                <th class="text-nowrap" style="width:10vw;">Payee</th>

                <th style="width:8vw;">Paid / Received Date</th>

                <th class="text-nowrap" style="width:5vw;">Cash In</th>

                <th class="text-nowrap" style="width:5vw;">Cash Out</th>

                <th class="text-nowrap" style="width:12vw;">Remarks</th>

                <th class="text-nowrap" style="width:6vw;">Actions</th>


            </tr>

            </thead>

            <tbody >
            <tr v-for='item in items' v-if="item.account == 3" :class="[(item.is_marked == '1' ? 'red' : ''), (item.is_marked == '2' ? 'orange' : ''), (item.is_marked == '3' ? 'green' : ''), (item.is_marked == '4' ? 'blue' : '')]">
                <td>{{item.created_at | dateString('YYYY-MM-DD')}}</td>

                <td>{{item.category}}<span v-if="item.sub_category != ''">>>{{item.sub_category}}</span></td>

                <td><span v-if="item.category == 'Projects'">{{item.project_name}}</span></td> 

                <td style="text-align: left;"><span v-html="item.details.replace('&lt;br&gt;', '<br />')"></span></td>

                <td v-if="item.pic_url != ''" >
                    <a v-for="pic in item.pic_url" :href="`${mail_ip}${pic}`" target="_blank">
                        <i v-if="pic.endsWith('.jpg') || pic.endsWith('.png') || pic.endsWith('.jpeg')" class="fas fa-image fa-lg" style="display:block; margin: 0.5em;">
                        </i>
                        <i v-else="pic.endsWith('.jpg')" class="fas fa-file fa-lg" style="display:block; margin: 0.5em;" >
                        </i>
                    </a>
                </td>
                
                <td v-else>
                </td>

                <td>{{item.payee}}</td>

                <td>{{item.paid_date}}</td>

                <td style="text-align: right;">{{item.cash_in.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</td>

                <td style="text-align: right;">{{item.cash_out.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</td>


                <td style="text-align: left;">{{item.remarks}}</td>


                <td class="text-nowrap" v-if="is_viewer == '1'">
                    <button><i class="fas fa-lock" :class="[item.is_locked == '1'? 'red' : '']" v-on:click="lockRecord(item.id)"></i></button>
                </td>
                <td class="text-nowrap" v-else-if="item.is_locked == '0'">
                    <button data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne" v-on:click="edit(item.id)"><i class="fas fa-edit"></i>
                    </button>

 
                    <button data-toggle="modal" 
                            data-target="#exampleModalScrollable" v-on:click="edit(item.id)"><i class="fas fa-project-diagram"></i>
                    </button> 



                    <button v-on:click="deleteRecord(item.id)"><i class="fas fa-times" ></i></button>

                </td>
                <td class="text-nowrap" v-else>
                </td>

            </tr>
            </tbody>

            <thead class="thead-light">

            <tr>
                <th colspan="5">Total</th>
                <th style="text-align: center;" colspan="2"> </th>
                <th style="text-align: right;">{{accountThreeCashIn.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: right;">{{accountThreeCashOut.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: center;" colspan="2">
                    Net Cash Flow: {{accountThreeBalance.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
            </tr>

            </thead>

        </table>

        <br><br> -->

<!--         
        
        <table class="table table-sm table-bordered table-hover"  style="width:97vw;">

            <thead class="thead-light">



            <tr>

                <th colspan="11" style="font-size:larger; font-weight:700;">Security Bank</th>
            </tr>

            <tr>


                <th class="text-nowrap" style="width:6vw;">Date</th>

                <th class="text-nowrap" style="width:7vw;">Category</th>

                <!-- <th class="text-nowrap" style="width:7vw;">Project Name</th> 

                <th class="text-nowrap" style="width:20vw;">Details</th>

                <th class="text-nowrap" style="width:4vw;">Photos</th>

                <th class="text-nowrap" style="width:10vw;">Payee</th>

                <th style="width:8vw;">Paid / Received Date</th>

                <th class="text-nowrap" style="width:5vw;">Cash In</th>

                <th class="text-nowrap" style="width:5vw;">Cash Out</th>

                <th class="text-nowrap" style="width:12vw;">Remarks</th>

                <th class="text-nowrap" style="width:6vw;">Actions</th>


            </tr>

            </thead>

            <tbody >
            <tr v-for='item in items' v-if="item.account == 2" :class="[(item.is_marked == '1' ? 'red' : ''), (item.is_marked == '2' ? 'orange' : ''), (item.is_marked == '3' ? 'green' : ''), (item.is_marked == '4' ? 'blue' : '')]">
                <td>{{item.created_at | dateString('YYYY-MM-DD')}}</td>

                <td>{{item.category}}<span v-if="item.sub_category != ''">>>{{item.sub_category}}</span></td>

                <!-- <td><span v-if="item.category == 'Projects'">{{item.project_name}}</span></td> 

                <td style="text-align: left;"><span v-html="item.details.replace('&lt;br&gt;', '<br />')"></span></td>

                <td v-if="item.pic_url != ''" >
                    <a v-for="pic in item.pic_url" :href="`${mail_ip}${pic}`" target="_blank">
                        <i v-if="pic.endsWith('.jpg') || pic.endsWith('.png') || pic.endsWith('.jpeg')" class="fas fa-image fa-lg" style="display:block; margin: 0.5em;">
                        </i>
                        <i v-else class="fas fa-file fa-lg" style="display:block; margin: 0.5em;" >
                        </i>
                    </a>
                </td>
                
                <td v-else>
                </td>

                <td>{{item.payee}}</td>

                <td>{{item.paid_date}}</td>

                <td style="text-align: right;">{{item.cash_in.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</td>

                <td style="text-align: right;">{{item.cash_out.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</td>


                <td style="text-align: left;">{{item.remarks}}</td>


                <td class="text-nowrap" v-if="is_viewer == '1'">
                    <button><i class="fas fa-lock" :class="[item.is_locked == '1'? 'red' : '']" v-on:click="lockRecord(item.id)"></i></button>
                </td>
                <td class="text-nowrap" v-else-if="item.is_locked == '0'">
                    <button data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne" v-on:click="edit(item.id)"><i class="fas fa-edit"></i>
                    </button>

<!-- 
                    <button data-toggle="modal"
                            data-target="#exampleModalScrollable" v-on:click="edit(item.id)"><i class="fas fa-project-diagram"></i>
                    </button> 



                    <button v-on:click="deleteRecord(item.id)"><i class="fas fa-times" ></i></button>

                </td>
                <td class="text-nowrap" v-else>
                </td>

            </tr>
            </tbody>

            <thead class="thead-light">

            <tr>
                <th colspan="5">Total</th>
                <th style="text-align: center;" colspan="2"><!--Beginning Balance: 0.00</th>
                <th style="text-align: right;">{{accountTwoCashIn.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: right;">{{accountTwoCashOut.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: center;" colspan="2">
                    Net Cash Flow: {{accountTwoBalance.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
            </tr>

            </thead>

        </table> -->

<br><br>
<!-- 
         <table class="table table-sm table-bordered table-hover" style="width:97vw;">

            <thead class="thead-light">

            <tr>
                <th style="width:25vw; font-size:larger;">All Accounts</th>
                <th style="text-align: center; width:18vw; font-size:larger;"><!--Beginning Balance: 0.00</th>
                <th style="text-align: center; width:18vw; font-size:larger;">Cash In: {{allCashIn.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: center; width:18vw; font-size:larger;">Cash Out: {{allCashOut.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
                <th style="text-align: center; width:18vw; font-size:larger;">
                Net Cash Flow: {{allBalance.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}</th>
            </tr>

            </thead>

        </table> -->





    </div>


</div>



<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="exampleModalScrollable">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="myLargeModalLabel">Split Record</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>


            <div class="modal-body">


                <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                    <div style="margin-bottom: -2vh;">&nbsp</div>

                    <tr>
                        <td style="width:15vw;">
                            <label>Date</label>
                        </td>

                        <td style="text-align: left;"><input type="date" class="form-control"
                                                             style="width:15vw;" readonly id="todays_date"></td>

                    </tr>

<!-- 
                    <tr>
                        <td>
                            <label>Account</label>
                        </td>

                        <td style="text-align: left;">
                            <input type="text" class="form-control" style="width:15vw;" readonly
                                   v-model="account_name">
                        </td>

                    </tr> -->

                    <tr>
                        <td>
                            <label>Operation Type</label>
                        </td>

                        <td style="text-align: left;">
                            <input type="text" class="form-control" style="width:15vw;" readonly v-model="formatOperationType(operation_type)">
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <label>Category</label>
                        </td>
                        <td style="text-align: left;">
                            <input type="text" class="form-control" style="width:25vw;" readonly v-model="category">
                        </td>

                    </tr>

<!-- 
                    <tr>
                        <td>
                            <label>Related Account</label>
                        </td>

                        <td style="text-align: left;">
                            <input type="text" class="form-control" style="width:25vw;" readonly
                                   v-model="related_account">
                        </td>

                    </tr> -->


                    <tr>
                        <td>
                            <label>Payee</label>
                        </td>

                        <td style="text-align: left;">
                            <input type="text" class="form-control" style="width:25vw;" readonly
                                   v-model="payee">
                        </td>

                    </tr>


                    <tr>
                        <td style="width:15vw;">
                            <label>Paid/Received Date</label>
                        </td>

                        <td style="text-align: left;"><input type="text" class="form-control"
                                                             style="width:15vw;" readonly v-model="paid_date"></td>

                    </tr>


                    <tr>
                        <td>
                            <label>Amount</label>
                        </td>

                        <td style="text-align: left; width:70vw;"><input type="text" class="form-control"
                                                                         style="width:15vw;" v-model="amount"
                                                                         readonly></td>

                    </tr>


                    <tr>
                        <td>
                            <label>Details</label>
                        </td>

                        <td style="text-align: left;">
                            <textarea class="form-control" rows="3" style="width:45vw;" v-model="details.replaceAll('&lt;br&gt;', '\n')" readonly></textarea>
                        </td>

                    </tr>


                    <tr>
                        <td>
                            <label>Remarks</label>
                        </td>

                        <td style="text-align: left;">
                            <input type="text" class="form-control" style="width:25vw;" readonly v-model="remarks">
                        </td>

                    </tr>


                    <tr>
                        <td>
                            <label>Photos</label>
                        </td>

                        <td style="text-align: left;">
                            <a :href="`${mail_ip}`" target="_blank"><i class="fas fa-image fa-lg" ></i></a>
                        </td>

                    </tr>

                </table>


                <hr>
                <h6>Split Record #1</h6>


                <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                    <div style="margin-bottom: -2vh;">&nbsp</div>

                    <tr>
                        <td style="width:8.5vw;">
                            <label>Category</label>
                        </td>

                        <td style="text-align: left;">
                            <select class="form-control" style="width:25vw;" id="categories" v-model ="split1.category">
                                <option>Accounting and govt payments</option>
                                <option>Bills</option>
                                <option>Client Refunds</option>
                                <option>Consignment</option>
                                <option>Credit Card</option>
                                <option>Marketing</option>
                                <option>Misc</option>
                                <option>Office Needs</option>
                                <option>Others</option>
                                <option>Projects</option>
                                <option>Rental</option>
                                <option>Salary</option>
                                <option>Sales Petty Cash</option>
                                <option>Store</option>
                                <option>Transportation Petty Cash</option>
                                <option>Payment for Container</option>
                                <option>NTD to PHP</option>
                                <option>Petty Cash</option>
                            </select>
                        </td>

                    </tr>

                    <tr v-if="split1.category == 'Marketing' ||  split1.category == 'Office Needs' || split1.category == 'Others' || split1.category ==  'Projects' || split1.category == 'Store'">
                        <td>
                            <label >Sub Category</label>
                        </td>

                        <td style="text-align: left;">
                            <select class="form-control" style="width:25vw;"v-model="split1.sub_category">
                                <option>Allowance</option>
                                <option>Commission</option>
                                <option>Delivery</option>
                                <option>Maintenance</option>
                                <option>Meals</option>
                                <option>Misc</option>
                                <option>Others</option>
                                <option>Outsource</option>
                                <option>Petty cash</option>
                                <option>Products</option>
                                <option>Supplies</option>
                                <option>Tools and Materials</option>
                                <option>Transportation</option>
                            </select>
                        </td>

                    </tr>

                    <tr v-if="split1.category == 'Projects'">
                            <td style="width: 15vw;">
                                <label>Project Name</label>
                            </td>
                            <td style="text-align: left;">
                                <input type="text" class="form-control custom-control-inline" style="width: 25vw;" v-model="split1.project_name">
                            </td>
                        </tr>


                    <tr id="payee">
                        <td>
                            <label>Payee</label>
                        </td>

                        <td style="text-align: left;">

                            <div class="" style="width:15vw;">
                                <v-select v-model="split1.payee"
                                          :options="payees"
                                          attach
                                          chips
                                          label="payeeName"
                                          multiple></v-select>
                            </div>

                        </td>

                    </tr>


                    <tr>
                        <td>
                            <label>Amount</label>
                        </td>

                        <td style="text-align: left;"><input type="text" class="form-control"
                                                                         style="width:15vw;" v-model="split1.amount"></td>

                    </tr>


                    <tr>
                        <td>
                            <label>Details</label>
                        </td>

                        <td style="text-align: left;"><textarea class="form-control" rows="2" :ref="'detail1'"
                                                                            style="width:45vw;" v-html="split1.details.replaceAll('&lt;br&gt;', '\n')"></textarea></td>

                    </tr>


                    <tr>
                        <td>
                            <label>Remarks</label>
                        </td>

                        <td style="text-align: left;"><input type="text" class="form-control" style="width:45vw;" v-model="split1.remarks">
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <label>Photos</label>
                        </td>

                        <td style="text-align: left;"><input type="file" ref="file1" @change="onChangeFileUpload($event,1)" multiple>
                        </td>

                    </tr>

                    <tr>
                            <td></td>
                            <td>
                                <div id="sc_product_files" style="display: flex; flex-direction:column; text-align: left;">
                                        <div class="custom-control custom-checkbox" v-for="(item,index) in pic_url_array1" :key="index">
                                            <div class="custom-control custom-checkbox" style="padding-top: 1%;" >
                                                    <input type="checkbox" class="custom-control-input" :id="item.id" v-model="item.is_checked" name="file_elements1" :value="item.pic_url">
                                                    <label class="custom-control-label" :for="item.id">
                                                        <a :href="baseURL + item.pic_url" target="_blank">{{item.pic_url}}</a>
                                                    </label>
                                            </div>
                                        </div>
                                </div>
                            </td>
                        </tr>

                    <tr>
                        <td>
                            <label>Record Color</label>
                        </td>

                        <td style="text-align: left;">
                            <div class="record_color">
                                <input type="radio" name="record_color1" id="record_color_black1" value="x" v-model="split1.is_marked" checked="checked">
                                <label for="record_color_black1" style="background-color: black;"></label>

                                <input type="radio" name="record_color1" id="record_color_red1" value="1" v-model="split1.is_marked">
                                <label for="record_color_red1" style="background-color: red;"></label>

                                <input type="radio" name="record_color1" id="record_color_orange1" value="2" v-model="split1.is_marked">
                                <label for="record_color_orange1" style="background-color: orange;"></label>

                                <input type="radio" name="record_color1" id="record_color_green1" value="3" v-model="split1.is_marked">
                                <label for="record_color_green1" style="background-color: green;"></label>

                                <input type="radio" name="record_color1" id="record_color_blue1" value="4" v-model="split1.is_marked">
                                <label for="record_color_blue1" style="background-color: blue;"></label>

                            </div>
                        </td>

                    </tr>

                </table>
                    <h6>Split Record #2</h6>


                    <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                        <div style="margin-bottom: -2vh;">&nbsp</div>

                        <tr>
                            <td style="width:8.5vw;">
                                <label>Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;" id="categories" v-model ="split2.category">
                                    <option>Accounting and govt payments</option>
                                    <option>Bills</option>
                                    <option>Client Refunds</option>
                                    <option>Consignment</option>
                                    <option>Credit Card</option>
                                    <option>Marketing</option>
                                    <option>Misc</option>
                                    <option>Office Needs</option>
                                    <option>Others</option>
                                    <option>Projects</option>
                                    <option>Rental</option>
                                    <option>Salary</option>
                                    <option>Sales Petty Cash</option>
                                    <option>Store</option>
                                    <option>Transportation Petty Cash</option>
                                    <option>Payment for Container</option>
                                    <option>NTD to PHP</option>
                                    <option>Petty Cash</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split2.category == 'Marketing' ||  split2.category == 'Office Needs' || split2.category == 'Others' || split2.category ==  'Projects' || split2.category == 'Store'">
                            <td>
                                <label >Sub Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;"v-model="split2.sub_category">
                                    <option>Allowance</option>
                                    <option>Commission</option>
                                    <option>Delivery</option>
                                    <option>Maintenance</option>
                                    <option>Meals</option>
                                    <option>Misc</option>
                                    <option>Others</option>
                                    <option>Outsource</option>
                                    <option>Petty cash</option>
                                    <option>Products</option>
                                    <option>Supplies</option>
                                    <option>Tools and Materials</option>
                                    <option>Transportation</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split2.category == 'Projects'">
                            <td style="width: 15vw;">
                                <label>Project Name</label>
                            </td>
                            <td style="text-align: left;">
                                <input type="text" class="form-control custom-control-inline" style="width: 25vw;" v-model="split2.project_name">
                            </td>
                        </tr>


                        <tr id="payee">
                            <td>
                                <label>Payee</label>
                            </td>

                            <td style="text-align: left;">

                                <div class="" style="width:15vw;">
                                    <v-select v-model="split2.payee"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                                </div>

                            </td>

                        </tr>


                        <tr>
                            <td>
                                <label>Amount</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control"
                                                                 style="width:15vw;" v-model="split2.amount"></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Details</label>
                            </td>

                            <td style="text-align: left;"><textarea class="form-control" rows="2" :ref="'detail2'"
                                                                    style="width:45vw;" v-html="split2.details.replaceAll('&lt;br&gt;', '\n')"></textarea></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Remarks</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control" style="width:45vw;" v-model="split2.remarks">
                            </td>

                        </tr>

                        <tr>
                            <td>
                                <label>Photos</label>
                            </td>

                            <td style="text-align: left;"><input type="file" ref="file2" @change="onChangeFileUpload($event,2)" multiple>
                            </td>

                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <div id="sc_product_files" style="display: flex; flex-direction:column; text-align: left;">
                                        <div class="custom-control custom-checkbox" v-for="(item,index) in pic_url_array2" :key="index">
                                            <div class="custom-control custom-checkbox" style="padding-top: 1%;" >
                                                    <input type="checkbox" class="custom-control-input" :id="item.id" v-model="item.is_checked" name="file_elements2" :value="item.pic_url">
                                                    <label class="custom-control-label" :for="item.id">
                                                        <a :href="baseURL + item.pic_url" target="_blank">{{item.pic_url}}</a>
                                                    </label>
                                            </div>
                                        </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                        <td>
                            <label>Record Color</label>
                        </td>

                        <td style="text-align: left;">
                            <div class="record_color">
                                <input type="radio" name="record_color2" id="record_color_black2" value="x" v-model="split2.is_marked" checked="checked">
                                <label for="record_color_black2" style="background-color: black;"></label>

                                <input type="radio" name="record_color2" id="record_color_red2" value="1" v-model="split2.is_marked">
                                <label for="record_color_red2" style="background-color: red;"></label>

                                <input type="radio" name="record_color2" id="record_color_orange2" value="2" v-model="split2.is_marked">
                                <label for="record_color_orange2" style="background-color: orange;"></label>

                                <input type="radio" name="record_color2" id="record_color_green2" value="3" v-model="split2.is_marked">
                                <label for="record_color_green2" style="background-color: green;"></label>

                                <input type="radio" name="record_color2" id="record_color_blue2" value="4" v-model="split2.is_marked">
                                <label for="record_color_blue2" style="background-color: blue;"></label>

                            </div>
                        </td>

                        </tr>

                    </table>
                    <h6>Split Record #3</h6>


                    <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                        <div style="margin-bottom: -2vh;">&nbsp</div>

                        <tr>
                            <td style="width:8.5vw;">
                                <label>Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;" id="categories" v-model ="split3.category">
                                    <option>Accounting and govt payments</option>
                                    <option>Bills</option>
                                    <option>Client Refunds</option>
                                    <option>Consignment</option>
                                    <option>Credit Card</option>
                                    <option>Marketing</option>
                                    <option>Misc</option>
                                    <option>Office Needs</option>
                                    <option>Others</option>
                                    <option>Projects</option>
                                    <option>Rental</option>
                                    <option>Salary</option>
                                    <option>Sales Petty Cash</option>
                                    <option>Store</option>
                                    <option>Transportation Petty Cash</option>
                                    <option>Payment for Container</option>
                                    <option>NTD to PHP</option>
                                    <option>Petty Cash</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split3.category == 'Marketing' ||  split3.category == 'Office Needs' || split3.category == 'Others' || split3.category ==  'Projects' || split3.category == 'Store'">
                            <td>
                                <label >Sub Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;"v-model="split3.sub_category">
                                    <option>Allowance</option>
                                    <option>Commission</option>
                                    <option>Delivery</option>
                                    <option>Maintenance</option>
                                    <option>Meals</option>
                                    <option>Misc</option>
                                    <option>Others</option>
                                    <option>Outsource</option>
                                    <option>Petty cash</option>
                                    <option>Products</option>
                                    <option>Supplies</option>
                                    <option>Tools and Materials</option>
                                    <option>Transportation</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split3.category == 'Projects'">
                            <td style="width: 15vw;">
                                <label>Project Name</label>
                            </td>
                            <td style="text-align: left;">
                                <input type="text" class="form-control custom-control-inline" style="width: 25vw;" v-model="split3.project_name">
                            </td>
                        </tr>


                        <tr id="payee">
                            <td>
                                <label>Payee</label>
                            </td>

                            <td style="text-align: left;">

                                <div class="" style="width:15vw;">
                                    <v-select v-model="split3.payee"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                                </div>

                            </td>

                        </tr>


                        <tr>
                            <td>
                                <label>Amount</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control"
                                                                 style="width:15vw;" v-model="split3.amount"></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Details</label>
                            </td>

                            <td style="text-align: left;"><textarea class="form-control" rows="2" :ref="'detail3'"
                                                                    style="width:45vw;" v-html="split3.details.replaceAll('&lt;br&gt;', '\n')"></textarea></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Remarks</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control" style="width:45vw;" v-model="split3.remarks">
                            </td>

                        </tr>

                        <tr>
                            <td>
                                <label>Photos</label>
                            </td>

                            <td style="text-align: left;"><input type="file" ref="file3" @change="onChangeFileUpload($event,3)" multiple>
                            </td>

                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <div id="sc_product_files" style="display: flex; flex-direction:column; text-align: left;">
                                        <div class="custom-control custom-checkbox" v-for="(item,index) in pic_url_array3" :key="index">
                                            <div class="custom-control custom-checkbox" style="padding-top: 1%;" >
                                                    <input type="checkbox" class="custom-control-input" :id="item.id" v-model="item.is_checked" name="file_elements3" :value="item.pic_url">
                                                    <label class="custom-control-label" :for="item.id">
                                                        <a :href="baseURL + item.pic_url" target="_blank">{{item.pic_url}}</a>
                                                    </label>
                                            </div>
                                        </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                        <td>
                            <label>Record Color</label>
                        </td>

                        <td style="text-align: left;">
                            <div class="record_color">
                                <input type="radio" name="record_color3" id="record_color_black3" value="x" v-model="split3.is_marked" checked="checked">
                                <label for="record_color_black3" style="background-color: black;"></label>

                                <input type="radio" name="record_color3" id="record_color_red3" value="1" v-model="split3.is_marked">
                                <label for="record_color_red3" style="background-color: red;"></label>

                                <input type="radio" name="record_color3" id="record_color_orange3" value="2" v-model="split3.is_marked">
                                <label for="record_color_orange3" style="background-color: orange;"></label>

                                <input type="radio" name="record_color3" id="record_color_green3" value="3" v-model="split3.is_marked">
                                <label for="record_color_green3" style="background-color: green;"></label>

                                <input type="radio" name="record_color3" id="record_color_blue3" value="4" v-model="split3.is_marked">
                                <label for="record_color_blue3" style="background-color: blue;"></label>

                            </div>
                        </td>

                        </tr>

                    </table>
                    <h6>Split Record #4</h6>


                    <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                        <div style="margin-bottom: -2vh;">&nbsp</div>

                        <tr>
                            <td style="width:8.5vw;">
                                <label>Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;" id="categories" v-model ="split4.category">
                                    <option>Accounting and govt payments</option>
                                    <option>Bills</option>
                                    <option>Client Refunds</option>
                                    <option>Consignment</option>
                                    <option>Credit Card</option>
                                    <option>Marketing</option>
                                    <option>Misc</option>
                                    <option>Office Needs</option>
                                    <option>Others</option>
                                    <option>Projects</option>
                                    <option>Rental</option>
                                    <option>Salary</option>
                                    <option>Sales Petty Cash</option>
                                    <option>Store</option>
                                    <option>Transportation Petty Cash</option>
                                    <option>Payment for Container</option>
                                    <option>NTD to PHP</option>
                                    <option>Petty Cash</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split4.category == 'Marketing' ||  split4.category == 'Office Needs' || split4.category == 'Others' || split4.category ==  'Projects' || split4.category == 'Store'">
                            <td>
                                <label >Sub Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;"v-model="split4.sub_category">
                                    <option>Allowance</option>
                                    <option>Commission</option>
                                    <option>Delivery</option>
                                    <option>Maintenance</option>
                                    <option>Meals</option>
                                    <option>Misc</option>
                                    <option>Others</option>
                                    <option>Outsource</option>
                                    <option>Petty cash</option>
                                    <option>Products</option>
                                    <option>Supplies</option>
                                    <option>Tools and Materials</option>
                                    <option>Transportation</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split4.category == 'Projects'">
                            <td style="width: 15vw;">
                                <label>Project Name</label>
                            </td>
                            <td style="text-align: left;">
                                <input type="text" class="form-control custom-control-inline" style="width: 25vw;" v-model="split4.project_name">
                            </td>
                        </tr>


                        <tr id="payee">
                            <td>
                                <label>Payee</label>
                            </td>

                            <td style="text-align: left;">

                                <div class="" style="width:15vw;">
                                    <v-select v-model="split4.payee"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                                </div>

                            </td>

                        </tr>


                        <tr>
                            <td>
                                <label>Amount</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control"
                                                                 style="width:15vw;" v-model="split4.amount"></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Details</label>
                            </td>

                            <td style="text-align: left;"><textarea class="form-control" rows="2" :ref="'detail4'"
                                                                    style="width:45vw;" v-html="split4.details.replaceAll('&lt;br&gt;', '\n')"></textarea></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Remarks</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control" style="width:45vw;" v-model="split4.remarks">
                            </td>

                        </tr>

                        <tr>
                            <td>
                                <label>Photos</label>
                            </td>

                            <td style="text-align: left;"><input type="file" ref="file4" @change="onChangeFileUpload($event,4)" multiple>
                            </td>

                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <div id="sc_product_files" style="display: flex; flex-direction:column; text-align: left;">
                                        <div class="custom-control custom-checkbox" v-for="(item,index) in pic_url_array4" :key="index">
                                            <div class="custom-control custom-checkbox" style="padding-top: 1%;" >
                                                    <input type="checkbox" class="custom-control-input" :id="item.id" v-model="item.is_checked" name="file_elements4" :value="item.pic_url">
                                                    <label class="custom-control-label" :for="item.id">
                                                        <a :href="baseURL + item.pic_url" target="_blank">{{item.pic_url}}</a>
                                                    </label>
                                            </div>
                                        </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                        <td>
                            <label>Record Color</label>
                        </td>

                        <td style="text-align: left;">
                            <div class="record_color">
                                <input type="radio" name="record_color4" id="record_color_black4" value="x" v-model="split4.is_marked" checked="checked">
                                <label for="record_color_black4" style="background-color: black;"></label>

                                <input type="radio" name="record_color4" id="record_color_red4" value="1" v-model="split4.is_marked">
                                <label for="record_color_red4" style="background-color: red;"></label>

                                <input type="radio" name="record_color4" id="record_color_orange4" value="2" v-model="split4.is_marked">
                                <label for="record_color_orange4" style="background-color: orange;"></label>

                                <input type="radio" name="record_color4" id="record_color_green4" value="3" v-model="split4.is_marked">
                                <label for="record_color_green4" style="background-color: green;"></label>

                                <input type="radio" name="record_color4" id="record_color_blue4" value="4" v-model="split4.is_marked">
                                <label for="record_color_blue4" style="background-color: blue;"></label>

                            </div>
                        </td>

                        </tr>

                    </table>
                    <h6>Split Record #5</h6>


                    <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                        <div style="margin-bottom: -2vh;">&nbsp</div>

                        <tr>
                            <td style="width:8.5vw;">
                                <label>Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;" id="categories" v-model ="split5.category">
                                    <option>Accounting and govt payments</option>
                                    <option>Bills</option>
                                    <option>Client Refunds</option>
                                    <option>Consignment</option>
                                    <option>Credit Card</option>
                                    <option>Marketing</option>
                                    <option>Misc</option>
                                    <option>Office Needs</option>
                                    <option>Others</option>
                                    <option>Projects</option>
                                    <option>Rental</option>
                                    <option>Salary</option>
                                    <option>Sales Petty Cash</option>
                                    <option>Store</option>
                                    <option>Transportation Petty Cash</option>
                                    <option>Payment for Container</option>
                                    <option>NTD to PHP</option>
                                    <option>Petty Cash</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split5.category == 'Marketing' ||  split5.category == 'Office Needs' || split5.category == 'Others' || split5.category ==  'Projects' || split5.category == 'Store'">
                            <td>
                                <label >Sub Category</label>
                            </td>

                            <td style="text-align: left;">
                                <select class="form-control" style="width:25vw;"v-model="split5.sub_category">
                                    <option>Allowance</option>
                                    <option>Commission</option>
                                    <option>Delivery</option>
                                    <option>Maintenance</option>
                                    <option>Meals</option>
                                    <option>Misc</option>
                                    <option>Others</option>
                                    <option>Outsource</option>
                                    <option>Petty cash</option>
                                    <option>Products</option>
                                    <option>Supplies</option>
                                    <option>Tools and Materials</option>
                                    <option>Transportation</option>
                                </select>
                            </td>

                        </tr>

                        <tr v-if="split5.category == 'Projects'">
                            <td style="width: 15vw;">
                                <label>Project Name</label>
                            </td>
                            <td style="text-align: left;">
                                <input type="text" class="form-control custom-control-inline" style="width: 25vw;" v-model="split5.project_name">
                            </td>
                        </tr>


                        <tr id="payee">
                            <td>
                                <label>Payee</label>
                            </td>

                            <td style="text-align: left;">

                                <div class="" style="width:15vw;">
                                    <v-select v-model="split5.payee"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                                </div>

                            </td>

                        </tr>


                        <tr>
                            <td>
                                <label>Amount</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control"
                                                                 style="width:15vw;" v-model="split5.amount"></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Details</label>
                            </td>

                            <td style="text-align: left;"><textarea class="form-control" rows="2" :ref="'detail5'"
                                                                    style="width:45vw;" v-html="split5.details.replaceAll('&lt;br&gt;', '\n')"></textarea></td>

                        </tr>


                        <tr>
                            <td>
                                <label>Remarks</label>
                            </td>

                            <td style="text-align: left;"><input type="text" class="form-control" style="width:45vw;" v-model="split5.remarks">
                            </td>

                        </tr>

                        <tr>
                            <td>
                                <label>Photos</label>
                            </td>

                            <td style="text-align: left;"><input type="file" ref="file5" @change="onChangeFileUpload($event,5)" multiple>
                            </td>

                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <div id="sc_product_files" style="display: flex; flex-direction:column; text-align: left;">
                                        <div class="custom-control custom-checkbox" v-for="(item,index) in pic_url_array5" :key="index">
                                            <div class="custom-control custom-checkbox" style="padding-top: 1%;" >
                                                    <input type="checkbox" class="custom-control-input" :id="item.id" v-model="item.is_checked" name="file_elements5" :value="item.pic_url">
                                                    <label class="custom-control-label" :for="item.id">
                                                        <a :href="baseURL + item.pic_url" target="_blank">{{item.pic_url}}</a>
                                                    </label>
                                            </div>
                                        </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                        <td>
                            <label>Record Color</label>
                        </td>

                        <td style="text-align: left;">
                            <div class="record_color">
                                <input type="radio" name="record_color5" id="record_color_black5" value="x" v-model="split5.is_marked" checked="checked">
                                <label for="record_color_black5" style="background-color: black;"></label>

                                <input type="radio" name="record_color5" id="record_color_red5" value="1" v-model="split5.is_marked">
                                <label for="record_color_red5" style="background-color: red;"></label>

                                <input type="radio" name="record_color5" id="record_color_orange5" value="2" v-model="split5.is_marked">
                                <label for="record_color_orange5" style="background-color: orange;"></label>

                                <input type="radio" name="record_color5" id="record_color_green5" value="3" v-model="split5.is_marked">
                                <label for="record_color_green5" style="background-color: green;"></label>

                                <input type="radio" name="record_color5" id="record_color_blue5" value="4" v-model="split5.is_marked">
                                <label for="record_color_blue5" style="background-color: blue;"></label>

                            </div>
                        </td>

                        </tr>

                    </table>
                <div style="margin-left:6vw; margin-top:2vh; margin-bottom:1.5vh;">

                    <button class="btn btn-secondary" style="width:10vw; font-weight:700; margin-left:2vw;" aria-label="Close" data-dismiss="modal"
                            v-on:click="reset()">Cancel
                    </button>

                    <button class="btn btn-primary" style="width:10vw; font-weight:700; margin-left:2vw;" aria-label="Close" :data-dismiss="dismiss"
                             v-on:click="add(2)">Confirm
                    </button>

                </div>


            </div>


        </div>

    </div>

</div>


</div>











</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script src="https://cdn.bootcss.com/moment.js/2.21.0/moment.js"></script>
<script src="js/vue-select.js"></script>
<script src="js/axios.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="js/a076d05399.js"></script> 
<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<script>

$(document).ready(function(){
    var today = new Date();
    var dd = ("0" + (today.getDate())).slice(-2);
    var mm = ("0" + (today.getMonth() + 1)).slice(-2);
    var yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;
    $("#todays-date").attr("value", today);
    $("#todays_date").attr("value", today);
    $("#todaysdate").attr("value", today);

});
    
</script>


<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script defer src="js/add_or_edit_price_record_v2.js"></script>

</html>
