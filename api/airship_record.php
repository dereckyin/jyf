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
$size  = (isset($_GET['size']) ? $_GET['size'] : 25);

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

        $query = "SELECT ss.id, 
                        date_receive, 
                        customer, 
                        address, 
                        description, 
                        quantity, 
                        kilo, 
                        supplier,
                        flight,
                        flight_date,
                        currency,
                        amount,
                        amount_php,
                        total,
                        total_php,
                        pay_date,
                        pay_status,
                        payee,
                        date_arrive,
                        receiver,
                        remark,
                        ss.`status` from airship_records ss 
                        where 1=1  ";

        $query_cnt = "SELECT count(*) as cnt from airship_records ss 
                        where 1=1  ";

        if($start_date!='') {
            $query = $query . " and ss.date_arrive >= '$start_date' ";
            $query_cnt = $query_cnt . " and ss.date_arrive >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.date_arrive <= '$end_date" . "T23:59:59' ";
            $query_cnt = $query_cnt . " and ss.date_arrive <= '$end_date" . "T23:59:59' ";
        }

        if (!empty($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        // order 
        $query = $query . " order by ss.date_receive  ";

        if (!empty($_GET['size'])) {
            $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
            if (false === $size) {
                $size = 10;
            }

            $offset = ($page - 1) * $size;

            $query = $query . " LIMIT " . $offset . "," . $size;
        }

        $cnt = 0;
        $stmt_cnt = $db->prepare( $query_cnt );
        $stmt_cnt->execute();

        while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
            $cnt = $row['cnt'];
        }


        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $date_receive = $row['date_receive'];
            $customer = $row['customer'];
            $address = $row['address'];
            $description = $row['description'];
            $quantity = $row['quantity'];
            $kilo = $row['kilo'];
            $supplier = $row['supplier'];
            $flight = $row['flight'];
            $flight_date = $row['flight_date'];
            $currency = $row['currency'];
            $total = $row['total'];
            $total_php = $row['total_php'];
            $amount_php = $row['amount_php'];
            $amount = $row['amount'];
            $pay_date = $row['pay_date'];
            $pay_status = $row['pay_status'];
            $payee = $row['payee'];
            $date_arrive = $row['date_arrive'];
            $receiver = $row['receiver'];
            $remark = $row['remark'];
            $status = $row['status'];

            $items = GetSalesDetail($id, $db, 'n');
            $items_php = GetSalesDetail($id, $db, 'p');
           
            $merged_results[] = array( 
                "is_edited" => 1,
                "id" => $id,
                "date_receive" => $date_receive,
                "customer" => $customer,
                "address" => $address,
                "description" => $description,
                "quantity" => $quantity,
                "kilo" => $kilo,
                "supplier" => $supplier,
                "flight" => $flight,
                "flight_date" => $flight_date,
                "currency" => $currency,
                "total" => $total,
                "total_php" => $total_php,
                "pay_date" => $pay_date,
                "pay_status" => $pay_status,
                "amount" => $amount,
                "amount_php" => $amount_php,
                "payee" => $payee,
                "date_arrive" => $date_arrive,
                "receiver" => $receiver,
                "remark" => $remark,
                "status" => $status,
                "items" => $items,
                "items_php" => $items_php,
                "cnt" => $cnt,
            );
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



function GetSalesDetail($sales_id, $db, $type){
    $query = "
            SELECT 0 as is_checked, id, title, qty, price 
                FROM airship_records_detail
            WHERE  airship_id = " . $sales_id . "
            AND `status` <> -1 AND type = '" . $type . "'
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $title = $row['title'] == "" ? "" : $row['title'];
        $qty = $row['qty'] == "" ? "" : $row['qty'];
        $price = $row['price'] == "" ? "" : $row['price'];
   
       
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "title" => $title,
            "qty" => $qty,
            "price" => $price,
           
        );
    }

    return $merged_results;
}
