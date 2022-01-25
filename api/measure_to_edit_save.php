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
     
  
        $detail = isset($_POST["detail"]) ? $_POST["detail"] : "";
        $detail_array = json_decode($detail, true);

        $ids = "";
        $recs = "";

        $conn->begin_transaction();
    
        $id = $detail_array['id'] == '' ? 0 : $detail_array['id'];

        $kilo = ($detail_array['kilo'] == '') ? 0 : $detail_array['kilo'];
        $cuft = ($detail_array['cuft'] == '') ? 0 : $detail_array['cuft'];
        $kilo_price = ($detail_array['kilo_price'] == '') ? 0 : $detail_array['kilo_price'];
        $cuft_price = ($detail_array['cuft_price'] == '') ? 0 : $detail_array['cuft_price'];
        $customer = ($detail_array['customer'] == '') ? "" : $detail_array['customer'];
        //$charge = ($kilo * $kilo_price > $cuft * $cuft_price ? $kilo * $kilo_price : $cuft * $cuft_price);
        $charge = ($detail_array['charge'] == '') ? 0 : $detail_array['charge'];

        $query = "update measure_detail set 
                    customer = ?, 
                    kilo = ?, 
                    cuft = ?, 
                    kilo_price = ?, 
                    cuft_price = ?, 
                    charge = ?, 
                    mdf_user = ?, 
                    mdf_time = now()
                    where id = ?";

        // prepare the query
        $stmt = $conn->prepare($query);

        $stmt->bind_param(
            "sdddddsi",
            $customer,
            $kilo,
            $cuft,
            $kilo_price,
            $cuft_price,
            $charge,
            $user,
            $id
        );


        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $conn->rollback();
                http_response_code(501);
                echo json_encode("Failure3 at " . date("Y-m-d") . " " . date("h:i:sa"));
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

        break;

}

http_response_code(200);
echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
// Close connection
mysqli_close($conn);

