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

switch ($method) {

    case 'POST':
        $id = (isset($_POST['id']) ? $_POST['id'] : 0);

         $ids = "";
        $recs = "";

        $conn->begin_transaction();

        $last_id = $id;
        $query = "update measure_ph set 
                    status = -1, 
                    del_time = now(), 
                    del_user = ?
                  where id = ?";


        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "si",
            $user,
            $id
        );


        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
        
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

        // for loading
        $query = "UPDATE loading
            SET
                measure_num = 0 WHERE measure_num = " . $id;

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

        // for measure_record_detail
        $query = "select * from measure_detail where measure_id = " . $id;
        $stmt1 = $conn->prepare($query);
     
        $stmt1->execute();

        $result = $stmt1->get_result();
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $record = DeleteMeasureDetailRecord($row['id'], $conn);
        }
        
        // for measure_detail
        $query = "delete from measure_detail where measure_id = " . $id;
        $stmt = $conn->prepare($query);
 
        $stmt->execute();


        $conn->commit();

        break;

}

http_response_code(200);
echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
// Close connection
mysqli_close($conn);



function DeleteMeasureDetailRecord($id, $db){
    $query = "
            Delete
                FROM measure_record_detail
            WHERE  detail_id = " . $id . "
          
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

}