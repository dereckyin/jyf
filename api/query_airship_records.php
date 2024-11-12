<?php
error_reporting(0);
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
include_once 'objects/receive_record.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate loading object
$receive_record = new ReceiveRecord($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);


$date_start = (isset($_POST['date_start']) ?  $_POST['date_start'] : '');
$date_end = (isset($_POST['date_end']) ?  $_POST['date_end'] : '');
$pay_start = (isset($_POST['pay_start']) ?  $_POST['pay_start'] : '');
$pay_end = (isset($_POST['pay_end']) ?  $_POST['pay_end'] : '');
$flight_start = (isset($_POST['flight_start']) ?  $_POST['flight_start'] : '');
$flight_end = (isset($_POST['flight_end']) ?  $_POST['flight_end'] : '');
$arrive_start = (isset($_POST['arrive_start']) ?  $_POST['arrive_start'] : '');
$arrive_end = (isset($_POST['arrive_end']) ?  $_POST['arrive_end'] : '');

$customer = (isset($_POST['customer']) ?  $_POST['customer'] : '');
//$customer = urldecode($customer);
$supplier = (isset($_POST['supplier']) ?  $_POST['supplier'] : '');
//$supplier = urldecode($supplier);

$description = (isset($_POST['description']) ?  $_POST['description'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
$sort = (isset($_POST['sort']) ?  $_POST['sort'] : '');


// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $cus_str = "";
            $sup_str = "";
    
    
            if(!empty($customer)) {
                $customer = rtrim($customer, '||');
          
                $cust = explode("||", $customer);
    
                foreach ($cust as &$value) {
                    $value = addslashes(trim($value));
                    $cus_str .= " r.customer like '" . $value . "%' ESCAPE '|' or ";
                }
    
                $cus_str = rtrim($cus_str, 'or ');
      
            }
    
            if(!empty($supplier)) {
                $supplier = rtrim($supplier, '||');
        
                $sup = explode("||", $supplier);
    
                foreach ($sup as &$value) {
                    $value = addslashes(trim($value));
                    $sup_str .= " r.supplier like '" . $value . "%'  ESCAPE '|' or ";
    
                }
    
                $sup_str = rtrim($sup_str, 'or ');
           
    
            }
    
    
            $query = "SELECT r.id, 
                            date_receive, 
                            mode,
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
                            ratio,
                            total_php,
                            pay_date,
                            pay_status,
                            payee,
                            date_arrive,
                            receiver,
                            remark,
                            sn,
                            r.`status` from airship_records r 
                            where 1=1  ";

$query_cnt = "SELECT count(*) as cnt from airship_records r 
where 1=1  ";
    
if(!empty($date_start)) {
    $date_start = str_replace('/', '-', $date_start);

    $query = $query . " and r.date_receive >= '$date_start' ";
    $query_cnt = $query_cnt . " and r.date_receive >= '$date_start' ";
}

if(!empty($date_end)) {
    $date_end = str_replace('/', '-', $date_end);

    $query = $query . " and r.date_receive <= '$date_end' ";
    $query_cnt = $query_cnt . " and r.date_receive <= '$date_end' ";
}

if(!empty($pay_start)) {
    $pay_start = str_replace('/', '-', $pay_start);

    $query = $query . " and r.pay_date >= '$pay_start' ";
    $query_cnt = $query_cnt . " and r.pay_date >= '$pay_start' ";
}

if(!empty($pay_end)) {
    $pay_end = str_replace('/', '-', $pay_end);

    $query = $query . " and r.pay_date <= '$pay_end' ";
    $query_cnt = $query_cnt . " and r.pay_date <= '$pay_end' ";
}

if(!empty($flight_start)) {
    $flight_start = str_replace('/', '-', $flight_start);

    $query = $query . " and r.flight_date >= '$flight_start' ";
    $query_cnt = $query_cnt . " and r.flight_date >= '$flight_start' ";
}

if(!empty($flight_end)) {
    $flight_end = str_replace('/', '-', $flight_end);

    $query = $query . " and r.flight_date <= '$flight_end' ";
    $query_cnt = $query_cnt . " and r.flight_date <= '$flight_end' ";
}

if(!empty($arrive_start)) {
    $arrive_start = str_replace('/', '-', $arrive_start);

    $query = $query . " and r.date_arrive >= '$arrive_start' ";
    $query_cnt = $query_cnt . " and r.date_arrive >= '$arrive_start' ";
}

if(!empty($arrive_end)) {
    $arrive_end = str_replace('/', '-', $arrive_end);

    $arrive_end = $arrive_end . "T23:59:59 ";

    $query = $query . " and r.date_arrive <= '$arrive_end' ";
    $query_cnt = $query_cnt . " and r.date_arrive <= '$arrive_end' ";
}


    
            if(!empty($description)) {
                $query = $query . " and r.description like '%$description%' ";
                $query_cnt = $query_cnt . " and r.description like '%$description%' ";
            }
    
            if(!empty($remark)) {
                $query = $query . " and r.remark like '%$remark%' ";
                $query_cnt = $query_cnt . " and r.remark like '%$remark%' ";
            }
    
            if(!empty($sup_str)) {
                $query = $query . " and ($sup_str) ";
                $query_cnt = $query_cnt . " and ($sup_str) ";
            }
    
            if(!empty($cus_str)) {
                $query = $query . " and ($cus_str) ";
                $query_cnt = $query_cnt . " and ($cus_str) ";
            }
    
            if($sort == 'd') 
                $query = $query . " order by r.date_receive desc ";
            else
                $query = $query . " order by r.date_receive ";

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
            $mode = $row['mode'];
            $customer = $row['customer'];
            $address = $row['address'];
            $description = $row['description'];
            $quantity = $row['quantity'];
            $kilo = $row['kilo'];
            $supplier = $row['supplier'];
            $flight = $row['flight'];
            $flight_date = $row['flight_date'];
            $currency = $row['currency'];
            $ratio = $row['ratio'];
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
            $sn = $row['sn'];

            $items = GetSalesDetail($id, $db, 'n');
            $items_php = GetSalesDetail($id, $db, 'p');

            $export = GetExportRecord($row['id'], $db);
           
            $merged_results[] = array( 
                "is_edited" => 1,
                "id" => $id,
                "date_receive" => $date_receive,
                "mode" => $mode,
                "customer" => $customer,
                "address" => $address,
                "description" => $description,
                "quantity" => $quantity,
                "kilo" => $kilo,
                "supplier" => $supplier,
                "flight" => $flight,
                "flight_date" => $flight_date,
                "currency" => $currency,
                "ratio" => $ratio,
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
                "sn" => $sn,
                "cnt" => $cnt,

                "export" => $export,
            );
        }

        // response in json format
        echo json_encode($merged_results);
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


function GetExportRecord($id, $db){
    $query = "SELECT DATE_FORMAT(upd_time, '%Y/%m/%d') upd_time, file_export, adv, exp_dr from
               airship_records_export
                    WHERE measure_detail_id = " . $id . "
           
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $upd_time = $row['upd_time'];
        $file_export = $row['file_export'];
        $adv = $row['adv'];
        $exp_dr = $row['exp_dr'];
       
        $merged_results[] = array(
            "upd_time" => $upd_time,
            "file_export" => $file_export,
            "adv" => $adv,
            "exp_dr" => $exp_dr,
        );
    }

    return $merged_results;
}

?>