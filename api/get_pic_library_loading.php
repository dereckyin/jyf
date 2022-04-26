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
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

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

require_once "db.php";

header('Access-Control-Allow-Origin: *');  
$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;

switch ($method) {
    case 'GET':
    $id = stripslashes((isset($_GET['id']) ?  $_GET['id'] : ""));
    $page = stripslashes((isset($_GET['page']) ?  $_GET['page'] : ""));
    $size = stripslashes((isset($_GET['size']) ?  $_GET['size'] : ""));

    $subquery = "";

    $merged_results = array();

    $key=array();

    $sql = "SELECT '' is_checked, gc.id pid, gc.batch_id, gc.gcp_name, r.date_receive, r.customer, r.supplier, r.quantity, r.remark, 'LOADING' `type` FROM gcp_storage_file gc LEFT JOIN receive_library r ON gc.batch_id = r.id WHERE gc.batch_type = 'LIBRARY' and gc.status <> -1 ";

    // $sql = "CALL createReceiveList(); ";
    // run SQL statement
    $result = mysqli_query($conn,$sql);

    /* fetch data */
    while($row = mysqli_fetch_assoc($result))
            $merged_results[] = $row;

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
    
    break;

    
}

// Close connection
mysqli_close($conn);
