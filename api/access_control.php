<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : '');
$action = (isset($_POST['action']) ?  $_POST['action'] : 1);

$car_access1 = (isset($_POST['car_access1']) ?  $_POST['car_access1'] : '');
$car_access2 = (isset($_POST['car_access2']) ?  $_POST['car_access2'] : '');
$editable = (isset($_POST['editable']) ?  $_POST['editable'] : '');
$innova = (isset($_POST['innova']) ?  $_POST['innova'] : '');


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    if ($action == 1) {
        //select all
        try {
            $query = "SELECT car_access1, car_access2, innova, editable from access_control where id = 1";

            $stmt = $db->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    } else if ($action == 3) {
        //update
        try {
            $query = "UPDATE access_control
                        set car_access1 = :car_access1, 
                            car_access2 = :car_access2,
                            editable = :editable
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $id = 1;

            $car_access1 = htmlspecialchars(strip_tags($car_access1));
            $car_access2 = htmlspecialchars(strip_tags($car_access2));
            $editable = htmlspecialchars(strip_tags($editable));

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':car_access1', $car_access1);
            $stmt->bindParam(':car_access2', $car_access2);
            $stmt->bindParam(':editable', $editable);

            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    return true;
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    return false;
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                return false;
            }

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }    else if ($action == 4 && $innova != '') {
        //update
        try {
            $query = "UPDATE access_control
                        set innova = :innova
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $id = 1;

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':innova', $innova);
          

            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    return true;
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    return false;
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                return false;
            }

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }
}
