<?php
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
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$keyword = (isset($_GET['keyword']) ? $_GET['keyword'] : "");

// if jwt is not empty
if($jwt){
 
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // get data with group_id first
        $query = "
            SELECT pick_group.id, pick_group.group_id, pick_group.measure_detail_id
                FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id
            WHERE  group_id <> 0 ";

        if($keyword == 'N')
            $query .= " AND measure_detail.pickup_status = '' ";

        if($keyword == 'A')
            $query .= " AND measure_detail.pickup_status = 'C' and measure_detail.payment_status = '' ";

        if($keyword == '')
            $query .= " AND NOT (measure_detail.pickup_status = 'C' and measure_detail.payment_status = 'C') ";

        $query .= " order by group_id desc";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $pre_group_id = 0;
        $id = 0;
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            if($row['group_id'] != $pre_group_id && $pre_group_id != 0)
            {
                $merged_results[] = array( 
                    "is_checked" => 0,
                    "id" => $row['id'],
                    "order" => $row['id'],
                    "group_id" => $pre_group_id,
                    "measure" => $items,
                    "ar" => GetAr($items),
                    "ar_amount" => GetArAmount($items),
                );

                $items = [];
                $pre_group_id = $row['group_id'];
            }

            $id = $row['id'];
            $group_id = $row['group_id'];
            $measure_detail_id = $row['measure_detail_id'];

            $mes = GetMeasureDetail($measure_detail_id, $db);
            if(!empty($mes))
                $items = array_merge($items, $mes);
        }

        if($id != 0)
        {
            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "order" => $id,
                "group_id" => $group_id,
                "measure" => $items,
                "ar" => GetAr($items),
                "ar_amount" => GetArAmount($items),
            );
        }

        // get data without group_id 
        $query = "
            SELECT pick_group.id, pick_group.group_id, pick_group.measure_detail_id
                FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id
            WHERE  group_id = 0";

        if($keyword == 'N')
            $query .= " AND measure_detail.pickup_status = '' ";

        if($keyword == 'A')
            $query .= " AND measure_detail.pickup_status = 'C' and measure_detail.payment_status = '' ";

        if($keyword == '')
            $query .= " AND NOT (measure_detail.pickup_status = 'C' and measure_detail.payment_status = 'C') ";

        $query .= " order by group_id desc";

        $stmt = $db->prepare($query);
        $stmt->execute();

        $id = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $group_id = $row['group_id'];
            $measure_detail_id = $row['measure_detail_id'];

            $items = GetMeasureDetail($measure_detail_id, $db);

            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "order" => $row['id'],
                "group_id" => $group_id,
                "measure" => $items,
                "ar" => GetAr($items),
                "ar_amount" => GetArAmount($items),
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

function GetMeasureDetail($measure_detail_id, $db){
    $query = "
            SELECT 0 as is_checked, id, kilo, cuft, kilo_price, cuft_price, charge, encode, encode_status, pickup_status, payment_status, DATE_FORMAT(crt_time, '%Y/%m/%d') crt_time
                FROM measure_detail
            WHERE  id = " . $measure_detail_id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $kilo = $row['kilo'] == 0 ? "" : $row['kilo'];
        $cuft = $row['cuft'] == 0 ? "" : $row['cuft'];
        $kilo_price = $row['kilo_price'] == 0 ? "" : $row['kilo_price'];
        $cuft_price = $row['cuft_price'] == 0 ? "" : $row['cuft_price'];
        $charge = $row['charge'] == 0 ? "" : $row['charge'];

        $encode = $row['encode'] == "" ? "" : $row['encode'];
        $encode_status = $row['encode_status'] == "" ? "" : $row['encode_status'];
        $pickup_status = $row['pickup_status'] == "" ? "" : $row['pickup_status'];
        $payment_status = $row['payment_status'] == "" ? "" : $row['payment_status'];
        $crt_time = $row['crt_time'] == "" ? "" : $row['crt_time'];

        $record = GetMeasureDetailRecord($row['id'], $db);
        $record_cust = GetMeasurePersonRecord($row['id'], $db);

        $payment = GetPaymentRecord($row['id'], $db);
        
        for($i = 0; $i < count($payment); $i++)
          $payment[$i]['ar'] = $charge;
        
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "order" => $id,
            "group_id" => 0,
            "kilo" => $kilo,
            "cuft" => $cuft,
            "kilo_price" => $kilo_price,
            "cuft_price" => $cuft_price,
            "charge" => $charge,
           "record" => $record,
           "encode" => $encode,
           "encode_status" => $encode_status,
           "pickup_status" => $pickup_status,
           "payment_status" => $payment_status,
           "record_cust" => $record_cust,
           "payment" => $payment,
           "crt_time" => $crt_time,
        );
    }

    return $merged_results;
}

function GetAr($array)
{
    $amount = 0;

    foreach($array as $item) {
        $amount += ($item['charge'] == "" ? 0 : $item['charge']);
    }

    return $amount;
}

function GetArAmount($array)
{
    $amount = 0;

    foreach($array as $item) {
        $amount += ($item['charge'] == "" ? 0 : $item['charge']);

        $payment = $item['payment'];
        foreach($payment as $pay) {
            $amount = $amount - ($pay['amount'] == "" ? 0 : $pay['amount']);
        }
    }

    return $amount;
}


function GetMeasurePersonRecord($id, $db){
    $query = "SELECT distinct coalesce(cp.customer, '') cust
                FROM measure_record_detail rd
                    left JOIN receive_record rc ON
                    rd.record_id = rc.id
                    left join contactor_ph cp on cp.id = rd.cust
               
                    WHERE rd.detail_id = " . $id . "
            AND rd.`status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
        $merged_results .= $row['cust'] . ",";
  
    }

    return rtrim($merged_results, ',');
}

function GetMeasureDetailRecord($id, $db){
    $query = "SELECT rd.detail_id, rc.id, rc.date_receive, rc.customer, rc.description, rc.quantity, rc.supplier, rc.remark, rd.cust cust_id, coalesce(cp.customer, '') cust, pick_date, pick_person, pick_note, pick_time, pick_user
                FROM measure_record_detail rd
                    left JOIN receive_record rc ON
                    rd.record_id = rc.id
                    left join contactor_ph cp on cp.id = rd.cust
               
                    WHERE rd.detail_id = " . $id . "
            AND rd.`status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $measure_id = $row['detail_id'];
        $id = $row['id'];
        $date_receive = $row['date_receive'];
        $customer = $row['customer'];
        $description = $row['description'];
        $quantity = $row['quantity'];
        $supplier = $row['supplier'];
        $remark = $row['remark'];
        $cust = $row['cust'];
        $cust_id = $row['cust_id'];
        $pick_date = $row['pick_date'];
        $pick_person = $row['pick_person'];
        $pick_note = $row['pick_note'];
        $pick_time = $row['pick_time'];
        $pick_user = $row['pick_user'];

        $merged_results[] = array(
            "id" => $id,
            "date_receive" => $date_receive,
            "customer" => $customer,
            "description" => $description,
            "quantity" => $quantity,
            "supplier" => $supplier,
            "remark" => $remark,
            "cust" => $cust,
            "cust_id" => $cust_id,
            "pick_date" => $pick_date,
            "pick_person" => $pick_person,
            "pick_note" => $pick_note,
            "pick_time" => $pick_time,
            "pick_user" => $pick_user,
            "measure_id" => $measure_id,
          
        );
    }

    return $merged_results;
}


function GetPaymentRecord($id, $db){
    $query = "SELECT id, `type`, issue_date, payment_date, person, amount, remark
                FROM payment
                                 
                    WHERE detail_id = " . $id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
        $id = $row['id'];
        $type = $row['type'];
        $issue_date = $row['issue_date'];
        $payment_date = $row['payment_date'];
        $person = $row['person'];
        $amount = $row['amount'];
        $remark = $row['remark'];
       

        $merged_results[] = array(
            "id" => $id,
            "type" => $type,
            "issue_date" => $issue_date,
            "payment_date" => $payment_date,
            "person" => $person,
            "amount" => $amount,
            "remark" => $remark,
            "ar" => "",
        );
    }

    return $merged_results;
}
?>