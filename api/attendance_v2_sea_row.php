<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$subquery = "";

$merged_results = array();

$mdate = isset($_GET['mdate']) ? $_GET['mdate'] : date("Y/m/d");
$edate = isset($_GET['edate']) ? $_GET['edate'] : date('Y/m/d',strtotime("-1 days"));

$merged_results = array();

$query = "SELECT id, username, duty_date, duty_time, location, duty_type 
FROM on_duty_v2 
WHERE  duty_date in( '" . $mdate . "' , '" . $edate . "') AND duty_type in('A', 'B') ";

$stmt = $db->prepare( $query);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC))
    $merged_results[] = $row;


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