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
$ids = (isset($_GET['ids']) ?  $_GET['ids'] : "");
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
        if(!empty($ids))
        {
            $merged_results = array();
            
            http_response_code(200);
            $recode = $receive_record->GetReceiveRecordByBatchNumber($ids);

            $i = 0;
            foreach ($recode as $row) 
            {
                $i++;
                $rec = array();
                $rec[]=array(
                    "id" => $row['id'],
                    "date_receive" => rtrim($row['date_receive'], "<br>"), 
                    "customer" => $row['customer'], 
                    "description" => rtrim($row['description'], "<br>"), 
                    "quantity" => rtrim($row['quantity'], "<br>"), 

                    "cust" => "",
           
                    "supplier" => rtrim($row['supplier'], "<br>"), 
                    "remark" => rtrim($row['remark'], "<br>"),
                    "container" => rtrim($row['container'], "<br>"),
                    "date_arrive" => rtrim($row['date_arrive'], "<br>"),
                );


                // add record
                $new = [
                    "order" => $i,
                    "is_checked" => 0,
                    "group_id" => 0,
                    "kilo" => "", 
                    "cuft" => "", 
                    "kilo_price" => "", 
                    "cuft_price" => "", 
                    "charge" => "",
                    "record" => $rec,
                ];
                $merged_results[] = $new;
            }
            // response in json format
            echo json_encode($merged_results);
        }
        else
        {
            $merged_results = [];
            http_response_code(200);
            json_encode(
                $merged_results);
         
        }
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