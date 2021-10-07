<?php include 'check.php';?>
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

    <style>
        img.ui-datepicker-trigger {
            padding-left: 10px;
            margin: -8px;
        }

        p {
            margin: 0;
            padding: 0;
        }

        div.tablebox.d02 > ul > li {
            font-size: 14px;
        }

        div.tablebox.d02 > ul.sent > li {
            color: green;
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
                    <a class="btn small" href="loading.php">新增貨櫃記錄
                        <eng>New Container Record</eng>
                    </a>
                    <a class="btn small" href="loading_edit.php">修改貨櫃記錄
                        <eng>Edit Container Record</eng>
                    </a>
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
                            <li>E-Mail 寄送狀態
                                <eng>Status of Sending E-Mail</eng>
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
                            <li>{{ record.date_arrive }}</li>
                            <li>{{ record.mail_cnt }} / {{ record.cnt }}</li>
                        </ul>
                    </div>
                </div>

                <div class="btnbox">
                    <a class="btn small" @click="editRecord()">查看
                        <eng>Show</eng>
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
                <div class="tablebox lo01">
                    <ul><!-- 配色底用 --></ul>
                    <ul>
                        <li>備註
                            <eng>Remark</eng>
                        </li>
                        <li><input type="text" name="remark" v-model="record.remark"></li>
                    </ul>
                </div>
            </div>
            <div class="block record show">
                <h6>貨櫃內貨物
                    <eng>Goods in the Container</eng>
                </h6>
                <!-- list -->
                <div class="mainlist">

                    <div class="tablebox d02">
                        <ul class="header">
                            <li>勾選
                                <eng>Check</eng>
                            </li>
                            <li>收件日期
                                <eng>Date Receive</eng>
                            </li>
                            <li>收件人
                                <eng>Company/Customer</eng>
                            </li>
                            <li>E-Mail
                            </li>
                            <li>照片
                                <eng>Picture</eng>
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
                            <li>補充說明
                                <eng>Notes</eng>
                            </li>
                            <li>功能</li>
                        </ul>
                        <ul v-for='(receive_record, index) in displayedPosts' :key="index" :class="[receive_record.mail_cnt > 0 ? 'sent' : '']">
                            <li>
                                <input type="checkbox" name="record_id" class="alone" @change="updateWeightAndCult"
                                       :value="receive_record.index" :true-value="1"
                                       v-model:checked="receive_record.is_checked">
                            </li>
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
                                    <label> {{receive_record.email}}</label>
                                </div>
                                <input name="email"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="email" maxlength="256">
                            </li>
                            <li>
                                <i class="fas fa-image" v-if="receive_record.pic.length > 0" @click="zoom(receive_record.id)"></i>
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
                                    <label> {{receive_record.mail_note}}</label>
                                </div>
                                <input name="mail_note"
                                       v-show="receive_record.is_edited == 0"
                                       v-model="mail_note" maxlength="512">
                            </li>
                            <li>
                                <button v-show="receive_record.is_edited == 1" @click="editRow(receive_record)">修改
                                </button>
                                <button v-show="receive_record.is_edited == 0" @click="confirmRow(receive_record)">確認
                                </button>
                                <button v-show="receive_record.is_edited == 0" @click="cancelRow(receive_record)">取消
                                </button>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="btnbox">
                    <a class="btn small" v-if="isEditing == true" @click="toggleCheckbox();">全選 / 全取消
                        <eng>All/Undo</eng>
                    </a>
                    <a class="btn small" v-if="isEditing == true" @click="mail_client()">E-Mail寄送
                        <eng>Send E-Mail</eng>
                    </a>
                </div>

            </div>
        </div>

        <div class="modal" id="emailModal">
            <div max-width="85vw" style="text-align: center;">

                第 <span style="color: red">{{ mail_send }}</span> 個收貨記錄正在寄送E-Mail ( 總共要處理 {{ mail_to_send }} 個收貨記錄)

                <div style="font-weight: 700; margin-top: 5px;" v-if="send_all">
                    全部處理完畢
                </div>

            </div>

            <div max-width="85vw" style="border: 1px solid #C0C0C0; margin-top: 10px; padding: 10px 10px 0;">
                <h6 style="padding-bottom: 10px;">錯誤訊息</h6>

                <template v-for="(item, index) in msg">
                    第 {{item.i}} 個收貨記錄 (收件人: {{ item.customer }}, 貨品名稱: {{ item.desc }})<br>
                    原因: {{ item.msg }}
                    <hr>
                </template>
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
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script type="text/javascript" src="js/send_email.js" defer></script>
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
