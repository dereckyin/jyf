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

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);

$sid = (isset($_POST['sid']) ?  $_POST['sid'] : 0);

$date_use = (isset($_POST['date_use']) ?  $_POST['date_use'] : '');
$car_use = (isset($_POST['car_use']) ?  $_POST['car_use'] : '');
$driver = (isset($_POST['driver']) ?  $_POST['driver'] : '');
$time_out = (isset($_POST['time_out']) ?  $_POST['time_out'] : '');
$time_in = (isset($_POST['time_in']) ?  $_POST['time_in'] : '');
$status = (isset($_POST['status']) ?  $_POST['status'] : 0);
$kind = (isset($_POST['kind']) ?  $_POST['kind'] : '1');

$schedule_Name_f = (isset($_POST['schedule_Name_f']) ?  $_POST['schedule_Name_f'] : "");
$date_use_f = (isset($_POST['date_use_f']) ?  $_POST['date_use_f'] : "");
$car_use_f = (isset($_POST['car_use_f']) ?  $_POST['car_use_f'] : "");
$driver_f = (isset($_POST['driver_f']) ?  $_POST['driver_f'] : "");
$helper_f = (isset($_POST['helper_f']) ?  $_POST['helper_f'] : "");
$time_out_f = (isset($_POST['time_out_f']) ?  $_POST['time_out_f'] : "");
$time_in_f = (isset($_POST['time_in_f']) ?  $_POST['time_in_f'] : "");
$notes_f = (isset($_POST['notes_f']) ?  $_POST['notes_f'] : "");
$items_f = (isset($_POST['items_f']) ?  $_POST['items_f'] : []);
$status_f = (isset($_POST['status_f']) ?  $_POST['status_f'] : 0);
$creator_f = (isset($_POST['creator_f']) ?  $_POST['creator_f'] : "");

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

require_once '../vendor/autoload.php';
include_once 'config/database.php';
// include_once 'objects/work_calender.php';
include_once 'config/conf.php';

include_once 'mail.php';


$database = new Database();
$db = $database->getConnection();
$conf = new Conf();


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

    $tout = $date_use . " " . $time_out;
    $tin = $date_use . " " . $time_in;
    
    try {
        $sql = "update 
                    car_calendar_check
                set 
                    date_use = :date_use,
                    car_use = :car_use,
                    driver = :driver,
                    time_out = :time_out,
                    time_in = :time_in,
                    updated_by = :updated_by,
                    updated_at = now(),
                    status = :status
                where `feliix` = 1 and id = :id";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':date_use', $date_use);
        $stmt->bindParam(':car_use', $car_use);
        $stmt->bindParam(':driver', $driver);
        $stmt->bindParam(':time_out',  $tout);
        $stmt->bindParam(':time_in',  $tin);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':updated_by', $user_name);
        
        $stmt->execute();

        $att = get_car_schedule_word_feliix($sid, "1", $schedule_Name_f, $date_use_f, $car_use_f, $driver_f, $helper_f, $time_out_f, $time_in_f, $notes_f, $items_f, $status_f, $creator_f, $date_use, $car_use, $driver, $tout, $tin);
        send_car_edit_mail_5_feliix($sid, $user_name, $date_use, $car_use, $driver, $tout, $tin, $att);

        http_response_code(200);
        echo json_encode(array("id" => $id, "created_by" => $user_name, "created_at" => date("Y-m-d H:i:s"), "status" => "success"));

    } catch (Exception $e) {
        http_response_code(501);
        echo json_encode(array("insertion error" => $e->getMessage()));
        die();
    }

}