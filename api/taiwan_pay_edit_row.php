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
        $kilo = isset($_POST["kilo"]) ? $_POST["kilo"] : "";
        $cuft = isset($_POST["cuft"]) ? $_POST["cuft"] : "";
        $note = isset($_POST["note"]) ? $_POST["note"] : "";
        $status = isset($_POST["status"]) ? $_POST["status"] : "";
        $rate = isset($_POST["rate"]) ? $_POST["rate"] : "";
      
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
            $query = "INSERT INTO taiwan_pay_record(record_id, ar_php, ar, amount, payment_date, note, rate, crt_time, crt_user) values(?, ?, ?, ?, ?, ?, ?, now(), ?)";


            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "ddddssss",
                $record_id,
                $ar_php,
                $ar,
                $amount,
                $payment_date,
                $note,
                $rate,
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
                "note = '" . $note . "', rate = '". $rate . "', status = '". $status . "'  WHERE id = " . $payment_id . "";

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

            if($status == "1")
            {
                $query = "select detail_id from measure_record_detail where record_id = " . $record_id;
                $result = mysqli_query($conn, $query);
                $detail_id = 0;
                while ($row = mysqli_fetch_array($result)) {
                    $detail_id = $row['detail_id'];
                }
                if($detail_id != 0)
                {
                    $php_amount = $ar_php != '' ? $ar_php : 0;
                    $tw_amount = $ar != '' ? $ar : 0;
                    $php_note = "P" . $php_amount . "=nt" . $tw_amount . "(rate=" . $rate . ")" . ($note == '' ? '' : "," . $note);
                    $query = "insert into payment(detail_id, type, payment_date, amount, remark, status, crt_time, crt_user) values(". $detail_id . ", 4, '" . $payment_date . "', " . $php_amount . ", '" . $php_note . "', '0' , now(), '" . $user . "')";
                    $stmt = $conn->prepare($query);
    
                    try {
                        // execute the query, also check if query was successful
                        if (!$stmt->execute()) {
                            $conn->rollback();
                            http_response_code(501);
                            echo json_encode("Failure3 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
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
            
                
            }

            if($kilo != "" || $cuft != "")
            {
                $query = "update receive_record set kilo = " . $kilo . ", cuft = ". $cuft . " where id = " . $record_id;
                $stmt = $conn->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if (!$stmt->execute()) {
                        $conn->rollback();
                        http_response_code(501);
                        echo json_encode("Failure4 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
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
        }
        

        $conn->commit();

        break;

}

http_response_code(200);
echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
// Close connection
mysqli_close($conn);
