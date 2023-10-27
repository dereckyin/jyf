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
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

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

require_once "db.php";


header('Access-Control-Allow-Origin: *');  

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;
$user_id = $decoded->data->id;

    switch ($method) {
    
        case 'POST':
    
        $id = (isset($_POST['id']) ?  $_POST['id'] : "");
        $record = (isset($_POST['record']) ?  $_POST['record'] : "");
        $encode_status = (isset($_POST['encode_status']) ?  $_POST['encode_status'] : "");
        $detail_array = json_decode($record, true);

        $measure_id = 0;

        $conn->begin_transaction();

        for ($i = 0; $i < count($detail_array); $i++) {
            $measure_id = ($detail_array[$i]['measure_id'] == '') ? 0 : $detail_array[$i]['measure_id'];
            $rid = ($detail_array[$i]['id'] == '') ? 0 : $detail_array[$i]['id'];
            $pick_date = ($detail_array[$i]['org_pick_date'] == '') ? "" : $detail_array[$i]['org_pick_date'];
            $pick_note = ($detail_array[$i]['pick_note'] == '') ? "" : $detail_array[$i]['pick_note'];
            $pick_person = ($detail_array[$i]['pick_person'] == '') ? "" : $detail_array[$i]['pick_person'];

            $receipt_number = ($detail_array[$i]['receipt_number'] == '') ? "" : $detail_array[$i]['receipt_number'];
            $checker = ($detail_array[$i]['checker'] == '') ? "" : $detail_array[$i]['checker'];
            
            $query = "UPDATE receive_record  
            SET
            pick_date = '" . $pick_date . "',
            pick_note = '" . $pick_note . "',
            pick_person = '" . $pick_person . "',
            pick_user = '" . $user . "',
            pick_time = now(),
            real_pick_time = '" . str_replace('-', '/', $pick_date) . "',
            receipt_number = '" . $receipt_number . "'
            WHERE id = " . $rid;

            $stmt = $conn->prepare($query);

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

        // for loading
        $query = "UPDATE measure_detail
            SET
            pickup_status = '" . $encode_status . "'
            WHERE id = " . $measure_id;

        $stmt = $conn->prepare($query);

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
            
        $conn->commit();
    }

    // Close connection
    mysqli_close($conn);


?>
