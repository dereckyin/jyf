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
include_once 'objects/receive_record.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate loading object
$receive_record = new ReceiveRecord($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$date_start = (isset($_POST['date_start']) ?  $_POST['date_start'] : '');
$date_end = (isset($_POST['date_end']) ?  $_POST['date_end'] : '');
$container_number = (isset($_POST['container_number']) ?  $_POST['container_number'] : '');
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            if($date_start == '' && $date_end == '')
            {
                $date_start = date('Y');

                $this_year = date("Y/m/d",strtotime($date_start . '-01-01' . " first day of 0 year"));
                $last_year = date("Y/m/d",strtotime($date_start . '-01-01' . " first day of 1 year"));

                $date_start    = $this_year;
                $date_end      = $last_year;
            }

            $recode = $receive_record->TaiwanPayQueryDetail($date_start, $date_end, $container_number);
           
            // response in json format
            echo json_encode(
                $recode);
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
}
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}

?>