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

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : '');

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$schedule_Name = (isset($_POST['schedule_Name']) ?  $_POST['schedule_Name'] : '');
$date_use = (isset($_POST['date_use']) ?  $_POST['date_use'] : '');
$car_use = (isset($_POST['car_use']) ?  $_POST['car_use'] : '');
$driver = (isset($_POST['driver']) ?  $_POST['driver'] : '');
$helper = (isset($_POST['helper']) ?  $_POST['helper'] : '');
$time_out = (isset($_POST['time_out']) ?  $_POST['time_out'] : '');
$time_in = (isset($_POST['time_in']) ?  $_POST['time_in'] : '');
$notes = (isset($_POST['notes']) ?  $_POST['notes'] : '');
$items = (isset($_POST['items']) ?  $_POST['items'] : []);


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

use Google\Cloud\Storage\StorageClient;
use PhpOffice\PhpWord\IOFactory;

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
        $sql = "insert into car_calendar_main (schedule_Name, date_use, car_use, driver, helper, time_out, time_in, notes, items, created_by, created_at) 
        values (:schedule_Name, :date_use, :car_use, :driver, :helper, :time_out, :time_in, :notes, :items, :created_by, now())";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':schedule_Name', $schedule_Name);
        $stmt->bindParam(':date_use', $date_use);
        $stmt->bindParam(':car_use', $car_use);
        $stmt->bindParam(':driver', $driver);
        $stmt->bindParam(':helper', $helper);
        $stmt->bindParam(':time_out',  $tout);
        $stmt->bindParam(':time_in',  $tin);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':items', $items);
        $stmt->bindParam(':created_by', $user_name);

        $stmt->execute();

        http_response_code(200);
        //echo json_encode(array("message" => " Insert success at " . date("Y-m-d") . " " . date("h:i:sa")));

    } catch (Exception $e) {
        http_response_code(501);
        echo json_encode(array("insertion error" => $e->getMessage()));
        die();
    }

}