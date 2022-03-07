<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$keyword = (isset($_GET['keyword']) ? $_GET['keyword'] : "");
$start_date = (isset($_GET['start_date']) ? $_GET['start_date'] : "");
$end_date = (isset($_GET['end_date']) ? $_GET['end_date'] : "");
$page = (isset($_GET['page']) ? $_GET['page'] : 1);

$merged_results = array();

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';


use Google\Cloud\Storage\StorageClient;

$database = new Database();
$db = $database->getConnection();

$conf = new Conf();

use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
}
else
{

    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $query = "SELECT id, `status`, payment from details_ntd_php ss where 1=1  ";

        if($start_date!='') {
            $query = $query . " and ss.crt_time >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.crt_time <= '$end_date" . " 23:59:59' ";
        }

        if($keyword != '')
            $query .= " AND (LOWER(JSON_EXTRACT(payment, \"$.client_name\")) like '%" . strtolower($keyword) . "%' or LOWER(JSON_EXTRACT(payment, \"$.payee_name\")) like '%" . strtolower($keyword) . "%' or LOWER(JSON_EXTRACT(payment, \"$.remark\")) like '%" . strtolower($keyword) . "%' or LOWER(JSON_EXTRACT(payment, \"$.payee\")) like '%" . strtolower($keyword) . "%') ";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }

        // response in json format
        echo json_encode($merged_results);
      
    }
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
