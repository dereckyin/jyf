<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require_once '../vendor/autoload.php';

$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

require_once "db.php";


header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;

function isExist($record_id, $conn){
    $id = 0;
    $sql = "SELECT id FROM taiwan_pay_record  where record_id = $record_id";
    $result = mysqli_query($conn, $sql);

    // die if SQL statement failed
    while ($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
    }
    return $id;
}

switch ($method) {

    case 'POST':

        $record_id = (isset($_POST["record_id"]) ?  $_POST["record_id"] : "");
        $ar_php = isset($_POST["ar_php"]) ? $_POST["ar_php"] : "";
        $ar = isset($_POST["ar"]) ? $_POST["ar"] : "";
        $amount = isset($_POST["amount"]) ? $_POST["amount"] : "";
        $payment_date = isset($_POST["payment_date"]) ? $_POST["payment_date"] : "";
        $note = isset($_POST["note"]) ? $_POST["note"] : "";
      
        $record_id == '' ? $record_id = 0 : $record_id = $record_id;
        $ar_php == '' ? $ar_php = null : $ar_php = $ar_php;
        $ar == '' ? $ar = null : $ar = $ar;
        $amount == '' ? $amount = null : $amount = $amount;

        if($record_id == 0){
            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
            die();
        }

        $payment_id = isExist($record_id, $conn);
        
        $conn->begin_transaction();

        if($payment_id == 0){
            $query = "INSERT INTO taiwan_pay_record(record_id, ar_php, ar, amount, payment_date, note, crt_time, crt_user) values(?, ?, ?, ?, ?, ?, now(), ?)";


            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "ddddsss",
                $record_id,
                $ar_php,
                $ar,
                $amount,
                $payment_date,
                $note,
                $user
            );


            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = mysqli_insert_id($conn);
                } else {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure1 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $conn->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

        if($payment_id != 0){
            // for loading
            $query = "UPDATE taiwan_pay_record SET ";

            if($ar_php != ''){
                $query .= "ar_php = " . $ar_php . ", ";
            }
            else{
                $query .= "ar_php = null, ";
            }

            if($ar != ''){
                $query .= "ar = " . $ar . ", ";
            }else{
                $query .= "ar = null, ";
            }
            if($amount != ''){
                $query .= "amount = " . $amount . ", ";
            }else{
                $query .= "amount = null, ";
            }
                
            $query .=  "payment_date = '" . $payment_date . "', " .
                "note = '" . $note . "'  WHERE id = " . $payment_id . "";

            $stmt = $conn->prepare($query);

        // $stmt->bind_param("is", $last_id, $measure_container_id);


            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $conn->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }
        

        $conn->commit();

        break;

}

http_response_code(200);
echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
// Close connection
mysqli_close($conn);
