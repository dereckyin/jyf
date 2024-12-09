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
//$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$id = stripslashes($_POST["id"]);

$date_receive = stripslashes($_POST["date_receive"]);
$customer = stripslashes($_POST["customer"]);
$email_customer = stripslashes($_POST["email_customer"]);
$description = stripslashes($_POST["description"]);
$quantity = stripslashes($_POST["quantity"]);
$supplier = stripslashes($_POST["supplier"]);
$email = stripslashes($_POST["email"]);
$mail_note = stripslashes($_POST["mail_note"]);
$kilo = stripslashes($_POST["kilo"]);
$cuft = stripslashes($_POST["cuft"]);
$taiwan_pay = stripslashes($_POST["taiwan_pay"]);
$courier_money = stripslashes($_POST["courier_money"]);
$remark = stripslashes($_POST["remark"]);

$customer = trim($customer);
$supplier = trim($supplier);

$receive_record->date_receive = $date_receive;
$receive_record->customer = $customer;
$receive_record->email_customer = $email_customer;
$receive_record->description = $description;
$receive_record->quantity = $quantity;
$receive_record->supplier = $supplier;
$receive_record->email = $email;
$receive_record->mail_note = $mail_note;
$receive_record->kilo = $kilo;
$receive_record->cuft = $cuft;
$receive_record->taiwan_pay = $taiwan_pay;
$receive_record->courier_money = $courier_money;
$receive_record->remark = $remark;
$receive_record->id = $id;

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user = $decoded->data->username;

        $receive_record->mdf_user = $user;
        $receive_record->UpdateReceiveRecordById($id);
        http_response_code(200);
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