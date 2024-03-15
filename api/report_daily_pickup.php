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

// if jwt is not empty
if($jwt){
 
    $date_start = str_replace('/', '-', $date_start);
    $date_end = str_replace('/', '-', $date_end);
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
        http_response_code(200);

        $query = "select distinct pick_date, TRIM(measure_detail.customer) customer, encode, 0 customer_count, 0 encode_count  FROM pick_group 
        LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id 
        left join measure_record_detail on measure_record_detail.detail_id = measure_detail.id
        left join receive_record on receive_record.id = measure_record_detail.record_id
        WHERE  pick_group.status = 0 and receive_record.pick_date >= '$date_start' and receive_record.pick_date <= '$date_end'
        order by pick_date, TRIM(measure_detail.customer)";
       

        $stmt = $db->prepare( $query );
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pick_date = '';
        $customer = '';

        $encode_count = 0;
        $customer_count = 0;

        $pick_date_index = 0;
        $customer_index = 0;

        $pre_pick_date = '';
        $pre_customer = '';

        for($i = 0; $i < count($result); $i++) {
            $pick_date = $result[$i]['pick_date'];
            $customer = $result[$i]['customer'];

            if($pre_pick_date != $pick_date) {
                $pre_pick_date = $pick_date;
                $pre_customer = $customer;
                $encode_count = 0;
                $customer_count = 0;
                $pick_date_index = $i;
                $customer_index = $i;
            }

            if($pre_customer != $customer) {
                $pre_customer = $customer;
                $customer_count = 0;
                $customer_index = $i;
            }

            $encode_count++;
            $customer_count++;

            $result[$pick_date_index]['encode_count'] = $encode_count;
            $result[$customer_index]['customer_count'] = $customer_count;
        }



        // $groupedData = [];

        // while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //     $date = $row['pick_date'];
        //     $customer = $row['customer'];
        //     $encode = $row['encode'];

        //     // Initialize date if not exists
        //     if (!isset($groupedData[$date])) {
        //         $groupedData[$date] = [];
        //     }

        //     // Initialize customer if not exists
        //     if (!isset($groupedData[$date][$customer])) {
        //         $groupedData[$date][$customer] = [
        //             "customer" => $customer,
        //             "encodes" => []
        //         ];
        //     }

        //     // Add encode to the customer
        //     $groupedData[$date][$customer]['encodes'][] = ["encode" => $encode];
        // }

        // // Transform to the desired format
        // $result = [];
        // foreach ($groupedData as $date => $customers) {
        //     $customerList = [];
        //     foreach ($customers as $customer) {
        //         $customerList[] = $customer;
        //     }

        //     // count all encodes
        //     $total = 0;
        //     foreach ($customerList as $customer) {
        //         $total += count($customer['encodes']);
        //     }

        //     $result[] = ["date" => $date, "customers" => $customerList, "total" => $total];

        // }

        echo json_encode($result, JSON_UNESCAPED_SLASHES);
                
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



?>