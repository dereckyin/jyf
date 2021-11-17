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
   
        $conn->begin_transaction();

        // for measure_detail
        $query = "update measure_detail 
                    set payment_status = ''
            WHERE id in (select measure_detail_id from pick_group where group_id in (" . $id . "))";

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

        // for payment
        $query = "update payment 
                    set status = -1, 
                    mdf_time = now(),
                    mdf_user = '" . $user . "'
            WHERE detail_id in (select measure_detail_id from pick_group where group_id in (" . $id . "))";

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

        // for group
        $query = "update pick_group 
                    set group_id = 0
            WHERE group_id in (" . $id . ")";

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
