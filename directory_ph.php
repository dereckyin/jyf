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

            if(!$decoded->data->phili)
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
    </style>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script>
        $(function () {
            $('header').load('include/header_admin.php');
        })
    </script>
</head>

<body>
<div id="contactor">
    <div class="bodybox">
        <!-- header -->
        <header></header>
        <!-- header end -->
        <div class="mainContent">
            <h6>Directory
                <cht>客戶通訊錄</cht>
            </h6>
            <!-- add form -->
            <div class="block" v-if="!isEditing">
                <div class="tablebox d01">
                    <ul>
                        <li>Company Name
                            <cht>公司名</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="company" maxlength="256">
                            <span class="text-danger" v-if="company_err" text="Please input company name"></span></li>
                    </ul>
                    <ul>
                        <li>Customer's Name
                            <cht>客戶名</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="customer" maxlength="256">
                            <span class="text-danger" v-if="customer_err" text="Please input customer name"></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Address
                            <cht>地址</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="address" maxlength="256">
                            <span class="text-danger" v-if="address_err" text="Please input address"></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Landline Number
                            <cht>市話號碼</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="phone" maxlength="256">
                            <span class="text-danger" v-if="phone_err" text="Please input phone"></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Fax Number
                            <cht>傳真號碼</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="fax" maxlength="256">
                            <span class="text-danger" v-if="fax_err" text="Please input fax"></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Mobile Number
                            <cht>手機號碼</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="mobile" maxlength="256">
                            <span class="text-danger" v-if="mobile_err" text="Please input mobile"></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>E-mail</li>
                        <li>
                            <input type="text" name="" v-model.lazy="email" maxlength="256">
                            <span class="text-danger" v-if="email_err" text="Please input email"></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Remarks
                            <cht>備註</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="remark" maxlength="256">
                            <span class="text-danger" v-if="remark_err" text="Please input emark"></span></li>
                        </li>
                    </ul>
                </div>

                <div class="btnbox">
                    <a class="btn" @click="createReceiveRecord()">Save <cht>儲存</cht></a>
                </div>
            </div>

            <!-- eidt form -->
            <div class="block" v-else>
                <div class="tablebox d01">
                    <ul>
                        <li>Company Name
                            <cht>公司名</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.company" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                    </ul>
                    <ul>
                        <li>Customer's Name
                            <cht>客戶名</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.customer" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Address
                            <cht>地址</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.address" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Landline Number
                            <cht>市話號碼</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.phone" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Fax Number
                            <cht>傳真號碼</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.fax" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Mobile Number
                            <cht>手機號碼</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.mobile" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>E-mail</li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.email" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                        </li>
                    </ul>
                    <ul>
                        <li>Remarks
                            <cht>備註</cht>
                        </li>
                        <li>
                            <input type="text" name="" v-model.lazy="record.remark" maxlength="256">
                            <span class="text-danger" v-if="" v-text=""></span></li>
                        </li>
                    </ul>
                </div>

                <div class="btnbox">
                    <a class="btn" @click="cancelReceiveRecord($event)" style="color:white;">Cancel <cht>取消</cht></a>
                    <a class="btn" @click="editReceiveRecord($event)" style="color:white;">Save <cht>儲存</cht></a>
                </div>
            </div>

            <div class="block record show">
                <h6>Directory
                    <cht>通訊錄</cht>
                </h6>
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
                        <div class="searchblock" style="float:left;">Search <input type="text" v-model="keyword"></div>
                    </div>
                    <div class="tablebox d02">
                        <ul class="header">
                            <li><cht>勾選</cht>
                                Check
                            </li>
                            <li><cht>公司名</cht>
                                Company Name
                            </li>
                            <li><cht>客戶名</cht>
                                Customer's Name
                            </li>
                            <li><cht>地址</cht>
                                Address
                            </li>
                            <li><cht>市話號碼</cht>
                                Landline Number
                            </li>
                            <li><cht>傳真號碼</cht>
                                Fax Number
                            </li>
                            <li><cht>手機號碼</cht>
                                Mobile Number
                            </li>
                            <li>E-Mail</li>
                            <li><cht>備註</cht>
                                Remarks
                            </li>
                        </ul>
                        <ul v-for='(contactor, index) in displayedPosts'>
                            <li>
                                <input type="checkbox" name="record_id" class="alone" :value="contactor.index"
                                       :true-value="1" v-model:checked="contactor.is_checked">
                            </li>
                            <li>{{ contactor.company }}</li>
                            <li>{{ contactor.customer }}</li>
                            <li>{{ contactor.address }}</li>
                            <li>{{ contactor.phone }}</li>
                            <li>{{ contactor.fax }}</li>
                            <li>{{ contactor.mobile }}</li>
                            <li>{{ contactor.remark }}</li>
                      
                        </ul>
                    </div>
                </div>
                <div class="btnbox">
                    <a class="btn small selbtn" style="color:white;" @click="toggleCheckbox();">Select All/Undo <cht>全選/全取消</cht></a>
                    <a class="btn small" style="color:white;" @click="editRecord()">Edit <cht>修改</cht></a>
                    <a class="btn small" style="color:white;" @click="deleteRecord()">Delete <cht>刪除</cht></a>
                    <a class="btn small" style="color:white;" v-bind:href="pageUrl">Export to Excel <cht>匯出</cht></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap  -->
<script src="js/bootstrap/popper.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script type="text/javascript" src="js/contactor_ph.js" defer></script>
</body>
</html>
