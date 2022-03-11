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
$container_number = (isset($_POST['container_number']) ?  $_POST['container_number'] : '');
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
        http_response_code(200);

        if($date_start == '' && $date_end == '')
        {
            $date_start = date('Y');

            $this_year = date("Y/m/d",strtotime($date_start . '-01-01' . " first day of 0 year"));
            $last_year = date("Y/m/d",strtotime($date_start . '-01-01' . " first day of 1 year"));

            $date_start    = $this_year;
            $date_end      = $last_year;
        }

            
        $merged_results = array();


        $query = "select mp.id,
                    sum(IF(abs(charge - (md.kilo * md.kilo_price)) > abs(charge - (md.cuft * md.cuft_price)), 0, charge)) charge_kilo,
                    sum(IF(abs(charge - (md.kilo * md.kilo_price)) <= abs(charge - (md.cuft * md.cuft_price)), 0, charge)) charge_cuft,
                    sum(if(md.payment_status = 'C', md.charge, 0)) charge,
                    sum(md.charge) - sum(if(md.payment_status = 'C', md.charge, 0)) ar
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

            $id = $row["id"];
            $charge_kilo = $row["charge_kilo"];
            $charge_cuft = $row["charge_cuft"];
            $charge = $row["charge"];
            $ar = $row["ar"];

            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "charge_kilo" => $charge_kilo,
                "charge_cuft" => $charge_cuft,
                "loading" => $items,
                "charge" => $charge,
                "ar" => $ar
            );
            
        }

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


function GetLoadingDetail($conn, $id){
    $sql = "select 
    mp.id, 
    l.container_number,
    ldh.eta_date,
    ldh.date_arrive 
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
        $eta_date_ary = explode(",", $row['eta_date']);
        $date_arrive_ary = explode(",", $row['date_arrive']);

        $merged_results[] = array( 
            "loading_id" => $loading_id,
            "container_number" => $container_number,
            "eta_date" => end($eta_date_ary),
            "date_arrive" => end($date_arrive_ary),
        );
    }

    return $merged_results;

}


?>