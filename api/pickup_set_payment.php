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
            $id = ($detail_array[$i]['id'] == '') ? 0 : $detail_array[$i]['id'];
            $type = ($detail_array[$i]['type'] == '') ? 0 : $detail_array[$i]['type'];
            $detail_id = ($detail_array[$i]['detail_id'] == '') ? 0 : $detail_array[$i]['detail_id'];
            $issue_date = ($detail_array[$i]['issue_date'] == '') ? "" : $detail_array[$i]['issue_date'];
            $payment_date = ($detail_array[$i]['payment_date'] == '') ? "" : $detail_array[$i]['payment_date'];
            $person = ($detail_array[$i]['person'] == '') ? "" : $detail_array[$i]['person'];
            $amount = ($detail_array[$i]['amount'] == '') ? 0 : $detail_array[$i]['amount'];
            $remark = ($detail_array[$i]['remark'] == '') ? "" : $detail_array[$i]['remark'];


            if($id != 0)
            {
                // for payment
                $query = "update payment 
                        set status = -1,
                        mdf_user = '" . $user . "', 
                        mdf_time = now()
                WHERE id = " . $id;

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

            
            $query = "insert into payment (detail_id, `type`, issue_date, payment_date, person, amount, remark, status, crt_time, crt_user)
                VALUES (" . $detail_id . ", " . $type . ", '" . $issue_date . "', '" . $payment_date . "', '" . $person . "', " . $amount . ", '" . $remark . "', 0, now(), '" . $user . "')";  
          
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

                
            // for status
            $query = "UPDATE measure_detail
                SET
                payment_status = '" . $encode_status . "'
                WHERE id = " . $detail_id;

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

        $conn->commit();
    }

    // Close connection
    mysqli_close($conn);


?>
