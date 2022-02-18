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


use Google\Cloud\Storage\StorageClient;

require_once "db.php";

header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;
$user_id = $decoded->data->id;
    
switch ($method) {

    case 'POST':

        $pick_id = isset($_POST["pick_id"]) ? $_POST["pick_id"] : 0;
    
        $measure = isset($_POST["measure"]) ? $_POST["measure"] : "";
        $measure_array = json_decode($measure, true);

        $measure_a = isset($_POST["group_a"]) ? $_POST["group_a"] : "";
        $measure_a_array = json_decode($measure_a, true);

        $measure_b = isset($_POST["group_b"]) ? $_POST["group_b"] : "";
        $measure_b_array = json_decode($measure_b, true);


        $row = array();

        // get pick_group
        $query = "select measure_id, measure_detail_id from pick_group where id = " . $pick_id . " and `status` <> -1";
        $result = mysqli_query($conn,$query);

        $measure_id = 0;
        $measure_detail_id = 0;

        if(mysqli_num_rows($result) == 1)
        {
            $row = mysqli_fetch_object($result);
            $measure_id = $row->measure_id;
            $measure_detail_id = $row->measure_detail_id;
        }
        
        $conn->begin_transaction();

        // delete payment
        $query = "delete from payment where detail_id = " . $measure_array["id"];
        $stmt = $conn->prepare($query);

        if ($stmt === FALSE) {
            die ("Error: " . $conn->error);
         }
     
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

        // delete measure_record_detail
        $query = "delete
                    FROM measure_record_detail
                WHERE  detail_id = " . $measure_array["id"];
        $stmt = $conn->prepare($query);
     
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

        
        // delete measure_detail
        $query = "delete from measure_detail where measure_id = " . $measure_array["id"];
        $stmt = $conn->prepare($query);
 
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

        // delete pick_group
        $query = "update pick_group set `status` = -1, del_user = '" . $user . "', del_time = now() where measure_detail_id = " . $measure_array["id"];
        $stmt = $conn->prepare($query);
 
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

        $last_id = $measure_id;


        // add again group a
        $kilo = ($measure_a_array['kilo'] == '') ? 0 : $measure_a_array['kilo'];
        $cuft = ($measure_a_array['cuft'] == '') ? 0 : $measure_a_array['cuft'];
        $kilo_price = ($measure_a_array['kilo_price'] == '') ? 0 : $measure_a_array['kilo_price'];
        $cuft_price = ($measure_a_array['cuft_price'] == '') ? 0 : $measure_a_array['cuft_price'];
        $customer = ($measure_a_array['customer'] == '') ? "" : $measure_a_array['customer'];
        //$charge = ($kilo * $kilo_price > $cuft * $cuft_price ? $kilo * $kilo_price : $cuft * $cuft_price);
        $charge = ($measure_a_array['charge'] == '') ? 0 : $measure_a_array['charge'];

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

                for ($j = 0; $j < count($measure_array['record']); $j++) {
                    if($measure_array['record'][$j]['group'] == "A")
                    {
                        $record_id = $measure_array['record'][$j]['id'];
                        $cust = $measure_array['record'][$j]['cust'];

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

                $query = "INSERT INTO pick_group (group_id, measure_id, measure_detail_id, crt_user, crt_time)
                        values(0, ?, ?, ?, now())";

                // prepare the query
                $stmt = $conn->prepare($query);

                $stmt->bind_param(
                    "iis",
                    $measure_id,
                    $last_measuer_id,
                    $user
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

            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $conn->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }
        



        // add again group b
        $kilo = ($measure_b_array['kilo'] == '') ? 0 : $measure_b_array['kilo'];
        $cuft = ($measure_b_array['cuft'] == '') ? 0 : $measure_b_array['cuft'];
        $kilo_price = ($measure_b_array['kilo_price'] == '') ? 0 : $measure_b_array['kilo_price'];
        $cuft_price = ($measure_b_array['cuft_price'] == '') ? 0 : $measure_b_array['cuft_price'];
        $customer = ($measure_b_array['customer'] == '') ? "" : $measure_b_array['customer'];
        //$charge = ($kilo * $kilo_price > $cuft * $cuft_price ? $kilo * $kilo_price : $cuft * $cuft_price);
        $charge = ($measure_b_array['charge'] == '') ? 0 : $measure_b_array['charge'];

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

                for ($j = 0; $j < count($measure_array['record']); $j++) {
                    if($measure_array['record'][$j]['group'] == "B")
                    {
                        $record_id = $measure_array['record'][$j]['id'];
                        $cust = $measure_array['record'][$j]['cust'];

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

                $query = "INSERT INTO pick_group (group_id, measure_id, measure_detail_id, crt_user, crt_time)
                        values(0, ?, ?, ?, now())";

                // prepare the query
                $stmt = $conn->prepare($query);

                $stmt->bind_param(
                    "iis",
                    $measure_id,
                    $last_measuer_id,
                    $user
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

            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $conn->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }
    

        $conn->commit();

        break;

}

http_response_code(200);
echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
// Close connection
mysqli_close($conn);

