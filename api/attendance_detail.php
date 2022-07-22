<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
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

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$username = (isset($_GET['username']) ?  $_GET['username'] : '');
$date = (isset($_GET['date']) ?  $_GET['date'] : '');

$merged_results = array();

/* fetch data */

$query = "SELECT  id, username, '' department, '' title, duty_date, TIME_FORMAT(duty_time, '%h:%i %p') duty_time, location, COALESCE(on_duty.pic_url, '') pic_url, COALESCE(remark, '') remark, COALESCE(`explain`, '') exp, on_duty.duty_type FROM  on_duty  WHERE duty_date = '" . $date . "' AND  on_duty.username = '" . $username . "' ORDER BY duty_type, on_duty.created_at ";

$stmt = $db->prepare( $query );
$stmt->execute();


while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row_id = $row['id'];
    $row_username = $row['username'];
    $row_date = $row['duty_date'];
    $row_time = $row['duty_time'];
    $row_location = $row['location'];
    $row_pic_url = $row['pic_url'];
    $row_remark = trim($row['remark']);
    $row_explain = $row['exp'];
    $row_department = $row['department'];
    $row_title = $row['title'];
    $row_duty_type = $row['duty_type'];
    $row_location_detail = GetLocation($row['location']);

    $merged_results[] = array( "id" => $row_id,
                                "username" => $row_username,
                                "duty_date" => $row_date,
                                "duty_time" => $row_time,
                                "location" => $row_location,
                                "pic_url" => $row_pic_url,
                                "department" => $row_department,
                                "title" => $row_title,
                                "remark" => $row_remark,
                                "duty_explain" => $row_explain,
                                "duty_type" => $row_duty_type,
                                "location_detail" => $row_location_detail
    );

}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

function GetLocation($loc)
{
    $location = "";
    switch ($loc) {
        case "A":
            $location = "Antel Office";
            break;
        case "M":
            $location = "Main Office";
            break;
        case "T":
            $location = "Taiwan Office";
            break;
        case "B":
            $location = "Shangri-La Store";
            break;
        case "C":
            $location = "Caloocan Warehouse";
            break;
        case "D":
            $location = "Installation";
            break;
        case "E":
            $location = "Client Meeting";
            break;
        case "F":
                $location = "Others";
                break;
        case "W":
                $location = "Warehouse";
                break;
    }

    return $location;
}