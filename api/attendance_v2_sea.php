<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$subquery = "";

$merged_results = array();

$sql = "SELECT id, staff username FROM staff_list_sea where status <> 'D' and punch = 1 order by staff";

$stmt = $db->prepare( $sql );
$stmt->execute();

$mdate = isset($_GET['mdate']) ? $_GET['mdate'] : date("Y/m/d");

$edate = isset($_GET['edate']) ? $_GET['edate'] : "";

// replace / as -
$mdate = str_replace("-", "/", $mdate);

/* fetch data */
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if (isset($row)){

        //$mdate = "2020/05/11";
        $subquery = "SELECT id, username, duty_date, duty_time, location, duty_type 
                        FROM on_duty_v2 
                    WHERE  duty_date = '" . $mdate . "' AND duty_type in('A', 'B') and username = '" . $row['username']. "' ORDER BY created_at ";
        //$subquery = " SELECT user.id, username, duty_date, duty_time, location FROM user LEFT JOIN on_duty ON user.id = on_duty.uid WHERE duty_date = '2020/05/11' AND on_duty.duty_type = 'A' and on_duty.uid = 1 ORDER BY on_duty.created_at ";

        $stmt1 = $db->prepare( $subquery );
        $stmt1->execute();

        $row_id = 0;
        $row_name = "";
        $row_count = 0;
        $row_date = "";
        $row_out = "";
        $row_time = "";
        $row_location = "";

        while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            $row_id = $row1['id'];
            $row_name = $row1['username'];

            $dateObject = new DateTime($row1['duty_date'] . " " . $row1['duty_time']);

            if($row1['duty_type'] == 'A')
                $row_date .= "<div style='color:green; display:inline;'>" . $dateObject->format('Y/m/d h:i A') . "</div><br>";
            if($row1['duty_type'] == 'B')
                $row_out .= "<div style='color:grey; display:inline;'>" . $dateObject->format('Y/m/d h:i A') . "</div><br>";

            $row_location .= GetLocation($row1['location']) . "<br>";

            $row_count++;
        }

        $subquery2 = " SELECT id, username, duty_date, duty_time, location FROM  on_duty_v2  WHERE  duty_date = '" . $mdate . "' AND duty_type = 'B' and username = '" . $row['username']. "' ORDER BY created_at ";
        $stmt2 = $db->prepare( $subquery2 );
        $stmt2->execute();

        $leave = 0;
        while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $leave = 1;
        }

        if($row_count != 0)
        {
            $merged_results[] = array("is_checked" => 1, "id" => $row_id, "username" => $row_name, "duty_date" => rtrim($row_date, "<br>"), "duty_out" => rtrim($row_out, "<br>"), "location" => rtrim($row_location, "<br>"), "date" => $mdate, "leave" => $leave);
        }
        else
        {
            $merged_results[] = array("is_checked" => 0, "id" => $row['id'], "username" => $row['username'], "duty_date" => "", "duty_out" => "",  "location" =>"", "date" => $mdate, "leave" => $leave);
        }


    }
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

function GetLocation($loc)
{
    $location = "";
    switch ($loc) {
        case "A":
            $location = "Antel Office";
            break;
        case "M":
            $location = "Main Office";
            break;
        case "T":
            $location = "Taiwan Office";
            break;
        case "B":
            $location = "Shangri-La Store";
            break;
        case "C":
            $location = "Caloocan Warehouse";
            break;
        case "D":
            $location = "Installation";
            break;
        case "E":
            $location = "Client Meeting";
            break;
        case "F":
            $location = "Others";
            break;
            case "W":
                $location = "Warehouse";
            break;
    }

    return $location;
}