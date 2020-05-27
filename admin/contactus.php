<?php include 'check.php';?>
<!doctype html>
<html class="easy-sidebar-active">
<head>

    <meta charset="utf-8">
    <title>後台管理(聯絡我們)</title>
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

                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="font-size: 25px; text-decoration: none; color:white; font-weight: bold">聯絡我們</a>

                </h4>
            </div>
			
			<div class="container-fluid" style="border:1px solid black;">
			 


			</div>

            <div id="collapseOne" class="panel-collapse collapse show" role="tabpanel" aria-labelledby="headingOne">

                <div class="panel-body">

                    <table class="table table-striped table-hover table-sm ">

                        <thead class="thead-dark">

                            <tr>
                            	<th>Check 勾選</th>
                                <th>稱謂</th>
                                <th>姓名</th>
                                <th>聯絡方式1</th>
                                <th>聯絡方式2</th>
                                <th>登記日期</th>
                            </tr>

                        </thead>

                         <tbody>

                            <tr v-for='(receive_record, index) in displayedPosts'>
                                <td><input type="checkbox" name="record_id" class="alone" :value="receive_record.index" :true-value="1" v-model:checked="receive_record.is_checked"></td>
                                <td>{{ (receive_record.gender == 'M') ? "先生" : "女士" }}</td>
                                <td>{{ receive_record.customer }}</td>
								<td>{{ receive_record.emailinfo }}</td>
								<td>{{ receive_record.telinfo }}</td>
                                <td>{{ receive_record.crt_time }}</td>
                            </tr>


                         </tbody>

                    </table>
					
				
					
					<div class="form-inline form-check" style="margin-bottom: 10px;">
						<div class="col-md-12 text-center">
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
<script type="text/javascript" src="js/contactus.js" defer></script> 











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
