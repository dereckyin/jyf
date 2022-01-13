<?php include 'check.php';?>
<!doctype html>
<html class="easy-sidebar-active">
<head>

    <meta charset="utf-8">
    <title>後台管理(使用者)</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/easy-sidebar.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">



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
                       style="font-size: 25px; text-decoration: none; color: white; font-weight: bold;">使用者管理</a>
                </h4>
            </div>

            <div class="container-fluid" style="border: 1px solid #ddd; margin-bottom: 50px; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px;">
                <div class="block" v-if="!isEditing">
                    <div>
                        <div class="needs-validation form-horizontal" novalidate>
                            <ul>
                                <li>
                                    使用者名稱&nbsp;User Name
                                </li>
                                <li>
                                    <input type="text" class="form-control" v-model="username" required
                                           onfocus="this.placeholder = ''" onblur="this.placeholder = ''">
                                </li>
                                <li>
                                    <span class="text-danger" v-if="error_username" v-text="error_username"></span>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    Email
                                </li>
                                <li>
                                    <input type="text" class="form-control" placeholder="" v-model="email" required
                                           onfocus="this.placeholder = ''" onblur="this.placeholder = ''"
                                           maxlength="128" size="24">
                                </li>
                                <li>
                                    <span class="text-danger" v-if="error_email" v-text="error_email"></span>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    Password
                                </li>
                                <li>
                                    <input type="password" class="form-control" placeholder="" v-model="password"
                                           required onfocus="this.placeholder = ''" onblur="this.placeholder = ''"
                                           maxlength="128" size="24">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    Confirm Password
                                </li>
                                <li>
                                    <input type="password" class="form-control" placeholder="" v-model="password1"
                                           required onfocus="this.placeholder = ''" onblur="this.placeholder = ''"
                                           maxlength="128" size="24">
                                </li>
                                <li>
                                    <span class="text-danger" v-if="error_password" v-text="error_password"></span>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" name="status" required id="A1"
                                           :true-value="1" v-model:checked="status" @change="updateStatus"> 啟用海運網站
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" name="phili" required id="A1"
                                           :true-value="1" v-model:checked="phili" @change="updatePhili"> 啟用海運菲律賓端業務
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" name="status" required id="A1"
                                           :true-value="1" v-model:checked="sea_expense" @change="updateSeaExpense"> 啟用海運支出記錄
                                </li>
                            </ul>
                            
                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" name="status" required id="A1"
                                           :true-value="1" v-model:checked="sea_expense_v2" @change="updateSeaExpense_v2"> 啟用海運支出記錄2
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" name="status_1" required id="A1"
                                           :true-value="1" v-model:checked="status_1" @change="updateStatus_1"> 啟用零件支出記錄
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" name="status_2" required id="A1"
                                           :true-value="1" v-model:checked="status_2" @change="updateStatus_2"> 啟用零件支出記錄2
                                </li>
                            </ul>
                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" name="taiwan_read" required id="A1"
                                           :true-value="1" v-model:checked="taiwan_read" @change="update_taiwan_read"> 啟用台灣唯讀
                                </li>
                            </ul>
                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" name="phili_read" required id="A1"
                                           :true-value="1" v-model:checked="phili_read" @change="update_phili_read"> 啟用菲律賓唯讀
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" name="is_admin" required id="A1"
                                           :true-value="1" v-model:checked="is_admin" @change="updateIsAdmin"> 是否為管理者
                                </li>
                            </ul>

                            <div class="brn-box">
                                <button type="button" class="btn btn-danger" @click="cancelReceiveRecord($event)">取消<br>Cancel
                                </button>
                                <button type="button" class="btn btn-primary" @click="createReceiveRecord()">儲存<br>SAVE
                                </button>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="block" v-else>
                    <div>
                        <div class="needs-validation form-horizontal" novalidate>
                            <ul>
                                <li>
                                    使用者名稱&nbsp;User Name
                                </li>
                                <li>
                                    <input type="text" class="form-control" v-model="record.username"  required onfocus="this.placeholder = ''" onblur="this.placeholder = ''">
                                </li>
                                <li>
                                    <span class="text-danger" v-if="error_username" v-text="error_username"></span>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    Email
                                </li>
                                <li>
                                    <input type="text" class="form-control" placeholder=""  required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="128" size="24" v-model="record.email" >
                                </li>
                                <li>
                                    <span class="text-danger" v-if="error_email" v-text="error_email"></span>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.status" @change="updateEditStatus" required> 啟用海運網站
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.phili" @change="updateEditPhili" required> 啟用海運菲律賓端業務
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.sea_expense" @change="updateEditSeaExpense" required> 啟用海運支出記錄
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.sea_expense_v2" @change="updateEditSeaExpense_v2" required> 啟用海運支出記錄2 
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.status_1" @change="updateEditStatus_1" required> 啟用零件支出記錄
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.status_2" @change="updateEditStatus_2" required> 啟用零件支出記錄2
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.taiwan_read" @change="update_edit_taiwan_read" required> 啟用台灣唯讀
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
								<input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.phili_read" @change="update_edit_phili_read" required> 啟用菲律賓唯讀
                                </li>
                            </ul>

                            <ul>
                                <li>
                                </li>
                                <li>
                                    <input class="form-check-input" type="checkbox" id="B2" :true-value="1"  v-model:checked="record.is_admin" @change="updateEditIsAdmin" required> 是否為管理者
                                </li>
                            </ul>

                            <div class="brn-box">
                                <button type="button" class="btn btn-danger" @click="cancelReceiveRecord($event)">取消<br>Cancel
                                </button>
                                <button type="button" class="btn btn-primary" @click="editReceiveRecord($event)">儲存<br>SAVE
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div id="collapseOne" role="tabpanel" aria-labelledby="headingOne" class="panel-collapse collapse show">
                <div class="panel-body">
                    <table class="table table-striped table-hover table-sm ">
                        <thead class="thead-dark">
                        <tr>
                            <th>Check 勾選</th>
                            <th>使用者名稱</th>
                            <th>email</th>
                            <th>啟用海運網站</th>
                            <th>啟用海運菲律賓端業務</th>
                            <th>啟用海運支出記錄</th>
                            <th>啟用海運支出記錄2</th>
                            <th>啟用零件支出記錄</th>
                            <th>啟用零件支出記錄2</th>
                            <th>啟用台灣唯讀</th>
                            <th>啟用菲律賓唯讀</th>
                            <th>是否為管理者</th>
                            <th>上次登入日期</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for='(rec, index) in receive_records'>
                            <td><input type="checkbox" name="record_id" class="alone" :value="rec.index" :true-value="1"
                                       v-model:checked="rec.is_checked"></td>
                            <td> {{ rec.username }}</td>
                            <td> {{ rec.email }}</td>
                            <td>{{ (rec.status == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.phili == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.sea_expense == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.sea_expense_v2 == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.status_1 == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.status_2 == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.taiwan_read == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.phili_read == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td>{{ (rec.is_admin == '1') ? "是 (yes)" : "否 (no)" }}</td>
                            <td> {{ rec.login_time }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="form-inline form-check" style="margin-bottom: 10px;">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-danger" @click="toggleCheckbox();">全選 / 全取消</button>
                            <button type="button" class="btn btn-primary" @click="editRecord()">修改</button>
                            <button type="button" class="btn btn-primary" @click="deleteRecord()">刪除</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="../js/axios.min.js"></script> 
<script type="text/javascript" src="js/user.js" defer></script> 











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
