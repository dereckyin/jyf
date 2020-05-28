<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
<title>中亞菲國際貿易有限公司</title>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=900, user-scalable=0, viewport-fit=cover"/>

<!-- CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>


<link rel="stylesheet" type="text/css" href="css/case.css"/>


<link rel="stylesheet" type="text/css" href="css/mediaquires.css"/>

<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>



<style>
img.ui-datepicker-trigger {
    padding-left: 10px;
    margin: -8px;
}

p {
  margin:0; 
  padding:0;
}


</style>

<script>
$(function(){
    $('header').load('include/header_admin.php');
});


</script>

</head>

<body>
<div class="bodybox"> 
  <!-- header -->
  <header> 
  </header>
  <!-- header end -->
  <div id='receive_record'>
    <div class="container " style="width: 100vw">

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
  </div>
</div>




</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script> 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
<script type="text/javascript" src="js/contactus.js" defer></script> 



</body>
</html>
