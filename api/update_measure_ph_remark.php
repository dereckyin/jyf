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


require_once '../vendor/autoload.php';

$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

require_once "db.php";


header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;

switch ($method) {

    case 'POST':
     
  
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        $remark = isset($_POST["remark"]) ? $_POST["remark"] : "";
     

        $query = "update measure_ph set 
                    notes = ?, 
                    mdf_user = ?, 
                    mdf_time = now()
                    where id = ?";

        // prepare the query
        $stmt = $conn->prepare($query);

        $stmt->bind_param(
            "ssi",
            $remark,
            $user,
            $id
        );


        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                http_response_code(501);
                echo json_encode("Failure3 at " . date("Y-m-d") . " " . date("h:i:sa"));
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
// Close connection
mysqli_close($conn);

