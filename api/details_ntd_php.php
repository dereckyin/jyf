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

        $query = "SELECT ss.id, 
                        client_name, 
                        payee_name, 
                        amount, 
                        amount_php, 
                        rate, 
                        rate_yahoo, 
                        total_receive,
                        overpayment,
                        pay_date,
                        payee,
                        remark,
                        ss.`status` from details_ntd_php ss left join details_ntd_php_record sr
                        on ss.id = sr.sales_id
                        where 1=1  ";

        if($start_date!='') {
            $query = $query . " and sr.receive_date >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and sr.receive_date <= '$end_date" . " 23:59:59' ";
        }

        if($keyword != '')
            $query .= " AND (ss.client_name like '%" . $keyword . "%' or ss.payee_name like '%" . $keyword . "%' or ss.remark like '%" . $keyword . "%' or ss.payee like '%" . $keyword . "%') ";

        $query .= "group by
            ss.id, 
            ss.client_name, 
            ss.payee_name, 
            ss.amount,
            ss.amount_php,
            ss.rate,
            ss.rate_yahoo,
            ss.total_receive,
            ss.overpayment,
            ss.pay_date,
            ss.payee,
            ss.remark,
            ss.`status` ";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $client_name = $row['client_name'];
            $payee_name = $row['payee_name'];
            $amount = $row['amount'];
            $amount_php = $row['amount_php'];
            $rate = $row['rate'];
            $rate_yahoo = $row['rate_yahoo'];
            $total_receive = $row['total_receive'];
            $overpayment = $row['overpayment'];
            $pay_date = $row['pay_date'];
            $payee = $row['payee'];
            $remark = $row['remark'];
            $status = $row['status'];
 
            $items = GetSalesDetail($id, $db);
           
            $merged_results[] = array( 
                "is_edited" => 1,
                "id" => $id,
                "client_name" => $client_name,
                "payee_name" => $payee_name,
                "amount" => $amount,
                "amount_php" => $amount_php,
                "rate" => $rate,
                "rate_yahoo" => $rate_yahoo,
                "total_receive" => $total_receive,
                "overpayment" => $overpayment,
                "pay_date" => $pay_date,
                "payee" => $payee,
                "remark" => $remark,
                "details"=> $items,
                "status" => $status
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



function GetSalesDetail($sales_id, $db){
    $query = "
            SELECT 0 as is_checked, id, receive_date, payment_method, account_number, check_details, receive_amount
                FROM details_ntd_php_record
            WHERE  sales_id = " . $sales_id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $receive_date = $row['receive_date'] == "" ? "" : $row['receive_date'];
        $payment_method = $row['payment_method'] == "" ? "" : $row['payment_method'];
        $account_number = $row['account_number'] == "" ? "" : $row['account_number'];
        $check_details = $row['check_details'] == "" ? "" : $row['check_details'];
        $receive_amount = $row['receive_amount'] == "" ? "" : $row['receive_amount'];
    
       
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "receive_date" => $receive_date,
            "payment_method" => $payment_method,
            "account_number" => $account_number,
            "check_details" => $check_details,
           "receive_amount" => $receive_amount,
        );
    }

    return $merged_results;
}
