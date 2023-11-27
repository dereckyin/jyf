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

$reason = (isset($_POST['reason']) ?  $_POST['reason'] : '');

$status = (isset($_POST['status']) ?  $_POST['status'] : 0);


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

    // get current car_calendar_main status
    $current_status = "";
    $sql = "select status from car_calendar_main where id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $sid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_status = $row['status'];

    
    try {
        $sql = "update 
                    car_calendar_main
                set 
                    updated_by = :updated_by,
                    updated_at = now(),
                    status = :status
                where id = :id";

        $stmt = $db->prepare($sql);


        $stmt->bindParam(':updated_by', $user_name);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $sid);

        $stmt->execute();

        //$arr = $stmt->errorInfo();

        // // update car_calendar_main status
        // $sql = "update car_calendar_check set status = -1 where `feliix` = 0 and id = :id";

        // $stmt = $db->prepare($sql);
        // $stmt->bindParam(':id', $id);

        // $stmt->execute();

        //$arr = $stmt->errorInfo();

        if($status == '1')
        {
            $att = get_car_schedule_word($sid, "1", $date_use, $car_use, $driver, $tout, $tin);
            send_car_under_review_mail_4($sid, $user_name, $date_use, $car_use, $driver, $tout, $tin, $att, $reason);
        }


        http_response_code(200);
        echo json_encode(array("sid" => $id, "updated_by" => $user_name, "updated_at" => date("Y-m-d H:i:s"), "status" => "success"));

    } catch (Exception $e) {
        http_response_code(501);
        echo json_encode(array("insertion error" => $e->getMessage()));
        die();
    }

}