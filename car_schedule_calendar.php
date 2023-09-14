<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
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
            $user_id = "";
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

            if(!is_numeric($user_id))
                header( 'location:index' );

        }
        catch (Exception $e){

            header( 'location:index' );
        }

        //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
        //    header( 'location:index.php' );
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        header( 'location:index' );
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'/>
    <title>
        Schedule Calendar
    </title>

   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">

          <link rel="stylesheet" href="css/vue-select.css" type="text/css">
    <link href='https://unpkg.com/fullcalendar@5.1.0/main.min.css' rel='stylesheet'/>
    

    <script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script defer src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

    <style>

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        #calendar {
            max-width: 90%;
            margin: 2% auto;
            margin-top: 0;
        }

        #filter {
            display: flex;
            margin-top: 2%;
            padding: 0 5vw 20px;
        }

        #filter input {
            width: 200px;
            margin-right: 20px;
        }

        #filter input:nth-of-type(2) {
            margin-right: 30px;
        }

        .add {
            display: flex;
            justify-content: center;
            margin-bottom: 1%;
        }

        .add__input {
            width: 86%;
            border: none;
            border-bottom: 3px solid rgb(222,225,230);
            margin-right: 1%;
            font-size: medium;
        }


        i {
            display: block;
            font-size: x-large;
            color: #206766;
        }

        i:hover {
            opacity: 0.7;
        }

        .messageboard {
            width: 90%;
            border: 1.5px solid rgb(222,225,230);
            border-radius: 10px;
            padding: 1%;
            margin-left:5%;
            margin-bottom: 16px;
        }

        .message__item {
            padding: 5px;
            margin-bottom: 0.5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid rgb(222,225,230);

        }
 
        .message__item input{
            background-color: white;
        }

        .message__item__input {
            width: 100%;
            border: none;
            font-size: medium;
            padding: 5px;
        }

        .table__item{
            padding: 3pt;
            border: 2px solid rgb(222,225,230);
        }

        .agenda__text{
            text-align: left;
            padding: .375rem .75rem;
        }

        .fc-daygrid-event {
            white-space: initial!important;
        }

        .fc-event-title {
            display: inline!important;
        }

        div.fc-event-title.fc-sticky > i.fa-check-square{
            font-size: 18px;
            margin: 0 5px 0 2px;
            color: white;
            vertical-align: -3px;
        }

        a.fc-daygrid-event.fc-daygrid-dot-event.fc-event.fc-event-start.fc-event-end.fc-event-past > i.fa-check-square {
            font-size: 18px;
            margin: 0 1px 0 2px;
            color: black;
            vertical-align: -3px;
        }
        div.button_box{
            display: flex;
            justify-content: space-evenly;
        }

        div.button_box > button{
            width: 125px;
            font-weight: 700;
        }

        @media (min-width: 576px) {

            .modal-xl {
                max-width: 90vw;
            }
        }

        @media (min-width: 992px){
            .modal-xl {
                max-width: 800px;
            }
        }

        @media (min-width: 1200px){
            .modal-xl {
                max-width: 1140px;
            }
        }

        .select_disabled {
            pointer-events:none;
            color: #bfcbd9;
            cursor: not-allowed;
            background-image: none;
            background-color: #eef1f6;
            border-color: #d1dbe5;   
        }

    </style>

</head>
<body>

<div style="background: rgb(2,106,167); padding: 0.5vh; height:7.5vh;">

    <a href="default" style="margin-left:1vw; position: relative; top:-10%;" ><span style="color: white;">&#9776;</span></a>

    <a href="default"><span style="margin-left:1vw; font-weight:700; font-size:xx-large; color: white;">FELIIX</span></a>

</div>

<div id="filter">
    <input type="month" class="form-control" id="sdate">
    <input type="month" class="form-control" id="edate">
    <button class="btn btn-primary" onclick="app.getInitial()">Filter Schedules</button>
</div>

<div id='calendar'></div>




<!-- Feliix Schedule Form Begin -->
<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="exampleModalScrollable">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel"></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>


            <div class="modal-body">

                <!-- 指派車輛管理者填寫的表單 -->
                <div id="approval_section" style="margin: 0 0 20px; padding-bottom: 20px; border-bottom: 3px solid #dee2e6;">

                    <div class="row">
                        <div class="col-12" style="text-align: center;">

                            <h4 style="background: palegreen; padding: 8px; margin: 0 20px 5px;">Request Review 1</h4>

                        </div>

                    </div>

                    <br>

                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Date</label>

                        </div>

                        <div class="col-10">

                            <input type="date" class="form-control" style="width:40%;" id="sc_date">

                        </div>
                    </div>

                    <br>

                    <div class="form-inline row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Time</label>

                        </div>

                        <div class="col-10">

                            <input type="time" class="form-control" style="width:40%; margin-right:1%; padding-right: 0; text-align: center;" id="sc_stime"> to <input type="time" class="form-control" style="width:40%; margin-left:1%; padding-right: 0; text-align: center;" id="sc_etime">

                        </div>

                    </div>

                    <br>

                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Assigned Car</label>

                        </div>

                        <div class="col-10">

                            <Select class="form-control" style="width:40%;" id="???">
                                <option value="0">Choose One</option>
                                <option value="Alphard">Alphard</option>
                                <option value="Avanza">Avanza</option>
                                <option value="Travis 1">Travis 1</option>
                                <option value="Travis 2">Travis 2</option>
                                <option value="Toyota Rush">Toyota Rush</option>
                            </Select>

                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Assigned Driver</label>

                        </div>

                        <div class="col-10">

                            <input type="text" class="form-control" style="width:90%;" id="???">
                            <input type="text" class="form-control" style="display: none;" id="???">

                        </div>
                    </div>

                    <br>

                    <div class="button_box">

                        <button class="btn btn-secondary" id="btn_reset???">Reset</button>

                        <button class="btn btn-primary" id="btn_???">Assign</button>

                        <button class="btn btn-secondary" id="btn_???">Not Yet Assign</button>

                        <button class="btn btn-danger" id="btn_???">Reject</button>

                        <button class="btn btn-primary" id="btn_edit???">Edit</button>

                        <button class="btn btn-secondary" id="btn_cancel">Cancel</button>

                        <button class="btn btn-primary" id="btn_save">Save</button>

                    </div>

                </div>


                <!-- 申請用車人填寫的表單 -->
                <div class="row">

                    <div class="col-12" style="text-align: center;">

                        <h4 style="background: #dee2e6; padding: 8px; margin: 0 20px 20px;">Content of Request</h4>

                    </div>

                </div>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Project</label>
                    </div>

                    <div class="col-10">

                        <input type="text" class="form-control" style="width:90%;" id="sc_project">
                        <input type="text" class="form-control" style="display: none;" id="sc_title">

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Color</label>
                    </div>

                    <div class="col-10" style="display:flex; align-items: center;">

                        <div class="custom-control custom-radio" style="display:inline-block;">
                            <input type="radio" class="custom-control-input" name="sc_color" id="sc_color_orange" value="#FECC28" onchange="enable_forOther(this);" >
                            <label class="custom-control-label" for="sc_color_orange" style="background-color: #FECC28; width: 18px; height: 18px; margin-left: 2px;"></label>
                        </div>

                        <div class="custom-control custom-radio" style="display:inline-block; margin-left: 20px;">
                            <input type="radio" class="custom-control-input" name="sc_color" id="sc_color_red" value="#4EB5BB" onchange="enable_forOther(this);" >
                            <label class="custom-control-label" for="sc_color_red" style="background-color: #4EB5BB; width: 18px; height: 18px; margin-left: 2px;"></label>
                        </div>

                        <div class="custom-control custom-radio" style="display:inline-block; margin-left: 20px;">
                            <input type="radio" class="custom-control-input" name="sc_color" id="sc_color_purple" value="#009858" onchange="enable_forOther(this);" >
                            <label class="custom-control-label" for="sc_color_purple" style="background-color: #009858; width: 18px; height: 18px; margin-left: 2px;"></label>
                        </div>

                        <div class="custom-control custom-radio" style="display:inline-block; margin-left: 20px;">
                            <input type="radio" class="custom-control-input" name="sc_color" id="sc_color_green" value="#A671AD" onchange="enable_forOther(this);" >
                            <label class="custom-control-label" for="sc_color_green" style="background-color: #A671AD; width: 18px; height: 18px; margin-left: 2px;"></label>
                        </div>

                        <div class="custom-control custom-radio" style="display:inline-block; margin-left: 20px;">
                            <input type="radio" class="custom-control-input" name="sc_color" id="sc_color_blue" value="#F19DB4" onchange="enable_forOther(this);" >
                            <label class="custom-control-label" for="sc_color_blue" style="background-color: #F19DB4; width: 18px; height: 18px; margin-left: 2px;"></label>
                        </div>

                        <div class="custom-control custom-radio" style="display:inline-block; margin-left: 20px;">
                            <input type="radio" class="custom-control-input" name="sc_color" id="sc_color_teal" value="#141415" onchange="enable_forOther(this);" >
                            <label class="custom-control-label" for="sc_color_teal" style="background-color: #141415; width: 18px; height: 18px; margin-left: 2px;"></label>
                        </div>

                        <div class="custom-control custom-radio" style="display:inline-block; margin-left: 20px;">
                            <input type="radio" class="custom-control-input" name="sc_color" id="sc_color_other" value="1" onchange="enable_forOther(this);" >
                            <label class="custom-control-label" for="sc_color_other" style="margin-left: 2px;">Other </label>
                        </div>

                        <input type="color" class="form-control" style="margin-left: 5px; width: 30px; padding: 2px;" id="sc_color">



                    </div>

                </div>

                <br>

                <div id="last_editor" style="display:none;">
                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Editor</label>
                        </div>

                        <div class="col-10">


                            <input type="text" class="form-control" style="width:90%;" disabled id="sc_editor">

                        </div>


                    </div>

                    <br>

                </div>



                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Date</label>
                    </div>

                    <div class="col-10">


                        <input type="date" class="form-control" style="width:40%;" id="sc_date">

                    </div>

                </div>

                <br>

                <div class="form-inline row" id="projects">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Related Project</label>
                    </div>

                    <div class="col-10">

                    <!-- dropdown -->
                    <select class="form-control" style="width:90%;" id="sc_related_project_id" @change="getStages()" v-model="project_id">
                        <option value=""></option>
                        <option v-for="project in projects" :value="project.id">{{project.project_name}}</option>
                    </select>
                    <select class="form-control" style="width:90%;" id="sc_related_stage_id"  v-model="stage_id">
                        <option v-for="stage in stages" :value="stage.id">{{stage.sequence}} : {{stage.stage}}</option>
                    </select>

                    </div>

                </div>

                <br>

                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Sales Executive</label>
                    </div>

                    <div class="col-10">


                        <input type="text" class="form-control" style="width:90%;" id="sc_sales">

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Project-in-charge</label>
                    </div>

                    <div class="col-10">


                        <input type="text" class="form-control" style="width:90%;" id="sc_incharge">

                    </div>

                </div>

                <br>

                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Relevant Persons</label>
                    </div>

                    <div class="col-10">

                    <v-select id="sc_relevant"  style="width:90%;" :options="users" attach chips label="username" v-model="attendee"
                      multiple></v-select>
                     

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Time</label>
                    </div>

                    <div class="col-10">

                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" id="sc_time" checked>
                            <label class="custom-control-label" for="sc_time"> all-day</label>
                        </div>

                        <input type="time" class="form-control" style="width:30%; margin-left:5%; margin-right:1%; padding-right: 0; text-align: center;" id="sc_stime" disabled> to <input type="time" class="form-control" style="width:30%; margin-left:1%; padding-right: 0; text-align: center;" id="sc_etime" disabled>

                    </div>

                </div>

                <br><hr>

                <div class="row">

                    <div class="col-12" style="text-align: center;">

                        <table style="width:100%;" id="agenda_table">

                            <tr>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_location"></td>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_agenda"></td>
                                <td style="padding-bottom: 10pt;"><input type="time" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_appointtime"></td>
                                <td style="padding-bottom: 10pt;"><input type="time" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_endtime"></td>
                                <td style="padding-bottom: 10pt;"><i class="fas fa-plus-circle" id="add_agenda"></i></td>

                            </tr>


                            <tr>
                                <th class="table__item" style="width:35%;">Location</th>
                                <th class="table__item" style="width:30%;">Agenda</th>
                                <th class="table__item" style="width:10%;">Appoint Time</th>
                                <th class="table__item" style="width:10%;">End Time</th>
                                <th class="table__item" style="width:15%;">Actions</th>

                            </tr>



                        </table>
                    </div>



                </div>

                <hr><br>


                <div class="form-inline row" id="install">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Installer needed</label>
                    </div>

                    <div class="col-10">
                        <!--

                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="AS" id="AS">
                            <label class="custom-control-label" for="AS">AS</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block; margin-left:1%;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="RM" id="RM">
                            <label class="custom-control-label" for="RM">RM</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block; margin-left:1%;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="RS" id="RS">
                            <label class="custom-control-label" for="RS">RS</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block; margin-left:1%;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="CJ" id="CJ">
                            <label class="custom-control-label" for="CJ">CJ</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block; margin-left:1%;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="JO" id="JO">
                            <label class="custom-control-label" for="JO">JO</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block; margin-left:1%;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="EO" id="EO">
                            <label class="custom-control-label" for="EO">EO</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block; margin-left:1%;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="JM" id="JM">
                            <label class="custom-control-label" for="JM">JM</label>
                        </div>

                        -->

                        <!--
                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="EO" id="EO">
                            <label class="custom-control-label" for="EO">EO</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="JM" id="JM">
                            <label class="custom-control-label" for="JM">JM</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="JC" id="JC">
                            <label class="custom-control-label" for="JC">JC</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="GV" id="GV">
                            <label class="custom-control-label" for="GV">GV</label>
                        </div>

                        <div class="custom-control custom-checkbox" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" value="JS" id="JS">
                            <label class="custom-control-label" for="JS">JS</label>
                        </div>
                        -->
                        <template v-for="(username, index) in installer">
                            <div class="custom-control custom-checkbox" style="display:inline-block;" >
                                <input type="checkbox" class="custom-control-input" name="sc_Installer_needed" :value="username" :id="username">
                                <label class="custom-control-label" :for="username">{{username}}</label>
                            </div>

                            <br>
                        </template>

                        <div style="display: flex; margin-top: 10px;">
                            <label>Others:</label>
                            <input type="text" class="form-control" style="margin-left:1%; border-top: 0; border-right: 0; border-left: 0; border-radius: 0; width: 300px;" id="sc_Installer_needed_other" name="sc_Installer_needed_other">
                        </div>

                    </div>

                </div>

                <br>

                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Things to Bring</label>
                    </div>

                    <div class="col-10">

                        <div>
                            From <input type="text" class="form-control" style="width:30%; margin-left:1%;"
                                        placeholder="Location" id="sc_location1">
                        </div>

                        <div>

                            <textarea class="form-control" style="width:90%; margin-top:1%; resize:none; overflow:auto;" rows="5"
                                      onkeyup="autogrow(this);" id="sc_things"></textarea>
                        </div>

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Products to Bring</label>
                    </div>

                    <div class="col-10">

                        <div>
                            From <input type="text" class="form-control" style="width:30%; margin-left:1%;"
                                        placeholder="Location" id="sc_location2">
                        </div>

                        <div style="margin-top: 1%;">

                            <textarea class="form-control" style="width:90%; resize:none; overflow:auto;" rows="5"
                                      onkeyup="autogrow(this);" id="sc_products"></textarea>
                        </div>

                        <div id="upload_input" style="display: flex; align-items: center; margin-top:1%;">
                            Upload PO/Quote <input type="file" ref="file" id="fileload" name="file[]" onChange="onChangeFileUpload(event)" class="form-control" style="width:70%; margin-left:1%;" multiple>
                        </div>
						<div  id="sc_product_files" style="display: flex; align-items: center;">
                        </div>
						<input  id="sc_product_files_hide" style="display: none;" value="">
                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Service</label>
                    </div>

                    <div class="col-10">


                        <Select class="form-control" style="width:40%;" id="sc_service">
                            <option value="0">Choose One</option>
                            <option value="1">innova</option>
                            <option value="2">avanza gold</option>
                            <option value="3">avanza gray</option>
                            <option value="4">L3001</option>
                            <option value="5">L3002</option>
                            <option value="6">Grab</option>
                        </Select>

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Driver</label>
                    </div>

                    <div class="col-10" style="display: flex;">


                        <Select class="form-control" style="width:40%;" onchange="action_forOther(this);"  id="sc_driver1">
                            <option value="0">Choose One</option>
                            <option value="1">MG</option>
                            <option value="2">AY</option>
                            <option value="3">EV</option>
                            <option value="4">JB</option>
                            <option value="5">MA</option>
                            <option value="6">Others</option>
                        </Select>

                        <input type="text" class="form-control" style="margin-left:2%; width: 48%;"
                               id="sc_driver_other">

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Back-up Driver</label>
                    </div>

                    <div class="col-10" style="display: flex;">


                        <Select class="form-control" style="width:40%;" onchange="action_forOther_Backup(this);" id="sc_driver2">
                            <option value="0">Choose One</option>
                            <option value="1">MG</option>
                            <option value="2">AY</option>
                            <option value="3">EV</option>
                            <option value="4">JB</option>
                            <option value="5">MA</option>
                            <option value="6">Others</option>
                        </Select>

                        <input type="text" class="form-control" style="margin-left:2%; width: 48%;"
                               id="sc_backup_driver_other">

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Photoshoot Request</label>
                    </div>

                    <div class="col-10">

                        <div class="custom-control custom-radio" style="display: inline-block;">
                            <input type="radio" id="photoshoot_yes" name="sc_Photoshoot_request" value="Yes" class="custom-control-input">
                            <label class="custom-control-label" for="photoshoot_yes">Yes</label>
                        </div>

                        <div class="custom-control custom-radio" style="display: inline-block; margin-left:1%;">
                            <input type="radio" id="photoshoot_no" name="sc_Photoshoot_request" value="No" class="custom-control-input">
                            <label class="custom-control-label" for="photoshoot_no">No</label>
                        </div>

                    </div>

                </div>

                <br>


                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Notes</label>
                    </div>

                    <div class="col-10">

                        <textarea class="form-control" style="width:90%; resize:none; overflow:auto;" rows="5"
                                      onkeyup="autogrow(this);" id="sc_notes"></textarea>

                    </div>

                </div>

                <hr>

                <br><input type="hidden" id="lock" value=""/><input type="hidden" id="confirm" value=""/>

                <div class="button_box">

                    <button class="btn btn-secondary" style="width: 155px" id="btn_reset">Reset Schedule</button>

                    <button class="btn btn-primary" id="btn_add">Add</button>

                    <button class="btn btn-secondary" id="btn_export">Export</button>

                    <button class="btn btn-secondary" style="width: 155px" id="btn_duplicate">Duplicate Schedule</button>

                    <button class="btn btn-danger" id="btn_delete">Delete</button>

                    <button class="btn btn-primary" id="btn_edit">Edit</button>

                    <button class="btn btn-secondary" id="btn_cancel">Cancel</button>

                    <button class="btn btn-primary" id="btn_save">Save</button>
                    
                    <button class="btn btn-info" id="btn_lock">Lock</button>

                    <button class="btn btn-info" id="btn_unlock">Unlock</button>

                    <button class="btn btn-info" id="btn_confirm">Confirmed</button>

                    <button class="btn btn-info" id="btn_unconfirm">Unconfirmed</button>

                </div>

                <br>


            </div>


        </div>

    </div>

</div>
<!-- Feliix Schedule Form End -->



<!-- Servictory Schedule Form Begin -->
<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="serviceModalScrollable">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel"></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>


            <div class="modal-body">

                <!-- 指派車輛管理者填寫的表單 -->
                <div id="approval_section" v-if="is_admin" style="margin: 0 0 20px; padding-bottom: 20px; border-bottom: 3px solid #dee2e6;">

                    <div class="row">
                        <div class="col-12" style="text-align: center;">

                            <h4 style="background: palegreen; padding: 8px; margin: 0 20px 5px;">Request Review 2</h4>

                        </div>

                    </div>

                    <br>

                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Date</label>

                        </div>

                        <div class="col-10">

                            <input type="date" class="form-control" style="width:40%;" id="sc_date">

                        </div>
                    </div>

                    <br>

                    <div class="form-inline row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Time</label>

                        </div>

                        <div class="col-10">

                            <input type="time" class="form-control" style="width:40%; margin-right:1%; padding-right: 0; text-align: center;" id="sc_stime"> to <input type="time" class="form-control" style="width:40%; margin-left:1%; padding-right: 0; text-align: center;" id="sc_etime">

                        </div>

                    </div>

                    <br>

                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Assigned Car</label>

                        </div>

                        <div class="col-10">

                            <Select class="form-control" style="width:40%;" id="???">
                                <option value="0">Choose One</option>
                                <option value="Alphard">Alphard</option>
                                <option value="Avanza">Avanza</option>
                                <option value="Travis 1">Travis 1</option>
                                <option value="Travis 2">Travis 2</option>
                                <option value="Toyota Rush">Toyota Rush</option>
                            </Select>

                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Assigned Driver</label>

                        </div>

                        <div class="col-10">

                            <input type="text" class="form-control" style="width:90%;" id="???">
                            <input type="text" class="form-control" style="display: none;" id="???">

                        </div>
                    </div>

                    <br>

                    <div class="button_box">

                        <button class="btn btn-secondary" id="btn_reset???">Reset</button>

                        <button class="btn btn-primary" id="btn_???">Assign</button>

                        <button class="btn btn-secondary" id="btn_???">Not Yet Assign</button>

                        <button class="btn btn-danger" id="btn_???">Reject</button>

                        <button class="btn btn-primary" id="btn_edit???">Edit</button>

                        <button class="btn btn-secondary" id="btn_cancel">Cancel</button>

                        <button class="btn btn-primary" id="btn_save">Save</button>

                    </div>

                </div>


                <!-- 申請用車人填寫的表單 -->
                <div class="row">

                    <div class="col-12" style="text-align: center;">

                        <h4 style="background: #dee2e6; padding: 8px; margin: 0 20px 20px;">Content of Request</h4>

                    </div>

                </div>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Schedule Name</label>

                    </div>

                    <div class="col-10">

                        <input type="text" class="form-control" style="width:90%;" id="schedule_Name" v-model="schedule_Name">

                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Date Use</label>

                    </div>

                    <div class="col-10">

                        <input type="date" class="form-control" style="width:40%;" id="date_use" v-model="date_use">

                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Car Use</label>

                    </div>

                    <div class="col-10">

                        <Select class="form-control" style="width:40%;" id="car_use" v-model="car_use">
                            <option value="">Choose One</option>
                            <option value="Alphard">Alphard</option>
                            <option value="Avanza">Avanza</option>
                            <option value="Travis 1">Travis 1</option>
                            <option value="Travis 2">Travis 2</option>
                            <option value="Toyota Rush">Toyota Rush</option>
                        </Select>

                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Driver</label>

                    </div>

                    <div class="col-10">

                        <input type="text" class="form-control" style="width:90%;" id="driver" v-model="driver">
 

                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Helper</label>

                    </div>

                    <div class="col-10">

                        <input type="text" class="form-control" style="width:90%;" id="helper" v-model="helper">
 

                    </div>
                </div>

                <br>

                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Time Out</label>

                    </div>

                    <div class="col-10">

                        <input type="time" class="form-control" style="width:40%; padding-right: 0; text-align: center;" id="time_out" v-model="time_out">

                    </div>

                </div>

                <br>

                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Time In</label>

                    </div>

                    <div class="col-10">

                        <input type="time" class="form-control" style="width:40%; padding-right: 0; text-align: center;" id="time_in" v-model="time_in">

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Notes</label>
                    </div>

                    <div class="col-10">

                        <textarea class="form-control" style="width:90%; resize:none; overflow:auto;" rows="5"
                                      onkeyup="autogrow(this);" id="notes" v-model="notes"></textarea>

                    </div>

                </div>

                <br><hr>

                <div class="row">

                    <div class="col-12" style="text-align: center;">

                        <table style="width:100%;" id="agenda_table">

                            <tr>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="item_schedule" v-model="item_schedule"></td>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="item_company" v-model="item_company"></td>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="item_address" v-model="item_address"></td>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="item_purpose" v-model="item_purpose"></td>
                                <td style="padding-bottom: 10pt;"><i class="fas fa-plus-circle" id="add_agenda" @click="add_item"></i></td>

                            </tr>


                            <tr>
                                <th class="table__item" style="width:10%;">Schedule</th>
                                <th class="table__item" style="width:15%;">Company</th>
                                <th class="table__item" style="width:40%;">Address</th>
                                <th class="table__item" style="width:20%;">Purpose</th>
                                <th class="table__item" style="width:15%;">Actions</th>
                            </tr>

                            <tr v-for='(it, index) in items'>
                                <td>
                                    {{ it.schedule }}
                                </td>
                                <td>
                                    {{ it.company }}
                                </td>
                                <td>
                                    {{ it.address }}
                                </td>
                                <td>
                                    {{ it.purpose }}
                                </td>
                                <td>
                                    <i class="fas fa-arrow-alt-circle-up" @click="set_up(index, it.id)"></i>
                                    <i class="fas fa-arrow-alt-circle-down" @click="set_down(index, it.id)"></i>
                                    <i class="fas fa-trash-alt" @click="del(it.id)"></i>
                                </td>
                            </tr>

                        </table>
                    </div>

                </div>

                <br><hr><br>

                <input type="hidden" id="lock" value=""/><input type="hidden" id="confirm" value=""/>

                <div class="button_box">

                    <button class="btn btn-secondary" style="width: 155px;" id="btn_reset">Reset Schedule</button>

                    <button class="btn btn-secondary" id="btn_export">Export</button>

                    <button class="btn btn-secondary" style="width: 155px;" id="btn_duplicate">Duplicate Schedule</button>

                    <button class="btn btn-primary" id="btn_edit">Edit</button>

                    <button class="btn btn-primary" style="width: 155px;" id="btn_???">Send Request</button>

                    <button class="btn btn-secondary" style="width: 155px;" id="btn_???">Withdraw Request</button>

                    <button class="btn btn-danger" id="btn_delete">Delete</button>

                    <button class="btn btn-primary" id="btn_save" @click="service_save()">Save</button>

                    <button class="btn btn-primary" style="width: 200px" id="btn_???">Save and Send Request</button>

                    <button class="btn btn-secondary" id="btn_cancel">Cancel</button>

                </div>

                <br>

            </div>

        </div>

    </div>

</div>
<!-- Servictory Schedule Form End -->




<script>

    function enable_forOther(selector){
        if(selector.value != "1")
            document.getElementById("sc_color").disabled = true;
        else
            document.getElementById("sc_color").disabled = false;
        
        console.log(selector.value);
    }

    function action_forOther(selector){

        if(selector.value != 6){
            document.getElementById("sc_driver_other").style.display = "none";
        }else{
            document.getElementById("sc_driver_other").value = "";
            document.getElementById("sc_driver_other").style.display = "";
        }
    }

    function action_forOther_Backup(selector){

    if(selector.value != 6){
        document.getElementById("sc_backup_driver_other").style.display = "none";
    }else{
        document.getElementById("sc_backup_driver_other").value = "";
        document.getElementById("sc_backup_driver_other").style.display = "";
    }
}
</script>

<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/car_schedule_calender.js?v=<?php uniqid(); ?>"></script>
<script src="js/moment.js"></script>
<script defer src='https://unpkg.com/fullcalendar@5.1.0/main.min.js'></script>
<script defer src='https://fullcalendar.io/js/fullcalendar-2.1.1/fullcalendar.min.js'></script>
</html>
