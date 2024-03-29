<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-select.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
 


    <style type="text/css">
        hr {
            border: none;
            height: 1px;
            size: 1;

            /* Set the hr color */
            color: #333; /* old IE */
            background-color: #333; /* Modern Browsers */
        }

        select {
            background-image: url(../images/ui/icon_form_select_arrow.svg);
        }

        .mainContent > h6 {
            letter-spacing: 0;
        }

        .mainContent > h6 > cht {
            font-size: 24px;
            margin-left: 8px;
            letter-spacing: 5px;
            opacity: 0.5;
        }

        .btnbox a.btn {
            letter-spacing: 0;
        }

        .btnbox a.btn cht {
            font-size: 12px;
            letter-spacing: 3px;
            margin-left: 4px;
        }

        div.tablebox > .header cht {
            display: block;
            font-weight: 400;
        }

        div.tablebox > ul > li > cht {
            font-size: 12px;
        }

        .tb_measure {
            width: 100%;
        }

        .tb_measure thead tr th, .tb_measure tbody tr td {
            font-size: 16px;
            padding: 12px 8px;
            text-align: center;
            color: #333;
            vertical-align: middle;
            min-width: 50px;
            width: auto;
            font-weight: 500;
        }

        .tb_measure thead tr th {
            background-color: #bbb;
        }

        .tb_measure tbody tr td {
            font-weight: 300;
        }

        .tb_measure thead tr th cht {
            display: block;
            font-size: 12px;
        }

        .tb_measure {
            border: 0.5px solid #999;
            border-bottom: none;
        }

        .tb_measure tbody tr td {
            border: 0.5px solid #999;
        }

        .tb_measure tbody td input[type="number"] {
            width: 110px;
            text-align: center;
        }

        .tb_measure tbody td select {
            width: 100%;
        }

        .ph_client {
            background-color: #EFEFEF;
        }

        input.hasDatepicker {
            width: calc(100% - 30px) !important;
            margin-right: 5px;
        }

        button.btn.dropdown-toggle {
            background-color: white;
            border: 1px solid #999;
            border-radius: 5px;
        }

        ul.dropdown-menu.inner li {
            display: block;
        }

        .ph_client .dropdown-menu.show{
            max-width: 60vw!important;
            transform: none!important;
        }

    </style>


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


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
    <div class="mainContent" id="measure">

        <h6>
            Measurement
            <cht>丈量</cht>
        </h6>

        <div class="block">
            <div class="btnbox">
                <a class="btn small" @click="create_measurement">Create Measurement Record
                    <cht>新增丈量記錄</cht>
                </a>
                <a class="btn small" href="edit_measurement.php">Edit Measurement Record
                    <cht>修改丈量記錄</cht>
                </a>
            </div>
        </div>
        <div class="block record show" v-show="show_detail">
            <h6>
                On the Way Container Records
                <cht>未領貨櫃記錄</cht>
            </h6>
            <!-- list -->
            <div class="mainlist">

                <div class="listheader">
                    <div class="pageblock" style="float:right;"> Page Size:
                        <select v-model="perPage">
                            <option v-for="item in inventory" :value="item" :key="item.id">
                                {{ item.name }}
                            </option>
                        </select> Page:
                        <div class="pageblock">
                            <a class="first micons" @click="page=1">first_page</a>
                            <a class="prev micons" :disabled="page == 1"
                               @click="page < 1 ? page = 1 : page--">chevron_left</a>
                            <select v-model="page">
                                <option v-for="pg in pages" :value="pg">
                                    {{ pg }}
                                </option>
                            </select>

                            <a class="next micons" :disabled="page == pages.length"
                               @click="page++">chevron_right</a>
                            <a class="last micons" @click="page=pages.length">last_page</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox d02">
                    <ul class="header">
                        <li>
                            <cht>勾選</cht>
                            Check
                        </li>
                        <li>
                            <cht>櫃號</cht>
                            Container Number
                        </li>
                        <li>S/O</li>
                        <li>
                            <cht>船公司</cht>
                            Shipping Line Company
                        </li>
                        <li>
                            <cht>結關日期</cht>
                            Date Send
                        </li>
                        <li>ETA</li>
                    </ul>
                    <ul v-for='(record, index) in displayedLoading'>
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.id" :true-value="1"
                                   v-model:checked="record.is_checked"></li>
                        <li>{{ record.container_number }}</li>
                        <li>{{ record.so }}</li>
                        <li>{{ record.ship_company }}</li>
                        <li>{{ record.date_sent }}</li>
                        <li>{{ record.etd_date }}</li>
                    </ul>

                </div>

            </div>
            <div class="btnbox">
                <a class="btn small" @click="exportReceiveRecords()" v-if="show_record == false">Export to Excel, Pdf, Print
                    <cht>貨物明細匯出</cht>

                </a>
                <a class="btn small" @click="showReceiveRecords()" v-if="show_record == false">Create Measurement Record
                    <cht>新增丈量記錄</cht>
                </a>

                <a class="btn small" @click="cancelRecord()" v-if="show_record == true">Cancel
                    <cht>取消</cht>
                </a>
            </div>
        </div>


        <div class="block" v-show="show_record">
            <div class="tablebox d01">
                <ul>
                    <li>
                        Qty of Containers
                        <cht>貨櫃數量</cht>
                    </li>
                    <li><input type="text" name="measure_qty" disabled v-model="measure_qty"></li>
                    <li>
                        Container Number
                        <cht>櫃號</cht>
                    </li>
                    <li><input type="text" name="measure_container" disabled v-model="measure_container"></li>
                </ul>

                <ul>
                    <li>
                        Date Encoded
                        <cht>丈量日期</cht>
                    </li>
                    <li>
                        <date-encode id="date_encode" @update-date="update_date_encode" v-model="date_encode"
                                     style="width: calc(40% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-encode>
                        
                    </li>
                    <li>
                        Date C/R (Date Container arrived Manila)
                        <cht>貨櫃到倉日期</cht>
                    </li>
                    <li>
                        <date-cr id="date_cr" @update-date="update_date_cr" v-model="date_cr"
                                 style="width: calc(40% - 40px); border: 1px solid #999; border-radius: 5px; background-color: #fff; padding: 5px;"></date-cr>
                        
                    </li>
                </ul>

                <ul>
                    <li>
                        Currency Rate
                        <cht>匯率</cht>
                    </li>
                    <li>
                        <input type="text" name="currency_rate" v-model="currency_rate">
                    </li>
                    <li>
                        Remark
                        <cht>備註</cht>
                    </li>
                    <li><input type="text" name="remark" v-model="remark"></li>
                </ul>
            </div>
        </div>


        <div class="block record show" v-show="show_record">
            <h6>Container Content
                <cht>貨物內容</cht>
            </h6>

            <div class="mainlist" style="overflow-x: auto;">
                <table class="tb_measure">
                    <thead>
                    <tr>
                        <th>
                            <cht>勾選</cht>
                            Check
                        </th>
                        <th>
                            <cht>收貨日期</cht>
                            Date Receive

                        </th>
                        <th>
                            <cht>收件人</cht>
                            Company/Customer
                        </th>
                        <th>
                            <cht>收件人(菲)</cht>
                            Company/Customer(PH)
                        </th>
                        <th>
                            <cht>貨品名稱</cht>
                            Description

                        </th>
                        <th>
                            <cht>件數</cht>
                            Qty
                        </th>
                        <th>
                            <cht>重量</cht>
                            Kilo
                        </th>
                        <th>
                            <cht>才積</cht>
                            Cuft
                        </th>
                        <th>
                            <cht>重量單價</cht>
                            Price per Kilo
                        </th>
                        <th>
                            <cht>才積單價</cht>
                            Price per Cuft
                        </th>
                        <th>
                            <cht>收費金額</cht>
                            Amount
                        </th>
                        <th>
                            <cht>寄貨人</cht>
                            Supplier
                        </th>
                        <th>
                            <cht>備註</cht>
                            Remark
                        </th>
                    </tr>
                    </thead>




                    <tbody>
                        <template v-for='(row, i) in receive_records'>
                            <tr v-for='(item, j) in row.record'>
                                
                                <td v-if="j == 0" :rowspan="row.record.length">
                                  <input type="checkbox" name="record_id" true-value="1" class="alone" value="" v-model="row.is_checked">
                                </td>
                                <td>
                                    {{ item.date_receive }}
                                </td>
                                <td>
                                    {{ item.customer }}
                                </td>
                                <td class="ph_client">
                                    <select class="selectpicker" data-live-search="true" :id="'client_' + item.id" v-model="item.cust">
                                        <option value=""></option>
                                        <option v-for='(con, index) in contactor' :value="con.id">{{ con.customer }}</option>
                                    </select>
                                </td>
                                <td>
                                    {{ item.description }}
                                </td>
                                <td>
                                    {{ item.quantity }}
                                </td>
                                <td v-if="j == 0" :rowspan="row.record.length">
                                    <input type="number" min="0" v-model="row.kilo" @change=change_A(row)>
                                </td>
                                <td v-if="j == 0" :rowspan="row.record.length">
                                    <input type="number" min="0" v-model="row.cuft" @change=change_B(row)>
                                </td>
                                <td v-if="j == 0" :rowspan="row.record.length">
                                    <input type="number" min="0" v-model="row.kilo_price" @change=change_C(row)>
                                </td>
                                <td v-if="j == 0" :rowspan="row.record.length">
                                    <input type="number" min="0" v-model="row.cuft_price" @change=change_D(row)>
                                </td>
                                <td v-if="j == 0" :rowspan="row.record.length">
                                    <input type="number" min="0" v-model="row.charge">
                                </td>
                                <td>
                                {{ item.supplier }}
                                </td>
                                <td>
                                {{ item.remark }}
                                </td>
                            </tr>
                        </template>


                    </tbody>
                </table>

                <div class="btnbox" style="border: none; margin-top: 10px;">
                    <a class="btn small" @click="merge_item()">
                        Merge Items
                        <cht>合併項目</cht>
                    </a>
                    <a class="btn small" @click="decompose_item()">
                        Decompose Item
                        <cht>拆分項目</cht>
                    </a>
                    <a class="btn small" @click="save_measure()">
                        Save
                        <cht>儲存</cht>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Bootstrap  -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/vue.js"></script>
<script type="text/javascript" src="js/create_measurement.js" defer></script>
<script src="js/npm/sweetalert2@9.js"></script>

</body>
</html>
