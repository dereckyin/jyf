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

            $check1 = GetCheck($db, $row['id'], "1");
            $check2 = GetCheck($db, $row['id'], "2");

            $merged_results[count($merged_results) - 1]['check1'] = $check1;
            $merged_results[count($merged_results) - 1]['check2'] = $check2;

        }


        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
    } catch (Exception $e) {
        http_response_code(401);

        echo json_encode(array("message" => ".$e."));
    }

}

function GetCheck($db, $sid, $kind)
{
    $result = array();

    $query = "SELECT * from car_calendar_check 
              where `status` <> -1 and kind = '" . $kind . "' and sid = " . $sid . "";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }

    return $result;
}