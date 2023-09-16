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
$method = $_SERVER['REQUEST_METHOD'];

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

    switch ($method) {
        case "GET":
    
            $ret = GetAccess("car_access1", $GLOBALS["user_name"], $db);
            $access["car_access1"] = $ret;
            $ret = GetAccess("car_access2", $GLOBALS["user_name"], $db);
            $access["car_access2"] = $ret;

    
            $jsonEncodedReturnArray = json_encode($access, JSON_PRETTY_PRINT);
            echo $jsonEncodedReturnArray;
            break;
        
    }
}

function GetAccess($field, $username, $db){
    $access = false;

    $query = "SELECT * FROM access_control WHERE ". $field . " LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access = true;
        }
    return $access;
}