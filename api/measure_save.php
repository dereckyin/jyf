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
include_once 'objects/measure.php';
include_once 'objects/loading.php';
include_once 'objects/measure_history.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate loading object
$measure = new Measure($db);
$loading = new Loading($db);
$measure_history = new MeasureHistory($db);
 
// get posted data
//$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$date_encode = stripslashes($_POST["date_encode"]);
$date_cr = stripslashes($_POST["date_cr"]);
$loading_id = stripslashes($_POST["loading_id"]);
$currency_rate = stripslashes($_POST["currency_rate"]);
$remark = stripslashes($_POST["remark"]);
$customer = stripslashes($_POST["customer"]);

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user = $decoded->data->username;

        $measure->date_encode = $date_encode;
        $measure->date_arrive = $date_cr;
        $measure->currency_rate = $currency_rate;
        $measure->remark = $remark;
        $measure->crt_user = $user;

        $measure_id = $measure->Add();

        if(empty($measure_id))
        {
            http_response_code(501);
            return;
        }

        $loading->SetLoadingMeasure($measure_id, $loading_id);

        $cust = explode(",", $customer);

        $arr = "";
        foreach ($cust as $arr) {
            $values = explode("|", $arr);

            $measure_history->measure_id = $measure_id;
            $measure_history->customer = $values[0];
            $measure_history->kilo = $values[1];
            $measure_history->cuft = $values[2];
            $measure_history->price_kilo = $values[3];
            $measure_history->price_cuft = $values[4];

            $measure_history->Add();

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