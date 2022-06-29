<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/login_history.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);
$login_history = new LoginHistory($db);
// get posted data
//$data = json_decode(file_get_contents("php://input"));

$username = (isset($_POST['username']) ?  $_POST['username'] : "");
$password = (isset($_POST['password']) ?  $_POST['password'] : "");

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

 
// set product property values
$user->username = $username;
$user_exists = $user->userCanLogin();

if($user_exists)
    $login_history->uid = $user->id;
else
    $login_history->uid = 0;

$login_history->ip = $_SERVER['REMOTE_ADDR'];


 
// generate json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// check if email exists and if password is correct
if($user_exists && password_verify($password, $user->password) && $cap == 1 && ($user->status == 1 || $user->status_1 == 1 || $user->status_2 == 1 || $user->phili == 1)){
 
    $token = array(
       "iss" => $iss,
       "aud" => $aud,
       "iat" => $iat,
       "nbf" => $nbf,
       "data" => array(
           "id" => $user->id,
           "username" => $user->username,
           "email" => $user->email,
           "is_admin" => $user->is_admin,
           "status" => $user->status,
           "phili" => $user->phili,
           "status_1" => $user->status_1,
           "status_2" => $user->status_2,
           "taiwan_read" => $user->taiwan_read,
           "phili_read" => $user->phili_read,
           "report1" => $user->report1,
           "report2" => $user->report2,
           "airship" => $user->airship,
           "airship_read" => $user->airship_read,
           "sea_expense" => $user->sea_expense,
           "sea_expense_v2" => $user->sea_expense_v2,
       )
    );

    // write login log
    $login_history->status = "login";
    $login_history->create();
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
            array(
                "message" => "Success Login",
                "jwt" => $jwt,
                "uid" => passport_encrypt(base64_encode($user->username)),
                "pg" => ($user->status === "1" ? "main" : ""),
                "pg1" => ($user->status_1 === "1" ? "other" : ""),
                "pg2" => ($user->status_2 === "1" ? "other" : ""),
            )
        );
 
}
else if($user_exists && !password_verify($password, $user->password))
{
    $returnArray = array('error' => 'Wrong Username or Password');
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

    echo $jsonEncodedReturnArray;
}
else if(!$user_exists)
{
    $returnArray = array('error' => 'Wrong Username or Password');
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

    echo $jsonEncodedReturnArray;
}
// login failed
else{
    if($user->status == 0 && $user->status_1 == 0 && $user->status_2 == 0)
    {
        if($login_history->uid !== 0)
        {
            // write login log
            $login_history->status = "Wrong Username or Password";
            $login_history->create();
        }
        else
        {
            // write login log
            $login_history->status = $user->username . " not existed";
            $login_history->create();
        }


        $returnArray = array('error' => 'Permission Denied');
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    } else
    {
        // write login log
        $login_history->status = "Invalid user ID or password";
        $login_history->create();

        $returnArray = array('error' => 'Wrong Username or Password');
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    }

    echo $jsonEncodedReturnArray;
}



?>