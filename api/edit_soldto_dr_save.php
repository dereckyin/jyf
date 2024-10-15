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

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;


switch ($method) {

    case 'POST':
        $id = (isset($_POST['id']) ? $_POST['id'] : 0);
        $encode = (isset($_POST["encode"]) ?  $_POST["encode"] : "");
        $encode_edit = (isset($_POST["encode_edit"]) ?  $_POST["encode_edit"] : "");
        $record_cust_json = isset($_POST["record_cust"]) ? $_POST["record_cust"] : "";
        $record_cust = json_decode($record_cust_json, true);
        $record_cust_edit_json = isset($_POST["record_cust_edit"]) ? $_POST["record_cust_edit"] : "";
        $record_cust_edit = json_decode($record_cust_edit_json, true);

        $last_id = $id;
        $query = "update measure_detail set ";

        if($encode != $encode_edit){
            $query .= "encode = :encode, ";
        }

        if($record_cust[0]['cust'] != $record_cust_edit[0]['cust'] && $record_cust[0]['kind'] == "measure_detail"){
            $query .= "customer = :record_cust, ";
        }

        $query .= "mdf_time = now(), 
                    mdf_user = :user
                    where id = :id";
        try {
            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            if($encode != $encode_edit){
                $stmt->bindParam(':encode', $encode_edit);
            }
            
            if($record_cust[0]['cust'] != $record_cust_edit[0]['cust'] && $record_cust[0]['kind'] == "measure_detail"){
                $stmt->bindParam(':record_cust', $record_cust_edit[0]['cust']);
            }

            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':id', $id);
        
            // execute the query, also check if query was successful
            if ($stmt->execute()) {

        
            } else {
                http_response_code(501);
                echo json_encode("Failure1 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $stmt->errorInfo());
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());

            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        break;

}

http_response_code(200);
echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));


