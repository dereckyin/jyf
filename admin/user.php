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
        .table th,td {
                text-align: center;
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


<div class="container " style="margin-left: -0.5vw;width: 100vw; padding-left: 1vw">

    <div class="row" style="background-color: rgb(34,34,34)">

        <a class="btn easy-sidebar-toggle navbar-brand" ><span style="color: white;">&#9776;</span></a>

    </div>



    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" >

        <div class="panel panel-default"  id='mainContent'>

            <div class="panel-heading" role="tab" id="headingOne" style="background-color:lightskyblue;">

                <h4 class="panel-title">

                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="font-size: 25px; text-decoration: none; color:white; font-weight: bold">使用者管理</a>

                </h4>
            </div>
			
			<div class="container-fluid" style="border:1px solid black;">
			  <div class="block" v-if="!isEditing">
				<div>
				  <div class="needs-validation form-horizontal" novalidate>
					<div class="form-inline" style="margin-bottom: 10px;">
					  <div class="col-md-3 text-right"> 使用者名稱&nbsp;
						User Name </div>
					  <div class="col-md-9">
						<input type="text" class="form-control" v-model="username" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''">
						
					  </div>
					  <span class="text-danger" v-if="error_username" v-text="error_username"></span>
					</div>
					<div class="form-inline" style="margin-bottom: 10px;">
					  <div class="col-md-3 text-right"> Email </div>
					  <div class="col-md-9">
						<input type="text" class="form-control" placeholder="" v-model="email" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="128" size="24">
					
					  </div>
					  <span class="text-danger" v-if="error_email" v-text="error_email"></span>
				  </div>
				  <div>
					  <div class="col-md-3 text-right"> password </div>
					  <div class="col-md-3">
						<input type="password" class="form-control" placeholder="" v-model="password" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="128" size="24">
					
					  </div>
					  <div class="col-md-3 text-right"> comfirm password </div>
					  <div class="col-md-3">
						<input type="password" class="form-control" placeholder="" v-model="password1" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="128" size="24">
						
					  </div>

					  <span class="text-danger" v-if="error_password" v-text="error_password"></span>
					</div>
				
					
					
					<div class="form-inline" style="margin-bottom: 10px;">
					  <div class="col-md-3"></div>
					  <div class="col-md-3">
						<input class="form-check-input" type="checkbox" name="status" required id="A1" :true-value="1" v-model:checked="status" @change="updateStatus">
						啟用 </div>
					  <div class="col-md-3 text-right">
						<input class="form-check-input" type="checkbox" name="is_admin" required id="A1" :true-value="1" v-model:checked="is_admin" @change="updateIsAdmin">
						是否為管理者 </div>
					
					</div>
					
					<div class="form-inline form-check" style="margin-bottom: 10px;">
						<div class="col-md-12 text-center">
							<button type="button" class="btn btn-danger" @click="cancelReceiveRecord($event)">取消<p>Cancel</p></button>
							<button type="button" class="btn btn-primary" @click="createReceiveRecord()">儲存<p>SAVE</p></button>
						</div>
					</div>
				  </div>
				</div>
			  </div>


			  <div class="block" v-else>
				<div>
				  <div class="needs-validation form-horizontal" novalidate>
					<div class="form-inline" style="margin-bottom: 10px;">
					  <div class="col-md-3 text-right"> 使用者名稱&nbsp;
						User Name </div>
					  <div class="col-md-9">
						<input type="text" class="form-control" v-model="record.username"  required onfocus="this.placeholder = ''" onblur="this.placeholder = ''">
						
					  </div>
					  <span class="text-danger" v-if="error_username" v-text="error_username"></span>
					</div>
					<div class="form-inline" style="margin-bottom: 10px;">
					  <div class="col-md-3 text-right"> Email </div>
					  <div class="col-md-9">
						<input type="text" class="form-control" placeholder=""  required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="128" size="24" v-model="record.email" >
					
					  </div>
					  <span class="text-danger" v-if="error_email" v-text="error_email"></span>
				  </div>
					
					<div class="form-inline" style="margin-bottom: 10px;">
					  <div class="col-md-3"></div>
					  <div class="col-md-3">
						<input class="form-check-input" type="checkbox" id="B1" :true-value="1"  v-model:checked="record.status" @change="updateEditStatus" required>
						啟用 </div>
					  <div class="col-md-3 text-right">
						<input class="form-check-input" type="checkbox" id="B2" :true-value="1"  v-model:checked="record.is_admin" @change="updateEditIsAdmin" required>
						是否為管理者 </div>
					
					</div>
					
					<div class="form-inline form-check" style="margin-bottom: 10px;">
						<div class="col-md-12 text-center">
							<button type="button" class="btn btn-danger" @click="cancelReceiveRecord($event)">取消<p>Cancel</p></button>
							<button type="button" class="btn btn-primary" @click="editReceiveRecord($event)">儲存<p>SAVE</p></button>
						</div>
					</div>
				  </div>
				</div>
			  </div>



			</div>

            <div id="collapseOne" class="panel-collapse collapse show" role="tabpanel" aria-labelledby="headingOne">

                <div class="panel-body">

                    <table class="table table-striped table-hover table-sm ">

                        <thead class="thead-dark">

                            <tr>
                            	<th>Check 勾選</th>
                                <th>使用者名稱</th>
                                <th>email</th>
                                <th>是否啟用</th>
                                <th>是否為管理者</th>
                                <th>上次登入日期</th>
                            </tr>

                        </thead>

                         <tbody>

                            <tr v-for='(receive_record, index) in displayedPosts'>
                                <td><input type="checkbox" name="record_id" class="alone" :value="receive_record.index" :true-value="1" v-model:checked="receive_record.is_checked"></td>
                                <td> {{ receive_record.username }}</td>
                                <td> {{ receive_record.email }}</td>
								<td>{{ (receive_record.status == 1) ? "是 (yes)" : "否 (no)" }}</td>
								<td>{{ (receive_record.is_admin == '1') ? "是 (yes)" : "否 (no)" }}</td>
                                <td> {{ receive_record.login_time }}</td>
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
