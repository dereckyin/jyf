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
        $user_id = $decoded->data->id;
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

$account = (isset($_GET['account']) ?  $_GET['account'] : 0);
$category = (isset($_GET['category']) ?  $_GET['category'] : '');
$sub_category = (isset($_GET['sub_category']) ?  $_GET['sub_category'] : '');
$start_date = (isset($_GET['start_date']) ?  $_GET['start_date'] : '');
$end_date = (isset($_GET['end_date']) ?  $_GET['end_date'] : '');
$keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : '');
$select_date_type =(isset($_GET['select_date_type']) ?  $_GET['select_date_type'] : 0);

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
//$size = (isset($_GET['size']) ?  $_GET['size'] : "");


$merged_results = array();



$query = "SELECT *, 0 i1, 0 i2, 0 i3, 0 o1, 0 o2, 0 o3, 0 ai, 0 ao from price_record_salary where 1 = 1 ";
$sql = "";
$sql2 = "";
$sql3 = "";
            if($account!=0) {
                $sql = $sql . " and account = '$account' ";
            }else{
                $sql = $sql . " and account !=0 ";
            }
            
            if($select_date_type == 0){
                
            if(!empty($start_date)) {
                $sql = $sql . " and created_at >= '$start_date' ";
            }

            if(!empty($end_date)) {
                $sql = $sql . " and created_at < date_add('$end_date', interval 1 day) ";
            }
            }
            
            if($select_date_type == 1){
                
            if(!empty($start_date)) {
                $sql = $sql . " and paid_date >= '$start_date' ";
            }

            if(!empty($end_date)) {
                $sql = $sql . " and paid_date < date_add('$end_date', interval 1 day) ";
            }
            }
            
            if(!empty($category)) {
                $sql = $sql . " and category = '$category' ";
            }
            
            if(!empty($sub_category)) {
                $sql = $sql . " and sub_category = '$sub_category' ";
            }
            
            if(!empty($keyword)) {
                $sql2 = "or remarks like '%$keyword%' and 1 = 1".$sql;
                $sql3 = "or payee like '%$keyword%' and 1 = 1".$sql;
                $sql = $sql . " and details like '%$keyword%'";
            }
            
            $query = $query.$sql.$sql2.$sql3." order by created_at asc";

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}


//if(!empty($_GET['size'])) {
//    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
//    if(false === $size) {
//        $size = 10;
//    }
//
//    $offset = ($page - 1) * $size;
//
//    $query = $query . " LIMIT " . $offset . "," . $size;
//}


$stmt = $db->prepare( $query );
$stmt->execute();



//while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//    array_push($merged_results,$row);
//}

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

$_result = array();

$i1 = 0.0;
$i2 = 0.0;
$i3 = 0.0;

$o1 = 0.0;
$o2 = 0.0;
$o3 = 0.0;

$ai = 0.0;
$ao = 0.0;

foreach ($merged_results as &$value) {
    $value['pic_array'] = GetPicArray($value['pic_url']);

    if($value['is_enabled'] == true) 
    {
        if($value['account'] == 1)
        {
            $i1 += $value['cash_in'];
            $o1 += $value['cash_out'];
        }

        if($value['account'] == 2)
        {
            $i2 += $value['cash_in'];
            $o2 += $value['cash_out'];
        }

        if($value['account'] == 3)
        {
            $i3 += $value['cash_in'];
            $o3 += $value['cash_out'];
        }

        if($value['account'] == 1 || $value['account'] == 2 || $value['account'] == 3)
        {
            $ai += $value['cash_in'];
            $ao += $value['cash_out'];
        }
    }

    $_result[] = $value;
}

if(count($_result) > 0)
{
    $_result[0]['i1'] = $i1;
    $_result[0]['o1'] = $o1;
    $_result[0]['i2'] = $i2;
    $_result[0]['o2'] = $o2;
    $_result[0]['i3'] = $i3;
    $_result[0]['o3'] = $o3;
    $_result[0]['ai'] = $ai;
    $_result[0]['ao'] = $ao;
}

echo json_encode($_result, JSON_UNESCAPED_SLASHES);

function GetPicArray($pic_url){
    if(trim($pic_url) == '') {
        return array();
    }
    $merged_results = explode(",", $pic_url);
    $_result = array();
    $id = 1;
    foreach ($merged_results as &$value) {
         $_result[] = array(
             "is_checked" => true,
             "id" => $id++,
             "pic_url" => $value,
         );
     }
     return $_result;
 }