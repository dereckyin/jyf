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

function UpdateLoadingDateArriveHistory($date_arrive, $id, $conn){
    $sql = "SELECT id, date_arrive FROM loading_date_history  where loading_id in ($id)";
    $result = mysqli_query($conn, $sql);

    // die if SQL statement failed
    while ($row = mysqli_fetch_array($result)) {
        $loading_id = $row['id'];
        $date_arrive_ary = explode(",", $row['date_arrive']);

        if (!in_array($date_arrive, $date_arrive_ary)) {
            array_push($date_arrive_ary, $date_arrive);
        }

        $date_arrive_str = ltrim(implode(",", $date_arrive_ary), ",");


        $sql = "update loading_date_history set 
                                            date_arrive = '$date_arrive_str'
                                    where id = $loading_id";
        $query = $conn->query($sql);
    }

}

switch ($method) {

    case 'POST':

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

        $last_id = 0;
        $query = "INSERT INTO measure_ph(date_encode, date_arrive, currency_rate, remark, crt_time, crt_user) values(?, ?, ?, ?, now(), ?)";


        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssdss",
            $date_encode,
            $date_cr,
            $currency_rate,
            $remark,
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

        // for loading
        $query = "UPDATE loading
            SET
                measure_num = " . $last_id . ", date_arrive = '" . $date_cr . "'  WHERE id in (" . $measure_container_id . ")";

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

        UpdateLoadingDateArriveHistory($date_cr, $measure_container_id, $conn);

        // for record
        for ($i = 0; $i < count($detail_array); $i++) {

            $kilo = ($detail_array[$i]['kilo'] == '') ? 0 : $detail_array[$i]['kilo'];
            $cuft = ($detail_array[$i]['cuft'] == '') ? 0 : $detail_array[$i]['cuft'];
            $kilo_price = ($detail_array[$i]['kilo_price'] == '') ? 0 : $detail_array[$i]['kilo_price'];
            $cuft_price = ($detail_array[$i]['cuft_price'] == '') ? 0 : $detail_array[$i]['cuft_price'];
            // $charge = ($kilo * $kilo_price > $cuft * $cuft_price ? $kilo * $kilo_price : $cuft * $cuft_price);
            $charge = ($detail_array[$i]['charge'] == '') ? 0 : $detail_array[$i]['charge'];

            $cus = isset($detail_array[$i]['customer']) ? $detail_array[$i]['customer'] : "";
            $customer = $cus;

            $query = "INSERT INTO measure_detail (measure_id, customer, kilo, cuft, kilo_price, cuft_price, charge, crt_user, crt_time)
                            values(?, ?, ?, ?, ?, ?, ?, ?, now())";

            // prepare the query
            $stmt = $conn->prepare($query);

            $stmt->bind_param(
                "isddddds",
                $last_id,
                $customer,
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
