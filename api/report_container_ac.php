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

$date_start = (isset($_POST['date_start']) ?  $_POST['date_start'] : '');
$date_end = (isset($_POST['date_end']) ?  $_POST['date_end'] : '');
$type = (isset($_POST['type']) ?  $_POST['type'] : '');

$space = (isset($_POST['space']) ?  $_POST['space'] : '');

// if jwt is not empty
if($jwt){
 
    $date_start = str_replace('-', '/', $date_start);
    $date_end = str_replace('-', '/', $date_end);
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
        http_response_code(200);
            
        $merged_results = array();

        $query = "select mp.id,
                    sum(IF(abs(charge - (md.kilo * md.kilo_price)) > abs(charge - (md.cuft * md.cuft_price)), 0, charge)) charge_kilo,
                    sum(IF(abs(charge - (md.kilo * md.kilo_price)) <= abs(charge - (md.cuft * md.cuft_price)), 0, charge)) charge_cuft,
                    sum(if(md.payment_status = 'C', md.charge, 0)) charge,
                    sum(md.charge) - sum(if(md.payment_status = 'C', md.charge, 0)) ar,
                    mp.remark, mp.notes
                  from measure_ph mp 
                    left join measure_detail md on mp.id = md.measure_id 
                    left join loading l on mp.id = l.measure_num 
                    left join loading_date_history ldh on l.id = ldh.loading_id 
                  where mp.status <> -1
                    group by
                    mp.id";
       

        $stmt = $db->prepare( $query );
        $stmt->execute();


        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items = [];
            $items = GetLoadingDetail($db, $row["id"]);

            $taiwan_pay = [];
            $taiwan_pay = GetTaiwanPayDetail($db, $row["id"]);

            $taiwan_charge_kilo = 0;
            $philippine_charge_kilo = 0;
            $taiwan_charge_cuft = 0;
            $philippine_charge_cuft = 0;
            $taiwan_charge = 0;
            $philippine_charge = 0;

            $taiwan_complete_charge = 0;
            $philippine_complete_charge = 0;

            $taiwan_incomplete_charge = 0;
            $philippine_incomplete_charge = 0;

            $taiwan_ar = 0;
            $philippine_ar = 0;
            $taiwan_courier = 0;

            foreach($taiwan_pay as $item)
            {
                if($item['taiwan_pay'] == 'taiwan_pay')
                {
                    $taiwan_charge_kilo += $item['total_charge_kilo'];
                    $taiwan_charge_cuft += $item['total_charge_cuft'];
                    $taiwan_charge += $item['total_charge'];

                    if($item['payment_status'] == 'C')
                        $taiwan_complete_charge += $item['total_amount'];
                    else
                        $taiwan_incomplete_charge += $item['total_charge'];

                    $taiwan_ar += $item['total_ar'];
                    $taiwan_courier += $item['courier'];
                }
                else
                {
                    $philippine_charge_kilo += $item['total_charge_kilo'];
                    $philippine_charge_cuft += $item['total_charge_cuft'];
                    $philippine_charge += $item['total_charge'];

                    if($item['payment_status'] == 'C')
                        $philippine_complete_charge += $item['total_amount'];
                    else
                        $philippine_incomplete_charge += $item['total_charge'];

                    $philippine_complete_charge += $item['total_complete_charge'];
                    $philippine_ar += $item['total_ar'];
                }
            }

            $id = $row["id"];
            $charge_kilo = $row["charge_kilo"];
            $charge_cuft = $row["charge_cuft"];
            $charge = $row["charge"];
            $ar = $row["ar"];
            $remark = $row["remark"];

            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "charge_kilo" => $charge_kilo,
                "charge_cuft" => $charge_cuft,
                "taiwan_charge_kilo" => $taiwan_charge_kilo,
                "philippine_charge_kilo" => $philippine_charge_kilo,
                "taiwan_charge_cuft" => $taiwan_charge_cuft,
                "philippine_charge_cuft" => $philippine_charge_cuft,
                "taiwan_charge" => $taiwan_charge,
                "philippine_charge" => $philippine_charge,
                "taiwan_complete_charge" => $taiwan_complete_charge,
                "philippine_complete_charge" => $philippine_complete_charge,
                
                "taiwan_incomplete_charge" => $taiwan_incomplete_charge,
                "philippine_incomplete_charge" => $philippine_incomplete_charge,

                "taiwan_ar" => $taiwan_ar,
                "philippine_ar" => $philippine_ar,
                "taiwan_courier" => $taiwan_courier,
                "loading" => $items,
                "charge" => $charge,
                "ar" => $ar,
                "remark" => $remark,
                "notes" => $row["notes"]
            );
            
        }

        if($space == "s")
        {
            if($type == "1")
            {
                $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                    $in_value = false;
    
                    foreach($a['loading'] as $item)
                    {
                        if($item['eta_date'] == "")
                        {
                            $in_value = true;
                        }
                    
                    }
    
                    return $in_value;
                });
            }
            
            if($type == "2")
            {
                $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                    $in_value = false;
    
                    foreach($a['loading'] as $item)
                    {
                        if($item['date_arrive'] == "")
                        {
                            $in_value = true;
                        }
                    
                    }
    
                    return $in_value;
                });
            }

            
        }
        else if ($space == "i")
        {
            if($type == "1")
            {

                $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                    $in_value = false;
    
                    foreach($a['loading'] as $item)
                    {
                        if(($item['date_arrive'] <= $date_end && $item['date_arrive'] >= $date_start) || $item['eta_date'] == "")
                        {
                            $in_value = true;
                        }
                    
                    }
    
                    return $in_value;
                });
            }

            if($type == "2" )
            {

                $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                    $in_value = false;
    
                    foreach($a['loading'] as $item)
                    {
                        if(($item['date_arrive'] <= $date_end && $item['date_arrive'] >= $date_start) || $item['date_arrive'] == "")
                        {
                            $in_value = true;
                        }
                    
                    }
    
                    return $in_value;
                });
            }
        }
        else
        {
            if($date_end != '' && $date_start != '' && $type == "1")
            {
                $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                    $in_value = false;
    
                    foreach($a['loading'] as $item)
                    {
                        if($item['eta_date'] <= $date_end && $item['eta_date'] >= $date_start)
                        {
                            $in_value = true;
                        }
                    
                    }
    
                    return $in_value;
                });
            }
    
            if($date_end != '' && $date_start != '' && $type == "2")
            {
                $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                    $in_value = false;
    
                    foreach($a['loading'] as $item)
                    {
                        if($item['date_arrive'] <= $date_end && $item['date_arrive'] >= $date_start)
                        {
                            $in_value = true;
                        }
                    
                    }
    
                    return $in_value;
                });
            }

            
        }
        

        if($type == '1')
            usort($merged_results, "compare_eta");
        else
            usort($merged_results, "compare_arrive");

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
                
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

function compare_eta($a, $b)
{
    $last_a = '';
    $last_b = '';

    foreach($a['loading'] as $item)
    {
        if($item['eta_date'] > $last_a)
        {
            $last_a = $item['eta_date'];
        }
    }

    foreach($b['loading'] as $item)
    {
        if($item['eta_date'] > $last_b)
        {
            $last_b = $item['eta_date'];
        }
    }

   return ($last_a > $last_b);
}

function compare_arrive($a, $b)
{
    $last_a = '';
    $last_b = '';

    foreach($a['loading'] as $item)
    {
        if($item['date_arrive'] > $last_a)
        {
            $last_a = $item['date_arrive'];
        }
    }

    foreach($b['loading'] as $item)
    {
        if($item['date_arrive'] > $last_b)
        {
            $last_b = $item['date_arrive'];
        }
    }

   return ($last_a > $last_b);

}

function filter_eta($a, $b)
{
    $last_a = '';
    $last_b = '';

    foreach($a['loading'] as $item)
    {
        if($item['eta_date'] > $last_a)
        {
            $last_a = $item['eta_date'];
        }
    }

    foreach($b['loading'] as $item)
    {
        if($item['eta_date'] > $last_b)
        {
            $last_b = $item['eta_date'];
        }
    }

   return ($last_a > $last_b);
}

function GetLoadingDetail($conn, $id){
    $sql = "select 
    mp.id, 
    l.container_number,
    CONCAT_WS(',', l.date_sent, IFNULL(ldh.date_sent, '')) date_sent,
    CONCAT_WS(',', l.eta_date, IFNULL(ldh.eta_date, '')) eta_date,
    CONCAT_WS(',', l.date_arrive, IFNULL(ldh.date_arrive, '')) date_arrive
from measure_ph mp 
    left join loading l on mp.id = l.measure_num 
    left join loading_date_history ldh on l.id = ldh.loading_id  
where mp.id =  ($id)";
    
            
    $stmt = $conn->prepare( $sql );
    $stmt->execute();

    $merged_results = [];

    // die if SQL statement failed
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $loading_id = $row['id'];
        $container_number = $row['container_number'];
        $date_sent_ary = explode(",", rtrim($row['date_sent'], ','));
        $eta_date_ary = explode(",", rtrim($row['eta_date'], ','));
        $date_arrive_ary = explode(",", rtrim($row['date_arrive'], ','));

        $merged_results[] = array( 
            "loading_id" => $loading_id,
            "container_number" => $container_number,
            "date_sent" => end($date_sent_ary),
            "eta_date" => end($eta_date_ary),
            "date_arrive" => end($date_arrive_ary),
        );
    }

    return $merged_results;

}

function GetTaiwanPayDetail($conn, $id){
    $sql = "select md.id, md.charge, md.payment_status, rr.taiwan_pay, p.courier, tpr.amount, 
        IF(abs(charge - (md.kilo * md.kilo_price)) > abs(charge - (md.cuft * md.cuft_price)), 0, charge) charge_kilo, 
        IF(abs(md.charge - (md.kilo * md.kilo_price)) <= abs(md.charge - (md.cuft * md.cuft_price)), 0, charge) charge_cuft,
        if(md.payment_status = 'C', md.charge, 0) complete_charge,
        md.charge - if(md.payment_status = 'C', md.charge, 0) ar
    from measure_ph mp 
        left join measure_detail md on md.measure_id = mp.id and md.status <> -1
        left join payment p on p.detail_id = md.id and p.status <> -1
        left join measure_record_detail mrd on mrd.detail_id = md.id and mrd.status <> -1
        left join receive_record rr on mrd.record_id = rr.id and rr.status <> 'D'
        left join taiwan_pay_record tpr on tpr.record_id = rr.id 
    where mp.id =  ($id)
    order by md.id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $merged_results = [];
    $records_by_id = [];

    // Collect records by md.id
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $measure_detail_id = $row['id'];
        $records_by_id[$measure_detail_id][] = $row;
    }

    // Separate records into taiwan_pay_records and philippine_pay_records
    $taiwan_pay_records = [];
    $philippine_pay_records = [];

    foreach ($records_by_id as $measure_detail_id => $records) {
        $all_taiwan_pay = true;
        $total_charge = 0;
        $total_amount = 0;
        $total_charge_kilo = 0;
        $total_charge_cuft = 0;
        $total_complete_charge = 0;
        $total_ar = 0;

        foreach ($records as $record) {
            $total_charge = $record['charge'];
            $total_amount = $record['amount'];
            $total_charge_kilo = $record['charge_kilo'];
            $total_charge_cuft = $record['charge_cuft'];
            $total_complete_charge = $record['complete_charge'];
            $total_ar = $record['ar'];
            if ($record['taiwan_pay'] != 1) {
                $all_taiwan_pay = false;
            }
        }

        // Determine payment status and categorize accordingly
        $payment_status = $records[0]['payment_status']; // Assuming same payment status for all

        if ($all_taiwan_pay) {
            $taiwan_pay_records[] = [
                "measure_detail_id" => $measure_detail_id,
                "total_charge" => $total_charge,
                "total_amount" => $total_amount,
                "total_charge_kilo" => $total_charge_kilo,
                "total_charge_cuft" => $total_charge_cuft,
                "payment_status" => $payment_status,
                "courier" => $records[0]['courier'],
                "taiwan_pay" => 'taiwan_pay',
                "total_complete_charge" => $total_complete_charge,
                "total_ar" => $total_ar
            ];
        } else {
            $philippine_pay_records[] = [
                "measure_detail_id" => $measure_detail_id,
                "total_charge" => $total_charge,
                "total_amount" => $total_amount,
                "total_charge_kilo" => $total_charge_kilo,
                "total_charge_cuft" => $total_charge_cuft,
                "payment_status" => $payment_status,
                "courier" => $records[0]['courier'],
                "taiwan_pay" => 'philippine_pay',
                "total_complete_charge" => $total_complete_charge,
                "total_ar" => $total_ar
            ];
        }
    }

    // Combine taiwan_pay_records and philippine_pay_records and return
    $merged_results = array_merge($taiwan_pay_records, $philippine_pay_records);
    return $merged_results;
}


?>