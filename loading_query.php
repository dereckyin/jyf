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
    <link rel="stylesheet" href="css/jquery-ui/1.12.1/jquery-ui.css">
    <link rel="stylesheet" href="css/bootstrap/4.3.1/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/datatables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="js/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

    <!-- jQuery和js載入 -->
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

    </style>

    <script>
        $(function () {
            $('header').load('include/header_admin.php');
        })
    </script>
</head>

<body>
<div class="bodybox">
    <!-- header -->
    <header></header>
    <!-- header end -->
    <div id='receive_record'>
        <div class="mainContent">
            <h6>貨櫃記錄
                <eng>Loading Goods into Container</eng>
            </h6>
            <div class="block">
                <div class="btnbox">
                    <?php
                    if($taiwan_read == "0")
                    {
                    ?>
                    <a class="btn small" href="loading.php">新增貨櫃記錄
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
            <div class="block record show">
                <h6>當前貨櫃紀錄
                    <eng>Current Container Records</eng>
                </h6>
                <!-- list -->
                <div class="mainlist">

                    <div class="listheader">
                        <div class="pageblock" style="float:right;"> Page Size:
                            <select v-model="perPage_loading">
                                <option v-for="item in inventory" :value="item" :key="item.id">
                                    {{ item.name }}
                                </option>
                            </select> Page:
                            <div class="pageblock">
                                <a class="first micons" @click="page_loading=1">first_page</a>
                                <a class="prev micons" :disabled="page_loading == 1"
                                   @click="page_loading < 1 ? page_loading = 1 : page_loading--">chevron_left</a>
                                <select v-model="page_loading">
                                    <option v-for="pg in pages_loading" :value="pg">
                                        {{ pg }}
                                    </option>
                                </select>

                                <a class="next micons" :disabled="page_loading == pages_loading.length"
                                   @click="page_loading++">chevron_right</a>
                                <a class="last micons" @click="page_loading=pages_loading.length">last_page</a>
                            </div>
                        </div>
                        <!-- <div class="searchblock" style="float:left;">搜尋<input type="text"></div> -->
                    </div>

                    <div class="tablebox d02">
                        <ul class="header">
                            <li>勾選
                                <eng>Check</eng>
                            </li>
                            <li>櫃號
                                <eng>Container Number</eng>
                            </li>
                            <li>S/O</li>
                            <li>船公司
                                <eng>Shipping Line Company</eng>
                            </li>
                            <li>結關日期
                                <eng>Date Sent</eng>
                            </li>
                            <li>O/B</li>
                            <li>ETA</li>
                            <li>貨櫃到倉日期
                                <eng>Date C/R</eng>
                            </li>
                            <li>領櫃人
                                <eng>Broker</eng>
                            </li>
                        </ul>
                        <ul v-for='(record, index) in displayedLoading'>
                            <li>
                                <input type="checkbox" name="record_id" class="alone" :value="record.index"
                                       :true-value="1" v-model:checked="record.is_checked">
                            </li>
                            <li>{{ record.container_number }}</li>
                            <li>{{ record.so }}</li>
                            <li>{{ record.ship_company }}</li>
                            <li>{{ record.date_sent }}</li>
                            <li :style="[record.ob_date_his.length > 10 ? {'color': 'red'} : {'color': 'black'}]">{{
                                record.ob_date }}
                            </li>
                            <li :style="[record.eta_date_his.length > 10 ? {'color': 'red'} : {'color': 'black'}]">{{
                                record.eta_date }}
                            </li>
                            <li :style="[record.date_arrive_his.length > 10 ? {'color': 'red'} : {'color': 'black'}]">{{
                                record.date_arrive }}
                            </li>
                            <li>{{ record.broker }}</li>
                        </ul>
                    </div>
                </div>

                <div class="btnbox">
                    <a class="btn small" @click="editRecord();">查看
                        <eng>Show</eng>
                    </a>
                    <a class="btn small" v-bind:href="pageUrl">匯出
                        <eng>Export to Excel, Pdf, Print
                        </eng>
                    </a>
                </div>
            </div>

            <div class="block">
                <div class="tablebox d01">
                    <ul>
                        <li>麥頭
                            <eng>Shipping Mark</eng>
                        </li>
                        <li><input type="text" name="shipping_mark" v-model="record.shipping_mark"></li>
                        <li>櫃號
                            <eng>Container Number</eng>
                        </li>
                        <li><input type="text" name="container_number" v-model="record.container_number"></li>
                    </ul>
                    <ul>
                        <li>空櫃重
                            <eng>Empty Container Weight</eng>
                        </li>
                        <li><input type="text" name="estimate_weight" v-model="record.estimate_weight"></li>
                        <li>實際櫃重
                            <eng>Actual Weight</eng>
                        </li>
                        <li><input type="text" name="actual_weight" v-model="record.actual_weight"></li>
                    </ul>
                    <ul>
                        <li>封條
                            <eng>Seal</eng>
                        </li>
                        <li><input type="text" name="seal" v-model="record.seal"></li>
                        <li>S/O</li>
                        <li><input type="text" name="so" v-model="record.so"></li>
                    </ul>
                </div>
                <div class="tablebox d01">
                    <ul>
                        <li>船公司
                            <eng>Shipping Line Company</eng>
                        </li>
                        <li><input type="text" name="ship_company" v-model="record.ship_company"></li>
                        <li>船名航次
                            <eng>Shipping Line Boat</eng>
                        </li>
                        <li><input type="text" name="ship_boat" v-model="record.ship_boat"></li>
                    </ul>
                    <ul>
                        <!-- <li>領櫃<eng>Neck Cabinet</eng></li>
                        <li><input type="text" name="neck_cabinet" v-model="record.neck_cabinet"></li> -->
                        <li>出貨人
                            <eng>Shipper</eng>
                        </li>
                        <li>
                            <select v-model="record.shipper">
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
                            <select v-model="record.broker">
                                <option v-for="item in name" :value="item.name" :key="item.id"
                                        :selected="item.name == record.broker">
                                    {{ item.name }}
                                </option>
                            </select>
                        </li>
                    </ul>
                </div>
                <div class="tablebox lo01 withbtn">
                    <ul>
                        <li>結關
                            <eng>Date Sent</eng>
                        </li>
                        <li>ETD</li>
                        <li>O/B</li>
                        <li>ETA</li>
                        <li>C/R</li>
                    </ul>
                    <ul style="white-space: pre-wrap;">
                        <li> {{ (typeof record.date_send_his !== 'undefined') ?
                            record.date_send_his.replace(/(?:\r\n|\r|\n|,)/g, '\n') : "" }}
                        </li>
                        <li>{{ (typeof record.etd_date_his !== 'undefined') ?
                            record.etd_date_his.replace(/(?:\r\n|\r|\n|,)/g, '\n') : "" }}
                        </li>
                        <li>{{ (typeof record.ob_date_his !== 'undefined') ?
                            record.ob_date_his.replace(/(?:\r\n|\r|\n|,)/g, '\n') : "" }}
                        </li>
                        <li>{{ (typeof record.eta_date_his !== 'undefined') ?
                            record.eta_date_his.replace(/(?:\r\n|\r|\n|,)/g, '\n') : "" }}
                        </li>
                        <li>{{ (typeof record.date_arrive_his !== 'undefined') ?
                            record.date_arrive_his.replace(/(?:\r\n|\r|\n|,)/g, '\n') : "" }}
                        </li>
                    </ul>
                </div>
                <div class="tablebox d01 withbtn">
                    <ul>
                        <li>結關
                            <eng>Date Sent</eng>
                        </li>
                        <li><input type="text" name="neck_cabinet" v-model="record.date_sent"></li>
                        <li>ETD</li>
                        <li><input type="text" name="neck_cabinet" v-model="record.etd_date"></li>
                        <li>O/B</li>
                        <li><input type="text" name="neck_cabinet" v-model="record.ob_date"></li>
                        <li>ETA</li>
                        <li><input type="text" name="neck_cabinet" v-model="record.eta_date"></li>
                        <li>C/R</li>
                        <li><input type="text" name="neck_cabinet" v-model="record.date_arrive"></li>
                    </ul>
                </div>
                <div class="tablebox d01">
                    <ul><!-- 配色底用 --></ul>
                    <ul>
                        <li>備註
                            <eng>Remark</eng>
                        </li>
                        <li><input type="text" name="remark" v-model="record.remark"></li>
                    </ul>
                    <ul>
                        <li>貨櫃照片
                            <eng>Container Photo</eng>
                        </li>
                        <li style="display: flex; align-items: center; flex-wrap: wrap;">
                            <div class="photobox" v-for="(item, index) in record.pic">
                                <img v-if="item.type == 'FILE'" :src="'img/' + item.gcp_name">
                                <img v-if="item.type == 'LOADING'" :src="url_ip + item.gcp_name">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="block record show">
                <h6>當前貨櫃紀錄
                    <eng>Current Container Records</eng>
                </h6>
                <!-- list -->
                <div class="mainlist" style="overflow-x: auto;">

                    <div class="tablebox d02">
                        <ul class="header">
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
                            <li>
                                <?php
                          if($taiwan_read == 0)  
                          {
                        ?>
                                功能
                                <?php
                                }
                            ?>
                            </li>
                        </ul>
                        <ul v-for='(receive_record, index) in displayedPosts' :key="index">
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.date_receive}}</label>
                                </div>
                                <input name="receive_record"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="date_receive" maxlength="10">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.customer.replace(/\\/g, '') }}</label>
                                </div>
                                <input name="customer"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="customer" maxlength="256">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <i class="fas fa-image"  v-if="receive_record.pic != ''" @click="zoom_rec(receive_record.id)"></i> 
                                </div>
                                <i class="fas fa-image"  @click="" v-if="receive_record.pic != '' && receive_record.is_edited == 0" @click="zoom_rec(receive_record.id)"></i> 
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.description}}</label>
                                </div>
                                <input name="description"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="description" maxlength="512">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.quantity}}</label>
                                </div>
                                <input name="quantity"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="quantity" maxlength="128">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{receive_record.supplier.replace(/\\/g, '') }}</label>
                                </div>
                                <input name="supplier"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="supplier" maxlength="256">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{(receive_record.kilo == 0) ? "" : receive_record.kilo}}</label>
                                </div>
                                <input name="kilo"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="kilo">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{(receive_record.cuft == 0) ? "" : receive_record.cuft}}</label>
                                </div>
                                <input name="cuft"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="cuft">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{ (receive_record.taiwan_pay == 1) ? "是 (yes)" : "否 (no)" }} </label>
                                </div>
                                <select name="taiwan_pay" v-show="receive_record.is_edited == 0"
                                        :id='"taiwan_pay"+receive_record.id'>
                                    <option value="1" :selected="taiwan_pay == 1 ? 'selected' : ''">是 (yes)</option>
                                    <option value="0" :selected="taiwan_pay == 0 ? 'selected' : ''">否 (no)</option>
                                </select>
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <label> {{(receive_record.courier_money == 0) ? "" : receive_record.courier_money
                                        }}</label>
                                </div>
                                <input name="courier_money"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="courier_money">
                            </li>
                            <li>
                                <div v-show="receive_record.is_edited == 1">
                                    <p v-html="receive_record.remark.replace(/(?:\r\n|\r|\n)/g, '&nbsp')"></p>
                                </div>
                                <input name="e_remark"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="e_remark" maxlength="512">
                            </li>

                            <li>
                                <?php
                          if($taiwan_read == 0)  
                          {
                        ?>
                                <button v-show="receive_record.is_edited == 1" @click="editRow(receive_record)">修改
                                </button>
                                <button v-show="receive_record.is_edited == 1 && receive_record.pic == ''" @click="get_photo_library(receive_record)">圖片庫
                                </button>
                                <button v-show="receive_record.is_edited == 0" @click="confirmRow(receive_record)">確認
                                </button>
                                <button v-show="receive_record.is_edited == 0" @click="cancelRow(receive_record)">取消
                                </button>
                                <?php
                          }
                        ?>
                            </li>
                        </ul>
                    </div>

                </div>

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

            </div>
        </div>

        
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
        
        <!-- Photo Modal Begin-->
        <div class="modal" id="photoModal1">

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
                        <button type="button" class="btn btn-warning" data-dismiss="modal" @click="delete_library1()">刪除
                            Delete
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="choose_library1()">
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
        
    </div>
</div>
<!-- The Modal -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">編輯PROJECT</h5>
            </div>
            <div class="modal-body">
                content
            </div>
            <div class="modal-footer">
                <a class="btn" data-dismiss="modal">取消</a>
                <a class="btn">確認</a>
            </div>
        </div>
    </div>
</div>
<!-- The Modal -->



<!-- Bootstrap  -->
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/jquery/1.12.4/jquery-1.12.4.js"></script>
<script src="js/jquery/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="js/datatables/datatables.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>
<script src="js/jquery/validate/jquery.validate.js"></script>
<script type="text/javascript" src="js/loading_query.js" defer></script>
<script defer src="js/a076d05399.js"></script>
</body>
</html>
