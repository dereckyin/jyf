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
$search = (isset($_GET['search']) ? $_GET['search'] : "");

// if jwt is not empty
if($jwt){
 
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // get data with group_id first
        $query = "
        SELECT distinct trim(container_number) as container_number
        FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id left join loading  on loading.measure_num = measure_detail.measure_id
    WHERE  group_id <> 0 and pick_group.status = 0 and group_id IN (
    
        select group_id FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id
        WHERE  group_id <> 0 and pick_group.status = 0 ";

        if($keyword == 'N')
            $query .= " AND measure_detail.pickup_status = '' ";

        if($keyword == 'A')
            $query .= " AND measure_detail.pickup_status = 'C' and group_id not in (select group_id FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id group by group_id, pick_group.status, payment_status  having group_id <> 0 and pick_group.status = 0 and payment_status = 'C')";

        if($keyword == 'F')
            $query .= " AND NOT (measure_detail.pickup_status = 'C' and group_id in (select group_id FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id group by group_id, pick_group.status, payment_status  having group_id <> 0 and pick_group.status = 0 and payment_status = 'C')) ";

        if($keyword == 'D')
            $query .= " AND (measure_detail.pickup_status = 'C' and group_id in (select group_id FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id group by group_id, pick_group.status, payment_status  having group_id <> 0 and pick_group.status = 0 and payment_status = 'C')) ";

        if($search != '')
            $query .= " AND (measure_detail.encode = '$search' ) ";

        $query .= ") order by container_number";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $container_number = $row['container_number'];
            if($container_number != '')
            {
                $merged_results[] = array(
                    "container_number" => $container_number,
                
                );
            }
      
        }

        // get data without group_id 
        $query = "
            SELECT distinct trim(container_number) as container_number
                FROM pick_group LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id left join loading  on loading.measure_num = measure_detail.measure_id
            WHERE  group_id = 0 and pick_group.status = 0 ";

        if($keyword == 'N')
            $query .= " AND measure_detail.pickup_status = '' ";

        if($keyword == 'A')
            $query .= " AND measure_detail.pickup_status = 'C' and measure_detail.payment_status = '' ";

        if($keyword == 'F')
            $query .= " AND NOT (measure_detail.pickup_status = 'C' and measure_detail.payment_status = 'C') ";

        if($keyword == 'D')
            $query .= " AND (measure_detail.pickup_status = 'C' and measure_detail.payment_status = 'C') ";

        if($search != '')
            $query .= " AND (measure_detail.encode = '$search' ) ";

        $query .= " order by container_number";

        $stmt = $db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $container_number = $row['container_number'];
            if($container_number != '' && !existsInArray($container_number, $merged_results))
            {
                $merged_results[] = array(
                    "container_number" => $container_number,
                
                );
            }
      
        }

        // order by container_number
        usort($merged_results, function($a, $b) {
            return $a['container_number'] <=> $b['container_number'];
        });

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

function existsInArray($entry, $array) {
    
    foreach ($array as $compare) {
        if ($compare["container_number"] == $entry) {
            return true;
        }
    }
    return false;

}

?>