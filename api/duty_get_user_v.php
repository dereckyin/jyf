<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// if decode succeed, show user details
try {

    http_response_code(200);

    $merged_results = array();

    $query = "SELECT id, staff username from staff_list where status <> 'D' and punch = 1 order by staff";

    $stmt = $db->prepare( $query);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        $merged_results[] = $row;

    // response in json format
    echo json_encode($merged_results);
}

// if decode fails, it means jwt is invalid
catch (Exception $e){

    // set response code
    http_response_code(401);

    // show error message
    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
}

?>