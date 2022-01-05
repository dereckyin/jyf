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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

<!-- CSS -->
<link rel="stylesheet" href="js/bootstrap/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

<!-- jQuery和js載入 --> 
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script> 
<script type="text/javascript" src="js/rm/realmediaScript.js"></script> 
<script>
$(function(){
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
      <h6>客戶通訊錄
        <eng>Directory</eng>
      </h6>
      <!-- add form -->
      <div class="block" v-if="!isEditing">
        <div class="tablebox d01">
          <ul>
            <li>麥頭
              <eng>Shipping Mark</eng>
            </li>
            <li>
              <input type="text" name="shipping_mark" v-model.lazy="shipping_mark" maxlength="128">
              <span class="text-danger" v-if="error_shipping_mark" v-text="error_shipping_mark"></span>
            </li>
          </ul>
        </div>
        <div class="tablebox d01">
          <ul>
            <li>收件人
              <eng>Company/Customer</eng>
            </li>
            <li>
              <input type="text" name="customer" v-model.lazy="customer" maxlength="256">
              <span class="text-danger" v-if="error_customer">{{error_customer}}</span>
          </ul>
          <ul>
            <li>電話
              <eng>Phone</eng>
            </li>
            <li>
              <input type="text" name="c_phone" v-model.lazy="c_phone" maxlength="80">
              <span class="text-danger" v-if="error_c_phone" v-text="error_c_phone"></span>
            </li>
          </ul>
          <ul>
            <li>傳真
              <eng>Fax</eng>
            </li>
            <li>
              <input type="text" name="c_fax" v-model.lazy="c_fax" maxlength="80">
              <span class="text-danger" v-if="error_c_fax" v-text="error_c_fax"></span>
            </li>
          </ul>
          <ul>
            <li>E-mail</li>
            <li>
              <input type="text" name="c_email" v-model.lazy="c_email" maxlength="256">
              <span class="text-danger" v-if="error_c_email" v-text="error_c_email"></span>
            </li>
          </ul>
        </div>
        <div class="tablebox d01">
          <ul>
            <li>寄件人
              <eng>Supplier</eng>
            </li>
            <li>
              <input type="text" name="supplier" v-model.lazy="supplier" maxlength="256">
              <span class="text-danger" v-if="error_supplier" v-text="error_supplier">{{error_supplier}}</span>
          </ul>
          <ul>
            <li>電話
              <eng>Phone</eng>
            </li>
            <li>
              <input type="text" name="s_phone" v-model.lazy="s_phone" maxlength="80">
              <span class="text-danger" v-if="error_s_phone" v-text="error_s_phone"></span>
            </li>
          </ul>
          <ul>
            <li>傳真
              <eng>Fax</eng>
            </li>
            <li>
              <input type="text" name="s_fax" v-model.lazy="s_fax" maxlength="80">
              <span class="text-danger" v-if="error_s_fax" v-text="error_s_fax"></span>
            </li>
          </ul>
          <ul>
            <li>E-mail</li>
            <li>
              <input type="text" name="s_email" v-model.lazy="s_email" maxlength="256">
              <span class="text-danger" v-if="error_s_email" v-text="error_s_email"></span>
            </li>
          </ul>
          <ul>
            <li>抬頭<eng>Company Title</eng></li>
            <li>
              <input type="text" name="company_title" v-model.lazy="company_title" maxlength="128">
              <span class="text-danger" v-if="error_company_title" v-text="error_company_title"></span>
            </li>
          </ul>
          <ul>
            <li>統編<eng>VAT Number</eng></li>
            <li>
              <input type="text" name="vat_number" v-model.lazy="vat_number" maxlength="40">
              <span class="text-danger" v-if="error_vat_number" v-text="error_vat_number"></span>
            </li>
          </ul>
          <ul>
            <li>地址<eng>Address</eng></li>
            <li>
              <input type="text" name="address" v-model.lazy="address" maxlength="256">
              <span class="text-danger" v-if="error_address" v-text="error_address"></span>
            </li>
          </ul>
        </div>
        <div class="btnbox">
        <?php
if($taiwan_read == "0")
{
        ?>
        <a class="btn" @click="createReceiveRecord()">儲存
          <eng>Save</eng>
          </a>
<?php
}
?>
        </div>
      </div>
      <!-- eidt form -->
      <div class="block" v-else>
        <div class="tablebox d01">
          <ul>
            <li>麥頭
              <eng>Shipping Mark</eng>
            </li>
            <li>
              <input type="text" name="shipping_mark" v-model.lazy="record.shipping_mark" maxlength="128">
              <span class="text-danger" v-if="error_shipping_mark" v-text="error_shipping_mark"></span>
            </li>
          </ul>
        </div>
        <div class="tablebox d01">
          <ul>
            <li>收件人
              <eng>Company/Customer</eng>
            </li>
            <li>
              <input type="text" name="customer" v-model.lazy="record.customer" maxlength="256">
              <span class="text-danger" v-if="error_customer" v-text="error_customer">{{error_customer}}</span>
          </ul>
          <ul>
            <li>電話
              <eng>Phone</eng>
            </li>
            <li>
              <input type="text" name="c_phone" v-model.lazy="record.c_phone" maxlength="80">
              <span class="text-danger" v-if="error_c_phone" v-text="error_c_phone"></span>
            </li>
          </ul>
          <ul>
            <li>傳真
              <eng>Fax</eng>
            </li>
            <li>
              <input type="text" name="c_fax" v-model.lazy="record.c_fax" maxlength="80">
              <span class="text-danger" v-if="error_c_fax" v-text="error_c_fax"></span>
            </li>
          </ul>
          <ul>
            <li>E-mail</li>
            <li>
              <input type="text" name="c_email" v-model.lazy="record.c_email" maxlength="256">
              <span class="text-danger" v-if="error_c_email" v-text="error_c_email"></span>
            </li>
          </ul>
        </div>
        <div class="tablebox d01">
          <ul>
            <li>寄件人
              <eng>Supplier</eng>
            </li>
            <li>
              <input type="text" name="supplier" v-model.lazy="record.supplier" maxlength="256">
              <span class="text-danger" v-if="error_supplier" v-text="error_supplier">{{error_supplier}}</span>
          </ul>
          <ul>
            <li>電話
              <eng>Phone</eng>
            </li>
            <li>
              <input type="text" name="s_phone" v-model.lazy="record.s_phone" maxlength="80">
              <span class="text-danger" v-if="error_s_phone" v-text="error_s_phone"></span>
            </li>
          </ul>
          <ul>
            <li>傳真
              <eng>Fax</eng>
            </li>
            <li>
              <input type="text" name="s_fax" v-model.lazy="record.s_fax" maxlength="80">
              <span class="text-danger" v-if="error_s_fax" v-text="error_s_fax"></span>
            </li>
          </ul>
          <ul>
            <li>E-mail</li>
            <li>
              <input type="text" name="s_email" v-model.lazy="record.s_email" maxlength="256">
              <span class="text-danger" v-if="error_s_email" v-text="error_s_email"></span>
            </li>
          </ul>
          <ul>
            <li>抬頭<eng>Company</eng></li>

            <li>
              <input type="text" name="company_title" v-model.lazy="record.company_title" maxlength="128">
              <span class="text-danger" v-if="error_company_title" v-text="error_company_title"></span>
            </li>
          </ul>
          <ul>
            <li>統編<eng>VAT Number</eng></li>
            <li>
              <input type="text" name="vat_number" v-model.lazy="record.vat_number" maxlength="40">
              <span class="text-danger" v-if="error_vat_number" v-text="error_vat_number"></span>
            </li>
          </ul>
          <ul>
            <li>地址<eng>Address</eng></li>
            <li>
              <input type="text" name="address" v-model.lazy="record.address" maxlength="256">
              <span class="text-danger" v-if="error_address" v-text="error_address"></span>
            </li>
          </ul>
        </div>
        <div class="btnbox"><a class="btn" @click="cancelReceiveRecord($event)" style="color:white;">取消
          <eng>Cancel</eng>
          </a><a class="btn" @click="editReceiveRecord($event)" style="color:white;">儲存
          <eng>Save</eng>
          </a></div>
      </div>
      <div class="block record show">
        <h6>通訊錄
          <eng>Directory</eng>
        </h6>
        <!-- list -->
        <div class="mainlist">
          <div class="listheader">
            <div class="pageblock" style="float:right;"> Page Size:
              <select v-model="perPage">
                <option v-for="item in inventory" :value="item" :key="item.id"> {{ item.name }} </option>
              </select>
              Page:
              <div class="pageblock"> <a class="first micons" @click="page=1">first_page</a> <a class="prev micons" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">chevron_left</a>
                <select v-model="page">
                  <option v-for="pg in pages" :value="pg"> {{ pg }} </option>
                </select>
                <a class="next micons" :disabled="page == pages.length" @click="page++">chevron_right</a> <a class="last micons" @click="page=pages.length">last_page</a> </div>
            </div>
            <div class="searchblock" style="float:left;">搜尋<input type="text" v-model="keyword"></div>
          </div>
          <div class="tablebox d02">
            <ul class="header">
              <li>勾選
                <eng>Check</eng>
              </li>
              <li>麥頭
                <eng>Shipping Mark</eng>
              </li>
              <li>收件人
                <eng>Company/Customer</eng>
              </li>
              <li>電話
                <eng>Phone</eng>
              </li>
              <li>E-Mail</li>
              <li>寄件人
                <eng>Supplier</eng>
              </li>
              <li>電話
                <eng>Phone</eng>
              </li>
              <li>E-Mail</li>
              <li>抬頭
                <eng>Title</eng>
              </li>
              <li>統編
                <eng>VAT Number</eng>
              </li>
              <li>地址
                <eng>Address</eng>
              </li>
            </ul>
            <ul v-for='(contactor, index) in displayedPosts'>
              <li>
                <input type="checkbox" name="record_id" class="alone" :value="contactor.index" :true-value="1" v-model:checked="contactor.is_checked">
              </li>
              <li>{{ contactor.shipping_mark }}</li>
              <li>{{ contactor.customer }}</li>
              <li>{{ contactor.c_phone }}</li>
              <li>{{ contactor.c_email }}</li>
              <li>{{ contactor.supplier }}</li>
              <li>{{ contactor.s_phone }}</li>
              <li>{{ contactor.s_email }}</li>
              <li>{{ contactor.company_title }}</li>
              <li>{{ contactor.vat_number }}</li>
              <li>{{ contactor.address }}</li>
            </ul>
          </div>
        </div>
        <div class="btnbox"> <a class="btn small selbtn" style="color:white;" @click="toggleCheckbox();">全選 / 全取消
          <p>All/Undo</p>
          </a> 
<?php
if($taiwan_read == "0")
{
?>
          <a class="btn small" style="color:white;" @click="editRecord()">修改
          <p>Edit</p>
          </a> <a class="btn small" style="color:white;" @click="deleteRecord()">刪除
          <p>Delete</p>
          </a> 
<?php
}
?>
          <a class="btn small" style="color:white;" v-bind:href="pageUrl">匯出
          <p>Export</p>
          </a> </div>
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
      <div class="modal-body"> content </div>
      <div class="modal-footer"> <a class="btn" data-dismiss="modal">取消</a> <a class="btn">確認</a> </div>
    </div>
  </div>
</div>
<!-- The Modal --> 
<!-- Bootstrap  --> 
<script src="js/bootstrap/popper.min.js"></script> 
<script src="js/bootstrap/bootstrap.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="js/axios.min.js"></script> 
<script type="text/javascript" src="js/contactor.js" defer></script>
</body>
</html>
