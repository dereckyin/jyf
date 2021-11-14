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
        $date_encode = (isset($_POST["date_encode"]) ?  $_POST["date_encode"] : "");
        $date_cr = isset($_POST["date_cr"]) ? $_POST["date_cr"] : "";
        $measure_container_id = isset($_POST["measure_container_id"]) ? $_POST["measure_container_id"] : "";
        $currency_rate = isset($_POST["currency_rate"]) ? $_POST["currency_rate"] : "";
        $remark = isset($_POST["remark"]) ? $_POST["remark"] : "";
        $detail = isset($_POST["detail"]) ? $_POST["detail"] : "";
        $detail_array = json_decode($detail, true);

        $ids = "";
        $recs = "";

        $conn->begin_transaction();

        $last_id = $id;
        $query = "update measure_ph set 
                    date_encode = ?, 
                    date_arrive = ?, 
                    currency_rate = ?,
                    remark = ?, 
                    mdf_time = now(), 
                    mdf_user = ?
                  where id = ?";


        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssdssi",
            $date_encode,
            $date_cr,
            $currency_rate,
            $remark,
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
/*
        // for pickup
        $query = "UPDATE loading
            SET
                measure_num = " . $last_id . " WHERE id in (" . $measure_container_id . ")";

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
*/
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


        // add again
        for ($i = 0; $i < count($detail_array); $i++) {

            $kilo = ($detail_array[$i]['kilo'] == '') ? 0 : $detail_array[$i]['kilo'];
            $cuft = ($detail_array[$i]['cuft'] == '') ? 0 : $detail_array[$i]['cuft'];
            $kilo_price = ($detail_array[$i]['kilo_price'] == '') ? 0 : $detail_array[$i]['kilo_price'];
            $cuft_price = ($detail_array[$i]['cuft_price'] == '') ? 0 : $detail_array[$i]['cuft_price'];
            $charge = ($kilo * $kilo_price > $cuft * $cuft_price ? $kilo * $cuft_price : $cuft * $cuft_price);

            $query = "INSERT INTO measure_detail (measure_id, kilo, cuft, kilo_price, cuft_price, charge, crt_user, crt_time)
                            values(?, ?, ?, ?, ?, ?, ?, now())";

            // prepare the query
            $stmt = $conn->prepare($query);

            $stmt->bind_param(
                "iddddds",
                $last_id,
                $kilo,
                $cuft,
                $kilo_price,
                $cuft_price,
                $charge,
                $user
            );


            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure3 at " . date("Y-m-d") . " " . date("h:i:sa"));
                    die();
                }
                else
                {
                    $last_measuer_id = mysqli_insert_id($conn);

                    for ($j = 0; $j < count($detail_array[$i]['record']); $j++) {
                        $record_id = $detail_array[$i]['record'][$j]['id'];
                        $cust = $detail_array[$i]['record'][$j]['cust'];

                        if($cust == '')
                            $cust = 0;
        
                        $query = "INSERT INTO measure_record_detail (detail_id, record_id, cust, crt_user, crt_time)
                                    values(?, ?, ?, ?, now())";
        
                        // prepare the query
                        $stmt = $conn->prepare($query);
        
                        $stmt->bind_param(
                            "iiis",
                            $last_measuer_id,
                            $record_id,
                            $cust,
                            $user
                        );

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



function DeleteMeasureDetailRecord($id, $db){
    $query = "
            Delete
                FROM measure_record_detail
            WHERE  detail_id = " . $id;

    $stmt1 = $db->prepare($query);

    $stmt1->execute();

}