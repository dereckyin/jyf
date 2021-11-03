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
$ids = (isset($_GET['ids']) ?  $_GET['ids'] : "");
// if jwt is not empty
if($jwt){
 
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
        if(!empty($ids))
        {
            http_response_code(200);
            
            $merged_results = GetMeasureDetail($ids, $db);
            // response in json format
            echo json_encode($merged_results);
        }
        else
        {
            $merged_results = [];
            http_response_code(200);
            json_encode(
                $merged_results);
         
        }
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


function GetMeasureByBatchNumber($id, $db){
    $query = "
            SELECT 0 as is_checked, id, date_encode, date_arrive, currency_rate, remark
                FROM measure_ph
            WHERE  id = " . $id;

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $date_encode = $row['date_encode'];
        $date_arrive = $row['date_arrive'];
        $currency_rate = $row['currency_rate'];
        $remark = $row['remark'];

        $record = GetMeasureDetail($row['id'], $db);

        $merged_results = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "date_encode" => $date_encode,
            "date_arrive" => $date_arrive,
            "currency_rate" => $currency_rate,
            "remark" => $remark,
            "record" => $record,
        );
    }

    return $merged_results;
}

function GetMeasureDetail($id, $db){
    $query = "
            SELECT 0 as is_checked, id, kilo, cuft, kilo_price, cuft_price, charge
                FROM measure_detail
            WHERE  measure_id = " . $id . "
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

        $record = GetMeasureDetailRecord($row['id'], $db);


        $merged_results[] = array(
            "is_checked" => $is_checked,
            "order" => $id,
            "group_id" => 0,
            "kilo" => $kilo,
            "cuft" => $cuft,
            "kilo_price" => $kilo_price,
            "cuft_price" => $cuft_price,
            "charge" => $charge,
           "record" => $record,
        );
    }

    return $merged_results;
}

function GetMeasureDetailRecord($id, $db){
    $query = "SELECT rc.id, rc.date_receive, rc.customer, rc.description, rc.quantity, rc.supplier, rc.remark, rd.cust
                FROM measure_record_detail rd
                    left JOIN receive_record rc ON
                    rd.record_id = rc.id
                    WHERE rd.detail_id = " . $id . "
            AND rd.`status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
        $id = $row['id'];
        $date_receive = $row['date_receive'];
        $customer = $row['customer'];
        $description = $row['description'];
        $quantity = $row['quantity'];
        $supplier = $row['supplier'];
        $remark = $row['remark'];
        $cust = $row['cust'];

        

        $merged_results[] = array(
            "id" => $id,
            "date_receive" => $date_receive,
            "customer" => $customer,
            "description" => $description,
            "quantity" => $quantity,
            "supplier" => $supplier,
            "remark" => $remark,
            "cust" => $cust,
          
        );
    }

    return $merged_results;
}
?>