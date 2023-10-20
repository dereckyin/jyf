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

//include_once 'mail.php';


$database = new Database();
$db = $database->getConnection();

$database_feliix = new Database_Feliix();
$db_feliix = $database_feliix->getConnection();

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
        work_calendar_main
                set 
                    updated_by = :updated_by,
                    updated_at = now(),
                    status = :status
                where id = :id";

        $stmt = $db_feliix->prepare($sql);


        $stmt->bindParam(':updated_by', $user_name);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        if($status == 0)
        {
            // update car_calendar_main status
            $sql = "update car_calendar_check set status = -1 where `feliix` = 1 and sid = :id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);

            $stmt->execute();
        }

        $att = get_car_schedule_word($id, "0", $date_use, $car_use, $driver, $tout, $tin);
        send_car_reject_mail_3($id, $user_name, $date_use, $car_use, $driver, $tout, $tin, $att, $reason);
        

        http_response_code(200);
        echo json_encode(array("sid" => $id, "updated_by" => $user_name, "updated_at" => date("Y-m-d H:i:s"), "status" => "success"));

    } catch (Exception $e) {
        http_response_code(501);
        echo json_encode(array("insertion error" => $e->getMessage()));
        die();
    }

}