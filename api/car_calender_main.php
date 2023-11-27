<?php
ob_start();
//error_reporting(0);
error_reporting(E_ALL);
ini_set('log_errors', true);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$sdate = (isset($_POST['sdate']) ?  $_POST['sdate'] : '');
$edate = (isset($_POST['edate']) ?  $_POST['edate'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

require_once '../vendor/autoload.php';
include_once 'config/database.php';
// include_once 'objects/work_calender.php';
include_once 'config/conf.php';

//include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

$database_feliix = new Database_Feliix();
$db_feliix = $database_feliix->getConnection();
$conf_feliix = new Conf_Feliix();

//$workCalenderMain = new WorkCalenderMain($db);
//$workCalenderDetails = new WorkCalenderDetails($db);
//$workCalenderMessages = new WorkCalenderMessages($db);
//$le = new Leave($db);

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
        die();
    }

    try {

        $merged_results = array();
            
        $query = "SELECT * from car_calendar_main main 
                  where `status` <> -1  ";

        if($sdate != ""){
            $query .= " and main.date_use >= '" . $sdate . "-01 00:00:00' ";
        }

        if($edate != ""){
            // edate be the last day of the month
            $edate = date("Y-m-t", strtotime($edate . "-01"));

            $query .= " and main.date_use < '" . $edate . " 23:59:59' ";
            
        }

        $query .= " order by main.id";

        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;

            $check1 = GetCheck($db, $row['id'], "1", "0");
            $check2 = GetCheck($db, $row['id'], "2", "0");

            $merged_results[count($merged_results) - 1]['check1'] = $check1;
            $merged_results[count($merged_results) - 1]['check2'] = $check2;

            $merged_results[count($merged_results) - 1]['feliix'] = "";
        }

        // for feliix
        $merged_results_feliix = array();
        $merged_results_feliix = GetFeliix($db_feliix, $sdate, $edate, $db);

        foreach ($merged_results_feliix as $key => $value) {
            $merged_results[] = $value;
        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
    } catch (Exception $e) {
        http_response_code(401);

        echo json_encode(array("message" => ".$e."));
    }

}

function GetFeliix($db, $sdate, $edate, $db_check)
{
    //select all
    try {
        $query = "SELECT main.id, main.title, main.start_time, main.end_time, 
                        main.color, main.color_other, main.text_color, main.all_day, main.photoshoot_request, 
                        main.project, main.sales_executive, main.project_in_charge, main.project_relevant,
                        main.installer_needed, main.installer_needed_other, main.things_to_bring_location,
                        main.things_to_bring, main.installer_needed_location,
                        main.products_to_bring, main.products_to_bring_files,
                        main.service, main.driver, main.driver_other, main.back_up_driver, main.back_up_driver_other,
                        main.notes, main.`lock`, main.related_project_id, main.related_stage_id,
                        main.created_by, main.created_at, main.updated_by, main.updated_at, main.confirm,
                        detail.main_id, detail.agenda, detail.appoint_time, detail.end_time d_end_time, detail.sort, detail.location, main.status
                    from work_calendar_main main 
                    left join work_calendar_details detail on detail.main_id = main.id and detail.is_enabled = true
                    where main.is_enabled = true and main.status > 0 ";

        if($sdate != ""){
            $query .= " and main.start_time >= '" . $sdate . "-01 00:00:00' ";
        }

        if($edate != ""){
            // edate be the last day of the month
            $edate = date("Y-m-t", strtotime($edate . "-01"));

            $query .= " and main.start_time < '" . $edate . " 23:59:59' ";
            
        }

        $query .= " order by main.id, detail.sort ";

        $stmt = $db->prepare($query);
        $stmt->execute();

        $merged_results = array();
        $detail_array = array();

        // master
        $id = 0;
        $title = "";
        $start_time = "";
        $end_time = "";
        $color = "";
        $color_other = "";
        $text_color = "";
        $all_day = "";
        $photoshoot_request = "";
        $project = "";
        $sales_executive = "";
        $project_in_charge = "";
        $project_relevant = "";
        $installer_needed = "";
        $installer_needed_other = "";
        $things_to_bring_location = "";
        $things_to_bring = "";
        $installer_needed_location = "";
        $products_to_bring = "";
        $products_to_bring_files = "";
        $service = "";
        $driver = "";
        $driver_other = "";
        $back_up_driver = "";
        $back_up_driver_other = "";
        $notes = "";
        $lock = "";
        $related_project_id = "";
        $related_stage_id = "";
        $created_by = "";
        $created_at = "";
        $updated_by = "";
        $updated_at = "";
        $status = 0;

        $confirm = "";

        $check1 = array();
        $check2 = array();

        // detail
        $main_id = 0;
        $agenda = "";
        $appoint_time = "";
        $d_end_time = "";
        $sort = "";
        $location = "";

        $old_id = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if($old_id != $row['id'] && $old_id != 0)
            {
                // remove item from array where main_id = ''
                $detail_array = array_filter($detail_array, function($item) {
                    return $item['main_id'] != '';
                });

                $merged_results[] = array(
                    "id" => $id,
                    "title" => $title,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "color" => $color,
                    "color_other" => $color_other,
                    "text_color" => $text_color,
                    "all_day" => $all_day,
                    "confirm" => $confirm,
                    "photoshoot_request" => $photoshoot_request,
                    "project" => $project,
                    "sales_executive" => $sales_executive,
                    "project_in_charge" => $project_in_charge,
                    "project_relevant" => $project_relevant,
                    "installer_needed" => $installer_needed,
                    "installer_needed_other" => $installer_needed_other,
                    "things_to_bring_location" => $things_to_bring_location,
                    "things_to_bring" => $things_to_bring,
                    "installer_needed_location" => $installer_needed_location,
                    "products_to_bring" => $products_to_bring,
                    "products_to_bring_files" => $products_to_bring_files,
                    "service" => $service,
                    "driver" => $driver,
                    "driver_other" => $driver_other,
                    "driver_text" => $driver_text,
                    "back_up_driver" => $back_up_driver,
                    "back_up_driver_other" => $back_up_driver_other,
                    "notes" => $notes,
                    "lock" => $lock,
                    "related_project_id" => $related_project_id,
                    "related_stage_id" => $related_stage_id,
                    "created_by" => $created_by,
                    "created_at" => $created_at,
                    "updated_by" => $updated_by,
                    "updated_at" => $updated_at,
                    "detail" => $detail_array,
                    "status" => $status,

                    
                    "schedule_Name" => $title,
                    "date_use" => $start_time,
                    "car_use" => $service,
                    "time_out" => $start_time,
                    "time_in" => $end_time,

                    "check1" => $check1,
                    "check2" => $check2,
                    "feliix" => "1",
                    
                );

                $detail_array = array();

            }

            $id = $row['id'];
            $title = $row['title'];
            $start_time = $row['start_time'];
            $end_time = $row['end_time'];
            $color = $row['color'];
            $color_other = $row['color_other'];
            $text_color = $row['text_color'];
            $all_day = $row['all_day'];
            $confirm = $row['confirm'];
            $photoshoot_request = $row['photoshoot_request'];
            $project = $row['project'];
            $sales_executive = $row['sales_executive'];
            $project_in_charge = $row['project_in_charge'];
            $project_relevant = $row['project_relevant'];
            $installer_needed = $row['installer_needed'];
            $installer_needed_other = $row['installer_needed_other'];
            $things_to_bring_location = $row['things_to_bring_location'];
            $things_to_bring = $row['things_to_bring'];
            $installer_needed_location = $row['installer_needed_location'];
            $products_to_bring = $row['products_to_bring'];
            $products_to_bring_files = $row['products_to_bring_files'];
            $service = $row['service'];
            $driver = $row['driver'];
            $driver_other = $row['driver_other'];

            $driver_text = GetDriver($driver, $driver_other);

            $back_up_driver = $row['back_up_driver'];
            $back_up_driver_other = $row['back_up_driver_other'];
            $notes = $row['notes'];
            $lock = $row['lock'];
            $related_project_id = $row['related_project_id'];
            $related_stage_id = $row['related_stage_id'];
            $created_by = $row['created_by'];
            $created_at = $row['created_at'];
            $updated_by = $row['updated_by'];
            $updated_at = $row['updated_at'];

            $d_end_time = $row['d_end_time'];

            $status = $row['status'];

            $main_id = $row['main_id'];
            $agenda = $row['agenda'];
            $appoint_time = $row['appoint_time'];
            $end_time = $row['end_time'];
            $sort = $row['sort'];
            $location = $row['location'];

            $check1 = GetCheck($db_check, $row['id'], "1", "1");
            $check2 = GetCheck($db_check, $row['id'], "2", "1");

            $old_id = $id;

            $detail_array[] = array(
                "main_id" => $main_id,
                "agenda" => $agenda,
                "appoint_time" => $appoint_time,
                "end_time" => $d_end_time,
                "sort" => $sort,
                "location" => $location
            );

        }
    

        if($old_id != 0)
        {
            // remove item from array where main_id = ''
            $detail_array = array_filter($detail_array, function($item) {
                return $item['main_id'] != '';
            });

            $merged_results[] = array(
                "id" => $id,
                "title" => $title,
                "start_time" => $start_time,
                "end_time" => $end_time,
                "color" => $color,
                "color_other" => $color_other,
                "text_color" => $text_color,
                "all_day" => $all_day,
                "confirm" => $confirm,
                "photoshoot_request" => $photoshoot_request,
                "project" => $project,
                "sales_executive" => $sales_executive,
                "project_in_charge" => $project_in_charge,
                "project_relevant" => $project_relevant,
                "installer_needed" => $installer_needed,
                "installer_needed_other" => $installer_needed_other,
                "things_to_bring_location" => $things_to_bring_location,
                "things_to_bring" => $things_to_bring,
                "installer_needed_location" => $installer_needed_location,
                "products_to_bring" => $products_to_bring,
                "products_to_bring_files" => $products_to_bring_files,
                "service" => $service,
                "driver" => $driver,
                "driver_other" => $driver_other,
                "driver_text" => $driver_text,
                "back_up_driver" => $back_up_driver,
                "back_up_driver_other" => $back_up_driver_other,
                "notes" => $notes,
                "lock" => $lock,
                "related_project_id" => $related_project_id,
                "related_stage_id" => $related_stage_id,
                "created_by" => $created_by,
                "created_at" => $created_at,
                "updated_by" => $updated_by,
                "updated_at" => $updated_at,
                "detail" => $detail_array,
                "status" => $status,
                
                "schedule_Name" => $title,
                "date_use" => $start_time,
                "car_use" => $service,
                "time_out" => $start_time,
                "time_in" => $end_time,

                "check1" => $check1,
                "check2" => $check2,
                "feliix" => "1",
            );
        }
    } catch (Exception $e) {
        http_response_code(401);

        echo json_encode(array("message" => ".$e."));
    }

    $merged_results = RefactorInstallerNeeded($merged_results, $db);
    return $merged_results;
}

function RefactorInstallerNeeded($merged_results, $db)
{
    $tech = [];
    $query = "SELECT username  FROM `user` where status = 1 and title_id in (21, 22, 56, 57)";
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tech[] = $row['username'];
    }

    // iterate over each row and filter installer_needed to installer_needed_other
    foreach ($merged_results as $key => $value) {
        // convert installer_needed into array
        //$value['installer_needed'] = str_replace(" ", "", $value['installer_needed']);
        $installer_needed_array = explode(",", $value['installer_needed']);
        $installer_needed_other_array = explode(",", $value['installer_needed_other']);

        // trim space of array
        $installer_needed_array = array_map('trim', $installer_needed_array);
        $installer_needed_other_array = array_map('trim', $installer_needed_other_array);

        $installer = array();
        $installer_other = array();

        foreach ($installer_needed_other_array as $people) {
            if (in_array($people, $tech)) 
                $installer[] = $people;
            else
                $installer_other[] = $people;
        }

        foreach ($installer_needed_array as $people) {
            if (in_array($people, $tech)) 
                $installer[] = $people;
            else
                $installer_other[] = $people;
           
        }

        // installer_needed_array to string concate by comma
        $merged_results[$key]['installer_needed'] = trim(implode(",", $installer), ",");

        $merged_results[$key]['installer_needed_other'] = trim(implode(", ", array_unique($installer_other)), ", ");
        $merged_results[$key]['installer_needed_other'] = str_replace("  ", " ", $merged_results[$key]['installer_needed_other']);
    }

    return $merged_results;
}

function GetCheck($db, $sid, $kind, $feliix)
{
    $result = array();

    $query = "SELECT * from car_calendar_check 
              where `feliix` = " . $feliix . " and `status` <> -1 and kind = '" . $kind . "' and sid = " . $sid . " order by id desc limit 1";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }

    return $result;
}

function GetDriver($did, $other)
{
    $driver = "";
    if($did == '1')
        $driver = "MG";
    if($did == '2')
        $driver = "AY";
    if($did == '3')
        $driver = "EV";
    if($did == '4')
        $driver = "JB";
    if($did == '5')
        $driver = "MA";
    if($did == '6')
        $driver = $other;

    return $driver;
}