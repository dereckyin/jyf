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
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);


$date_start = (isset($_POST['date_start']) ?  $_POST['date_start'] : '');
$date_end = (isset($_POST['date_end']) ?  $_POST['date_end'] : '');
$container_number = (isset($_POST['container_number']) ?  $_POST['container_number'] : '');
$sold_to = (isset($_POST['sold_to']) ?  $_POST['sold_to'] : '');
$search = (isset($_POST['dr']) ?  $_POST['dr'] : '');

if(!empty($container_number)) {
    $container_number = rtrim($container_number, '||');
    $containers = explode("||", $container_number);
}

if(!empty($sold_to)) {
    $sold_to = rtrim($sold_to, '||');
    $solds = explode("||", $sold_to);
}

if($jwt){
 
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // get data with group_id first
        $query = "
            SELECT pick_group.id, pick_group.group_id, pick_group.measure_detail_id
                FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id
            WHERE  group_id <> 0 and pick_group.status = 2 and group_id IN (
            
                select group_id FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id
                WHERE group_id <> 0 and pick_group.status = 2 ";

        $query .= ") order by group_id desc";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $pre_group_id = 0;
        $pre_id = 0;
        $id = 0;
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            if($row['group_id'] != $pre_group_id && $pre_group_id != 0)
            {
                $merged_results[] = array( 
                    "is_checked" => 0,
                    "id" => $pre_id,
                    "order" => $pre_id,
                    "group_id" => $pre_group_id,
                    "measure" => UniformPaymentStatus($items),
                    "ar" => GetAr($items),
                    "ar_amount" => GetArAmount($items),
                    "payments" => GetPayments($items),
                    "measure_detail_id" => $measure_detail_id,
                );

                $items = [];
                $pre_group_id = $row['group_id'];
                $pre_id = $row['id'];
            }

            $id = $row['id'];
            $group_id = $row['group_id'];
            $pre_group_id = $row['group_id'];
            $pre_id = $row['id'];
            $measure_detail_id = $row['measure_detail_id'];

            if(!existsInArray($measure_detail_id, $items))
            {
                $mes = GetMeasureDetail($measure_detail_id, $group_id, $db);
                if(!empty($mes))
                {
                    $items = array_merge($items, $mes);
                }
            }
        }

        if($id != 0)
        {
            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "order" => $id,
                "group_id" => $group_id,
                "measure" => UniformPaymentStatus($items),
                "ar" => GetAr($items),
                "ar_amount" => GetArAmount($items),
                "payments" => GetPayments($items),
                "measure_detail_id" => $measure_detail_id,
            );
        }

        // get data without group_id 
        $query = "
            SELECT pick_group.id, pick_group.group_id, pick_group.measure_detail_id
                FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id
            WHERE  group_id = 0 and pick_group.status = 2 ";

        $query .= " order by group_id desc";

        $stmt = $db->prepare($query);
        $stmt->execute();

        $id = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $group_id = $row['group_id'];
            $measure_detail_id = $row['measure_detail_id'];

            $items = GetMeasureDetail($measure_detail_id, $group_id, $db);

            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "order" => $row['id'],
                "group_id" => $group_id,
                "measure" => $items,
                "ar" => GetAr($items),
                "ar_amount" => GetArAmount($items),
                "payments" => GetPayments($items),
                "measure_detail_id" => $measure_detail_id,
            );
        }

        if($date_end != '' && $date_start != '')
        {
            $merged_results = array_filter($merged_results, function($a) use ($date_start, $date_end) {
                $in_value = false;

                foreach($a['measure'] as $item)
                {
                    if($item['date_arrive'] <= $date_end && $item['date_arrive'] >= $date_start)
                    {
                        $in_value = true;
                    }
                
                }

                return $in_value;
            });
        }
/*
        if($date_start != '')
        {
            $merged_results = array_filter($merged_results, function($a) use ($date_start) {
                $in_value = false;

                foreach($a['measure'] as $item)
                {
                    if($item['date_arrive'] >= $date_start)
                    {
                        $in_value = true;
                    }
                
                }

                return $in_value;
            });
        }
*/
        if(isset($solds))
        {
            $merged_results = array_filter($merged_results, function($a) use ($solds) {
                $in_value = false;

                foreach($a['measure'] as $item)
                {
                    foreach($item['record_cust'] as $cust)
                    {
                        foreach($solds as $sold)
                        {
                            if(strpos(strtolower($cust), strtolower($sold)) !== false)
                            {
                                $in_value = true;
                            }
                        }
                    
                    }

                
                
                }

                return $in_value;
            });
        
        }

        if(isset($containers))
        {
            $merged_results = array_filter($merged_results, function($a) use ($containers) {
                $in_value = false;

                foreach($a['measure'] as $item)
                {
                    foreach($containers as $container)
                    {
                        if(strpos(strtolower($item['container_number']), strtolower($container)) !== false)
                        {
                            $in_value = true;
                        }
                    }
                
                }

                return $in_value;
            });
        
        }

        if($search != '')
        {
            $merged_results = array_filter($merged_results, function($a) use ($search) {
                $in_value = false;

                foreach($a['measure'] as $item)
                {
                    if(strpos(strtolower($item['encode']), strtolower($search)) !== false)
                    {
                        $in_value = true;
                    }
                
                }

                return $in_value;
            });
        
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

function UniformPaymentStatus($merged_results){
    // if any record of merged_result payment_status = 'C' then all merged_result payment_status = 'C'
    $payment_complete = false;
    for($i = 0; $i < count($merged_results); $i++){
        if($merged_results[$i]['payment_status'] == 'C'){
            $payment_complete = true;
        }
    }

    if($payment_complete){
        for($i = 0; $i < count($merged_results); $i++){
            $merged_results[$i]['payment_status'] = 'C';
        }
    }

    return $merged_results;
}

function GetMeasureDetail($measure_detail_id, $group_id, $db){
    $query = "
            SELECT distinct 0 as is_checked, measure_detail.id, kilo, cuft, kilo_price, cuft_price, charge, encode, encode_status, pickup_status, payment_status, DATE_FORMAT(measure_detail.crt_time, '%Y/%m/%d') crt_time, 
            (SELECT date_arrive FROM measure_ph WHERE measure_detail.measure_id = measure_ph.id) date_arrive,
(SELECT GROUP_CONCAT(container_number separator ', ') FROM loading WHERE loading.measure_num = measure_detail.measure_id) container_number, days, way, kilo_unit, kilo_amount, kilo_remark, cuft_unit, cuft_amount, cuft_remark
                FROM measure_detail
         
            WHERE  measure_detail.id = " . $measure_detail_id . "

            AND measure_detail.`status` = ''
  
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

        $container_number = $row['container_number'] == "" ? "" : $row['container_number'];
        $date_arrive = $row['date_arrive'] == "" ? "" : $row['date_arrive'];

        $record = GetMeasureDetailRecord($row['id'], $db);
        $record_cust = GetMeasurePersonRecord($row['id'], $db);

        $payment = GetPaymentRecord($row['id'], $group_id, $db);
        
        for($i = 0; $i < count($payment); $i++)
          $payment[$i]['ar'] = $charge;
        
          $days = $row['days'] == "" ? "" : $row['days'];
          $way = $row['way'] == "" ? "" : $row['way'];
          $kilo_unit = $row['kilo_unit'] == "" ? "" : $row['kilo_unit'];
          $kilo_amount = $row['kilo_amount'] == "" ? "" : $row['kilo_amount'];
          $kilo_remark = $row['kilo_remark'] == "" ? "" : $row['kilo_remark'];
          $cuft_unit = $row['cuft_unit'] == "" ? "" : $row['cuft_unit'];
          $cuft_amount = $row['cuft_amount'] == "" ? "" : $row['cuft_amount'];
          $cuft_remark = $row['cuft_remark'] == "" ? "" : $row['cuft_remark'];

          $warehouse_fee = "";

        if($way == "kilo")
            $warehouse_fee = $kilo_amount;
        if($way == "cuft")
            $warehouse_fee = $cuft_amount;

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
           "container_number" => $container_number,
           "date_arrive" => $date_arrive,

           "days" => $days,
           "way" => $way,
           "kilo_unit" => $kilo_unit,
           "kilo_amount" => $kilo_amount,
           "kilo_remark" => $kilo_remark,
           "cuft_unit" => $cuft_unit,
           "cuft_amount" => $cuft_amount,
           "cuft_remark" => $cuft_remark,

           "warehouse_fee" => $warehouse_fee,
        );
    }

    return $merged_results;
}

function existsInArray($entry, $array) {
    
    foreach ($array as $compare) {
        if ($compare["id"] == $entry) {
            return true;
        }
    }
    return false;

}

function GetAr($array)
{
    $amount = 0;

    foreach($array as $item) {
        $amount += ($item['charge'] == "" ? 0 : $item['charge']);
    }

    return number_format((float)$amount, 2, '.', '');
    //return $amount;
}

function GetPayments($array)
{
    $payment = [];

    foreach($array as $item) {
        $payment = array_merge($payment, $item['payment']);
     
    }

    $keys = array_column($payment, 'payment_date');
    array_multisort($keys, SORT_ASC, $payment);


    return $payment;
}

function GetArAmount($array)
{
    $amount = 0;

    foreach($array as $item) {
        $amount += ($item['charge'] == "" ? 0 : $item['charge']);

        $amount += $item['way'] == "kilo" ? ($item['kilo_amount'] == "" ? 0 : $item['kilo_amount']) : 0;
        $amount += $item['way'] == "cuft" ? ($item['cuft_amount'] == "" ? 0 : $item['cuft_amount']) : 0;

        $payment = $item['payment'];
        foreach($payment as $pay) {
            $amount = $amount - (($pay['amount'] == "" ? 0 : $pay['amount']) - ($pay['change'] == "" ? 0 : $pay['change']) - ($pay['courier'] == "" ? 0 : $pay['courier']));
        }
    }

    return number_format((float)$amount, 2, '.', '');
}


function GetMeasurePersonRecord($id, $db){
    $query = "SELECT distinct coalesce(cp.customer, '') cust
                FROM measure_record_detail rd
                    left JOIN receive_record rc ON
                    rd.record_id = rc.id
                    left join contactor_ph cp on cp.id = rd.cust
               
                    WHERE rd.detail_id = " . $id . "
            AND rd.`status`= '' 
            and coalesce(cp.customer, '') <> ''

            union

            SELECT distinct coalesce(cp.customer, '') cust
                FROM measure_record_detail rd
                    left JOIN receive_record rc ON
                    rd.record_id = rc.id
                    left join measure_detail cp on cp.id = rd.detail_id
               
                    WHERE rd.detail_id = " . $id . "
            AND rd.`status` = ''
            and coalesce(cp.customer, '') <> ''

    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
        $merged_results[] = $row['cust'];
  
    }

    return $merged_results;
}

function GetMeasureDetailRecord($id, $db){
    $query = "SELECT rd.detail_id, rc.id, rc.date_receive, rc.customer, rc.description, rc.quantity, rc.supplier, rc.remark, rd.cust cust_id, case when coalesce(cp.customer, '')  <> '' then coalesce(cp.customer, '') when (SELECT coalesce(customer, '') FROM measure_detail WHERE id = " . $id . ") <> '' then (SELECT coalesce(customer, '') FROM measure_detail WHERE id =  " . $id . ") end cust, pick_date, pick_person, pick_note, pick_time, pick_user
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
        $org_pick_date = $row['pick_date'];
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
            "org_pick_date" => $org_pick_date,
            "pick_person" => $pick_person,
            "pick_note" => $pick_note,
            "pick_time" => $pick_time,
            "pick_user" => $pick_user,
            "measure_id" => $measure_id,
          
        );
    }

    return $merged_results;
}


function GetPaymentRecord($id, $group_id, $db){
    /*
    if($group_id <> 0)
    {
        $query = "SELECT id, detail_id, `type`, issue_date, payment_date, person, amount, `change`, courier, remark
        FROM payment    
            WHERE detail_id in (select measure_detail_id from pick_group where group_id = " . $group_id . ")
    AND `status` <> -1 order by payment_date 
        ";
    }
    else
    {
        $query = "SELECT id, detail_id, `type`, issue_date, payment_date, person, amount, `change`, courier, remark
            FROM payment
                            
                WHERE detail_id = " . $id . "
        AND `status` <> -1 order by payment_date 
        ";
    }
    */

    $query = "SELECT id, detail_id, `type`, issue_date, payment_date, person, amount, `change`, courier, remark
            FROM payment
                            
                WHERE detail_id = " . $id . "
        AND `status` <> -1 order by payment_date 
        ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
        $id = $row['id'];
        $detail_id = $row['detail_id'];
        $type = $row['type'];
        $issue_date = $row['issue_date'];
        $payment_date = $row['payment_date'];
        $person = $row['person'];
        $amount = $row['amount'];
        $change = $row['change'];
        $courier = $row['courier'];
        $remark = $row['remark'];
       

        $merged_results[] = array(
            "id" => $id,
            "detail_id" => $detail_id,
            "type" => $type,
            "issue_date" => $issue_date,
            "payment_date" => $payment_date,
            "person" => $person,
            "amount" => $amount,
            "change" => $change,
            "courier" => $courier,
            "remark" => $remark,
            "ar" => "",
        );
    }

    return $merged_results;
}

?>