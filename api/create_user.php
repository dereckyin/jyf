<?php
// required headers
header("Access-Control-Allow-Origin: https://webmatrix.myvnc.com/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate product object
$user = new User($db);
 
// get posted data
//$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$username = (isset($_POST['username']) ?  $_POST['username'] : "");
$email = (isset($_POST['email']) ?  $_POST['email'] : "");
$password = (isset($_POST['password1']) ?  $_POST['password1'] : "");

$user->username = $username;
$user->email = $email;
$user->password = $password;

$user->status = 0;
$user->phili = 0;
$user->status_1 = 0;
$user->status_2 = 0;
$user->taiwan_read = 0;
$user->phili_read = 0;
$user->report1 = 0;
$user->report2 = 0;
$user->airship = 0;
$user->airship_read = 0;
$user->sea_expense = 0;
$user->sea_expense_v2 = 0;
$user->gcash_expense_sea = 0;
$user->gcash_expense_sea_2 = 0;
$user->sea_feliix = 0;
$user->parts_feliix = 0;
$user->is_admin = 0;


// recaptchar
if(isset($_POST['g_recaptcha_response'])){
  $captcha=$_POST['g_recaptcha_response'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $secretKey = "6LdU3dUUAAAAAPZipKQ0kC3_hWk_P6bY37yZt87F";
    $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
    $responseKeys = json_decode($response,true);
    if(intval($responseKeys["success"]) !== true) {
        $cap = 1;
    }
}
 
// create the user
if(
    !empty($user->username) &&
    !empty($user->email) &&
    !empty($user->password) &&
    $cap == 1 &&
    !$user->userExists() &&
    $user->create()
){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>